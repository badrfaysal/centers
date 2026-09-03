<?php

namespace App\Http\Controllers;

use App\Models\SessionSchedule;
use App\Models\Child;
use App\Models\Specialist;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    /**
     * Display the Master Multi-Specialist Timetable and Calendar.
     */
    public function index(Request $request)
    {
        $query = SessionSchedule::with(['child', 'specialist'])->latest('session_date');

        if ($request->filled('specialist') && $request->specialist !== 'all') {
            $query->where('specialist_name', $request->specialist);
        }

        if ($request->filled('room') && $request->room !== 'all') {
            $query->where('room_name', $request->room);
        }

        if ($request->filled('child_id')) {
            $query->where('child_id', $request->child_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('session_date', $request->date);
        }

        $schedules = $query->paginate(15);

        // إحصائيات الكالندر
        $todayCount = SessionSchedule::whereDate('session_date', Carbon::today())->count();
        $thisWeekCount = SessionSchedule::whereBetween('session_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
        $totalScheduledCount = SessionSchedule::where('status', 'scheduled')->count();

        $specialists = Specialist::where('status', 'active')->orderBy('name')->get();
        $children = Child::where('status', 'active')->orderBy('name')->get();
        $rooms = $this->getRoomsList();

        $allCalendarSessions = SessionSchedule::with(['child', 'specialist'])->get();

        $calendarEvents = $allCalendarSessions->map(function($s) {
            return [
                'id' => $s->id,
                'child_id' => $s->child_id,
                'child_name' => $s->child ? $s->child->name : 'طفل',
                'specialist_name' => $s->specialist_name,
                'session_title' => $s->session_title,
                'session_date' => $s->session_date ? $s->session_date->format('Y-m-d') : '',
                'start_time' => $s->start_time,
                'end_time' => $s->end_time,
                'formatted_time_range' => $s->formatted_time_range,
                'status' => $s->status,
                'color' => $s->calendar_color,
                'room_name' => $s->room_name,
                'day_name_arabic' => $s->day_name_arabic,
                'attendance_status' => $s->attendance_status,
                'notes' => $s->notes,
                'whatsapp_reminder_url' => $s->whatsapp_reminder_url,
            ];
        })->values();

        $sessionTypes = \App\Http\Controllers\SettingController::getDropdownList('session_types');

        return view('schedules.index', compact(
            'schedules',
            'calendarEvents',
            'todayCount',
            'thisWeekCount',
            'totalScheduledCount',
            'specialists',
            'children',
            'rooms',
            'sessionTypes'
        ));
    }

    /**
     * Display the Specialist's Personal Timetable in Doctor Portal.
     */
    public function specialistTimetable(Request $request)
    {
        $specialists = Specialist::where('status', 'active')->orderBy('name')->get();
        
        $selectedSpecialistName = $request->input('specialist') ?? ($specialists->first()->name ?? 'د. أحمد يسري');

        $todaySessions = SessionSchedule::with('child')
            ->where('specialist_name', $selectedSpecialistName)
            ->whereDate('session_date', Carbon::today())
            ->orderBy('start_time')
            ->get();

        $weekSessions = SessionSchedule::with('child')
            ->where('specialist_name', $selectedSpecialistName)
            
            ->orderBy('session_date')
            ->orderBy('start_time')
            ->get();

        $assignedChildren = Child::where('main_specialist', 'like', "%{$selectedSpecialistName}%")->get();
        $allChildren = Child::where('status', 'active')->orderBy('name')->get();
        $rooms = $this->getRoomsList();

        $calendarEvents = $weekSessions->map(function($s) {
            return [
                'id' => $s->id,
                'child_id' => $s->child_id,
                'child_name' => $s->child ? $s->child->name : 'طفل',
                'specialist_name' => $s->specialist_name,
                'session_title' => $s->session_title,
                'session_date' => $s->session_date ? $s->session_date->format('Y-m-d') : '',
                'start_time' => $s->start_time,
                'end_time' => $s->end_time,
                'formatted_time_range' => $s->formatted_time_range,
                'status' => $s->status,
                'color' => $s->calendar_color,
                'room_name' => $s->room_name,
                'day_name_arabic' => $s->day_name_arabic,
                'attendance_status' => $s->attendance_status,
                'notes' => $s->notes,
                'whatsapp_reminder_url' => $s->whatsapp_reminder_url,
            ];
        })->values();

        $sessionTypes = \App\Http\Controllers\SettingController::getDropdownList('session_types');

        return view('schedules.specialist_timetable', compact(
            'specialists',
            'selectedSpecialistName',
            'todaySessions',
            'weekSessions',
            'calendarEvents',
            'assignedChildren',
            'allChildren',
            'rooms',
            'sessionTypes'
        ));
    }

    /**
     * Apologize for a full day of sessions for a specialist.
     */
    public function apologizeDay(Request $request)
    {
        $validated = $request->validate([
            'specialist_name' => 'required|string',
            'session_date' => 'required|date',
            'notes' => 'nullable|string'
        ]);

        $sessions = SessionSchedule::with('child')
            ->where('specialist_name', $validated['specialist_name'])
            ->where('session_date', $validated['session_date'])
            ->where('status', 'scheduled')
            ->get();

        if ($sessions->isEmpty()) {
            return back()->with('error', 'لا توجد جلسات مجدولة (غير مكتملة) في هذا اليوم للاعتذار عنها.');
        }

        // Cancel all scheduled sessions
        foreach ($sessions as $session) {
            $session->update([
                'status' => 'cancelled',
                'attendance_status' => 'absent',
                'notes' => 'تم الاعتذار عن اليوم بالكامل من قبل الأخصائي. ' . ($validated['notes'] ?? '')
            ]);
        }

        // Create an urgent parent message to notify the center
        $specialist = Specialist::where('name', $validated['specialist_name'])->first();
        \App\Models\ParentMessage::create([
            'child_id' => $sessions->first()->child_id, // We just need a valid child_id for the schema, but recipient is center
            'parent_name' => 'الأخصائي: ' . $validated['specialist_name'],
            'recipient_type' => 'center',
            'subject' => 'اعتذار طارئ عن يوم عمل: ' . $validated['session_date'],
            'message' => "يعتذر الأخصائي ({$validated['specialist_name']}) عن حضور دوامه في يوم {$validated['session_date']}. تم إلغاء جميع الجلسات المتبقية في هذا اليوم.",
            'is_urgent' => true,
        ]);

        return back()->with('success', 'تم الاعتذار عن الجلسات المتبقية في هذا اليوم وإرسال إشعار عاجل للإدارة.');
    }

    /**
     * Store a newly created session schedule.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'child_id'        => 'required|exists:children,id',
            'specialist_name' => 'required|string|max:100',
            'session_title'   => 'required|string|max:150',
            'session_date'    => 'required|date',
            'start_time'      => 'required',
            'end_time'        => 'nullable',
            'room_name'       => 'required|string|max:100',
            'is_recurring'    => 'nullable|boolean',
            'notes'           => 'nullable|string|max:1000',
        ]);

        $validated['is_recurring'] = $request->boolean('is_recurring');
        
        $dateObj = Carbon::parse($validated['session_date']);
        $days = ['Saturday'=>'السبت', 'Sunday'=>'الأحد', 'Monday'=>'الإثنين', 'Tuesday'=>'الثلاثاء', 'Wednesday'=>'الأربعاء', 'Thursday'=>'الخميس', 'Friday'=>'الجمعة'];
        $validated['day_of_week'] = $days[$dateObj->format('l')] ?? $dateObj->format('l');

        $spec = Specialist::where('name', $validated['specialist_name'])->first();
        if ($spec) {
            $validated['specialist_id'] = $spec->id;
            
            // Check working days
            $arabicDay = $validated['day_of_week'];
            if (is_array($spec->work_days) && !in_array($arabicDay, $spec->work_days)) {
                return redirect()->back()->with('error', "لا يمكن تسجيل الموعد. الأخصائي ({$spec->name}) لا يعمل في يوم {$arabicDay}.")->withInput();
            }
        }

        // Room Conflict Preventer
        $st = Carbon::parse($validated['start_time']);
        $et = !empty($validated['end_time']) ? Carbon::parse($validated['end_time']) : $st->copy()->addMinutes(45);
        $stStr = $st->format('H:i');
        $etStr = $et->format('H:i');

        $conflict = SessionSchedule::where('room_name', $validated['room_name'])
            ->where('session_date', $validated['session_date'])
            ->where('status', '!=', 'cancelled')
            ->where(function($q) use ($stStr, $etStr) {
                $q->where('start_time', '<', $etStr)
                  ->where('end_time', '>', $stStr);
            })
            ->first();

        if ($conflict) {
            return redirect()->back()->with('error', "الرادار الذكي يمنع التعارض: الغرفة ({$validated['room_name']}) مشغولة في نفس التوقيت بطفل آخر.")->withInput();
        }

        $schedule = SessionSchedule::create($validated);

        if ($validated['is_recurring']) {
            for ($i = 1; $i <= 3; $i++) {
                $nextDate = $dateObj->copy()->addWeeks($i);
                $recurringData = $validated;
                $recurringData['session_date'] = $nextDate->toDateString();
                SessionSchedule::create($recurringData);
            }
        }

        $child = Child::find($validated['child_id']);

        return redirect()->back()
            ->with('success', "تم تحديد وجدولة موعد الجلسة للبطل ({$child->name}) بنجاح! ستظهر فوراً في جدول الأخصائي وكالندر ولي الأمر.");
    }

    /**
     * Update an existing schedule.
     */
    public function update(Request $request, SessionSchedule $schedule)
    {
        $validated = $request->validate([
            'child_id' => 'required|exists:children,id',
            'specialist_name' => 'required|string',
            'session_title' => 'required|string',
            'session_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'room_name' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        $dateObj = Carbon::parse($validated['session_date']);
        $days = ['Saturday'=>'السبت', 'Sunday'=>'الأحد', 'Monday'=>'الإثنين', 'Tuesday'=>'الثلاثاء', 'Wednesday'=>'الأربعاء', 'Thursday'=>'الخميس', 'Friday'=>'الجمعة'];
        $validated['day_of_week'] = $days[$dateObj->format('l')] ?? $dateObj->format('l');

        $spec = Specialist::where('name', $validated['specialist_name'])->first();
        if ($spec) {
            $validated['specialist_id'] = $spec->id;

            // Check working days
            $arabicDay = $validated['day_of_week'];
            if (is_array($spec->work_days) && !in_array($arabicDay, $spec->work_days)) {
                return redirect()->back()->with('error', "لا يمكن تعديل الموعد. الأخصائي ({$spec->name}) لا يعمل في يوم {$arabicDay}.")->withInput();
            }
        }

        // Room Conflict Preventer
        $st = Carbon::parse($validated['start_time']);
        $et = !empty($validated['end_time']) ? Carbon::parse($validated['end_time']) : $st->copy()->addMinutes(45);
        $stStr = $st->format('H:i');
        $etStr = $et->format('H:i');

        $conflict = SessionSchedule::where('room_name', $validated['room_name'])
            ->where('session_date', $validated['session_date'])
            ->where('id', '!=', $schedule->id)
            ->where('status', '!=', 'cancelled')
            ->where(function($q) use ($stStr, $etStr) {
                $q->where('start_time', '<', $etStr)
                  ->where('end_time', '>', $stStr);
            })
            ->first();

        if ($conflict) {
            return redirect()->back()->with('error', "الرادار الذكي يمنع التعارض: الغرفة ({$validated['room_name']}) مشغولة في نفس التوقيت بطفل آخر.")->withInput();
        }

        // Reset status if it was cancelled
        $validated['status'] = 'scheduled';
        $validated['attendance_status'] = 'pending';

        $schedule->update($validated);

        return redirect()->back()->with('success', 'تم تعديل الموعد بنجاح!');
    }

    /**
     * Update attendance or status.
     */
    public function updateAttendance(Request $request, SessionSchedule $schedule)
    {
        $validated = $request->validate([
            'attendance_status' => 'required|in:pending,attended,absent',
        ]);

        $schedule->update([
            'attendance_status' => $validated['attendance_status'],
            'status'            => $validated['attendance_status'] === 'attended' ? 'completed' : $schedule->status,
        ]);

        return redirect()->back()->with('success', 'تم تحديث حالة حضور الجلسة بنجاح.');
    }

    /**
     * Remove session schedule.
     */
    public function destroy(SessionSchedule $schedule)
    {
        $schedule->delete();
        return redirect()->back()->with('success', 'تم حذف الموعد من الجدول بنجاح.');
    }

    private function getRoomsList(): array
    {
        return \App\Http\Controllers\SettingController::getDropdownList('rooms');
    }
}

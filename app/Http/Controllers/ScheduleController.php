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
                'specialist_id' => $s->specialist_id,
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
                'specialist_id' => $s->specialist_id,
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
            'session_date'    => 'required|date',
            'notes'           => 'nullable|string'
        ]);

        $sessions = SessionSchedule::with('child')
            ->where('specialist_name', $validated['specialist_name'])
            ->where('session_date', $validated['session_date'])
            ->where('status', 'scheduled')
            ->get();

        if ($sessions->isEmpty()) {
            return back()->with('error', 'لا توجد جلسات مجدولة (غير مكتملة) في هذا اليوم للاعتذار عنها.');
        }

        // We DO NOT cancel the sessions! We keep them scheduled, but we notify the admin and parents.
        $childrenIds = $sessions->pluck('child_id')->unique();
        
        $specialist = \App\Models\Specialist::where('name', $validated['specialist_name'])->first();
        
        foreach ($childrenIds as $childId) {
            $child = \App\Models\Child::find($childId);
            if (!$child) continue;

            \App\Models\ParentMessage::create([
                'child_id'       => $childId,
                'parent_name'    => $child->parent_name ?: 'ولي الأمر',
                'recipient_type' => 'center',
                'subject'        => 'اعتذار طارئ للأخصائي - سيتم توفير بديل',
                'message'        => "نعتذر لكم، لقد اعتذر الأخصائي ({$validated['specialist_name']}) عن عمل يوم {$validated['session_date']} لظروف طارئة. يرجى العلم أنه جاري توفير أخصائي بديل لتغطية الجلسة في نفس الموعد.",
                'is_urgent'      => true,
            ]);
        }

        return back()->with('success', 'تم إرسال إشعار اعتذارك للإدارة ولأولياء الأمور بنجاح. سيتم توفير أخصائي بديل لتغطية الجلسات.');
    }

    /**
     * Apologize for a specific session for a specialist.
     */
    public function apologizeSession(Request $request, SessionSchedule $sessionSchedule)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string'
        ]);

        if ($sessionSchedule->status === 'cancelled') {
            return back()->with('error', 'هذه الجلسة ملغاة بالفعل.');
        }

        // Add note indicating apology and cancel the session
        $newNote = "اعتذار الأخصائي لظروف طارئة";
        if (!empty($validated['notes'])) {
            $newNote .= " - " . $validated['notes'];
        }
        $sessionSchedule->notes = $sessionSchedule->notes ? $sessionSchedule->notes . "\n" . $newNote : $newNote;
        $sessionSchedule->status = 'cancelled';
        $sessionSchedule->save();

        $child = $sessionSchedule->child;
        if ($child) {
            \App\Models\ParentMessage::create([
                'child_id'       => $child->id,
                'parent_name'    => $child->parent_name ?: 'ولي الأمر',
                'recipient_type' => 'center',
                'subject'        => 'اعتذار طارئ للأخصائي عن الجلسة',
                'message'        => "نعتذر لكم، تم الاعتذار عن جلسة اليوم للأخصائي ({$sessionSchedule->specialist_name}) بتاريخ {$sessionSchedule->session_date} لظروف طارئة. سيتم إشعاركم في حال تم استبدال الأخصائي بأخصائي آخر.",
                'is_urgent'      => true,
            ]);
        }

        return back()->with('success', 'تم تسجيل الاعتذار عن الجلسة بنجاح، وتم إشعار الإدارة وولي الأمر.');
    }

    /**
     * Store a newly created session schedule.
     */
    public function store(Request $request)
    {
        if ($request->filled('session_date') && Carbon::parse($request->session_date)->isPast() && !Carbon::parse($request->session_date)->isToday()) {
            return redirect()->back()->with('error', 'لا يمكن جدولة موعد في تاريخ مضى.');
        }
        $validated = $request->validate([
            'child_id'        => 'required|exists:children,id',
            'specialist_name' => 'required|string|max:100',
            'session_title'   => 'required|string|max:150',
            'session_date'    => 'required|date',
            'start_time'      => 'required',
            'end_time'        => 'nullable',
            'room_name'       => 'required|string|max:100',
            'is_recurring'    => 'nullable|boolean',
            'recurring_weeks' => 'nullable|integer|min:1|max:52',
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

        $roomConflict = SessionSchedule::where('room_name', $validated['room_name'])
            ->where('session_date', $validated['session_date'])
            ->where('status', '!=', 'cancelled')
            ->where(function($q) use ($stStr, $etStr) {
                $q->where('start_time', '<', $etStr)
                  ->where('end_time', '>', $stStr);
            })
            ->first();

        if ($roomConflict) {
            return redirect()->back()->with('error', "الغرفة ({$validated['room_name']}) مشغولة في هذا الموعد بطفل آخر، برجاء اختيار غرفة أو قاعة أخرى.")->withInput();
        }

        // Specialist Conflict Preventer
        $specialistConflict = SessionSchedule::where('specialist_name', $validated['specialist_name'])
            ->where('session_date', $validated['session_date'])
            ->where('status', '!=', 'cancelled')
            ->where(function($q) use ($stStr, $etStr) {
                $q->where('start_time', '<', $etStr)
                  ->where('end_time', '>', $stStr);
            })
            ->first();

        if ($specialistConflict) {
            return redirect()->back()->with('error', "الأخصائي ({$validated['specialist_name']}) لديه جلسة أخرى في نفس التوقيت، برجاء تعديل وقت الجلسة.")->withInput();
        }

        $schedule = SessionSchedule::create($validated);

        if ($validated['is_recurring']) {
            $weeks = $request->input('recurring_weeks', 4);
            for ($i = 1; $i <= $weeks; $i++) {
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
        if (Carbon::parse($schedule->session_date)->isPast() && !Carbon::parse($schedule->session_date)->isToday()) {
            return redirect()->back()->with('error', 'لا يمكن تعديل جلسة مضى تاريخها.');
        }
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

        $roomConflict = SessionSchedule::where('room_name', $validated['room_name'])
            ->where('session_date', $validated['session_date'])
            ->where('id', '!=', $schedule->id)
            ->where('status', '!=', 'cancelled')
            ->where(function($q) use ($stStr, $etStr) {
                $q->where('start_time', '<', $etStr)
                  ->where('end_time', '>', $stStr);
            })
            ->first();

        if ($roomConflict) {
            return redirect()->back()->with('error', "الغرفة ({$validated['room_name']}) مشغولة في هذا الموعد بطفل آخر، برجاء اختيار غرفة أو قاعة أخرى.")->withInput();
        }

        // Specialist Conflict Preventer
        $specialistConflict = SessionSchedule::where('specialist_name', $validated['specialist_name'])
            ->where('session_date', $validated['session_date'])
            ->where('id', '!=', $schedule->id)
            ->where('status', '!=', 'cancelled')
            ->where(function($q) use ($stStr, $etStr) {
                $q->where('start_time', '<', $etStr)
                  ->where('end_time', '>', $stStr);
            })
            ->first();

        if ($specialistConflict) {
            return redirect()->back()->with('error', "الأخصائي ({$validated['specialist_name']}) لديه جلسة أخرى في نفس التوقيت، برجاء تعديل وقت الجلسة.")->withInput();
        }

        // Reset status if it was cancelled
        $validated['status'] = 'scheduled';
        $validated['attendance_status'] = 'pending';

        $oldSpecialist = $schedule->specialist_name;

        $schedule->update($validated);

        // Notify parent if specialist was replaced
        if ($oldSpecialist !== $validated['specialist_name']) {
            $child = $schedule->child;
            if ($child) {
                $dateStr = \Carbon\Carbon::parse($validated['session_date'])->format('Y-m-d');
                \App\Models\ParentMessage::create([
                    'child_id'       => $child->id,
                    'parent_name'    => $child->parent_name ?: 'ولي الأمر',
                    'recipient_type' => 'center',
                    'subject'        => 'تحديث عاجل: استبدال أخصائي الجلسة',
                    'message'        => "نحيطكم علماً بأنه تم تأكيد استبدال الأخصائي لجلسة طفلكم ({$child->name}) المجدولة بتاريخ {$dateStr}. سيقوم الأخصائي البديل ({$validated['specialist_name']}) بتقديم الجلسة بدلاً من الأخصائي ({$oldSpecialist}). شكراً لتفهمكم.",
                    'is_urgent'      => true,
                    'doctor_reply'   => 'تم تأكيد الموعد مع الأخصائي الجديد بالموعد المحدد.'
                ]);
            }
        }

        return redirect()->back()->with('success', 'تم تعديل الموعد بنجاح!');
    }

    /**
     * Update attendance or status.
     */
    public function updateAttendance(Request $request, SessionSchedule $schedule)
    {
        if (Carbon::parse($schedule->session_date)->isPast() && !Carbon::parse($schedule->session_date)->isToday()) {
            return redirect()->back()->with('error', 'لا يمكن تعديل حضور جلسة مضى تاريخها.');
        }
        $validated = $request->validate([
            'attendance_status' => 'required|in:pending,attended,absent',
        ]);

        $schedule->update([
            'attendance_status' => $validated['attendance_status'],
            'status'            => $validated['attendance_status'] === 'attended' ? 'completed' : $schedule->status,
        ]);

        if ($validated['attendance_status'] === 'attended') {
            $waitlist = \App\Models\Waitlist::where('child_id', $schedule->child_id)
                ->where('specialist_id', $schedule->specialist_id)
                ->whereDate('created_at', today())
                ->first();
            if ($waitlist) {
                $waitlist->update(['status' => 'scheduled']);
                \Illuminate\Support\Facades\Cache::put('center_screen_notification', [
                    'timestamp' => time(),
                    'type' => 'call',
                    'child_name' => $waitlist->child->name ?? '',
                    'specialist_name' => $waitlist->specialist->name ?? '',
                ], now()->addMinutes(5));
            }
        }

        return redirect()->back()->with('success', 'تم تحديث حالة حضور الجلسة بنجاح.');
    }

    /**
     * Remove session schedule.
     */
    public function destroy(SessionSchedule $schedule)
    {
        if (Carbon::parse($schedule->session_date)->isPast() && !Carbon::parse($schedule->session_date)->isToday()) {
            return redirect()->back()->with('error', 'لا يمكن حذف جلسة مضى تاريخها.');
        }
        $schedule->delete();
        return redirect()->back()->with('success', 'تم حذف الموعد من الجدول بنجاح.');
    }

    public function storeAttendanceAndPayment(Request $request, SessionSchedule $schedule)
    {
        if (Carbon::parse($schedule->session_date)->isPast() && !Carbon::parse($schedule->session_date)->isToday()) {
            return redirect()->back()->with('error', 'لا يمكن تعديل حضور جلسة مضى تاريخها.');
        }

        $validated = $request->validate([
            'session_price'   => 'required|numeric|min:0',
            'sessions_count'  => 'required|integer|min:1',
            'paid_amount'     => 'required|numeric|min:0',
            'payment_method'  => 'required|in:cash,transfer,visa',
            'invoice_id'      => 'nullable|exists:invoices,id' // If they are paying from an existing invoice
        ]);

        // If an invoice_id is passed, it means we are using a prepaid invoice
        if ($request->filled('invoice_id')) {
            $invoice = \App\Models\Invoice::findOrFail($request->invoice_id);
            if ($invoice->consumed_sessions < $invoice->sessions_count) {
                $invoice->increment('consumed_sessions');
                
                $schedule->update([
                    'attendance_status' => 'attended',
                    'status'            => 'completed',
                    'invoice_id'        => $invoice->id
                ]);

                $waitlist = \App\Models\Waitlist::where('child_id', $schedule->child_id)
                    ->where('specialist_id', $schedule->specialist_id)
                    ->whereDate('created_at', today())
                    ->first();
                if ($waitlist) {
                    $waitlist->update(['status' => 'scheduled']);
                    \Illuminate\Support\Facades\Cache::put('center_screen_notification', [
                        'timestamp' => time(),
                        'type' => 'call',
                        'child_name' => $waitlist->child->name ?? '',
                        'specialist_name' => $waitlist->specialist->name ?? '',
                    ], now()->addMinutes(5));
                }

                return redirect()->back()->with('success', 'تم تسجيل الحضور وخصم الجلسة من الرصيد المدفوع مسبقاً.');
            } else {
                return redirect()->back()->with('error', 'عذراً، رصيد الجلسات المدفوعة مسبقاً لهذه الفاتورة قد نفد.');
            }
        }

        // Create new invoice
        $totalAmount = $validated['session_price'] * $validated['sessions_count'];
        $remainingAmount = $totalAmount - $validated['paid_amount'];
        $paymentStatus = 'unpaid';
        if ($validated['paid_amount'] > 0) {
            $paymentStatus = $remainingAmount <= 0 ? 'paid' : 'partial';
        }

        $child = $schedule->child;
        $specialist = $schedule->specialist;

        $invoice = \App\Models\Invoice::create([
            'invoice_number'      => \App\Models\Invoice::generateNextInvoiceNumber(),
            'child_id'            => $schedule->child_id,
            'specialist_id'       => $schedule->specialist_id,
            'child_name'          => $child->name,
            'specialist_name'     => $specialist ? $specialist->name : $schedule->specialist_name,
            'parent_name'         => $child->parent_name,
            'session_price'       => $validated['session_price'],
            'sessions_count'      => $validated['sessions_count'],
            'consumed_sessions'   => 1, // Current session
            'total_amount'        => $totalAmount,
            'discount_amount'     => 0,
            'discount_percentage' => 0,
            'net_amount'          => $totalAmount,
            'paid_amount'         => $validated['paid_amount'],
            'remaining_amount'    => $remainingAmount,
            'payment_method'      => $validated['payment_method'],
            'payment_status'      => $paymentStatus,
            'invoice_date'        => now()->toDateString(),
        ]);

        $schedule->update([
            'attendance_status' => 'attended',
            'status'            => 'completed',
            'invoice_id'        => $invoice->id
        ]);

        $waitlist = \App\Models\Waitlist::where('child_id', $schedule->child_id)
            ->where('specialist_id', $schedule->specialist_id)
            ->whereDate('created_at', today())
            ->first();
        if ($waitlist) {
            $waitlist->update(['status' => 'scheduled']);
            \Illuminate\Support\Facades\Cache::put('center_screen_notification', [
                'timestamp' => time(),
                'type' => 'call',
                'child_name' => $waitlist->child->name ?? '',
                'specialist_name' => $waitlist->specialist->name ?? '',
            ], now()->addMinutes(5));
        }

        return redirect()->back()->with('success', 'تم تسجيل الحضور وإصدار الفاتورة بنجاح.')->with('print_invoice_id', $invoice->id);
    }

    public function transferSessions(Request $request)
    {
        $validated = $request->validate([
            'from_specialist_id' => 'required|exists:specialists,id',
            'to_specialist_id'   => 'required|exists:specialists,id|different:from_specialist_id',
            'transfer_date'      => 'required|date',
        ]);

        $fromSpecialist = \App\Models\Specialist::findOrFail($validated['from_specialist_id']);
        $toSpecialist = \App\Models\Specialist::findOrFail($validated['to_specialist_id']);

        $sessions = SessionSchedule::where('specialist_id', $fromSpecialist->id)
            ->whereDate('session_date', $validated['transfer_date'])
            ->where('status', 'scheduled')
            ->get();

        $count = 0;
        foreach ($sessions as $session) {
            $session->update([
                'specialist_id' => $toSpecialist->id,
                'specialist_name' => $toSpecialist->name,
            ]);
            $count++;
        }

        return redirect()->back()->with('success', "تم نقل {$count} جلسات بنجاح من {$fromSpecialist->name} إلى {$toSpecialist->name}.");
    }

    public function checkPrepaid(Request $request)
    {
        $childId = $request->child_id;
        $specialistId = $request->specialist_id;

        if (!$childId || !$specialistId) {
            return response()->json(['has_prepaid' => false]);
        }

        $invoiceQuery = \App\Models\Invoice::where('child_id', $childId)
            ->whereRaw('consumed_sessions < sessions_count');

        if (is_numeric($specialistId)) {
            $invoiceQuery->where('specialist_id', $specialistId);
        } else {
            // specialistId was passed as a string name
            $invoiceQuery->where('specialist_name', $specialistId);
        }

        $invoice = $invoiceQuery->latest('id')->first();

        if ($invoice) {
            return response()->json([
                'has_prepaid'       => true,
                'invoice_id'        => $invoice->id,
                'invoice_date'      => \Carbon\Carbon::parse($invoice->invoice_date)->format('Y-m-d'),
                'remaining_sessions'=> $invoice->sessions_count - $invoice->consumed_sessions,
            ]);
        }

        return response()->json(['has_prepaid' => false]);
    }

    private function getRoomsList(): array
    {
        return \App\Http\Controllers\SettingController::getDropdownList('rooms');
    }
}

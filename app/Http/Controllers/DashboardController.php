<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SessionSchedule;
use App\Models\Child;
use App\Models\Specialist;
use App\Models\Invoice;
use App\Models\ParentMessage;
use App\Models\TherapySession;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the main Speech & Rehabilitation Center Dashboard.
     */
    public function index()
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user && $user->role === 'parent') {
            return redirect()->route('parent.portal');
        } elseif ($user && $user->role === 'specialist') {
            return redirect()->route('doctor.portal');
        }

        $today = Carbon::today();
        $nowTime = Carbon::now()->format('H:i:s');

        // 1. إحصائيات ومؤشرات الأداء السريعة (KPIs)
        $todaySchedules = SessionSchedule::whereDate('session_date', $today)->get();
        
        $today_sessions_total = $todaySchedules->count();
        $today_sessions_done = $todaySchedules->where('status', 'completed')->count() + $todaySchedules->where('attendance_status', 'attended')->where('status', '!=', 'completed')->count();
        
        $today_sessions_active = $todaySchedules->filter(function($s) use ($nowTime) {
            return $s->status == 'scheduled' && $s->start_time <= $nowTime && ($s->end_time >= $nowTime || !$s->end_time);
        })->count();

        $today_sessions_pending = $todaySchedules->filter(function($s) use ($nowTime) {
            return $s->status == 'scheduled' && $s->start_time > $nowTime;
        })->count();

        $today_sessions_absent = $todaySchedules->where('attendance_status', 'absent')->count() + $todaySchedules->where('status', 'cancelled')->count();

        $active_children_count = Child::where('status', 'active')->count();
        $new_children_month = Child::whereMonth('created_at', Carbon::now()->month)
                                    ->whereYear('created_at', Carbon::now()->year)
                                    ->count();

        $active_specialists = $todaySchedules->pluck('specialist_id')->filter()->unique()->count();
        $total_specialists = Specialist::count();

        $today_revenue = Invoice::whereDate('invoice_date', $today)->sum('paid_amount');
        $month_revenue = Invoice::whereMonth('invoice_date', Carbon::now()->month)
                                ->whereYear('invoice_date', Carbon::now()->year)
                                ->sum('paid_amount');

        $unread_parent_notes = ParentMessage::where('status', 'new')->count();

        $stats = [
            'today_sessions_total'   => $today_sessions_total,
            'today_sessions_done'    => $today_sessions_done,
            'today_sessions_active'  => $today_sessions_active,
            'today_sessions_pending' => $today_sessions_pending,
            'today_sessions_absent'  => $today_sessions_absent,
            'active_children_count'  => $active_children_count,
            'new_children_month'     => $new_children_month,
            'active_specialists'     => $active_specialists,
            'total_specialists'      => $total_specialists,
            'today_revenue'          => $today_revenue,
            'month_revenue'          => $month_revenue,
            'unread_parent_notes'    => $unread_parent_notes,
        ];

        // 2. حالة غرف المركز والجلسات الجارية الآن (Live Rooms Dispatcher)
        $rooms = [];
        $uniqueRooms = $todaySchedules->pluck('room_name')->filter()->unique();
        if ($uniqueRooms->isEmpty()) {
            $uniqueRooms = ['غرفة التخاطب 1', 'غرفة التكامل الحسي', 'غرفة تنمية المهارات'];
        }

        foreach ($uniqueRooms as $roomName) {
            $currentSession = $todaySchedules->filter(function($s) use ($roomName, $nowTime) {
                return $s->room_name == $roomName && $s->start_time <= $nowTime && ($s->end_time >= $nowTime || !$s->end_time);
            })->first();

            if ($currentSession) {
                $startTime = Carbon::parse($currentSession->start_time);
                $endTime = $currentSession->end_time ? Carbon::parse($currentSession->end_time) : Carbon::parse($currentSession->start_time)->addMinutes(45);
                $totalMinutes = $startTime->diffInMinutes($endTime);
                $elapsedMinutes = $startTime->diffInMinutes(Carbon::now());
                $remainingMinutes = max(0, $totalMinutes - $elapsedMinutes);
                $progressPercentage = $totalMinutes > 0 ? min(100, ($elapsedMinutes / $totalMinutes) * 100) : 0;

                $child = Child::find($currentSession->child_id);
                $childName = $child ? $child->name : 'غير معروف';
                $childCode = $child ? $child->code : '';
                $diagnosis = $child ? $child->medical_diagnosis : '';
                $avatar = 'https://api.dicebear.com/7.x/bottts/svg?seed=' . urlencode($childName);

                $rooms[] = [
                    'id' => $currentSession->id,
                    'name' => $roomName,
                    'category' => $currentSession->session_title,
                    'status' => 'busy',
                    'specialist' => $currentSession->specialist_name,
                    'child' => [
                        'name' => $childName,
                        'code' => $childCode,
                        'avatar' => $avatar,
                        'diagnosis' => $diagnosis,
                    ],
                    'start_time' => $startTime->format('h:i A'),
                    'end_time' => $endTime->format('h:i A'),
                    'remaining_minutes' => $remainingMinutes,
                    'progress_percentage' => $progressPercentage,
                ];
            } else {
                $nextSession = $todaySchedules->filter(function($s) use ($roomName, $nowTime) {
                    return $s->room_name == $roomName && $s->start_time > $nowTime;
                })->sortBy('start_time')->first();

                if ($nextSession) {
                    $nextTime = Carbon::parse($nextSession->start_time)->format('h:i A');
                    $nextSessionTitle = $nextSession->session_title;
                } else {
                    $nextTime = 'لا يوجد موعد قادم اليوم';
                    $nextSessionTitle = '';
                }

                $rooms[] = [
                    'id' => uniqid(),
                    'name' => $roomName,
                    'category' => 'قاعة تأهيل',
                    'status' => 'available',
                    'next_session' => $nextTime . ($nextSessionTitle ? " ({$nextSessionTitle})" : ''),
                    'specialist' => $nextSession ? $nextSession->specialist_name : 'غير محدد',
                ];
            }
        }

        // 3. جدول جلسات اليوم التفاعلي (Today's Live Sessions)
        $todaySessions = [];
        foreach ($todaySchedules as $session) {
            $child = Child::find($session->child_id);
            if (!$child) continue;

            $statusStr = 'scheduled';
            if ($session->status == 'completed' || $session->attendance_status == 'attended') {
                $statusStr = 'completed';
            } elseif ($session->attendance_status == 'absent' || $session->status == 'cancelled') {
                $statusStr = 'absent';
            } elseif ($session->start_time <= $nowTime && ($session->end_time >= $nowTime || !$session->end_time)) {
                $statusStr = 'in_progress';
            }

            // Check if therapy session report exists
            $therapySession = TherapySession::where('child_id', $session->child_id)
                ->whereDate('session_date', $session->session_date)
                ->first();

            $hasReport = $therapySession ? true : false;
            $hasVideo = ($therapySession && $therapySession->video_path) ? true : false;
            
            $packageRemaining = 10; 

            $specialistRole = 'أخصائي';
            if ($session->specialist_id) {
                $spec = Specialist::find($session->specialist_id);
                $specialistRole = $spec ? $spec->specialty : 'أخصائي';
            }

            $endTimeParsed = $session->end_time ? Carbon::parse($session->end_time)->format('h:i A') : '';
            $timeSlot = Carbon::parse($session->start_time)->format('h:i A') . ($endTimeParsed ? ' - ' . $endTimeParsed : '');

            $todaySessions[] = [
                'id' => $session->id,
                'child_id' => $session->child_id,
                'child_name' => $child->name,
                'child_code' => $child->code,
                'child_age' => $child->age_text,
                'diagnosis' => $child->medical_diagnosis ?? 'غير محدد',
                'specialist_name' => $session->specialist_name,
                'specialist_role' => $specialistRole,
                'session_type' => $session->session_title,
                'room_name' => $session->room_name,
                'time_slot' => $timeSlot,
                'status' => $statusStr,
                'has_report' => $hasReport,
                'has_video' => $hasVideo,
                'video_duration' => $therapySession->video_duration ?? '0:00 د',
                'package_remaining' => $packageRemaining,
                'notes_preview' => $therapySession->clinical_notes ?? 'لا يوجد تقرير بعد',
                'parent_phone' => $child->parent_phone ?? $child->phone ?? '0',
            ];
        }

        // 4. حائط الملاحظات الواردة من أولياء الأمور
        $parentNotes = [];
        $messages = ParentMessage::with(['child'])->latest()->take(5)->get();
        foreach ($messages as $msg) {
            $parentNotes[] = [
                'id' => $msg->id,
                'parent_name' => $msg->parent_name,
                'parent_type' => 'ولي أمر',
                'child_name' => $msg->child ? $msg->child->name : 'غير محدد',
                'specialist_name' => $msg->recipient_type === 'specialist' ? 'الأخصائي المتابع' : 'المركز',
                'note' => $msg->message,
                'time_ago' => $msg->created_at->diffForHumans(),
                'is_read' => $msg->status !== 'new',
                'priority' => $msg->is_urgent ? 'high' : 'normal',
            ];
        }

        // 5. باقات أوشكت على الانتهاء
        $lowBalancePackages = [];
        $children = Child::where('status', 'active')->take(2)->get();
        foreach ($children as $child) {
            $lowBalancePackages[] = [
                'child_name' => $child->name,
                'child_code' => $child->code,
                'parent_name' => $child->parent_name,
                'parent_phone' => preg_replace('/[^0-9]/', '', $child->parent_phone ?? $child->phone ?? ''),
                'package_name' => 'باقة تأهيل',
                'remaining_sessions' => rand(1, 3),
                'total_sessions' => 12,
            ];
        }

        // 6. نشاط رسائل الواتساب الآلية اليوم
        $whatsappStats = [
            'sent_reminders' => rand(10, 30),
            'confirmed_by_parent' => rand(5, 20),
            'rescheduled' => rand(0, 5),
            'waiting_reply' => rand(0, 5),
        ];

        return view('dashboard.index', compact(
            'stats',
            'rooms',
            'todaySessions',
            'parentNotes',
            'lowBalancePackages',
            'whatsappStats'
        ));
    }

    public function getUrgentNotifications()
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        
        $query = \App\Models\ParentMessage::where('is_urgent', true)
            ->whereNull('doctor_reply')
            ->orderBy('created_at', 'desc')
            ->take(5);
            
        if ($user && $user->role === 'specialist') {
            $specialist = \App\Models\Specialist::where('user_id', $user->id)->first();
            if ($specialist) {
                // The parent_messages table does not have specialist_id column in migration
                // $query->where(function($q) use ($specialist) {
                //    $q->where('specialist_id', $specialist->id)
                //      ->orWhereNull('specialist_id');
                // });
            }
        }

        $messages = $query->with('child')->get()->map(function($msg) {
            $affectedChildrenHtml = '';
            if (strpos($msg->subject, 'اعتذار طارئ عن يوم عمل') !== false) {
                $specName = trim(str_replace('الأخصائي:', '', $msg->parent_name));
                $dateStr = trim(str_replace('اعتذار طارئ عن يوم عمل:', '', $msg->subject));
                
                $sessions = \App\Models\SessionSchedule::with('child')->where('specialist_name', 'like', "%{$specName}%")
                    ->where('session_date', $dateStr)
                    ->where('status', 'cancelled')
                    ->get();
                    
                if ($sessions->count() > 0) {
                    $affectedChildrenHtml .= '<div class="mt-4 text-left border-t pt-3"><p class="text-sm font-bold text-slate-700 mb-2">إبلاغ أولياء الأمور عبر الواتساب:</p><div class="flex flex-col gap-2 max-h-[200px] overflow-y-auto pr-1">';
                    foreach ($sessions as $sess) {
                        if ($sess->child) {
                            $msgText = urlencode("نعتذر لإبلاغكم بأنه تم إلغاء جلسة طفلكم (" . $sess->child->name . ") المقررة اليوم الموافق " . $dateStr . " نظراً لظرف طارئ للأخصائي (" . $specName . "). وسيتم التواصل معكم لتعويض الجلسة.");
                            $waLink = "https://wa.me/" . preg_replace('/[^0-9]/', '', $sess->child->parent_phone ?? $sess->child->phone ?? '') . "?text=" . $msgText;
                            $affectedChildrenHtml .= '<a href="' . $waLink . '" target="_blank" class="bg-emerald-50 text-emerald-700 p-2 rounded-lg text-[10px] font-bold flex items-center justify-between hover:bg-emerald-100 border border-emerald-200"><span class="truncate">' . $sess->child->name . '</span><i class="fa-brands fa-whatsapp text-lg"></i></a>';
                        }
                    }
                    $affectedChildrenHtml .= '</div></div>';
                }
            }

            return [
                'id' => $msg->id,
                'title' => $msg->subject,
                'body' => $msg->message,
                'time' => $msg->created_at->diffForHumans(),
                'sender' => $msg->parent_name,
                'child_name' => $msg->child ? $msg->child->name : '',
                'affected_html' => $affectedChildrenHtml
            ];
        });

        return response()->json(['notifications' => $messages]);
    }
}

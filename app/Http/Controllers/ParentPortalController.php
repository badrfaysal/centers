<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\TherapySession;
use App\Models\VideoComment;
use App\Models\ParentMessage;
use App\Models\SpecialistRating;
use App\Models\ConsultationBooking;
use App\Models\SessionSchedule;
use Illuminate\Http\Request;

class ParentPortalController extends Controller
{
    /**
     * Display the Parent Portal for viewing child therapy sessions, IEP goals, videos, and calendar.
     */
    public function index(Request $request, $code = null)
    {
        $child = null;
        $user = \Illuminate\Support\Facades\Auth::user();

        if ($user && $user->role === 'parent') {
            $child = Child::where('user_id', $user->id)->with(['therapySessions.comments', 'parentMessages', 'specialistRatings', 'sessionSchedules'])->first();
            if (!$child && $user->username) {
                 $child = Child::where('national_id', $user->username)->with(['therapySessions.comments', 'parentMessages', 'specialistRatings', 'sessionSchedules'])->first();
            }
        } elseif ($code) {
            $child = Child::where('code', $code)->with(['therapySessions.comments', 'parentMessages', 'specialistRatings', 'sessionSchedules'])->first();
        } elseif ($request->filled('child_code')) {
            $child = Child::where('code', $request->child_code)->with(['therapySessions.comments', 'parentMessages', 'specialistRatings', 'sessionSchedules'])->first();
        } elseif ($request->filled('child_id')) {
            $child = Child::where('id', $request->child_id)->with(['therapySessions.comments', 'parentMessages', 'specialistRatings', 'sessionSchedules'])->first();
        } else {
            $child = Child::with(['therapySessions.comments', 'parentMessages', 'specialistRatings', 'sessionSchedules'])->first();
        }

        $children = Child::select('id', 'name', 'code', 'photo_path', 'phone')->get();
        $sessions = $child ? $child->therapySessions()->latest()->get() : collect();
        $messages = $child ? $child->parentMessages()->latest()->get() : collect();
        $ratings = $child ? $child->specialistRatings()->latest()->get() : collect();
        $schedules = $child ? $child->sessionSchedules()->orderBy('day_of_week')->orderBy('start_time')->get() : collect();
        $iepGoals = [];
        if ($child) {
            // Find the latest session that has non-empty goals
            $latestSession = clone $child->therapySessions();
            $latestSession = collect($latestSession->get())->filter(function($s) {
                return !empty($s->goals_evaluated) && is_array($s->goals_evaluated) && count($s->goals_evaluated) > 0;
            })->sortByDesc('session_date')->first();
            
            if ($latestSession && is_array($latestSession->goals_evaluated)) {
                foreach ($latestSession->goals_evaluated as $goal) {
                    if (is_array($goal)) {
                        $pct = (int) ($goal['percentage'] ?? 0);
                        $iepGoals[] = [
                            'title' => $goal['text'] ?? 'هدف علاجي',
                            'category' => 'مهارة مستهدفة',
                            'status' => $pct >= 100 ? 'achieved' : 'in_progress',
                            'progress' => $pct,
                        ];
                    } elseif (is_string($goal)) {
                        $iepGoals[] = [
                            'title' => $goal,
                            'category' => 'مهارة مستهدفة',
                            'status' => 'in_progress',
                            'progress' => 0,
                        ];
                    }
                }
            }
        }
        $childMedia = $child ? \App\Models\ChildMedia::where('child_id', $child->id)->with(['uploader', 'comments.user'])->latest()->get() : collect();

        $calendarEvents = $schedules->map(function($s) {
            return [
                'id' => $s->id,
                'child_id' => $s->child_id,
                'specialist_name' => $s->specialist_name,
                'session_title' => $s->session_title,
                'session_date' => $s->session_date ? $s->session_date->format('Y-m-d') : '',
                'start_time' => $s->start_time,
                'end_time' => $s->end_time,
                'formatted_time_range' => $s->formatted_time_range,
                'room_name' => $s->room_name,
                'status' => $s->status,
                'attendance_status' => $s->attendance_status,
                'notes' => $s->notes,
                'day_name_arabic' => $s->day_name_arabic,
                'attendance_status' => $s->attendance_status,
                'notes' => $s->notes,
            ];
        })->values();

        return view('parent.portal', compact('child', 'children', 'sessions', 'messages', 'ratings', 'iepGoals', 'schedules', 'calendarEvents', 'childMedia'));
    }

    /**
     * Parent Bookings & Account Tracker (متابعة حالة الحجز، الموعد المحدد، وسعر الجلسة).
     */
    public function trackBookings(Request $request)
    {
        $phone = $request->input('phone') ?? session('parent_phone');
        $bookings = collect();
        $linkedChildren = collect();

        if ($phone) {
            $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
            $bookings = ConsultationBooking::where('phone', 'like', "%{$cleanPhone}%")
                ->latest()
                ->get();

            $linkedChildren = Child::where('phone', 'like', "%{$cleanPhone}%")->get();

            session(['parent_phone' => $phone]);
        }

        return view('parent.bookings_track', compact('bookings', 'phone', 'linkedChildren'));
    }

    /**
     * Store comment on a session video by parent.
     */
    public function storeComment(Request $request)
    {
        $validated = $request->validate([
            'therapy_session_id' => 'required|exists:therapy_sessions,id',
            'child_id'           => 'required|exists:children,id',
            'parent_name'        => 'required|string|max:100',
            'comment'            => 'required|string|max:1000',
        ]);

        VideoComment::create([
            'therapy_session_id' => $validated['therapy_session_id'],
            'child_id'           => $validated['child_id'],
            'parent_name'        => $validated['parent_name'],
            'comment'            => $validated['comment'],
        ]);

        return redirect()->back()->with('success', 'تم إرسال تعليقك وملاحظتك للأخصائي بنجاح! سيتم الرد عليك قريباً.');
    }

    /**
     * Store direct message to doctor/center.
     */
    public function storeMessage(Request $request)
    {
        if ($request->recipient_type === 'doctor') {
            $request->merge(['recipient_type' => 'specialist']);
        }

        $validated = $request->validate([
            'child_id'        => 'required|exists:children,id',
            'recipient_type'  => 'required|in:specialist,center',
            'parent_name'     => 'required|string|max:100',
            'subject'         => 'nullable|string|max:150',
            'message'         => 'required|string|max:2000',
        ]);

        ParentMessage::create($validated);

        return redirect()->back()->with('success', 'تم إرسال رسالتك لإدارة المركز بنجاح.');
    }

    /**
     * Store rating and review.
     */
    public function storeRating(Request $request)
    {
        $validated = $request->validate([
            'child_id'        => 'required|exists:children,id',
            'parent_name'     => 'required|string|max:100',
            'specialist_name' => 'nullable|string|max:100',
            'rating'          => 'required|integer|min:1|max:5',
            'feedback'        => 'nullable|string|max:2000',
        ]);

        SpecialistRating::create($validated);

        return redirect()->back()->with('success', 'شكراً لتقييمك وملاحظاتك القيمة، نسعى دائماً لتقديم أفضل رعاية لطفلك.');
    }

    private function getSampleGoals(): array
    {
        return [
            [
                'title' => 'نطق صوت حرف (الراء - R) بشكل منفرد وفي بداية الكلمة',
                'category' => 'نطق وتخاطب',
                'progress' => 85,
                'target' => 100,
                'status' => 'in_progress',
            ],
            [
                'title' => 'زيادة التواصل البصري أثناء الحديث لمدة لا تقل عن 10 ثوانٍ متصلة',
                'category' => 'سلوك وتواصل',
                'progress' => 70,
                'target' => 100,
                'status' => 'in_progress',
            ],
            [
                'title' => 'تركيب جملة مفيدة من 3 كلمات للتعبير عن الاحتياجات الأساسية',
                'category' => 'لغة وتعبير',
                'progress' => 90,
                'target' => 100,
                'status' => 'achieved',
            ],
            [
                'title' => 'الجلوس في الأنشطة الفردية لمدة 15 دقيقة دون مغادرة الكرسي',
                'category' => 'تعديل سلوك',
                'progress' => 60,
                'target' => 100,
                'status' => 'in_progress',
            ],
        ];
    }

    public function apologizeSession(Request $request, SessionSchedule $sessionSchedule)
    {
        // Cancel the session
        $sessionSchedule->update([
            'status' => 'cancelled',
            'attendance_status' => 'absent',
            'notes' => 'تم الاعتذار عن الجلسة من قبل ولي الأمر. ' . ($sessionSchedule->notes ? ' | ' . $sessionSchedule->notes : '')
        ]);

        // Find specialist ID if possible
        $specialist = \App\Models\Specialist::where('name', $sessionSchedule->specialist_name)->first();
        $child = \App\Models\Child::find($sessionSchedule->child_id);

        // Create an urgent parent message to notify the center and specialist
        $dateFormatted = $sessionSchedule->session_date->format('Y-m-d');
        ParentMessage::create([
            'child_id' => $sessionSchedule->child_id,
            'parent_name' => $child ? $child->parent_name : 'ولي الأمر',
            'specialist_id' => $specialist ? $specialist->id : null,
            'recipient_type' => 'center',
            'subject' => 'اعتذار عاجل - الطفل: ' . ($child ? $child->name : ''),
            'message' => "يعتذر ولي أمر الطفل/ة (" . ($child ? $child->name : '') . ") عن حضور جلسة يوم {$dateFormatted} الساعة {$sessionSchedule->formatted_time_range} مع الأخصائي {$sessionSchedule->specialist_name}.",
            'is_urgent' => true,
        ]);

        return back()->with('success', 'تم إرسال الاعتذار بنجاح، وتم إشعار المركز والأخصائي بذلك.');
    }
}




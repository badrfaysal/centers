<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\TherapySession;
use App\Models\Specialist;
use App\Models\VideoComment;
use App\Models\ParentMessage;
use App\Models\SpecialistRating;
use Illuminate\Http\Request;

class DoctorSessionController extends Controller
{
    /**
     * Display the Specialist Portal with Sessions, Parent Comments, Messages, and Ratings.
     */
    public function index(Request $request)
    {
        $specialists = $this->getSpecialistsList();

        // 1. سجل الجلسات السابقة مجمع بالطفل
        $childrenQuery = \App\Models\Child::whereHas('therapySessions', function ($q) use ($request) {
            if ($request->filled('specialist') && $request->specialist !== 'all') {
                $q->where('specialist_name', $request->specialist);
            }
        })->with(['therapySessions' => function($q) use ($request) {
            $q->latest();
            if ($request->filled('specialist') && $request->specialist !== 'all') {
                $q->where('specialist_name', $request->specialist);
            }
            $q->with('comments');
        }]);

        if ($request->filled('search')) {
            $search = $request->search;
            $childrenQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }
        $groupedChildren = $childrenQuery->paginate(10);

        // 2. تعليقات أولياء الأمور على الفيديوهات
        $videoComments = VideoComment::with(['session', 'child'])->latest()->get();

        // 3. رسائل واستفسارات أولياء الأمور الموجهة للأخصائي
        $parentMessages = ParentMessage::with('child')
            ->where('subject', 'not like', '%اعتذار طارئ عن يوم عمل%')
            ->latest()
            ->get()
            ->groupBy('child_id');

        // 4. تقييمات أولياء الأمور للأخصائيين
        $ratings = SpecialistRating::with('child')->latest()->get();

        // إحصائيات سريعة للأخصائي
        $totalSessionsCount = TherapySession::count();
        $pendingMessagesCount = ParentMessage::whereNull('doctor_reply')
            ->where('subject', 'not like', '%اعتذار طارئ عن يوم عمل%')
            ->count();
        $totalCommentsCount = VideoComment::where('sender_type', 'parent')->count();
        $avgRating = $ratings->avg('rating') ? number_format($ratings->avg('rating'), 1) : '5.0';

        return view('doctor.index', compact(
            'groupedChildren',
            'videoComments',
            'parentMessages',
            'ratings',
            'totalSessionsCount',
            'pendingMessagesCount',
            'totalCommentsCount',
            'avgRating',
            'specialists'
        ));
    }

    /**
     * Show the session logger form with live child selector and IEP goals.
     */
    public function create(Request $request)
    {
        $selectedChildId = $request->query('child_id');
        $selectedChild = $selectedChildId ? Child::find($selectedChildId) : null;
        
        $children = Child::where('status', 'active')->orderBy('name')->get();
        $specialists = $this->getSpecialistsList();
        $rooms = $this->getRoomsList();
        $sessionTypes = $this->getSessionTypesList();

        return view('doctor.create', compact('children', 'selectedChild', 'specialists', 'rooms', 'sessionTypes'));
    }

    /**
     * Store a newly logged session in database and attach to child profile.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'child_id'          => 'required|exists:children,id',
            'specialist_name'   => 'required|string|max:100',
            'session_date'      => 'required|date',
            'session_time'      => 'nullable|string|max:50',
            'room_name'         => 'required|string|max:50',
            'session_type'      => 'required|string|max:50',
            'child_mood'        => 'required|string|max:50',
            'clinical_notes'    => 'required|string|min:5',
            'home_exercise'     => 'nullable|string',
            'video'             => 'nullable|file|mimes:mp4,mov,avi,m4v,webm|max:51200',
            'video_title'       => 'nullable|string|max:150',
            'goals'             => 'nullable|array',
            'goals.*.text'      => 'nullable|string',
            'goals.*.percentage' => 'nullable|integer|min:0|max:100',
            'homework_file'     => 'nullable|file|mimes:mp4,mov,avi,webm,jpeg,jpg,png,m4a,mp3,wav|max:20480',
            'whatsapp_notify'   => 'nullable|boolean',
        ]);

        $goalsEvaluated = [];
        if ($request->has('goals') && is_array($request->goals)) {
            foreach ($request->goals as $goal) {
                if (!empty($goal['text'] ?? '')) {
                    $goalsEvaluated[] = [
                        'text' => $goal['text'],
                        'percentage' => (int) ($goal['percentage'] ?? 0),
                    ];
                }
            }
        }
        $validated['goals_evaluated'] = $goalsEvaluated;

        if ($request->hasFile('video')) {
            $path = $request->file('video')->store('session_videos', 'public');
            $validated['video_path'] = $path;
            $validated['video_duration'] = '0:45 دقيقة';
        }

        if ($request->hasFile('homework_file')) {
            $validated['homework_file_path'] = $request->file('homework_file')->store('homework_media', 'public');
        }

        $validated['whatsapp_notified'] = $request->boolean('whatsapp_notify');

        $session = TherapySession::create($validated);
        $child = Child::findOrFail($validated['child_id']);

        // تحديث حالة الحضور في الكالندر تلقائياً عند تسجيل الجلسة
        $schedule = \App\Models\SessionSchedule::where('child_id', $child->id)
            ->whereDate('session_date', $validated['session_date'])
            ->where('status', '!=', 'cancelled')
            ->first();
            
            if ($schedule) {
            $schedule->update([
                'attendance_status' => 'attended',
                'status' => 'completed'
            ]);
        }

        // إشعار شاشة المركز
        $currentTime = now()->format('H:i:s');
        $nextSession = \App\Models\SessionSchedule::where('specialist_name', $validated['specialist_name'])
            ->whereDate('session_date', today())
            ->where('start_time', '>', $currentTime)
            ->where('status', '!=', 'cancelled')
            ->orderBy('start_time', 'asc')
            ->first();

        $nextChildName = $nextSession && $nextSession->child ? $nextSession->child->name : null;

        \Illuminate\Support\Facades\Cache::put('center_screen_notification', [
            'finished_child' => $child->name,
            'next_child' => $nextChildName,
            'specialist' => $validated['specialist_name'],
            'timestamp' => time()
        ], 120);

        // إذا تم تفعيل إرسال ملخص الجلسة عبر الواتساب، نعيد التوجيه مع رابط الواتساب
        if ($session->whatsapp_notified) {
            $waUrl = $session->whatsapp_session_summary_url;
            return redirect()->route('children.show', $child)
                ->with('success', "تم تسجيل بيانات الجلسة للطفل ({$child->name}) بنجاح! ✅")
                ->with('whatsapp_url', $waUrl);
        }

        return redirect()->route('children.show', $child)
            ->with('success', "تم تسجيل بيانات الجلسة والملاحظات للطفل ({$child->name}) وحفظها في بروفايله بنجاح! ");
    }

    /**
     * Doctor replies to a parent's comment on a video.
     */
    public function replyComment(Request $request)
    {
        $validated = $request->validate([
            'therapy_session_id' => 'required|exists:therapy_sessions,id',
            'child_id'           => 'required|exists:children,id',
            'sender_name'        => 'required|string|max:100',
            'comment'            => 'required|string|min:2|max:1000',
        ]);

        $validated['sender_type'] = 'specialist';
        VideoComment::create($validated);

        return redirect()->route('doctor.portal')
            ->with('success', 'تم إرسال ردك على تعليق ولي الأمر بنجاح وسيظهر له في تطبيقه فوراً! ');
    }

    /**
     * Doctor replies to a direct parent message / note.
     */
    public function replyMessage(Request $request)
    {
        $validated = $request->validate([
            'message_id'   => 'required|exists:parent_messages,id',
            'doctor_reply' => 'required|string|min:2|max:2000',
            'replied_by'   => 'required|string|max:100',
        ]);

        $message = ParentMessage::findOrFail($validated['message_id']);
        $message->update([
            'doctor_reply' => $validated['doctor_reply'],
            'replied_by'   => $validated['replied_by'],
            'replied_at'   => now(),
        ]);

        return redirect()->route('doctor.portal')
            ->with('success', 'تم إرسال الرد على رسالة واستفسار ولي الأمر بنجاح! ');
    }

    private function getSpecialistsList(): array
    {
        $db = Specialist::where('status', 'active')->orderBy('name')->pluck('name')->toArray();
        return !empty($db) ? $db : [
            'د. أحمد يسري (أخصائي تخاطب ونطق)',
            'د. مروة كمال (تكامل حسي وتعديل سلوك)',
            'د. سارة إبراهيم (تأهيل تخاطب سمعي)',
            'أ. حسام فؤاد (صعوبات تعلم وتنمية مهارات)',
        ];
    }

    private function getRoomsList(): array
    {
        return \App\Http\Controllers\SettingController::getDropdownList('rooms');
    }

    private function getSessionTypesList(): array
    {
        return \App\Http\Controllers\SettingController::getDropdownList('session_types');
    }

    public function completeHomework(TherapySession $session)
    {
        $session->update([
            'is_homework_completed' => true
        ]);

        // إشعار للإدارة أو الأخصائي يمكن إضافته هنا مستقبلاً
        
        return redirect()->back()->with('success', 'تم تسجيل إنجاز التدريب المنزلي بنجاح. شكراً لتعاونكم!');
    }
}
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

        $specialistFilter = $request->specialist;
        if (auth()->check() && auth()->user()->role === 'specialist') {
            $specialistFilter = auth()->user()->name;
        }

        // 1. Ø³Ø¬Ù„ Ø§Ù„Ø¬Ù„Ø³Ø§Øª Ø§Ù„Ø³Ø§Ø¨Ù‚Ø© Ù…Ø¬Ù…Ø¹ Ø¨Ø§Ù„Ø·ÙÙ„
        $childrenQuery = \App\Models\Child::whereHas('therapySessions', function ($q) use ($specialistFilter) {
            if ($specialistFilter && $specialistFilter !== 'all') {
                $q->where('specialist_name', $specialistFilter);
            }
        })->with(['therapySessions' => function($q) use ($specialistFilter) {
            $q->latest();
            if ($specialistFilter && $specialistFilter !== 'all') {
                $q->where('specialist_name', $specialistFilter);
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

        // 2. ØªØ¹Ù„ÙŠÙ‚Ø§Øª Ø£ÙˆÙ„ÙŠØ§Ø¡ Ø§Ù„Ø£Ù…ÙˆØ± Ø¹Ù„Ù‰ Ø§Ù„ÙÙŠØ¯ÙŠÙˆÙ‡Ø§Øª
        $videoComments = VideoComment::with(['session', 'child'])->latest()->get();

        // 3. Ø±Ø³Ø§Ø¦Ù„ ÙˆØ§Ø³ØªÙØ³Ø§Ø±Ø§Øª Ø£ÙˆÙ„ÙŠØ§Ø¡ Ø§Ù„Ø£Ù…ÙˆØ± Ø§Ù„Ù…ÙˆØ¬Ù‡Ø© Ù„Ù„Ø£Ø®ØµØ§Ø¦ÙŠ
        $parentMessages = ParentMessage::with('child')
            ->where('recipient_type', 'specialist')
            ->latest()
            ->get()
            ->groupBy('child_id');

        // 4. تقييمات أولياء الأمور للأخصائيين
        $ratings = SpecialistRating::with('child')->latest()->get();

        // إحصائيات سريعة للأخصائي
        $totalSessionsCount = TherapySession::count();
        $pendingMessagesCount = ParentMessage::whereNull('doctor_reply')
            ->where('recipient_type', 'specialist')
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
            'video'             => 'nullable|array',
            'video.*'           => 'file|mimes:mp4,mov,avi,m4v,webm,jpeg,jpg,png|max:51200',
            'video_title'       => 'nullable|string|max:150',
            'goals'             => 'nullable|array',
            'goals.*.text'      => 'nullable|string',
            'goals.*.percentage' => 'nullable|integer|min:0|max:100',
            'homework_file'     => 'nullable|array',
            'homework_file.*'   => 'file|mimes:mp4,mov,avi,webm,jpeg,jpg,png,m4a,mp3,wav|max:20480',
            'whatsapp_notify'   => 'nullable|boolean',
        ]);

        $goalsEvaluated = [];
        if (!empty($validated['goals'])) {
            foreach ($validated['goals'] as $goal) {
                if (!empty($goal['text'])) {
                    $goalsEvaluated[] = [
                        'text' => $goal['text'],
                        'percentage' => $goal['percentage'] ?? 0
                    ];
                }
            }
        }
        $validated['goals_evaluated'] = $goalsEvaluated;

        if ($request->filled('youtube_link')) {
            $ytLink = $request->input('youtube_link');
            if (str_contains($ytLink, 'youtube.com') || str_contains($ytLink, 'youtu.be')) {
                $validated['video_path'] = [$ytLink];
                $validated['video_duration'] = 'يوتيوب';
            }
        } elseif ($request->hasFile('video')) {
            $paths = [];
            foreach ($request->file('video') as $file) {
                $paths[] = $file->store('session_videos', 'public');
            }
            $validated['video_path'] = $paths;
} 
            $validated['video_duration'] = '0:45 دقيقة';

        if ($request->hasFile('homework_file')) {
            $paths = [];
            foreach ($request->file('homework_file') as $file) {
                $paths[] = $file->store('homework_media', 'public');
            }
            $validated['homework_file_path'] = $paths;
        }

        $validated['whatsapp_notified'] = $request->boolean('whatsapp_notify');

        if (auth()->check() && auth()->user()->role === 'specialist') {
            $validated['specialist_name'] = auth()->user()->name;
        }

        $session = TherapySession::create($validated);
        $child = Child::findOrFail($validated['child_id']);

        if (!empty($validated['home_exercise'])) {
            $specialist_id = auth()->check() && auth()->user()->role === 'specialist' ? auth()->id() : null;
            \App\Models\Homework::create([
                'child_id' => $validated['child_id'],
                'specialist_id' => $specialist_id,
                'therapy_session_id' => $session->id,
                'title' => 'واجب منزلي: ' . ($validated['session_type'] ?? 'تخاطب'),
                'description' => $validated['home_exercise'],
                'status' => 'pending'
            ]);
        }

        // ØªØ­Ø¯ÙŠØ« Ø­Ø§Ù„Ø© Ø§Ù„Ø­Ø¶ÙˆØ± ÙÙŠ Ø§Ù„ÙƒØ§Ù„Ù†Ø¯Ø± ØªÙ„Ù‚Ø§Ø¦ÙŠØ§Ù‹ Ø¹Ù†Ø¯ ØªØ³Ø¬ÙŠÙ„ Ø§Ù„Ø¬Ù„Ø³Ø©
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

        // Ø¥Ø°Ø§ ØªÙ… ØªÙØ¹ÙŠÙ„ Ø¥Ø±Ø³Ø§Ù„ Ù…Ù„Ø®Øµ Ø§Ù„Ø¬Ù„Ø³Ø© Ø¹Ø¨Ø± Ø§Ù„ÙˆØ§ØªØ³Ø§Ø¨ØŒ Ù†Ø¹ÙŠØ¯ Ø§Ù„ØªÙˆØ¬ÙŠÙ‡ Ù…Ø¹ Ø±Ø§Ø¨Ø· Ø§Ù„ÙˆØ§ØªØ³Ø§Ø¨
        if ($session->whatsapp_notified) {
            $waUrl = $session->whatsapp_session_summary_url;
            return redirect()->route('children.show', $child)
                ->with('success', "ØªÙ… ØªØ³Ø¬ÙŠÙ„ Ø¨ÙŠØ§Ù†Ø§Øª Ø§Ù„Ø¬Ù„Ø³Ø© Ù„Ù„Ø·ÙÙ„ ({$child->name}) بنجاح! ✅")
                ->with('whatsapp_url', $waUrl);
        }

        return redirect()->route('children.show', $child)
            ->with('success', "ØªÙ… ØªØ³Ø¬ÙŠÙ„ Ø¨ÙŠØ§Ù†Ø§Øª Ø§Ù„Ø¬Ù„Ø³Ø© ÙˆØ§Ù„Ù…Ù„Ø§Ø­Ø¸Ø§Øª Ù„Ù„Ø·ÙÙ„ ({$child->name}) ÙˆØ­ÙØ¸Ù‡Ø§ ÙÙŠ Ø¨Ø±ÙˆÙØ§ÙŠÙ„Ù‡ Ø¨Ù†Ø¬Ø§Ø­! ");
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
            ->with('success', 'ØªÙ… Ø¥Ø±Ø³Ø§Ù„ Ø±Ø¯Ùƒ Ø¹Ù„Ù‰ ØªØ¹Ù„ÙŠÙ‚ ÙˆÙ„ÙŠ Ø§Ù„Ø£Ù…Ø± Ø¨Ù†Ø¬Ø§Ø­ ÙˆØ³ÙŠØ¸Ù‡Ø± Ù„Ù‡ ÙÙŠ ØªØ·Ø¨ÙŠÙ‚Ù‡ ÙÙˆØ±Ø§Ù‹! ');
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
            ->with('success', 'ØªÙ… Ø¥Ø±Ø³Ø§Ù„ Ø§Ù„Ø±Ø¯ Ø¹Ù„Ù‰ Ø±Ø³Ø§Ù„Ø© ÙˆØ§Ø³ØªÙØ³Ø§Ø± ÙˆÙ„ÙŠ Ø§Ù„Ø£Ù…Ø± Ø¨Ù†Ø¬Ø§Ø­! ');
    }

    private function getSpecialistsList(): array
    {
        $db = Specialist::where('status', 'active')->orderBy('name')->pluck('name')->toArray();
        return $db;
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

        // Ø¥Ø´Ø¹Ø§Ø± Ù„Ù„Ø¥Ø¯Ø§Ø±Ø© Ø£Ùˆ Ø§Ù„Ø£Ø®ØµØ§Ø¦ÙŠ ÙŠÙ…ÙƒÙ† Ø¥Ø¶Ø§ÙØªÙ‡ Ù‡Ù†Ø§ Ù…Ø³ØªÙ‚Ø¨Ù„Ø§Ù‹
        
        return redirect()->back()->with('success', 'تم تسجيل إنجاز التدريب المنزلي بنجاح. شكراً لتعاونكم!');
    }
}

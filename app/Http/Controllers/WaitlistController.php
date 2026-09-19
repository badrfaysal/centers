<?php

namespace App\Http\Controllers;

use App\Models\Waitlist;
use App\Models\Child;
use App\Models\Specialist;
use Illuminate\Http\Request;

class WaitlistController extends Controller
{
    public function index(Request $request)
    {
        // Clean up waitlist items that were auto-added from calendar but their session status changed
        $autoAddedWaitlists = \App\Models\Waitlist::where('status', 'waiting')
            ->where('notes', 'like', 'تلقائي من الكالندر%')
            ->get();

        foreach($autoAddedWaitlists as $waitlistItem) {
            $sessionStillPending = \App\Models\SessionSchedule::whereDate('session_date', today())
                ->where('child_id', $waitlistItem->child_id)
                ->where('specialist_id', $waitlistItem->specialist_id)
                ->where('status', 'scheduled')
                ->where('attendance_status', 'pending')
                ->exists();
                
            if (!$sessionStillPending) {
                $waitlistItem->update(['status' => 'cancelled']);
            }
        }

        // Auto-import today's sessions into waitlist
        $todaySessions = \App\Models\SessionSchedule::whereDate('session_date', today())
            ->where('status', 'scheduled')
            ->where('attendance_status', 'pending')
            ->get();

        foreach ($todaySessions as $session) {
            $exists = \App\Models\Waitlist::where('child_id', $session->child_id)
                ->where('specialist_id', $session->specialist_id)
                ->where('status', 'waiting')
                ->exists();

            if (!$exists) {
                \App\Models\Waitlist::create([
                    'child_id' => $session->child_id,
                    'specialist_id' => $session->specialist_id,
                    'priority' => 'normal',
                    'status' => 'waiting',
                    'notes' => 'تلقائي من الكالندر (وقت الجلسة: ' . \Carbon\Carbon::parse($session->start_time)->format('h:i A') . ')'
                ]);
            }
        }

        $query = Waitlist::with(['child', 'specialist'])->where('status', 'waiting');

        if ($request->filled('specialist_id') && $request->specialist_id !== 'all') {
            $query->where('specialist_id', $request->specialist_id);
        }

        // Sort by priority (high first) and then by oldest (first come first served)
        $waitlists = $query->orderByRaw("FIELD(priority, 'high', 'normal', 'low')")->orderBy('created_at', 'asc')->get();
        
        // Group by specialist for kanban/list view
        $groupedWaitlists = $waitlists->groupBy('specialist_id');
        
        $specialists = Specialist::where('status', 'active')->get();
        $children = Child::all();

        return view('waitlists.index', compact('groupedWaitlists', 'specialists', 'children', 'waitlists'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'child_id' => 'required|exists:children,id',
            'specialist_id' => 'required|exists:specialists,id',
            'priority' => 'required|in:high,normal,low',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Check if already in waiting list for this specialist
        $exists = Waitlist::where('child_id', $request->child_id)
            ->where('specialist_id', $request->specialist_id)
            ->where('status', 'waiting')
            ->exists();

        if ($exists) {
            return back()->withErrors(['child_id' => 'هذا الطفل موجود بالفعل في قائمة الانتظار لنفس الأخصائي!']);
        }

        Waitlist::create($validated);

        return redirect()->route('waitlists.index')->with('success', 'تم إضافة الطفل لقائمة الانتظار بنجاح!');
    }

    public function updateStatus(Request $request, Waitlist $waitlist)
    {
        $validated = $request->validate([
            'status' => 'required|in:waiting,scheduled,cancelled'
        ]);

        $waitlist->update(['status' => $validated['status']]);

        if ($validated['status'] == 'scheduled') {
            \Illuminate\Support\Facades\Cache::put('center_screen_notification', [
                'timestamp' => time(),
                'type' => 'call',
                'child_name' => $waitlist->child->name,
                'specialist_name' => $waitlist->specialist->name,
            ], now()->addMinutes(5));
        }

        $msg = $validated['status'] == 'scheduled' ? 'تم دخول الطفل الجلسة وتم إرسال النداء بنجاح.' : 'تم إلغاء الحالة من قائمة الانتظار.';
        return redirect()->route('waitlists.index')->with('success', $msg);
    }
}

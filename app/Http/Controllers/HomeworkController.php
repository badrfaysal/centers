<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\Homework;
use App\Models\HomeworkMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class HomeworkController extends Controller
{
    public function index(Request $request)
    {
        $query = Homework::with(['child', 'specialist', 'therapySession'])->latest();
        
        if (Auth::user()->role === 'specialist') {
            $query->where('specialist_id', Auth::id());
        }

        if ($request->has('child_id') && $request->child_id != '') {
            $query->where('child_id', $request->child_id);
        }

        $homeworks = $query->get();
        $children = Child::orderBy('name')->get();

        return view('doctor.homeworks.index', compact('homeworks', 'children'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'child_id' => 'required|exists:children,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $validated['specialist_id'] = Auth::id();
        $validated['status'] = 'pending';

        $homework = Homework::create($validated);

        return redirect()->route('doctor.homeworks.index')->with('success', 'تم إنشاء التمرين بنجاح.');
    }

    public function show(Homework $homework)
    {
        if (Auth::user()->role === 'specialist' && $homework->specialist_id !== Auth::id()) {
            abort(403);
        }

        $homework->load(['child', 'messages' => function($q) {
            $q->orderBy('created_at', 'asc');
        }]);

        // Mark unread messages from parent as read
        $homework->messages()->where('sender_type', 'parent')->whereNull('read_at')->update(['read_at' => now()]);

        return view('doctor.homeworks.show', compact('homework'));
    }

    public function reply(Request $request, Homework $homework)
    {
        if (Auth::user()->role === 'specialist' && $homework->specialist_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'content' => 'required_without:audio',
            'audio' => 'nullable|file|mimes:webm,ogg,mp3,wav,m4a|max:10240'
        ]);

        if ($request->hasFile('audio')) {
            $path = $request->file('audio')->store('homework_audio', 'public');
            HomeworkMessage::create([
                'homework_id' => $homework->id,
                'sender_type' => 'specialist',
                'message_type' => 'audio',
                'content' => $path,
            ]);
        } else if ($request->filled('content')) {
            HomeworkMessage::create([
                'homework_id' => $homework->id,
                'sender_type' => 'specialist',
                'message_type' => 'text',
                'content' => $request->content,
            ]);
        }

        $homework->update(['status' => 'reviewed']);

        return back()->with('success', 'تم إرسال الرد بنجاح.');
    }
}
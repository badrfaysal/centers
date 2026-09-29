<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\Homework;
use App\Models\HomeworkMessage;
use Illuminate\Http\Request;

class ParentHomeworkController extends Controller
{
    private function getChild($code)
    {
        return Child::where('code', $code)->firstOrFail();
    }

    public function index($code)
    {
        $child = $this->getChild($code);
        $homeworks = $child->homeworks()->latest()->get();
        
        return view('parent.homeworks.index', compact('child', 'homeworks'));
    }

    public function show($code, Homework $homework)
    {
        $child = $this->getChild($code);
        
        if ($homework->child_id !== $child->id) {
            abort(403);
        }

        $homework->load(['specialist', 'messages' => function($q) {
            $q->orderBy('created_at', 'asc');
        }]);

        // Mark unread messages from specialist as read
        $homework->messages()->where('sender_type', 'specialist')->whereNull('read_at')->update(['read_at' => now()]);

        return view('parent.homeworks.show', compact('child', 'homework'));
    }

    public function reply(Request $request, $code, Homework $homework)
    {
        $child = $this->getChild($code);
        
        if ($homework->child_id !== $child->id) {
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
                'sender_type' => 'parent',
                'message_type' => 'audio',
                'content' => $path,
            ]);
        } else if ($request->filled('content')) {
            HomeworkMessage::create([
                'homework_id' => $homework->id,
                'sender_type' => 'parent',
                'message_type' => 'text',
                'content' => $request->content,
            ]);
        }

        $homework->update(['status' => 'submitted']);

        return back()->with('success', 'تم إرسال ردك للأخصائي بنجاح.');
    }
}
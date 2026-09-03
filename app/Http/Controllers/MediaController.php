<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\ChildMedia;
use App\Models\MediaComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    /**
     * Display a listing of the media for the specialist or admin.
     */
    public function index()
    {
        $media = ChildMedia::with(['child', 'uploader', 'comments.user'])->latest()->get();
        $children = Child::select('id', 'name', 'code')->get();
        return view('media.index', compact('media', 'children'));
    }

    /**
     * Store a newly created media in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'child_id' => 'required',
            'title' => 'required|string|max:255',
            'file' => 'required|file|max:20480', // 20MB max
            'description' => 'nullable|string'
        ]);

        if ($request->child_id !== 'all') {
            $request->validate(['child_id' => 'exists:children,id']);
        }

        $file = $request->file('file');
        $path = $file->store('child_media', 'public');
        $size = number_format($file->getSize() / 1048576, 2) . ' MB';
        
        $extension = $file->getClientOriginalExtension();
        $type = in_array(strtolower($extension), ['mp4', 'mov', 'avi', 'wmv']) ? 'video' : (in_array(strtolower($extension), ['jpg', 'jpeg', 'png']) ? 'image' : 'document');

        if ($request->child_id === 'all') {
            $children = Child::where('status', 'active')->get();
            foreach ($children as $child) {
                ChildMedia::create([
                    'child_id' => $child->id,
                    'uploaded_by' => Auth::id(),
                    'title' => $request->title,
                    'description' => $request->description,
                    'file_path' => $path,
                    'file_type' => $type,
                    'file_size' => $size
                ]);
            }
            return redirect()->back()->with('success', 'تم رفع الملف وإضافته لجميع الأطفال بنجاح!');
        } else {
            ChildMedia::create([
                'child_id' => $request->child_id,
                'uploaded_by' => Auth::id(),
                'title' => $request->title,
                'description' => $request->description,
                'file_path' => $path,
                'file_type' => $type,
                'file_size' => $size
            ]);
            return redirect()->back()->with('success', 'تم رفع الملف بنجاح!');
        }
    }

    /**
     * Store a new comment on a media item.
     */
    public function comment(Request $request, $mediaId)
    {
        $request->validate(['comment' => 'required|string']);
        
        MediaComment::create([
            'child_media_id' => $mediaId,
            'user_id' => Auth::id(),
            'comment' => $request->comment,
        ]);

        return redirect()->back()->with('success', 'تم إضافة التعليق!');
    }

    /**
     * Delete a media item.
     */
    public function destroy($id)
    {
        $media = ChildMedia::findOrFail($id);
        
        if ($media->uploaded_by !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403);
        }

        Storage::disk('public')->delete($media->file_path);
        $media->delete();

        return redirect()->back()->with('success', 'تم حذف الملف بنجاح.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\ParentMessage;
use App\Models\SpecialistRating;
use Illuminate\Http\Request;

class AdminParentNoteController extends Controller
{
    /**
     * Display the Center Management Parent Notes & Complaints Dashboard.
     */
    public function index(Request $request)
    {
        $query = ParentMessage::with('child')->latest();

        // بحث بالاسم أو الكود أو نص الملاحظة
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('parent_name', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%")
                  ->orWhereHas('child', function ($qc) use ($search) {
                      $qc->where('name', 'like', "%{$search}%")
                         ->orWhere('code', 'like', "%{$search}%");
                  });
            });
        }

        // فلترة بالحالة (جديدة بانتظار الرد / عاجلة / تم الحل والرد)
        if ($request->filled('filter')) {
            if ($request->filter === 'new') {
                $query->whereNull('doctor_reply');
            } elseif ($request->filter === 'urgent') {
                $query->where('is_urgent', true);
            } elseif ($request->filter === 'resolved') {
                $query->whereNotNull('doctor_reply');
            }
        }

        // فلترة بالجهة (المركز / الأخصائي)
        if ($request->filled('recipient') && $request->recipient !== 'all') {
            $query->where('recipient_type', $request->recipient);
        }

        $messages = $query->paginate(12);

        // إحصائيات التنبيهات
        $newNotesCount = ParentMessage::whereNull('doctor_reply')->count();
        $urgentNotesCount = ParentMessage::where('is_urgent', true)->count();
        $resolvedNotesCount = ParentMessage::whereNotNull('doctor_reply')->count();
        $totalRatingsCount = SpecialistRating::count();
        
        // جلب تقييمات ومقترحات أولياء الأمور لإدارة المركز
        $ratings = SpecialistRating::with('child')->latest()->take(10)->get();

        return view('admin.parent_notes.index', compact(
            'messages',
            'ratings',
            'newNotesCount',
            'urgentNotesCount',
            'resolvedNotesCount',
            'totalRatingsCount'
        ));
    }

    /**
     * Center Administration replies to a parent's note / complaint.
     */
    public function reply(Request $request, ParentMessage $message)
    {
        $validated = $request->validate([
            'admin_reply' => 'required|string|min:2|max:3000',
            'replied_by'  => 'required|string|max:100',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $message->update([
            'doctor_reply' => $validated['admin_reply'],
            'replied_by'   => $validated['replied_by'],
            'admin_notes'  => $validated['admin_notes'] ?? null,
            'replied_at'   => now(),
            'status'       => 'resolved',
        ]);

        return redirect()->back()
            ->with('success', 'تم إرسال رد وتوجيه إدارة المركز لولي الأمر بنجاح وسيظهر له فوراً في بوابته! ');
    }

    /**
     * Toggle urgent flag on a parent note.
     */
    public function toggleUrgent(ParentMessage $message)
    {
        $message->update([
            'is_urgent' => !$message->is_urgent,
        ]);

        $statusText = $message->is_urgent ? 'تم تصنيف الملاحظة كـ عاجلة وهامة جداً ' : 'تم إلغاء تصنيف الأهمية العاجلة';
        return redirect()->back()->with('success', $statusText);
    }

    /**
     * Delete/Archive a parent message.
     */
    public function destroy(ParentMessage $message)
    {
        $message->delete();
        return redirect()->back()->with('success', 'تم حذف الملاحظة من السجل بنجاح.');
    }
}
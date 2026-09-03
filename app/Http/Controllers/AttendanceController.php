<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Child;
use App\Models\SessionSchedule;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function scanner()
    {
        return view('attendance.scanner');
    }

    public function scan($code)
    {
        $child = Child::where('code', $code)->first();

        if (!$child) {
            return redirect()->route('attendance.scanner')->with('error', 'الكود غير صالح. الطفل غير موجود.');
        }

        // Find today's session for this child
        $today = Carbon::today()->toDateString();
        $session = SessionSchedule::where('child_id', $child->id)
            ->where('session_date', $today)
            ->where('status', 'scheduled') // only if not cancelled or completed
            ->first();

        if (!$session) {
            return redirect()->route('attendance.scanner')->with('error', "لا توجد جلسات مجدولة للبطل ({$child->name}) اليوم.");
        }

        if ($session->attendance_status === 'attended') {
            return redirect()->route('attendance.scanner')->with('success', "البطل ({$child->name}) مسجل حضوره بالفعل مسبقاً.");
        }

        // Mark as attended
        $session->update([
            'attendance_status' => 'attended',
        ]);

        return redirect()->route('attendance.scanner')->with('success', "تم تسجيل حضور البطل ({$child->name}) بنجاح لجلسة ({$session->session_title}).");
    }

    // الشاشة الثابتة التي تعرض الـ QR في الاستقبال
    public function centerScreen()
    {
        return view('attendance.center_screen');
    }

    // الصفحة التي يصل لها ولي الأمر بعد مسح الـ QR بهاتفه
    public function parentCheckIn(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $child = null;

        // 1. Try to identify by logged-in user
        if ($user && $user->role === 'parent') {
            $children = Child::where('user_id', $user->id)->get();
            if ($children->isEmpty() && $user->username) {
                $children = Child::where('national_id', $user->username)->get();
            }

            if ($children->count() > 1) {
                if ($request->filled('selected_child_id')) {
                    $child = $children->where('id', $request->selected_child_id)->first();
                } else {
                    return view('attendance.select_child', compact('children'));
                }
            } else {
                $child = $children->first();
            }
        } 
        // 2. Try to identify by provided code in request
        elseif ($request->filled('code')) {
            $child = Child::where('code', $request->code)->first();
            if (!$child) {
                return view('attendance.checkin_result', [
                    'success' => false,
                    'message' => 'الكود المدخل غير صحيح. الرجاء التأكد والمحاولة مرة أخرى.',
                    'showForm' => true
                ]);
            }
        }

        // 3. If still no child, show the form to enter the code
        if (!$child) {
            return view('attendance.checkin_form');
        }

        // 4. Check session
        $today = Carbon::today()->toDateString();
        $session = SessionSchedule::where('child_id', $child->id)
            ->where('session_date', $today)
            ->where('status', 'scheduled')
            ->first();

        if (!$session) {
            return view('attendance.checkin_result', [
                'success' => false,
                'message' => "لا توجد جلسات مجدولة للبطل ({$child->name}) اليوم.",
                'showForm' => false
            ]);
        }

        if ($session->attendance_status === 'attended') {
            return view('attendance.checkin_result', [
                'success' => true,
                'message' => "مرحباً يا بطل ({$child->name})! تم تسجيل حضورك مسبقاً بنجاح لجلسة اليوم.",
                'showForm' => false
            ]);
        }

        // 5. Mark attended
        $session->update([
            'attendance_status' => 'attended',
        ]);

        return view('attendance.checkin_result', [
            'success' => true,
            'message' => "أهلاً بك يا بطل ({$child->name})! تم تأكيد حضورك بنجاح لجلسة ({$session->session_title}). نتمنى لك جلسة ممتعة ومفيدة!",
            'showForm' => false
        ]);
    }
}

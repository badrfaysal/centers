<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ForgotPasswordController extends Controller
{
    public function showForgotForm()
    {
        return view('auth.forgot_password');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'phone'    => 'required|string',
            'username' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'phone.required' => 'رقم الجوال مطلوب.',
            'username.required' => 'اسم المستخدم مطلوب.',
            'password.required' => 'كلمة المرور الجديدة مطلوبة.',
            'password.min' => 'كلمة المرور يجب أن لا تقل عن 6 أحرف.',
            'password.confirmed' => 'كلمة المرور غير متطابقة.',
        ]);

        $user = User::where('phone', $request->phone)
                    ->where('username', $request->username)
                    ->first();

        if (!$user) {
            return back()->withErrors(['error' => 'لا توجد بيانات مطابقة (تأكد من رقم الجوال واسم المستخدم).'])->withInput();
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->route('login')->with('success', 'تم إعادة تعيين كلمة المرور بنجاح! يمكنك الآن تسجيل الدخول.');
    }
}

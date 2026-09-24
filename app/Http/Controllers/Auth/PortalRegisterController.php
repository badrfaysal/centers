<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Child;
use App\Models\Specialist;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class PortalRegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.portal_register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'phone'    => 'required|string',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'phone.required' => 'رقم الجوال مطلوب.',
            'username.required' => 'اسم المستخدم مطلوب.',
            'username.unique' => 'اسم المستخدم مستخدم بالفعل، يرجى اختيار اسم آخر.',
            'password.required' => 'كلمة المرور مطلوبة.',
            'password.min' => 'كلمة المرور يجب أن لا تقل عن 6 أحرف.',
            'password.confirmed' => 'كلمة المرور غير متطابقة.',
        ]);

        $phone = $request->phone;

        // Check if user already exists with this phone
        $existingUser = User::where('phone', $phone)->first();
        if ($existingUser) {
            return back()->withErrors(['phone' => 'هذا الرقم له حساب مسجل بالفعل. يمكنك تسجيل الدخول أو استعادة كلمة المرور.'])->withInput();
        }

        // Find associated records
        $children = Child::where('phone', $phone)->get();
        $specialists = Specialist::where('phone', $phone)->get();

        if ($children->isEmpty() && $specialists->isEmpty()) {
            return back()->withErrors(['phone' => 'عذراً، لم نجد أي ملفات (لأطفال أو أخصائيين) مرتبطة برقم الجوال هذا في المركز. يرجى مراجعة الإدارة.'])->withInput();
        }

        $role = $specialists->isNotEmpty() ? 'specialist' : 'parent';
        
        // The name of the user account could be the parent's name or specialist's name
        $name = $role === 'specialist' ? $specialists->first()->name : $children->first()->parent_name;

        $user = User::create([
            'name'     => $name,
            'username' => $request->username,
            'phone'    => $phone,
            'password' => Hash::make($request->password),
            'role'     => $role,
        ]);

        // Link records to this new user
        foreach ($children as $child) {
            $child->update(['user_id' => $user->id]);
        }
        
        foreach ($specialists as $specialist) {
            $specialist->update(['user_id' => $user->id]);
        }

        Auth::login($user);

        if ($role === 'specialist') {
            return redirect()->route('doctor.timetable')->with('success', 'تم إنشاء حسابك وربطه بنجاح!');
        }

        return redirect()->route('parent.portal')->with('success', 'تم إنشاء حسابك وربطه بملفات أطفالك بنجاح!');
    }
}

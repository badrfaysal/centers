@extends('layouts.app')

@section('title', 'تسجيل الحضور الذكي')

@section('content')
<div class="max-w-md mx-auto pt-10">
    <div class="bg-white rounded-3xl p-8 text-center shadow-lg border-2 border-teal-500">
        
        <div class="w-24 h-24 bg-teal-100 text-teal-600 rounded-full flex items-center justify-center text-5xl mx-auto mb-6 shadow-inner">
            <i class="fa-solid fa-qrcode"></i>
        </div>
        
        <h1 class="text-2xl font-black text-slate-800 mb-2">أهلاً بك في المركز!</h1>
        <p class="text-slate-500 font-bold mb-8">لتأكيد حضور طفلك في الجلسة المجدولة، يرجى إدخال الكود الخاص به أدناه.</p>

        <form action="{{ route('attendance.parent_checkin') }}" method="GET" class="space-y-4">
            <div>
                <input type="text" name="code" required placeholder="كود الطفل (مثال: CH-1234)" class="w-full p-4 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:border-teal-500 font-mono text-center uppercase font-black text-lg text-slate-700">
            </div>
            
            <button type="submit" class="w-full py-4 rounded-2xl bg-teal-600 hover:bg-teal-700 text-white font-black text-lg shadow-md transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-check-to-slot"></i>
                <span>تأكيد وتسجيل الحضور</span>
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-slate-100">
            <p class="text-xs font-semibold text-slate-400 mb-3">أو قم بتسجيل الدخول كولي أمر لتسجيل الحضور تلقائياً</p>
            <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 w-full py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-black rounded-2xl transition">
                <i class="fa-solid fa-user-lock"></i>
                <span>تسجيل الدخول لبوابة أولياء الأمور</span>
            </a>
        </div>
        
    </div>
</div>
@endsection
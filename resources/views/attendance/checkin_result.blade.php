@extends('layouts.app')

@section('title', 'تأكيد الحضور')

@section('content')
<div class="max-w-md mx-auto pt-10">
    <div class="bg-white rounded-3xl p-8 text-center shadow-lg border-2 {{ $success ? 'border-emerald-500' : 'border-rose-500' }}">
        
        @if($success)
            <div class="w-24 h-24 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center text-5xl mx-auto mb-6 shadow-inner">
                <i class="fa-solid fa-check"></i>
            </div>
            <h1 class="text-2xl font-black text-slate-800 mb-4">تم تأكيد الحضور</h1>
            <p class="text-emerald-700 font-bold text-lg leading-relaxed">{{ $message }}</p>
        @else
            <div class="w-24 h-24 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center text-5xl mx-auto mb-6 shadow-inner">
                <i class="fa-solid fa-xmark"></i>
            </div>
            <h1 class="text-2xl font-black text-slate-800 mb-4">عفواً</h1>
            <p class="text-rose-700 font-bold text-lg leading-relaxed">{{ $message }}</p>
        @endif

        @if(isset($showForm) && $showForm)
        <div class="mt-6 pt-6 border-t border-slate-100">
            <form action="{{ route('attendance.parent_checkin') }}" method="GET" class="flex gap-2">
                <input type="text" name="code" required placeholder="أدخل كود الطفل (مثال: CH-1234)" class="flex-1 p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-teal-500 font-mono text-center uppercase font-bold text-sm">
                <button type="submit" class="px-5 py-3 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-black text-sm transition">
                    تسجيل الحضور
                </button>
            </form>
        </div>
        @endif

        <div class="mt-4 pt-4 border-t border-slate-100">
            <a href="{{ route('parent.portal') }}" class="inline-flex items-center justify-center gap-2 w-full py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-black rounded-2xl transition">
                <i class="fa-solid fa-arrow-right"></i>
                <span>العودة لبوابة ولي الأمر</span>
            </a>
        </div>
        
    </div>
</div>
@endsection
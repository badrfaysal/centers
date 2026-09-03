@extends('layouts.app')

@section('title', 'اختيار الطفل لتسجيل الحضور')

@section('content')
<div class="max-w-md mx-auto pt-10">
    <div class="bg-white rounded-3xl p-8 text-center shadow-lg border-2 border-teal-500">
        
        <h1 class="text-2xl font-black text-slate-800 mb-2">تسجيل الحضور</h1>
        <p class="text-slate-500 font-bold mb-6">لقد وجدنا أكثر من ملف مرتبط بحسابك. يرجى اختيار البطل الذي تود تسجيل حضوره لجلسة اليوم:</p>

        <form action="{{ route('attendance.parent_checkin') }}" method="GET" class="space-y-3">
            @foreach($children as $c)
            <label class="flex items-center gap-4 p-4 border border-slate-200 rounded-2xl cursor-pointer hover:border-teal-500 hover:bg-teal-50 transition">
                <input type="radio" name="selected_child_id" value="{{ $c->id }}" required class="w-5 h-5 text-teal-600 focus:ring-teal-500">
                <div class="text-right">
                    <span class="block font-black text-slate-800 text-lg">{{ $c->name }}</span>
                    <span class="block text-xs font-bold text-slate-400 mt-1">كود: {{ $c->code }}</span>
                </div>
            </label>
            @endforeach

            <button type="submit" class="w-full mt-6 py-4 rounded-2xl bg-teal-600 hover:bg-teal-700 text-white font-black text-lg shadow-md transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-check-to-slot"></i>
                <span>متابعة لتأكيد الحضور</span>
            </button>
        </form>
        
    </div>
</div>
@endsection
@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="space-y-6">

    <!-- الترويسة -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-white text-xl shadow-lg" style="background-color: {{ $color }};">
                <i class="fa-solid {{ $icon }}"></i>
            </div>
            <div>
                <h2 class="text-2xl font-black text-slate-800">{{ $title }}</h2>
                <p class="text-xs text-slate-500 font-semibold mt-0.5">{{ $subtitle }}</p>
            </div>
        </div>

        <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-50 transition shadow-xs flex items-center gap-2">
            <i class="fa-solid fa-arrow-right"></i>
            <span>العودة للوحة التحكم</span>
        </a>
    </div>

    <!-- كارت المحتوى النموذجي التفاعلي -->
    <div class="bg-white rounded-3xl p-8 md:p-12 border border-slate-100 shadow-sm text-center max-w-2xl mx-auto space-y-6">
        <div class="w-20 h-20 rounded-3xl mx-auto flex items-center justify-center text-3xl shadow-xl animate-bounce" style="background-color: {{ $color }}15; color: {{ $color }};">
            <i class="fa-solid {{ $icon }}"></i>
        </div>

        <div class="space-y-2">
            <h3 class="text-xl font-black text-slate-800">قسم {{ $title }} جاهز للربط والتخصيص </h3>
            <p class="text-xs text-slate-500 font-medium leading-relaxed">
                هذا القسم مربوط بنظام المسارات والهوية العامة. سنقوم ببناء شاشاته وقاعدة بياناته التفصيلية في الخطوات التالية حسب أولوياتك.
            </p>
        </div>

        <div class="pt-4 flex flex-wrap items-center justify-center gap-3">
            <a href="{{ route('settings.index') }}" class="px-5 py-2.5 rounded-xl text-white font-bold text-xs shadow-md transition hover:opacity-90" style="background-color: #0d9488;">
                <i class="fa-solid fa-palette ml-1"></i>
                <span>تخصيص هوية وألوان المركز</span>
            </a>
            <a href="{{ route('dashboard') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                <span>الذهاب للوحة التحكم</span>
            </a>
        </div>
    </div>

</div>
@endsection


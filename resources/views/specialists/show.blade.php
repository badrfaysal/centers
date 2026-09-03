@extends('layouts.app')

@section('title', 'بروفايل الأخصائي: ' . $specialist->name)

@section('content')
<div class="space-y-8" x-data="{ activeTab: 'children' }">

    <!-- ==================== 1. بطاقة الهوية وبروفايل الأخصائي ==================== -->
    <div class="bg-white rounded-3xl p-6 md:p-8 border border-slate-100 shadow-sm space-y-6">
        
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-6 border-b border-slate-100">
            
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5 text-center sm:text-right">
                <div class="relative">
                    <img src="{{ $specialist->avatar_url }}" alt="{{ $specialist->name }}" class="w-24 h-24 rounded-3xl object-cover ring-4 ring-slate-100 shadow-md">
                    <span class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full border-2 border-white {{ $specialist->status === 'active' ? 'bg-emerald-500' : 'bg-amber-500' }}" title="الحالة: {{ $specialist->status === 'active' ? 'نشط' : 'إجازة' }}"></span>
                </div>

                <div class="space-y-1.5">
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2.5">
                        <h2 class="text-2xl font-black text-slate-800">{{ $specialist->name }}</h2>
                        <span class="font-mono text-xs px-2.5 py-0.5 rounded-lg bg-slate-100 text-slate-700 font-bold" style="color: #0d9488;">{{ $specialist->code }}</span>
                        @if($specialist->status === 'active')
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800">
                                متاح للجلسات 
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800">
                                في إجازة 
                            </span>
                        @endif
                    </div>

                    <p class="text-xs text-slate-500 font-bold">
                        {{ $specialist->job_title }} • <span class="text-purple-700">{{ $specialist->specialization }}</span>
                    </p>

                    <p class="text-xs text-slate-400 font-medium">
                        هاتف: <span class="font-mono font-bold text-slate-700">{{ $specialist->phone }}</span> • الغرفة الافتراضية: <strong class="text-slate-800">{{ $specialist->default_room ?? 'غير محددة' }}</strong>
                    </p>
                </div>
            </div>

            <!-- أزرار الإجراءات السريعة -->
            <div class="flex flex-wrap items-center justify-center gap-2.5">
                <a href="{{ route('doctor.sessions.create') }}" class="px-4 py-2.5 rounded-2xl text-white font-black text-xs shadow-md transition flex items-center gap-1.5 hover:opacity-95" style="background-color: #0d9488;">
                    <i class="fa-solid fa-notes-medical"></i>
                    <span>تسجيل جلسة باسمه</span>
                </a>

                <a href="{{ route('specialists.edit', $specialist) }}" class="px-4 py-2.5 bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white rounded-2xl text-xs font-bold transition flex items-center gap-1.5 shadow-xs">
                    <i class="fa-solid fa-pen-to-square"></i>
                    <span>تعديل البيانات</span>
                </a>

                <a href="https://wa.me/2{{ $specialist->phone }}" target="_blank" class="px-4 py-2.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white rounded-2xl text-xs font-bold transition flex items-center gap-1.5 shadow-xs">
                    <i class="fa-brands fa-whatsapp text-sm"></i>
                    <span>مراسلة واتساب</span>
                </a>

                <a href="{{ route('specialists.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-2xl text-xs font-bold transition">
                    العودة للقائمة
                </a>
            </div>

        </div>

        <!-- أشرطة ومؤشرات الأداء -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs font-medium">
            <div class="p-3.5 rounded-2xl bg-cyan-50/70 border border-cyan-100 space-y-1">
                <span class="text-[10px] font-bold text-cyan-700 block">سنوات الخبرة:</span>
                <p class="font-black text-slate-800 text-sm">{{ $specialist->experience_years }} سنوات</p>
                <p class="text-[10px] text-cyan-600 font-bold">خبرة سريرية وتأهيلية</p>
            </div>

            <div class="p-3.5 rounded-2xl bg-purple-50/70 border border-purple-100 space-y-1">
                <span class="text-[10px] font-bold text-purple-700 block">الأطفال المتابعين:</span>
                <p class="font-black text-purple-950 text-sm">{{ $assignedChildren->count() }} أطفال</p>
                <p class="text-[10px] text-purple-600 font-bold">حالات نشطة</p>
            </div>

            <div class="p-3.5 rounded-2xl bg-emerald-50/70 border border-emerald-100 space-y-1">
                <span class="text-[10px] font-bold text-emerald-700 block">الجلسات الموثقة:</span>
                <p class="font-black text-emerald-800 text-sm">{{ $recentSessions->count() }} جلسة</p>
                <p class="text-[10px] text-emerald-600 font-bold">مع تقارير كاملة</p>
            </div>

            <div class="p-3.5 rounded-2xl bg-amber-50/70 border border-amber-100 space-y-1">
                <span class="text-[10px] font-bold text-amber-800 block">نظام المحاسبة:</span>
                <p class="font-black text-amber-900 text-xs">
                    {{ $specialist->salary_type === 'per_session' ? 'بالجلسة: ' . $specialist->session_rate . ' ج.م' : ($specialist->salary_type === 'percentage' ? 'نسبة ' . $specialist->session_rate . '%' : 'راتب ثابت') }}
                </p>
                <p class="text-[10px] text-amber-700 font-bold">نظام مالي معتمد</p>
            </div>
        </div>

    </div>

    <!-- ==================== 2. شريط التبويبات التفاعلية ==================== -->
    <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 pb-1 text-xs sm:text-sm font-bold">
        
        <button type="button" @click="activeTab = 'children'" :class="activeTab === 'children' ? 'border-b-2 font-black pb-3 text-slate-900' : 'text-slate-400 hover:text-slate-600 pb-3'" :style="activeTab === 'children' ? 'border-color: #0d9488; color: #0d9488;' : ''" class="px-3.5 transition flex items-center gap-2">
            <i class="fa-solid fa-child-reaching"></i>
            <span>الأطفال المسندين للمتابعة ({{ $assignedChildren->count() }})</span>
        </button>

        <button type="button" @click="activeTab = 'sessions'" :class="activeTab === 'sessions' ? 'border-b-2 font-black pb-3 text-slate-900' : 'text-slate-400 hover:text-slate-600 pb-3'" :style="activeTab === 'sessions' ? 'border-color: #0d9488; color: #0d9488;' : ''" class="px-3.5 transition flex items-center gap-2">
            <i class="fa-solid fa-notes-medical"></i>
            <span>سجل الجلسات المنفذة ({{ $recentSessions->count() }})</span>
        </button>

        <button type="button" @click="activeTab = 'profile'" :class="activeTab === 'profile' ? 'border-b-2 font-black pb-3 text-slate-900' : 'text-slate-400 hover:text-slate-600 pb-3'" :style="activeTab === 'profile' ? 'border-color: #0d9488; color: #0d9488;' : ''" class="px-3.5 transition flex items-center gap-2">
            <i class="fa-solid fa-id-card"></i>
            <span>المؤهلات وجدول العمل</span>
        </button>

    </div>

    <!-- ==================== تبويب 1: الأطفال المسندين للمتابعة ==================== -->
    <div x-show="activeTab === 'children'" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($assignedChildren as $ch)
            <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex items-center justify-between gap-3 hover:border-slate-300 transition">
                <div class="flex items-center gap-3">
                    <img src="{{ $ch->avatar_url }}" class="w-12 h-12 rounded-2xl object-cover ring-2 ring-slate-100 p-0.5">
                    <div>
                        <a href="{{ route('children.show', $ch) }}" class="font-extrabold text-sm text-slate-800 hover:underline block">
                            {{ $ch->name }}
                        </a>
                        <p class="text-[11px] font-mono font-bold" style="color: #0d9488;">{{ $ch->code }} • {{ $ch->age_text }}</p>
                        
                        <div class="flex flex-wrap gap-1 mt-1">
                            @foreach($ch->diagnoses_list as $d)
                            <span class="px-2 py-0.5 rounded-md text-[9px] font-bold bg-slate-100 text-slate-700">{{ $d }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <a href="{{ route('children.show', $ch) }}" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition shrink-0">
                    ملف الطفل ←
                </a>
            </div>
            @empty
            <div class="col-span-2 p-12 text-center bg-white rounded-3xl border border-slate-100 text-slate-400 text-xs font-bold">
                لم يتم إسناد أطفال لهذا الأخصائي حتى الآن. يمكنك اختياره كـ أخصائي معالج عند تسجيل أو تعديل أي طفل!
            </div>
            @endforelse
        </div>
    </div>

    <!-- ==================== تبويب 2: سجل الجلسات المنفذة ==================== -->
    <div x-show="activeTab === 'sessions'" class="space-y-4">
        <div class="space-y-3">
            @forelse($recentSessions as $sess)
            <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100 text-xs">
                    <div class="flex items-center gap-2 font-bold text-slate-800">
                        <span>جلسة للطفل: <strong class="text-teal-700">{{ $sess->child ? $sess->child->name : '' }}</strong></span>
                        <span class="text-slate-400 font-mono">• {{ $sess->session_date ? $sess->session_date->format('Y-m-d') : '' }}</span>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-lg bg-emerald-100 text-emerald-800 text-[10px] font-black">
                        {{ $sess->child_mood }}
                    </span>
                </div>
                <p class="text-xs text-slate-700 font-medium leading-relaxed bg-slate-50 p-3 rounded-xl">
                    {{ $sess->clinical_notes }}
                </p>
            </div>
            @empty
            <div class="p-12 text-center bg-white rounded-3xl border border-slate-100 text-slate-400 text-xs font-bold">
                لا توجد جلسات مسجلة باسم هذا الأخصائي بعد.
            </div>
            @endforelse
        </div>
    </div>

    <!-- ==================== تبويب 3: المؤهلات وجدول العمل ==================== -->
    <div x-show="activeTab === 'profile'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <!-- بطاقة المؤهلات والترخيص -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-4 text-xs font-medium">
            <h4 class="font-extrabold text-sm text-slate-800 border-b border-slate-100 pb-3">المؤهلات والتراخيص الأكاديمية</h4>

            <div class="space-y-1">
                <span class="text-slate-400 font-bold block text-[10px]">المؤهل العلمي:</span>
                <p class="p-3 bg-slate-50 rounded-xl font-bold text-slate-800">{{ $specialist->qualification ?? 'غير محدد' }}</p>
            </div>

            <div class="space-y-1">
                <span class="text-slate-400 font-bold block text-[10px]">رقم ترخيص مزاولة المهنة:</span>
                <p class="p-3 bg-slate-50 rounded-xl font-mono font-bold text-slate-800">{{ $specialist->license_number ?? 'غير مسجل' }}</p>
            </div>

            <div class="space-y-1">
                <span class="text-slate-400 font-bold block text-[10px]">السيرة الذاتية والنبذة:</span>
                <p class="p-3.5 bg-slate-50 rounded-xl leading-relaxed text-slate-700">{{ $specialist->bio ?? 'لا توجد نبذة مسجلة' }}</p>
            </div>
        </div>

        <!-- بطاقة مواعيد وأيام العمل -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-4 text-xs font-medium">
            <h4 class="font-extrabold text-sm text-slate-800 border-b border-slate-100 pb-3">جدول وأيام العمل بالمركز</h4>

            <div class="space-y-2">
                <span class="text-slate-400 font-bold block text-[10px]">أيام العمل المعتمدة:</span>
                <div class="flex flex-wrap gap-1.5">
                    @foreach((array)($specialist->work_days ?? []) as $d)
                    <span class="px-3 py-1.5 rounded-xl bg-teal-50 text-teal-800 border border-teal-200 font-bold">{{ $d }}</span>
                    @endforeach
                </div>
            </div>

            <div class="space-y-1 pt-2">
                <span class="text-slate-400 font-bold block text-[10px]">القاعة الافتراضية:</span>
                <p class="p-3 bg-slate-50 rounded-xl font-bold text-slate-800">{{ $specialist->default_room ?? 'غير محددة' }}</p>
            </div>
        </div>

    </div>

</div>
@endsection



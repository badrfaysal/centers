@extends('layouts.app')

@section('title', 'ملف الطفل: ' . $child->name)

@section('content')
<div class="space-y-8" x-data="{ 
    activeTab: 'iep',
    goalModalOpen: false,
    videoModalOpen: false,
    activeVideoSrc: null,
    activeVideoTitle: '',
    whatsappModalOpen: {{ session('whatsapp_url') ? 'true' : 'false' }},
    openVideo(src, title) {
        this.activeVideoSrc = src;
        this.activeVideoTitle = title;
        this.videoModalOpen = true;
    }
}">

    <!-- رسالة النجاح عند تسجيل جلسة -->
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl text-xs font-bold flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
        <span class="flex-1">{{ session('success') }}</span>
        @if(session('whatsapp_url'))
        <a href="{{ session('whatsapp_url') }}" target="_blank" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold flex items-center gap-2 transition shadow-md animate-pulse">
            <i class="fa-brands fa-whatsapp text-base"></i>
            <span>إرسال الملخص للأهل عبر واتساب</span>
        </a>
        @endif
    </div>
    @endif

    <!-- مودال واتساب: إرسال ملخص الجلسة لولي الأمر -->
    @if(session('whatsapp_url'))
    <div x-show="whatsappModalOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
         style="background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);">
        
        <div x-show="whatsappModalOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-90"
             x-transition:enter-end="opacity-100 scale-100"
             @click.away="whatsappModalOpen = false"
             class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden">
            
            <!-- Header -->
            <div class="bg-gradient-to-l from-emerald-500 to-emerald-600 p-6 text-center text-white">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fa-brands fa-whatsapp text-4xl"></i>
                </div>
                <h3 class="text-lg font-black">تم تسجيل الجلسة بنجاح! ✨</h3>
                <p class="text-emerald-100 text-sm mt-1">هل تريد إرسال ملخص الجلسة لولي الأمر الآن؟</p>
            </div>

            <!-- Content -->
            <div class="p-6 space-y-4">
                <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 text-right">
                    <p class="text-xs text-emerald-700 font-bold mb-2 flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-info"></i>
                        سيتم فتح واتساب برسالة جاهزة تحتوي على:
                    </p>
                    <ul class="text-xs text-emerald-800 font-medium space-y-1.5 pr-4">
                        <li class="flex items-center gap-1.5">📅 تاريخ ووقت الجلسة</li>
                        <li class="flex items-center gap-1.5">🩺 اسم الأخصائي ونوع الجلسة</li>
                        <li class="flex items-center gap-1.5">📝 ملاحظات وتقرير الأخصائي</li>
                        <li class="flex items-center gap-1.5">🎯 الأهداف التي تم التدريب عليها</li>
                        <li class="flex items-center gap-1.5">🏠 التمرين المنزلي المطلوب</li>
                        <li class="flex items-center gap-1.5">📱 رابط بوابة ولي الأمر</li>
                    </ul>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3">
                    <a href="{{ session('whatsapp_url') }}" target="_blank" @click="whatsappModalOpen = false"
                       class="flex-1 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-sm font-black flex items-center justify-center gap-2 transition shadow-lg shadow-emerald-200">
                        <i class="fa-brands fa-whatsapp text-xl"></i>
                        <span>إرسال عبر واتساب</span>
                    </a>
                    <button @click="whatsappModalOpen = false" type="button"
                            class="px-5 py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-2xl text-sm font-bold transition">
                        لاحقاً
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- ==================== 1. بطاقة الهوية والبروفايل الرئيسية ==================== -->
    <div class="bg-white rounded-3xl p-6 md:p-8 border border-slate-100 shadow-sm space-y-6">
        
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-6 border-b border-slate-100">
            
            <!-- صورة الطفل والاسم والبيانات السريعة -->
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5 text-center sm:text-right">
                <div class="relative group">
                    <img src="{{ $child->avatar_url }}" alt="{{ $child->name }}" class="w-24 h-24 rounded-3xl object-cover ring-4 ring-slate-100 shadow-md">
                    <span class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full border-2 border-white {{ $child->status === 'active' ? 'bg-emerald-500' : 'bg-amber-500' }}" title="حالة الطفل: {{ $child->status === 'active' ? 'نشط' : 'معلق' }}"></span>
                </div>

                <div class="space-y-1.5">
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2.5">
                        <h2 class="text-2xl font-black text-slate-800">{{ $child->name }}</h2>
                        <span class="font-mono text-xs px-2.5 py-1 rounded-xl font-bold bg-slate-100 text-slate-700" style="color: #0d9488;">{{ $child->code }}</span>
                        @if($child->status === 'active')
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span> نشط بالخطة
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800">
                                معلق
                            </span>
                        @endif
                    </div>

                    <!-- سطور التشخيصات المستقلة السريعة -->
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-1.5 pt-1">
                        @foreach($child->diagnoses_list as $diag)
                        <span class="px-2.5 py-0.5 rounded-lg text-[11px] font-bold bg-slate-100 text-slate-700 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full" style="background-color: #0d9488;"></span>
                            <span>{{ $diag }}</span>
                        </span>
                        @endforeach
                    </div>

                    <p class="text-xs text-slate-400 font-medium">
                        ولي الأمر: <strong class="text-slate-700">{{ $child->parent_name }}</strong> ({{ $child->parent_relation }}) • هاتف: <span class="font-mono font-bold text-slate-600">{{ $child->phone }}</span>
                    </p>
                </div>
            </div>

            <!-- أزرار الإجراءات السريعة (تسجيل جلسة، تعديل، واتساب، طباعة) -->
            <div class="flex flex-wrap items-center justify-center gap-2.5">
                @if(Auth::check() && Auth::user()->role === 'specialist')
                <a href="{{ route('doctor.sessions.create', ['child_id' => $child->id]) }}" class="px-4 py-2.5 rounded-2xl text-white font-black text-xs shadow-md transition flex items-center gap-1.5 hover:opacity-95" style="background-color: #0d9488;">
                    <i class="fa-solid fa-notes-medical"></i>
                    <span>تسجيل جلسة للطفل</span>
                </a>
                @endif

                <a href="{{ route('children.edit', $child) }}" class="px-4 py-2.5 bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white rounded-2xl text-xs font-bold transition flex items-center gap-1.5 shadow-xs">
                    <i class="fa-solid fa-pen-to-square"></i>
                    <span>تعديل الملف</span>
                </a>

                <a href="https://wa.me/2{{ $child->phone }}" target="_blank" class="px-4 py-2.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white rounded-2xl text-xs font-bold transition flex items-center gap-1.5 shadow-xs">
                    <i class="fa-brands fa-whatsapp text-sm"></i>
                    <span>مراسلة الواتساب</span>
                </a>

                <a href="{{ route('children.print', $child) }}" target="_blank" class="px-4 py-2.5 bg-slate-900 text-white hover:bg-slate-800 rounded-2xl text-xs font-bold transition flex items-center gap-1.5 shadow-md" title="طباعة تقرير طبي شامل A4">
                    <i class="fa-solid fa-print text-amber-400"></i>
                    <span>طباعة التقرير A4</span>
                </a>

                <a href="{{ route('children.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-2xl text-xs font-bold transition">
                    العودة للقائمة
                </a>
            </div>

        </div>

        <!-- أشرطة المقارنة والمؤشرات الأربعة السريعة -->
        @php
            $childSchedules = $child->sessionSchedules()->get();
            $attendedCount = $childSchedules->where('attendance_status', 'attended')->count();
            $excusedCount = $childSchedules->filter(function($s) {
                return $s->status === 'cancelled' || str_contains($s->notes ?? '', 'اعتذار');
            })->count();
            $absentCount = $childSchedules->where('attendance_status', 'absent')->count() - $childSchedules->filter(function($s) {
                return $s->attendance_status === 'absent' && ($s->status === 'cancelled' || str_contains($s->notes ?? '', 'اعتذار'));
            })->count();
            $absentCount = max(0, $absentCount);
            
            $totalStats = $attendedCount + $absentCount + $excusedCount;
            $attPct = $totalStats > 0 ? round(($attendedCount / $totalStats) * 100) : 0;
            $absPct = $totalStats > 0 ? round(($absentCount / $totalStats) * 100) : 0;
            $excPct = $totalStats > 0 ? round(($excusedCount / $totalStats) * 100) : 0;
        @endphp
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 text-xs font-medium">

            <!-- 1. مقارنة العمر الزمني والعقلي -->
            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                <span class="text-[10px] font-bold text-slate-400 block">العمر (زمني / عقلي):</span>
                <p class="font-black text-slate-800 text-sm">{{ $child->age_text }}</p>
                <p class="text-[11px] font-bold text-purple-700">
                    العقلي: {{ $child->mental_age ?? 'غير محدد' }}
                </p>
            </div>

            <!-- 2. الأخصائي المتابع -->
            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                <span class="text-[10px] font-bold text-slate-400 block">الأخصائي المعالج:</span>
                <p class="font-black text-slate-800 text-xs truncate">{{ $child->main_specialist ?? 'غير محدد' }}</p>
                <p class="text-[10px] font-bold text-slate-400">متابع الحالة بالمركز</p>
            </div>

            <!-- 3. دكتور المخ والأعصاب -->
            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                <span class="text-[10px] font-bold text-slate-400 block">دكتور المخ والأعصاب:</span>
                <p class="font-black text-blue-800 text-xs truncate">{{ $child->neurologist_name ?? 'لا يوجد' }}</p>
                <p class="text-[10px] text-slate-400 truncate">{{ $child->current_medications ? 'يتناول أدوية مسجلة' : 'بدون أدوية' }}</p>
            </div>

            <!-- 4. إحصائيات الحضور -->
            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 space-y-1 relative group">
                <span class="text-xs font-bold text-slate-500 block">إحصائيات الحضور (إجمالي: {{ $totalStats }}):</span>
                <div class="flex items-center justify-between font-black text-base px-1 pt-0.5 mt-1">
                    <span class="text-emerald-600 flex flex-col items-center gap-1 leading-none" title="حضر">{{ $attendedCount }}<span class="text-[10px] text-emerald-500 font-bold">حضر ({{ $attPct }}%)</span></span>
                    <span class="text-rose-500 flex flex-col items-center gap-1 leading-none" title="غاب">{{ $absentCount }}<span class="text-[10px] text-rose-400 font-bold">غاب ({{ $absPct }}%)</span></span>
                    <span class="text-amber-500 flex flex-col items-center gap-1 leading-none" title="اعتذر">{{ $excusedCount }}<span class="text-[10px] text-amber-400 font-bold">اعتذر ({{ $excPct }}%)</span></span>
                </div>
            </div>

            <!-- 5. رصيد الجلسات بالباقة -->
            <div class="p-3.5 rounded-2xl border space-y-1" style="background-color: #0d948808; border-color: #0d948830;">
                <span class="text-[10px] font-bold text-slate-400 block">رصيد باقة الجلسات:</span>
                <p class="font-black text-slate-800 text-sm" style="color: #0d9488;">
                    متبقي {{ $packageInfo['remaining_sessions'] }} من {{ $packageInfo['total_sessions'] }}
                </p>
                <p class="text-[10px] text-emerald-700 font-bold">جلسات منتظمة</p>
            </div>

        </div>

    </div>

    <!-- ==================== 2. شريط التبويبات التفاعلية (Profile Tabs) ==================== -->
    <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 pb-1 text-xs sm:text-sm font-bold">
        <button type="button" @click="activeTab = 'iep'" :class="activeTab === 'iep' ? 'border-b-2 font-black pb-3 text-slate-900' : 'text-slate-400 hover:text-slate-600 pb-3'" :style="activeTab === 'iep' ? 'border-color: #0d9488; color: #0d9488;' : ''" class="px-3.5 transition flex items-center gap-2">
            <i class="fa-solid fa-bullseye"></i>
            <span>الخطة العلاجية الفردية (IEP)</span>
        </button>

        <button type="button" @click="activeTab = 'medical'" :class="activeTab === 'medical' ? 'border-b-2 font-black pb-3 text-slate-900' : 'text-slate-400 hover:text-slate-600 pb-3'" :style="activeTab === 'medical' ? 'border-color: #0d9488; color: #0d9488;' : ''" class="px-3.5 transition flex items-center gap-2">
            <i class="fa-solid fa-brain"></i>
            <span>الملف الطبي والسريري</span>
        </button>

        <button type="button" @click="activeTab = 'sessions'" :class="activeTab === 'sessions' ? 'border-b-2 font-black pb-3 text-slate-900' : 'text-slate-400 hover:text-slate-600 pb-3'" :style="activeTab === 'sessions' ? 'border-color: #0d9488; color: #0d9488;' : ''" class="px-3.5 transition flex items-center gap-2">
            <i class="fa-solid fa-calendar-check"></i>
            <span>سجل الجلسات والتقارير</span>
            <span class="mr-1 px-2 py-0.5 rounded-full text-[10px] bg-slate-100 text-slate-700 font-black">{{ count($recentSessions) }}</span>
        </button>

        <button type="button" @click="activeTab = 'parent_notes'" :class="activeTab === 'parent_notes' ? 'border-b-2 font-black pb-3 text-slate-900' : 'text-slate-400 hover:text-slate-600 pb-3'" :style="activeTab === 'parent_notes' ? 'border-color: #0d9488; color: #0d9488;' : ''" class="px-3.5 transition flex items-center gap-2">
            <i class="fa-solid fa-comments"></i>
            <span>ملاحظات ولي الأمر والتواصل</span>
        </button>
    </div>

    <!-- ==================== تبويب 1: الخطة العلاجية الفردية (IEP Goals) ==================== -->
    <div x-show="activeTab === 'iep'" class="space-y-6">
        
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-black text-lg text-slate-800">الأهداف العلاجية المحددة للطفل (IEP Goals)</h3>
                <p class="text-xs text-slate-400 font-semibold mt-0.5">متابعة نسب التقدم ومراحل الإنجاز لكل هدف تأهيلي</p>
            </div>

            <button type="button" @click="alert('سيتم إضافة نافذة تعريف الأهداف في التحديث القادم')" class="px-4 py-2 rounded-2xl text-white font-bold text-xs shadow-md transition flex items-center gap-2" style="background-color: #0d9488;">
                <i class="fa-solid fa-plus"></i>
                <span>إضافة هدف علاجي جديد</span>
            </button>
        </div>

        <!-- كروت الأهداف العلاجية -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($iepGoals as $goal)
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-4 hover:border-slate-300 transition">
                
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-extrabold bg-slate-100 text-slate-700">
                            {{ $goal['category'] }}
                        </span>
                        <h4 class="font-extrabold text-sm text-slate-800 mt-2 leading-snug">{{ $goal['title'] }}</h4>
                    </div>

                    @if($goal['status'] === 'achieved')
                        <span class="px-2.5 py-1 rounded-xl text-xs font-black bg-emerald-100 text-emerald-800 shrink-0">
                            <i class="fa-solid fa-check ml-1"></i> مكتمل
                        </span>
                    @else
                        <span class="px-2.5 py-1 rounded-xl text-xs font-black bg-blue-50 text-blue-700 shrink-0">
                            قيد التدريب
                        </span>
                    @endif
                </div>

                <!-- شريط التقدم الذكي والمحطات -->
                <div class="space-y-3 pt-2">
                    <div class="flex justify-between items-center text-[10px] font-black text-slate-400 px-1">
                        <span class="{{ $goal['progress'] >= 0 ? 'text-teal-600' : '' }}">البداية</span>
                        <span class="{{ $goal['progress'] >= 25 ? 'text-teal-600' : '' }}">25%</span>
                        <span class="{{ $goal['progress'] >= 50 ? 'text-teal-600' : '' }}">50%</span>
                        <span class="{{ $goal['progress'] >= 75 ? 'text-teal-600' : '' }}">75%</span>
                        <span class="{{ $goal['progress'] >= 100 ? 'text-emerald-600' : '' }}">إتقان</span>
                    </div>
                    
                    <div class="relative w-full h-3 bg-slate-100 rounded-full overflow-hidden flex shadow-inner">
                        <!-- Segments -->
                        <div class="h-full border-r border-white/40 transition-all duration-700 {{ $goal['progress'] >= 25 ? 'bg-teal-500' : 'bg-transparent' }}" style="width: 25%"></div>
                        <div class="h-full border-r border-white/40 transition-all duration-700 {{ $goal['progress'] >= 50 ? 'bg-teal-500' : 'bg-transparent' }}" style="width: 25%"></div>
                        <div class="h-full border-r border-white/40 transition-all duration-700 {{ $goal['progress'] >= 75 ? 'bg-teal-500' : 'bg-transparent' }}" style="width: 25%"></div>
                        <div class="h-full transition-all duration-700 {{ $goal['progress'] >= 100 ? 'bg-emerald-500' : 'bg-transparent' }}" style="width: 25%"></div>
                        
                        <!-- Overlay for exact progress if not exactly on a quarter -->
                        <div class="absolute top-0 left-0 h-full bg-teal-400/30 transition-all duration-1000 rounded-full" style="width: {{ $goal['progress'] }}%"></div>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400 font-medium">
                    <span>المعالج: <strong class="text-slate-700">{{ $goal['specialist'] }}</strong></span>
                    <span>الموعد المستهدف: <strong class="text-slate-700 font-mono">{{ $goal['target_date'] }}</strong></span>
                </div>

            </div>
            @endforeach
        </div>

    </div>

    <!-- ==================== تبويب 2: الملف الطبي والسريري ==================== -->
    <div x-show="activeTab === 'medical'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <!-- بطاقة التشخيصات والمخ والأعصاب -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-5">
            <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                <i class="fa-solid fa-stethoscope text-base" style="color: #0d9488;"></i>
                <h4 class="font-extrabold text-sm text-slate-800">التشخيصات الطبية والمخ والأعصاب</h4>
            </div>

            <!-- سطور التشخيصات المستقلة -->
            <div class="space-y-2">
                <span class="text-xs font-bold text-slate-400">سطور التشخيصات المعتمدة:</span>
                <div class="space-y-1.5">
                    @foreach($child->diagnoses_list as $idx => $d)
                    <div class="p-3 rounded-2xl bg-purple-50/80 border border-purple-100 text-purple-950 font-bold text-xs flex items-center gap-2.5">
                        <span class="w-5 h-5 rounded-lg bg-purple-200 text-purple-800 flex items-center justify-center text-[10px]">{{ $idx + 1 }}</span>
                        <span>{{ $d }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="space-y-1 text-xs">
                <span class="text-slate-400 font-bold">طبيب المخ والأعصاب المتابع:</span>
                <p class="p-3 rounded-2xl bg-blue-50 text-blue-900 font-bold">
                    {{ $child->neurologist_name ?? 'لا يوجد طبيب مخ وأعصاب مسجل' }}
                </p>
            </div>

            <div class="space-y-1 text-xs">
                <span class="text-slate-400 font-bold">الأدوية والعلاجات الحالية:</span>
                <p class="p-3 rounded-2xl bg-amber-50 text-amber-900 font-semibold leading-relaxed">
                    {{ $child->current_medications ?? 'لا يتناول أدوية حالياً' }}
                </p>
            </div>
        </div>

        <!-- بطاقة اختبارات الذكاء والتاريخ المرضي -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-5">
            <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                <i class="fa-solid fa-brain text-base" style="color: #0d9488;"></i>
                <h4 class="font-extrabold text-sm text-slate-800">اختبارات الذكاء والمقاييس السابقة</h4>
            </div>

            <div class="space-y-1 text-xs">
                <span class="text-slate-400 font-bold">سجل اختبارات ومقاييس الذكاء السابقة:</span>
                <p class="p-3.5 rounded-2xl bg-slate-50 text-slate-800 font-medium leading-relaxed">
                    {{ $child->iq_tests_history ?? 'لم تسجل اختبارات ذكاء سابقة بعد' }}
                </p>
            </div>

            <div class="space-y-1 text-xs">
                <span class="text-slate-400 font-bold">الأجهزة المساعدة المستخدمة:</span>
                <p class="p-3 rounded-2xl bg-slate-50 text-slate-800 font-semibold">
                    {{ $child->assistive_devices ?? 'لا توجد أجهزة مساعدة' }}
                </p>
            </div>

            <div class="space-y-1 text-xs">
                <span class="text-slate-400 font-bold">التاريخ المرضي وملاحظات الحمل والولادة:</span>
                <p class="p-3 rounded-2xl bg-slate-50 text-slate-700 font-medium leading-relaxed">
                    {{ $child->medical_notes ?? 'لا توجد ملاحظات مرضية سابقة مسجلة' }}
                </p>
            </div>
        </div>

    </div>

    <!-- ==================== تبويب 3: سجل الجلسات والتقارير ==================== -->
    <div x-show="activeTab === 'sessions'" class="space-y-4">
        
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-black text-lg text-slate-800">سجل الجلسات المنفذة وتقارير الأخصائيين</h3>
                <p class="text-xs text-slate-400 font-semibold mt-0.5">تقارير الجلسات الموثقة بواسطة الأطباء وتفاصيل الاستجابة</p>
            </div>

            @if(Auth::check() && Auth::user()->role === 'specialist')
            <a href="{{ route('doctor.sessions.create', ['child_id' => $child->id]) }}" class="px-4 py-2 rounded-2xl text-white font-bold text-xs shadow-md transition flex items-center gap-2 hover:opacity-95" style="background-color: #0d9488;">
                <i class="fa-solid fa-plus"></i>
                <span>تسجيل تقرير جلسة جديدة</span>
            </a>
            @endif
        </div>

        <div class="space-y-4">
            @foreach($recentSessions as $sess)
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-4">
                
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-white text-sm" style="background-color: #0d9488;">
                            <i class="fa-solid fa-calendar-day"></i>
                        </div>
                        <div>
                            <h4 class="font-black text-sm text-slate-800">{{ $sess['date'] }}</h4>
                            <p class="text-[11px] text-slate-400 font-mono font-semibold">{{ $sess['time'] }} • {{ $sess['room'] }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 rounded-xl text-xs font-black bg-emerald-100 text-emerald-800">
                            استجابة الطفل: {{ $sess['mood'] }}
                        </span>

                        @if($sess['has_video'])
                        <button type="button" @click="openVideo('{{ $sess['video_path'] ?? '#' }}', 'فيديو جلسة {{ $sess['date'] }}')" class="px-3 py-1 rounded-xl text-xs font-black bg-purple-50 text-purple-700 hover:bg-purple-600 hover:text-white transition flex items-center gap-1.5 shadow-2xs">
                            <i class="fa-solid fa-play text-[10px]"></i>
                            <span>مشاهدة الفيديو ({{ $sess['video_duration'] }})</span>
                        </button>
                        @endif
                    </div>
                </div>

                <!-- نص تقرير الجلسة والتمرين المنزلي -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs font-medium">
                    <div class="p-3.5 rounded-2xl bg-slate-50 space-y-1">
                        <span class="text-slate-400 font-bold block text-[11px]"><i class="fa-regular fa-file-lines ml-1 text-teal-600"></i> تقرير الأخصائي عن الجلسة:</span>
                        <p class="text-slate-700 leading-relaxed font-semibold">{{ $sess['notes'] }}</p>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-amber-50/70 border border-amber-100 space-y-1">
                        <span class="text-amber-900 font-bold block text-[11px]"><i class="fa-solid fa-house-user ml-1 text-amber-600"></i> تمرين منزلي مطلوب من ولي الأمر:</span>
                        <p class="text-amber-950 font-semibold leading-relaxed">{{ $sess['home_exercise'] ?? 'متابعة التوجيهات العامة' }}</p>
                    </div>
                </div>

                <div class="pt-1 flex items-center justify-between text-[11px] text-slate-400 font-medium">
                    <span>الأخصائي المنفذ: <strong class="text-slate-700">{{ $sess['specialist'] }}</strong></span>
                </div>

            </div>
            @endforeach
        </div>
    </div>

    <!-- ==================== تبويب 4: ملاحظات وتواصل ولي الأمر المباشر ==================== -->
    <div x-show="activeTab === 'parent_notes'" class="space-y-5">
        <h3 class="font-black text-lg text-slate-800">حائط تواصل وملاحظات ولي الأمر المباشرة</h3>

        <div class="space-y-4">
            @foreach($parentNotes as $pnote)
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-4">
                
                <!-- رسالة ولي الأمر -->
                <div class="p-4 rounded-2xl bg-purple-50/60 border border-purple-100 space-y-2">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-black text-purple-950">{{ $pnote['author'] }} ({{ $pnote['relation'] }})</span>
                        <span class="text-[11px] text-slate-400">{{ $pnote['date'] }}</span>
                    </div>
                    <p class="text-xs text-purple-900 leading-relaxed font-semibold">{{ $pnote['text'] }}</p>
                </div>

                <!-- رد الدكتور المتابع -->
                <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-100 space-y-2 mr-6">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-black text-emerald-950"><i class="fa-solid fa-reply ml-1"></i> رد {{ $pnote['doctor'] }}</span>
                        <span class="text-[10px] text-emerald-700 font-bold">تم الرد</span>
                    </div>
                    <p class="text-xs text-emerald-900 leading-relaxed font-semibold">{{ $pnote['reply'] }}</p>
                </div>

            </div>
            @endforeach
        </div>
    </div>

    <!-- نافذة تشغيل الفيديو المنبثقة (Video Player Modal) -->
    <div x-show="videoModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-xs">
        <div @click.outside="videoModalOpen = false" class="bg-white rounded-3xl shadow-2xl border border-slate-100 max-w-lg w-full p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2 text-purple-700 font-bold text-sm">
                    <i class="fa-solid fa-video"></i>
                    <span x-text="activeVideoTitle"></span>
                </div>
                <button @click="videoModalOpen = false" class="text-slate-400 hover:text-slate-600 text-lg">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="rounded-2xl overflow-hidden bg-slate-950 flex items-center justify-center aspect-video">
                <template x-if="activeVideoSrc && activeVideoSrc !== '#'">
                    <video :src="activeVideoSrc" controls class="w-full h-full object-contain" autoplay></video>
                </template>
                <template x-if="!activeVideoSrc || activeVideoSrc === '#'">
                    <div class="text-center text-white/60 p-6 space-y-2">
                        <i class="fa-solid fa-play text-4xl text-purple-400"></i>
                        <p class="text-xs font-bold">معاينة فيديو الجلسة التوثيقي</p>
                    </div>
                </template>
            </div>

            <div class="pt-2 flex justify-end">
                <button @click="videoModalOpen = false" class="px-5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs">إغلاق</button>
            </div>
        </div>
    </div>

</div>
@endsection


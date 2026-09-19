@extends('layouts.app')

@section('title', 'بوابة ولي الأمر: ' . $child->name)

@section('content')
<div class="space-y-8" x-data="parentPortalCalendarApp()">

    <!-- ==================== 1. بطاقة ترحيب ولي الأمر وهوية الطفل ==================== -->
    <div class="bg-white rounded-3xl p-6 md:p-8 border border-slate-100 shadow-sm space-y-6">
        
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-6 border-b border-slate-100">
            
            <!-- بيانات الطفل وصورته وولي الأمر -->
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5 text-center sm:text-right">
                <div class="relative">
                    <img src="{{ $child->avatar_url }}" alt="{{ $child->name }}" class="w-24 h-24 rounded-3xl object-cover ring-4 ring-purple-100 shadow-md">
                    <span class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full border-2 border-white bg-emerald-500" title="الحالة: نشط"></span>
                </div>

                <div class="space-y-1.5">
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                        <span class="text-xs font-extrabold px-3 py-1 bg-purple-50 text-purple-700 rounded-xl">بوابة أولياء الأمور</span>
                        <h2 class="text-2xl font-black text-slate-800">{{ $child->name }}</h2>
                        <span class="font-mono text-xs px-2.5 py-0.5 rounded-lg bg-slate-100 text-slate-700 font-bold">{{ $child->code }}</span>
                    </div>

                    <p class="text-xs text-slate-500 font-medium">
                        مرحباً بك: <strong class="text-slate-800">{{ $child->parent_name }}</strong> ({{ $child->parent_relation }}) • الأخصائي المتابع: <strong class="text-slate-800">{{ $child->main_specialist ?? 'غير محدد' }}</strong>
                    </p>

                    <!-- سطور التشخيص المستقلة -->
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-1 pt-1">
                        @foreach($child->diagnoses_list as $dg)
                        <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-bold bg-slate-100 text-slate-700">
                            {{ $dg }}
                        </span>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- مبدل الأطفال السريع وإجراءات التواصل -->
            <div class="flex flex-col sm:flex-row items-center gap-3">
                
                @if($children->count() > 1)
                <div class="text-right w-full sm:w-auto">
                    <span class="text-[10px] font-bold text-slate-400 block mb-1">التبديل بين الأبناء:</span>
                    <select @change="switchChild($event.target.value)" class="p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold outline-none w-full sm:w-48">
                        @foreach($children as $ch)
                        <option value="{{ $ch->id }}" {{ $ch->id == $child->id ? 'selected' : '' }}>{{ $ch->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <a href="{{ route('parent.bookings.track', ['phone' => $child->phone]) }}" class="px-4 py-2.5 bg-teal-50 text-teal-800 hover:bg-teal-600 hover:text-white rounded-2xl text-xs font-bold transition flex items-center gap-1.5 border border-teal-200 shadow-2xs">
                    <i class="fa-solid fa-calendar-check text-xs"></i>
                    <span>متابعة طلبات الحجز</span>
                </a>

                <a href="https://wa.me/2{{ $child->phone }}" target="_blank" class="w-full sm:w-auto px-4 py-2.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white rounded-2xl text-xs font-bold transition flex items-center justify-center gap-1.5 border border-emerald-100 shadow-2xs">
                    <i class="fa-brands fa-whatsapp text-sm"></i>
                    <span>واتساب المركز</span>
                </a>
            </div>

        </div>

        <!-- إحصائيات سريعة للطفل لولي الأمر -->
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 text-xs font-medium">
            <div class="p-3.5 rounded-2xl bg-purple-50/60 border border-purple-100 space-y-1">
                <span class="text-[10px] font-bold text-purple-700 block">العمر الزمني:</span>
                <p class="font-black text-slate-800 text-sm">{{ $child->age_text }}</p>
                <p class="text-[10px] text-purple-600 font-bold">العقلي: {{ $child->mental_age ?? 'غير محدد' }}</p>
            </div>

            <div class="p-3.5 rounded-2xl bg-blue-50/60 border border-blue-100 space-y-1">
                <span class="text-[10px] font-bold text-blue-700 block">الجلسات الموثقة:</span>
                <p class="font-black text-slate-800 text-sm">{{ $sessions->count() }} جلسات</p>
                <p class="text-[10px] text-blue-600 font-bold">مع تقارير وفيديوهات</p>
            </div>

            <div class="p-3.5 rounded-2xl bg-emerald-50/60 border border-emerald-100 space-y-1">
                @php
                    $totalProgress = count($iepGoals) > 0 ? collect($iepGoals)->avg('progress') : 0;
                @endphp
                <span class="text-[10px] font-bold text-emerald-700 block">نسبة إنجاز الأهداف:</span>
                <p class="font-black text-emerald-800 text-sm">{{ round($totalProgress) }}% إتقان</p>
                <p class="text-[10px] text-emerald-600 font-bold">متوسط تقدم المهارات</p>
            </div>
            
            <div class="p-3.5 rounded-2xl bg-rose-50/60 border border-rose-100 space-y-1">
                @php
                    $c_schedules = $child->sessionSchedules()->get();
                    $c_att = $c_schedules->where('attendance_status', 'attended')->count();
                    $c_exc = $c_schedules->filter(function($s) { return $s->status === 'cancelled' || str_contains($s->notes ?? '', 'اعتذار'); })->count();
                    $c_abs = max(0, $c_schedules->where('attendance_status', 'absent')->count() - $c_exc);
                    $c_tot = $c_att + $c_abs + $c_exc;
                    $c_att_pct = $c_tot > 0 ? round(($c_att / $c_tot) * 100) : 0;
                    $c_abs_pct = $c_tot > 0 ? round(($c_abs / $c_tot) * 100) : 0;
                @endphp
                <span class="text-[10px] font-bold text-rose-700 block">إحصائيات الحضور:</span>
                <p class="font-black text-slate-800 text-sm text-emerald-600">{{ $c_att_pct }}% حضور</p>
                <p class="text-[10px] text-rose-600 font-bold">{{ $c_abs_pct }}% غياب</p>
            </div>

            <div class="p-3.5 rounded-2xl bg-amber-50/60 border border-amber-100 space-y-1">
                <span class="text-[10px] font-bold text-amber-800 block">رصيد باقة الجلسات:</span>
                <p class="font-black text-amber-900 text-sm">اشتراك نشط</p>
                <p class="text-[10px] text-amber-700 font-bold">باقة الجلسات</p>
            </div>
        </div>

    </div>


    <!-- رسائل النجاح إن وجدت -->
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl text-xs font-bold flex items-center gap-3 animate-in fade-in">
        <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <!-- ==================== 2. ???? ????????? ??????? (???? ???? ??????) ==================== -->
    <!-- ==================== تبويب 0: جدول وكالندر مواعيد جلسات طفلي (Interactive Visual Calendar) ==================== -->
    <div x-show="activeTab === 'calendar'" class="space-y-6">
        
        <!-- الجلسة القادمة المميزة إن وجدت -->
        @php
            $nextSession = $schedules
                ->where('attendance_status', 'pending')
                ->where('status', '!=', 'cancelled')
                ->where('session_date', '>=', now()->toDateString())
                ->sortBy(function($s) {
                    return $s->session_date->format('Y-m-d') . ' ' . $s->start_time;
                })
                ->first();
        @endphp

        @if($nextSession)
        <div class="rounded-3xl p-6 md:p-8 bg-gradient-to-r from-amber-500/10 via-teal-500/10 to-emerald-500/5 border-2 border-amber-300 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 text-amber-900 font-black text-xs uppercase tracking-wider">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
                    </span>
                    <span>الجلسة القادمة للبطل ({{ $child->name }}):</span>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-black bg-amber-500 text-white shadow-xs">
                    {{ $nextSession->day_name_arabic }} {{ $nextSession->session_date->format('Y-m-d') }}
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                <div class="p-4 rounded-2xl bg-white border border-amber-200 shadow-2xs space-y-1">
                    <span class="text-[10px] text-slate-400 font-bold block">الوقت المحدد:</span>
                    <p class="font-black text-base text-slate-900 font-mono">{{ $nextSession->formatted_time_range }}</p>
                </div>

                <div class="p-4 rounded-2xl bg-white border border-amber-200 shadow-2xs space-y-1">
                    <span class="text-[10px] text-slate-400 font-bold block">الأخصائي المعالج:</span>
                    <p class="font-black text-sm text-slate-900">{{ $nextSession->specialist_name }}</p>
                    <p class="text-[10px] text-teal-700 font-bold">{{ $nextSession->room_name }}</p>
                </div>

                <div class="p-4 rounded-2xl bg-white border border-amber-200 shadow-2xs space-y-1">
                    <span class="text-[10px] text-slate-400 font-bold block">نوع الجلسة:</span>
                    <p class="font-bold text-xs text-slate-800">{{ $nextSession->session_title }}</p>
                </div>
            </div>

            @if($nextSession->notes)
            <div class="p-3 bg-white/80 rounded-2xl border border-amber-200 text-xs text-slate-600">
                <span class="font-bold text-slate-800">توجيهات الأخصائي:</span> {{ $nextSession->notes }}
            </div>
            @endif
        </div>
        @endif

        <!-- صندوق الكالندر الشهري التفاعلي لولي الأمر -->
        <div class="bg-white rounded-3xl p-6 md:p-8 border border-slate-100 shadow-sm space-y-6">
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="flex items-center bg-slate-100 rounded-2xl p-1 shadow-2xs">
                        <button type="button" @click="prevMonth()" class="p-2 hover:bg-white text-slate-700 rounded-xl transition shadow-2xs" title="الشهر السابق">
                            <i class="fa-solid fa-chevron-right text-xs"></i>
                        </button>
                        <button type="button" @click="goToToday()" class="px-3 py-1.5 hover:bg-white text-slate-800 text-xs font-bold rounded-xl transition shadow-2xs">
                            اليوم
                        </button>
                        <button type="button" @click="nextMonth()" class="p-2 hover:bg-white text-slate-700 rounded-xl transition shadow-2xs" title="الشهر القادم">
                            <i class="fa-solid fa-chevron-left text-xs"></i>
                        </button>
                    </div>

                    <h3 class="text-xl font-black text-slate-900 flex items-center gap-2">
                        <span x-text="monthNames[currentMonth]"></span>
                        <span class="font-mono text-teal-700" x-text="currentYear"></span>
                    </h3>
                </div>

                <div class="flex items-center bg-slate-100 rounded-2xl p-1 text-xs font-bold">
                    <button type="button" @click="viewMode = 'calendar'" :class="viewMode === 'calendar' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-900'" class="px-3.5 py-1.5 rounded-xl transition flex items-center gap-1.5">
                        <i class="fa-solid fa-table-cells"></i>
                        <span>عرض الكالندر</span>
                    </button>
                    <button type="button" @click="viewMode = 'list'" :class="viewMode === 'list' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-900'" class="px-3.5 py-1.5 rounded-xl transition flex items-center gap-1.5">
                        <i class="fa-solid fa-list-ul"></i>
                        <span>عرض القائمة</span>
                    </button>
                </div>
            </div>

            <!-- ==================== شبكة الكالندر الشهري لولي الأمر ==================== -->
            <div x-show="viewMode === 'calendar'" class="space-y-2">
                
                <div class="grid grid-cols-7 gap-2 text-center text-xs font-extrabold text-slate-400 py-2 border-b border-slate-100">
                    <template x-for="day in dayNames" :key="day">
                        <div x-text="day" class="py-1"></div>
                    </template>
                </div>

                <div class="grid grid-cols-7 gap-2">
                    
                    <template x-for="blank in blankDays" :key="'blank-'+blank">
                        <div class="min-h-[100px] sm:min-h-[120px] p-2 bg-slate-50/40 rounded-2xl border border-slate-100/60 opacity-40"></div>
                    </template>

                    <template x-for="day in daysInMonth" :key="'day-'+day">
                        <div @click="handleDayClick(day)" 
                             :class="{
                                'ring-2 ring-amber-400 bg-amber-50/30 border-amber-300': isToday(day),
                                'bg-white border-slate-200 hover:border-teal-400': !isToday(day)
                             }" 
                             class="min-h-[100px] sm:min-h-[120px] p-2 rounded-2xl border transition shadow-2xs flex flex-col justify-between cursor-pointer hover:shadow-md">
                            
                            <div class="flex items-center justify-between">
                                <span :class="isToday(day) ? 'bg-amber-500 text-white font-black' : 'text-slate-700 font-extrabold'" 
                                      class="w-7 h-7 rounded-xl flex items-center justify-center text-xs font-mono shadow-2xs" 
                                      x-text="day"></span>
                                
                                <span x-show="getSessionsForDay(day).length > 0" class="w-2.5 h-2.5 rounded-full bg-teal-500 animate-ping"></span>
                            </div>

                            <!-- الجلسات في هذا اليوم -->
                            <div class="space-y-1 my-1 overflow-y-auto max-h-[75px]">
                                <template x-for="sess in getSessionsForDay(day)" :key="sess.id">
                                    <div @click.stop="openSessionDetail(sess)" 
                                         :class="{
                                            'bg-orange-100 text-orange-900 border-orange-400 line-through opacity-80': sess.status === 'cancelled',
                                            'bg-emerald-100 text-emerald-900 border-emerald-300': sess.attendance_status === 'attended' && sess.status !== 'cancelled',
                                            'bg-teal-50 text-teal-950 border-teal-200': sess.attendance_status !== 'attended' && sess.status !== 'cancelled'
                                         }" 
                                         class="p-1.5 rounded-xl border text-[10px] font-black truncate shadow-2xs flex items-center gap-1 cursor-pointer hover:shadow-md transition">
                                        <i class="fa-solid fa-clock text-[9px]" :class="sess.attendance_status === 'attended' ? 'text-emerald-600' : 'text-teal-600'"></i>
                                        <span class="font-mono text-[9px]" x-text="sess.start_time.substring(0, 5)"></span>
                                        <span class="truncate" x-text="sess.status === 'cancelled' ? 'اعتذار عن الجلسة' : sess.session_title"></span>
                                        <i x-show="sess.attendance_status === 'attended'" class="fa-solid fa-check-circle text-[9px] text-emerald-600 mr-auto"></i>
                                    </div>
                                </template>
                            </div>

                            <div class="text-[9px] font-bold text-slate-400 text-left">
                                <span x-show="getSessionsForDay(day).length > 0" x-text="getSessionsForDay(day).length + ' موعد'" class="text-teal-700"></span>
                            </div>

                        </div>
                    </template>

                </div>

            </div>

            <!-- ==================== عرض القائمة لولي الأمر ==================== -->
            <div x-show="viewMode === 'list'" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <template x-for="sess in filteredMonthSessions" :key="sess.id">
                        <div class="p-5 rounded-3xl border border-slate-200 bg-white shadow-2xs space-y-3 hover:border-teal-400 transition">
                            <div class="flex items-center justify-between pb-2 border-b border-slate-100 text-xs">
                                <div class="flex items-center gap-2 font-bold text-slate-800">
                                    <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                                    <span x-text="sess.day_name_arabic + ' (' + sess.session_date + ')'"></span>
                                </div>
                                <span class="font-mono font-bold text-teal-800" x-text="sess.formatted_time_range"></span>
                            </div>

                            <div>
                                <h5 class="font-black text-sm text-slate-900" x-text="sess.session_title"></h5>
                                <p class="text-[11px] text-slate-500 font-medium mt-0.5" x-text="'الأخصائي: ' + sess.specialist_name + ' • ' + sess.room_name"></p>
                            </div>

                            <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs font-bold">
                                <a :href="'https://wa.me/201012345678?text=' + encodeURIComponent('مرحباً، أستفسر عن موعد جلسة ابني يوم ' + sess.day_name_arabic + ' ' + sess.session_date)" target="_blank" class="text-emerald-700 hover:underline flex items-center gap-1">
                                    <i class="fa-brands fa-whatsapp text-emerald-600"></i>
                                    <span>استفسار واتساب</span>
                                </a>

                                <button type="button" @click="openSessionDetail(sess)" class="text-slate-600 hover:text-teal-700">
                                    تفاصيل الموعد &larr;
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
                
                <div x-show="filteredMonthSessions.length === 0" class="p-12 text-center text-slate-400 text-xs font-bold">
                    لا توجد جلسات مسجلة في هذا الشهر.
                </div>
            </div>

        </div>

    </div>

    <!-- ==================== مودال تفاصيل جلسة الطفل لولي الأمر ==================== -->
    <div x-show="detailModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 space-y-6 shadow-2xl border border-slate-100" @click.away="detailModalOpen = false">
            <template x-if="selectedSession">
                <div class="space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-700 flex items-center justify-center text-xl shadow-xs">
                                <i class="fa-solid fa-calendar-day"></i>
                            </div>
                            <div>
                                <h3 class="font-black text-lg text-slate-900" x-text="selectedSession.session_title"></h3>
                                <p class="text-xs text-slate-500 font-mono font-bold" x-text="selectedSession.day_name_arabic + ' ' + selectedSession.session_date + ' • ' + selectedSession.formatted_time_range"></p>
                            </div>
                        </div>
                        <button type="button" @click="detailModalOpen = false" class="p-2 text-slate-400 hover:text-slate-700 rounded-xl">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2 text-xs">
                        <p class="text-slate-800 font-bold flex items-center gap-2">
                            <i class="fa-solid fa-user-doctor text-blue-600"></i>
                            <span x-text="'الأخصائي المعالج: ' + selectedSession.specialist_name"></span>
                        </p>
                        <p class="text-slate-600 font-medium flex items-center gap-2">
                            <i class="fa-solid fa-door-open text-purple-600"></i>
                            <span x-text="'القاعة / الغرفة: ' + selectedSession.room_name"></span>
                        </p>
                        <p class="text-slate-600 font-medium flex items-center gap-2">
                            <i class="fa-solid fa-clipboard-check text-emerald-600"></i>
                            <span x-text="'حالة الجلسة: ' + (selectedSession.attendance_status === 'attended' ? 'تم الحضور' : (selectedSession.attendance_status === 'absent' ? 'غياب' : 'موعد مؤكد'))"></span>
                        </p>
                    </div>

                    <div x-show="selectedSession.notes" class="p-3 bg-amber-50 rounded-2xl border border-amber-200 text-slate-700 text-xs italic">
                        <span class="font-bold text-slate-800">توجيهات الأخصائي:</span> <span x-text="selectedSession.notes"></span>
                    </div>

                    <div class="pt-2 flex flex-col gap-2">
                        <a :href="'https://wa.me/201012345678?text=' + encodeURIComponent('مرحباً، أستفسر عن موعد جلسة طفلي في تمام ' + selectedSession.formatted_time_range + ' يوم ' + selectedSession.session_date)" target="_blank" class="w-full py-3 px-4 rounded-2xl bg-emerald-600 text-white font-bold text-xs shadow-md hover:bg-emerald-700 transition flex items-center justify-center gap-2">
                            <i class="fa-brands fa-whatsapp text-base"></i>
                            <span>تواصل مع الاستقبال على واتساب</span>
                        </a>
                        
                        <form x-show="selectedSession.status !== 'cancelled' && selectedSession.attendance_status !== 'attended'" :action="'{{ route('parent.session.apologize', 'SESSION_ID') }}'.replace('SESSION_ID', selectedSession.id)" method="POST" onsubmit="return confirm('هل أنت متأكد من رغبتك في الاعتذار عن هذه الجلسة؟ سيتم إشعار المركز بذلك.')">
                            @csrf
                            <button type="submit" class="w-full py-3 px-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 font-bold text-xs shadow-xs hover:bg-rose-100 hover:text-rose-800 transition flex items-center justify-center gap-2">
                                <i class="fa-solid fa-hand-paper text-base"></i>
                                <span>اعتذار عن هذه الجلسة</span>
                            </button>
                        </form>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- ==================== تبويب 1: فيديوهات الجلسات والتعليقات التفاعلية ==================== -->
    <div x-show="activeTab === 'videos'" class="space-y-6">
        <div>
            <h3 class="font-black text-lg text-slate-800">فيديوهات الجلسات التوثيقية والتعليقات</h3>
            <p class="text-xs text-slate-400 font-semibold mt-0.5">شاهد مقاطع تطور نطق وحركات طفلك في الجلسة واكتب تعليقك وسيرد عليك الأخصائي</p>
        </div>

        <div class="space-y-6">
            @forelse($sessions as $sess)
            <div class="bg-white rounded-3xl p-6 md:p-8 border border-slate-100 shadow-sm space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-700 flex items-center justify-center text-xl shadow-xs">
                            <i class="fa-solid fa-video"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="font-black text-base text-slate-800">{{ $sess->video_title ?? 'جلسة تدريب وتأهيل' }}</h4>
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-purple-100 text-purple-800">{{ $sess->video_duration ?? '0:45 دقيقة' }}</span>
                            </div>
                            <p class="text-xs text-slate-500 font-medium mt-0.5">
                                تاريخ: <span class="font-mono font-bold">{{ $sess->session_date ? $sess->session_date->format('Y-m-d') : 'اليوم' }}</span> • الأخصائي: <strong class="text-slate-700">{{ $sess->specialist_name }}</strong>
                            </p>
                        </div>
                    </div>
                </div>

                @if($sess->video_path && is_array($sess->video_path))
                    <div class="space-y-3">
                        @foreach($sess->video_path as $vPath)
                            <div class="rounded-3xl overflow-hidden bg-slate-900 border border-slate-800 shadow-md">
                                <video controls class="w-full max-h-[380px] bg-black">
                                    <source src="{{ asset('storage/' . $vPath) }}" type="video/mp4">
                                    متصفحك لا يدعم تشغيل الفيديو.
                                </video>
                            </div>
                        @endforeach
                    </div>
                @elseif($sess->video_path && is_string($sess->video_path))
                    <div class="rounded-3xl overflow-hidden bg-slate-900 border border-slate-800 shadow-md">
                        <video controls class="w-full max-h-[380px] bg-black">
                            <source src="{{ asset('storage/' . $sess->video_path) }}" type="video/mp4">
                            متصفحك لا يدعم تشغيل الفيديو.
                        </video>
                    </div>
                @endif

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1 text-xs">
                    <span class="font-bold text-slate-800">ملاحظات الأخصائي عن أداء الطفل بالجلسة:</span>
                    <p class="text-slate-600 leading-relaxed font-medium">{{ $sess->clinical_notes }}</p>
                </div>

                <!-- تعليقات الفيديو التفاعلية -->
                <div class="space-y-3 pt-2">
                    <span class="text-xs font-bold text-slate-800 block">التعليقات والمحادثة حول هذا الفيديو:</span>
                    <div class="space-y-2">
                        @foreach($sess->comments as $cm)
                        <div class="p-3 rounded-2xl {{ $cm->sender_type === 'specialist' ? 'bg-blue-50 border border-blue-200 text-blue-950 mr-6' : 'bg-slate-100 border border-slate-200 ml-6' }} text-xs space-y-1">
                            <div class="flex items-center justify-between font-bold">
                                <span>{{ $cm->sender_name ?? $cm->parent_name }} ({{ $cm->sender_type === 'specialist' ? 'الأخصائي' : 'ولي الأمر' }})</span>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $cm->created_at ? $cm->created_at->diffForHumans() : '' }}</span>
                            </div>
                            <p class="text-slate-700 font-medium">{{ $cm->comment }}</p>
                        </div>
                        @endforeach
                    </div>

                    <!-- نموذج إضافة تعليق من ولي الأمر -->
                    <form action="{{ route('parent.comment.store') }}" method="POST" class="flex gap-2 pt-2">
                        @csrf
                        <input type="hidden" name="therapy_session_id" value="{{ $sess->id }}">
                        <input type="hidden" name="child_id" value="{{ $child->id }}">
                        <input type="hidden" name="parent_name" value="{{ $child->parent_name }}">
                        
                        <input type="text" name="comment" required placeholder="اكتب تعليقك أو استفسارك للأخصائي حول هذا الفيديو..." class="flex-1 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs outline-none focus:bg-white focus:border-teal-500 font-medium">
                        <button type="submit" class="px-5 py-2.5 rounded-2xl text-white font-bold text-xs shadow-xs hover:opacity-95 transition" style="background-color: #0d9488;">
                            إرسال
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="p-12 text-center bg-white rounded-3xl border border-slate-100 text-slate-400 text-xs font-bold">
                لا توجد فيديوهات جلسات موثقة حتى الآن.
            </div>
            @endforelse
        </div>
    </div>

    <!-- ==================== تبويب 2: الأهداف وخطة طفلي (IEP Goals) ==================== -->
    <div x-show="activeTab === 'goals'" class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="font-black text-lg text-slate-800">الأهداف العلاجية المحددة لطفلك (IEP Goals)</h3>
                <p class="text-xs text-slate-400 font-semibold mt-0.5">تابع نسب إتقان طفلك للمهارات اللغوية والسلوكية والحركية</p>
            </div>
            @if($child)
            <button type="button" @click="$dispatch('open-suggest-goal-modal')" class="px-4 py-2.5 rounded-2xl bg-teal-50 text-teal-700 font-bold text-xs hover:bg-teal-600 hover:text-white border border-teal-100 transition shadow-xs flex items-center justify-center gap-2 shrink-0">
                <i class="fa-solid fa-plus-circle"></i>
                <span>اقتراح هدف جديد</span>
            </button>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($iepGoals as $g)
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-extrabold bg-slate-100 text-slate-700">
                            {{ $g['category'] }}
                        </span>
                        <h4 class="font-extrabold text-sm text-slate-800 mt-2">{{ $g['title'] }}</h4>
                    </div>

                    @if($g['status'] === 'achieved')
                        <span class="px-2.5 py-1 rounded-xl text-xs font-black bg-emerald-100 text-emerald-800 shrink-0">
                            <i class="fa-solid fa-check ml-1"></i> تم الإتقان
                        </span>
                    @else
                        <span class="px-2.5 py-1 rounded-xl text-xs font-black bg-purple-50 text-purple-700 shrink-0">
                            قيد التدريب
                        </span>
                    @endif
                </div>

                <div class="space-y-1.5">
                    <div class="flex justify-between text-xs font-bold">
                        <span class="text-slate-500">نسبة التقدم:</span>
                        <span class="text-teal-700 font-mono">{{ $g['progress'] }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                        <div class="h-2.5 rounded-full bg-teal-600 transition-all duration-500" style="width: {{ $g['progress'] }}%"></div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- ==================== تبويب 3: رسائل وملاحظات للأخصائي والمركز ==================== -->
    <div x-show="activeTab === 'messages'" class="space-y-6">
        <div class="bg-white rounded-3xl p-6 md:p-8 border border-slate-100 shadow-sm space-y-6">
            <h3 class="font-black text-lg text-slate-800">إرسال ملاحظة أو رسالة للأخصائي أو إدارة المركز</h3>
            
            <form action="{{ route('parent.message.store') }}" method="POST" class="space-y-4 text-xs font-medium">
                @csrf
                <input type="hidden" name="child_id" value="{{ $child->id }}">
                <input type="hidden" name="parent_name" value="{{ $child->parent_name }}">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5">الجهة المستلمة:</label>
                        <select name="recipient_type" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold">
                            <option value="specialist">الأخصائي المعالج ({{ $child->main_specialist ?? 'غير محدد' }})</option>
                            <option value="center">إدارة المركز العامة والاستقبال</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5">عنوان الرسالة / الموضوع:</label>
                        <input type="text" name="subject" placeholder="مثال: ملاحظة على نطق حرف الراء بالمنزل..." class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block font-bold text-slate-700 mb-1.5">نص الرسالة أو الاستفسار:</label>
                        <textarea name="message" required rows="3" placeholder="اكتب ملاحظاتك أو أي سلوك لاحظته على طفلك بالمنزل..." class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white leading-relaxed"></textarea>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-7 py-3 rounded-2xl text-white font-black text-xs shadow-md hover:opacity-95 transition" style="background-color: #0d9488;">
                        إرسال الرسالة
                    </button>
                </div>
            </form>
        </div>

        <!-- سجل الرسائل السابقة -->
        <div class="space-y-4">
            @foreach($messages as $msg)
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-3">
                <div class="flex items-center justify-between text-xs pb-2 border-b border-slate-100">
                    <span class="font-black text-slate-800">{{ $msg->subject ?? 'رسالة لولي الأمر' }}</span>
                    <span class="text-slate-400 font-mono">{{ $msg->created_at ? $msg->created_at->format('Y-m-d') : '' }}</span>
                </div>
                <p class="text-xs text-slate-600">{{ $msg->message }}</p>

                @if($msg->doctor_reply)
                <div class="p-3.5 rounded-2xl bg-blue-50 border border-blue-200 text-blue-950 text-xs space-y-1">
                    <span class="font-bold text-blue-900">رد الأخصائي ({{ $msg->replied_by ?? 'الاستشاري' }}):</span>
                    <p class="text-slate-700">{{ $msg->doctor_reply }}</p>
                </div>
                @else
                <p class="text-[11px] text-amber-600 font-bold">بانتظار رد الأخصائي...</p>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    <!-- ==================== تبويب 4: تقييم خدمات المركز ==================== -->
    <div x-show="activeTab === 'rating'" class="space-y-6">
        <div class="bg-white rounded-3xl p-6 md:p-8 border border-slate-100 shadow-sm space-y-6 max-w-2xl">
            <div>
                <h3 class="font-black text-lg text-slate-800">تقييم خدمات المركز والأخصائي المتابع</h3>
                <p class="text-xs text-slate-400 font-semibold mt-0.5">رأيك يهمنا دائماً لتطوير الخدمة ورعاية أبطالنا</p>
            </div>

            <form action="{{ route('parent.rating.store') }}" method="POST" class="space-y-5 text-xs font-medium">
                @csrf
                <input type="hidden" name="child_id" value="{{ $child->id }}">
                <input type="hidden" name="parent_name" value="{{ $child->parent_name }}">
                <input type="hidden" name="specialist_name" value="{{ $child->main_specialist ?? 'غير محدد' }}">
                <input type="hidden" name="rating" :value="starRating">

                <!-- النجوم التفاعلية -->
                <div class="space-y-2">
                    <label class="block font-bold text-slate-700">تقييمك العام للتجربة والرعاية:</label>
                    <div class="flex items-center gap-2 text-2xl text-slate-300">
                        <template x-for="i in 5" :key="i">
                            <button type="button" @click="starRating = i" class="transition hover:scale-110">
                                <i :class="i <= starRating ? 'fa-solid fa-star text-amber-400' : 'fa-regular fa-star text-slate-300'"></i>
                            </button>
                        </template>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1.5">رأيك وملاحظاتك التطويرية:</label>
                    <textarea name="feedback" rows="3" placeholder="اكتب تقييمك ورأيك في تقدم طفلك ومعاملة الفريق..." class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white leading-relaxed font-medium"></textarea>
                </div>

                <button type="submit" class="px-7 py-3 rounded-2xl text-white font-black text-xs shadow-md hover:opacity-95 transition" style="background-color: #0d9488;">
                    إرسال التقييم
                </button>
            </form>
        </div>
    </div>

    <!-- ==================== تبويب 5: التمارين والواجبات المنزلية ==================== -->
    <div x-show="activeTab === 'reports'" class="space-y-6">
        <div>
            <h3 class="font-black text-lg text-slate-800">التمارين والواجبات المنزلية الموصى بها</h3>
            <p class="text-xs text-slate-400 font-semibold mt-0.5">تدريبات يومية ينصح بها الأخصائي لتسريع نطق وتطور الطفل</p>
        </div>

        <div class="space-y-4">
            @forelse($sessions as $s)
            @if($s->home_exercise || $s->homework_file_path)
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-4 relative overflow-hidden">
                @if($s->is_homework_completed)
                <div class="absolute -right-12 top-6 bg-emerald-500 text-white text-[9px] font-black py-1 px-12 transform rotate-45 shadow-sm">
                    تم الإنجاز
                </div>
                @endif
                
                <div class="flex items-center justify-between text-xs pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2 font-bold text-slate-800">
                        <i class="fa-solid fa-house-chimney text-teal-600"></i>
                        <span>تمرين جلسة: {{ $s->session_date ? \Carbon\Carbon::parse($s->session_date)->format('Y-m-d') : '' }}</span>
                    </div>
                    <span class="text-slate-400 font-mono">{{ $s->specialist_name }}</span>
                </div>
                
                @if($s->home_exercise)
                <p class="text-xs text-slate-700 leading-relaxed font-medium">{{ $s->home_exercise }}</p>
                @endif

                @if($s->homework_file_path && is_array($s->homework_file_path))
                <div class="mt-3 flex flex-wrap gap-3">
                    @foreach($s->homework_file_path as $hwPath)
                    <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200/60 inline-block">
                        @php
                            $ext = pathinfo($hwPath, PATHINFO_EXTENSION);
                            $isImage = in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif']);
                            $isVideo = in_array(strtolower($ext), ['mp4', 'mov', 'webm']);
                            $isAudio = in_array(strtolower($ext), ['mp3', 'wav', 'm4a']);
                            $fileUrl = asset('storage/' . $hwPath);
                        @endphp
                        
                        @if($isImage)
                            <a href="{{ $fileUrl }}" target="_blank">
                                <img src="{{ $fileUrl }}" class="w-full max-w-sm rounded-xl object-cover shadow-sm" alt="مرفق الواجب">
                            </a>
                        @elseif($isVideo)
                            <video controls class="w-full max-w-sm rounded-xl shadow-sm">
                                <source src="{{ $fileUrl }}" type="video/{{ $ext === 'mov' ? 'mp4' : $ext }}">
                                متصفحك لا يدعم تشغيل الفيديو.
                            </video>
                        @elseif($isAudio)
                            <audio controls class="w-full max-w-sm">
                                <source src="{{ $fileUrl }}" type="audio/{{ $ext === 'm4a' ? 'mp4' : $ext }}">
                                متصفحك لا يدعم تشغيل الصوت.
                            </audio>
                        @else
                            <a href="{{ $fileUrl }}" target="_blank" class="flex items-center gap-2 text-xs font-bold text-teal-600 hover:underline">
                                <i class="fa-solid fa-download"></i> تحميل المرفق
                            </a>
                        @endif
                    </div>
                    @endforeach
                </div>
                @elseif($s->homework_file_path && is_string($s->homework_file_path))
                <div class="mt-3 p-3 bg-slate-50 rounded-2xl border border-slate-200/60 inline-block">
                    @php
                        $ext = pathinfo($s->homework_file_path, PATHINFO_EXTENSION);
                        $isImage = in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif']);
                        $isVideo = in_array(strtolower($ext), ['mp4', 'mov', 'webm']);
                        $isAudio = in_array(strtolower($ext), ['mp3', 'wav', 'm4a']);
                        $fileUrl = asset('storage/' . $s->homework_file_path);
                    @endphp
                    
                    @if($isImage)
                        <a href="{{ $fileUrl }}" target="_blank">
                            <img src="{{ $fileUrl }}" class="w-full max-w-sm rounded-xl object-cover shadow-sm" alt="مرفق الواجب">
                        </a>
                    @elseif($isVideo)
                        <video controls class="w-full max-w-sm rounded-xl shadow-sm">
                            <source src="{{ $fileUrl }}" type="video/{{ $ext === 'mov' ? 'mp4' : $ext }}">
                            متصفحك لا يدعم تشغيل الفيديو.
                        </video>
                    @elseif($isAudio)
                        <audio controls class="w-full max-w-sm">
                            <source src="{{ $fileUrl }}" type="audio/{{ $ext === 'm4a' ? 'mp4' : $ext }}">
                            متصفحك لا يدعم تشغيل الصوت.
                        </audio>
                    @else
                        <a href="{{ $fileUrl }}" target="_blank" class="flex items-center gap-2 text-xs font-bold text-teal-600 hover:underline">
                            <i class="fa-solid fa-download"></i> تحميل مرفق الواجب المنزلي
                        </a>
                    @endif
                </div>
                @endif

                @if(!$s->is_homework_completed)
                <div class="pt-3">
                    <form action="{{ route('homework.complete', $s->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-5 py-2.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 hover:bg-emerald-600 hover:text-white font-bold text-xs shadow-xs transition flex items-center justify-center gap-2 w-full sm:w-auto">
                            <i class="fa-solid fa-check-circle text-sm"></i>
                            <span>تم التدريب بالمنزل بنجاح ✅</span>
                        </button>
                    </form>
                </div>
                @else
                <div class="pt-3">
                    <div class="px-5 py-2.5 rounded-2xl bg-slate-50 text-slate-500 font-bold text-xs flex items-center justify-center gap-2 w-full sm:w-auto border border-slate-200">
                        <i class="fa-solid fa-check-double text-emerald-500"></i>
                        <span>تم توثيق إنجاز التدريب</span>
                    </div>
                </div>
                @endif
            </div>
            @endif
            @empty
            <div class="p-12 text-center bg-white rounded-3xl border border-slate-100 text-slate-400 text-xs font-bold">
                لا توجد واجبات منزلية مسجلة حتى الآن.
            </div>
            @endforelse
        </div>
    </div>

    <!-- ==================== تبويب 6: مكتبة الملفات والفيديوهات ==================== -->
    <div x-show="activeTab === 'media'" class="space-y-6">
        <div>
            <h3 class="font-black text-lg text-slate-800">مكتبة الملفات والفيديوهات</h3>
            <p class="text-xs text-slate-400 font-semibold mt-0.5">شاهد الملفات والفيديوهات التعليمية المرفوعة من الأخصائي وتفاعل معها</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @forelse($childMedia as $item)
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden flex flex-col h-full" x-data="{ commentOpen: false }">
                
                <div class="h-48 bg-slate-900 relative flex items-center justify-center overflow-hidden">
                    @if($item->file_type === 'video')
                        <video src="{{ asset('storage/' . $item->file_path) }}" controls class="w-full h-full object-cover"></video>
                    @elseif($item->file_type === 'image')
                        <img src="{{ asset('storage/' . $item->file_path) }}" class="w-full h-full object-cover">
                    @else
                        <i class="fa-solid fa-file-pdf text-6xl text-slate-700"></i>
                    @endif
                    <div class="absolute top-3 left-3 bg-white/90 px-2 py-1 rounded-lg text-[10px] font-black text-slate-800 shadow-sm backdrop-blur-md">
                        {{ $item->file_size }}
                    </div>
                </div>

                <div class="p-5 flex-1 flex flex-col">
                    <h3 class="font-black text-sm text-slate-800 line-clamp-1">{{ $item->title }}</h3>
                    <div class="flex items-center gap-2 mt-1 text-[10px] text-slate-400 font-medium">
                        <i class="fa-solid fa-user-pen"></i>
                        <span>بواسطة: {{ $item->uploader->name ?? '' }}</span>
                        <span class="mx-1">•</span>
                        <span>{{ $item->created_at->diffForHumans() }}</span>
                    </div>

                    @if($item->description)
                    <p class="text-xs text-slate-600 font-medium mt-3 bg-slate-50 p-3 rounded-2xl line-clamp-2">
                        {{ $item->description }}
                    </p>
                    @endif

                    <div class="mt-auto pt-4 flex gap-2">
                        <button @click="commentOpen = !commentOpen" class="flex-1 px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-bold hover:bg-slate-200 transition flex items-center justify-center gap-2">
                            <i class="fa-regular fa-comment-dots"></i>
                            <span>التعليقات ({{ $item->comments->count() }})</span>
                        </button>
                        @if($item->file_type === 'document')
                        <a href="{{ asset('storage/' . $item->file_path) }}" target="_blank" class="px-4 py-2 bg-indigo-50 text-indigo-700 rounded-xl text-xs font-bold hover:bg-indigo-100 transition flex items-center justify-center gap-2">
                            <i class="fa-solid fa-download"></i>
                        </a>
                        @endif
                    </div>
                </div>

                <div x-show="commentOpen" x-collapse class="border-t border-slate-100 bg-slate-50">
                    <div class="p-5 space-y-4">
                        <div class="space-y-3 max-h-48 overflow-y-auto custom-scrollbar pr-1">
                            @forelse($item->comments as $comment)
                            <div class="bg-white p-3 rounded-2xl shadow-sm border border-slate-100">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-[10px] font-black {{ $comment->user->role === 'parent' ? 'text-purple-600' : 'text-blue-600' }}">
                                        {{ $comment->user->name }}
                                    </span>
                                    <span class="text-[9px] text-slate-400">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-xs text-slate-700 font-medium leading-relaxed">{{ $comment->comment }}</p>
                            </div>
                            @empty
                            <p class="text-[11px] text-center text-slate-400 font-semibold py-2">لا توجد تعليقات بعد.</p>
                            @endforelse
                        </div>

                        <form action="{{ route('media.comment', $item->id) }}" method="POST" class="flex gap-2 relative">
                            @csrf
                            <input type="text" name="comment" required placeholder="اكتب تعليقاً..." class="w-full pl-10 pr-3 py-2 bg-white border border-slate-200 rounded-xl text-xs outline-none focus:border-purple-300">
                            <button type="submit" class="absolute left-1 top-1 bottom-1 w-8 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition flex items-center justify-center">
                                <i class="fa-solid fa-paper-plane text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full py-16 text-center bg-white rounded-3xl border border-dashed border-slate-200">
                <i class="fa-solid fa-photo-film text-4xl text-slate-300 mb-3"></i>
                <h3 class="font-bold text-slate-500">لا توجد ملفات أو فيديوهات مرفوعة حتى الآن</h3>
            </div>
            @endforelse
        </div>
    </div>

    <!-- تم حذف نافذة بطاقة الحضور القديمة -->

</div>

@php
    $todayCancelled = $calendarEvents->first(function($s) {
        return $s['session_date'] === date('Y-m-d') && $s['status'] === 'cancelled';
    });
@endphp

@push('scripts')
@if($todayCancelled)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'info',
            title: 'تنبيه: اعتذار عن الجلسات لليوم',
            html: '<div style="font-size: 1.1em; color: #555;">نعتذر لإبلاغكم بأنه تم إلغاء الجلسات المجدولة لطفلكم لليوم نظرًا لظرف طارئ واعتذار الأخصائي.<br><br><small>سيتم التواصل معكم لاحقاً لتعويض الجلسة.</small></div>',
            confirmButtonText: 'حسناً، شكراً للتوضيح',
            confirmButtonColor: '#0d9488',
            backdrop: 'rgba(0,0,0,0.6)'
        });
    });
</script>
@endif

<script>
function parentPortalCalendarApp() {
    return {
        activeTab: '{{ request('tab', 'calendar') }}',
        starRating: 5,
        selectedChildId: '{{ $child->id }}',
        viewMode: 'calendar',
        currentYear: new Date().getFullYear(),
        currentMonth: new Date().getMonth(),
        monthNames: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'],
        dayNames: ['السبت', 'الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة'],
        
        allSessions: {!! json_encode($calendarEvents) !!},

        blankDays: [],
        daysInMonth: [],
        detailModalOpen: false,
        selectedSession: null,

        init() {
            this.calculateCalendar();
        },

        calculateCalendar() {
            let firstDay = new Date(this.currentYear, this.currentMonth, 1).getDay();
            let dayOffset = (firstDay + 1) % 7;
            this.blankDays = Array.from({length: dayOffset}, (_, i) => i + 1);

            let totalDays = new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
            this.daysInMonth = Array.from({length: totalDays}, (_, i) => i + 1);
        },

        prevMonth() {
            if (this.currentMonth === 0) {
                this.currentMonth = 11;
                this.currentYear--;
            } else {
                this.currentMonth--;
            }
            this.calculateCalendar();
        },

        nextMonth() {
            if (this.currentMonth === 11) {
                this.currentMonth = 0;
                this.currentYear++;
            } else {
                this.currentMonth++;
            }
            this.calculateCalendar();
        },

        goToToday() {
            this.currentYear = new Date().getFullYear();
            this.currentMonth = new Date().getMonth();
            this.calculateCalendar();
        },

        formatDate(day) {
            let m = String(this.currentMonth + 1).padStart(2, '0');
            let d = String(day).padStart(2, '0');
            return `${this.currentYear}-${m}-${d}`;
        },

        isToday(day) {
            let today = new Date();
            return today.getFullYear() === this.currentYear &&
                   today.getMonth() === this.currentMonth &&
                   today.getDate() === day;
        },

        getSessionsForDay(day) {
            let dateStr = this.formatDate(day);
            return this.allSessions.filter(s => s.session_date === dateStr);
        },

        get filteredMonthSessions() {
            let mStr = String(this.currentMonth + 1).padStart(2, '0');
            let prefix = `${this.currentYear}-${mStr}`;
            return this.allSessions.filter(s => s.session_date.startsWith(prefix));
        },

        handleDayClick(day) {
            let sessions = this.getSessionsForDay(day);
            if (sessions.length > 0) {
                this.openSessionDetail(sessions[0]);
            }
        },

        openSessionDetail(sess) {
            this.selectedSession = sess;
            this.detailModalOpen = true;
        },

        switchChild(id) {
            window.location.href = '/parent-portal?child_id=' + id;
        }
    }
}
</script>

@if($child)
<form id="suggest-goal-form" action="{{ route('parent.message.store') }}" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="child_id" value="{{ $child->id }}">
    <input type="hidden" name="recipient_type" value="specialist">
    <input type="hidden" name="parent_name" value="ولي أمر {{ $child->name }}">
    <input type="hidden" name="subject" value="اقتراح هدف علاجي جديد">
    <textarea id="suggest-goal-text" name="message"></textarea>
</form>

<script>
    document.addEventListener('open-suggest-goal-modal', function() {
        Swal.fire({
            title: 'اقتراح هدف علاجي جديد',
            text: 'اكتب الهدف الذي تود إضافته لخطة طفلك وسيقوم الأخصائي بمراجعته واعتماده وإبلاغك.',
            input: 'textarea',
            inputPlaceholder: 'مثال: أريد من طفلي أن يتعلم كيف يعبر عن جوعه بكلمات واضحة...',
            inputAttributes: {
                'aria-label': 'Type your message here'
            },
            showCancelButton: true,
            confirmButtonText: 'إرسال للأخصائي',
            cancelButtonText: 'إلغاء',
            confirmButtonColor: '#0d9488',
            showLoaderOnConfirm: true,
            customClass: {
                popup: 'rounded-3xl',
                title: 'text-lg font-black text-slate-800 font-cairo',
                htmlContainer: 'text-xs text-slate-500 font-medium font-cairo',
                input: 'font-cairo text-sm p-4 rounded-2xl bg-slate-50 border border-slate-200 focus:bg-white',
                confirmButton: 'rounded-xl px-6 py-2.5 font-bold text-xs',
                cancelButton: 'rounded-xl px-6 py-2.5 font-bold text-xs'
            },
            preConfirm: (text) => {
                if (!text) {
                    Swal.showValidationMessage('يرجى كتابة الهدف أولاً')
                }
                return text;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('suggest-goal-text').value = result.value;
                document.getElementById('suggest-goal-form').submit();
            }
        });
    });
</script>
@endif

@endpush
@endsection








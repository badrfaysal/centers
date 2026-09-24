@extends('layouts.app')

@section('title', 'لوحة التحكم الرئيسية')

@section('content')
<div class="space-y-8" x-data="{ 
    selectedFilter: 'all', 
    reportModalOpen: false, 
    videoModalOpen: false,
    selectedChild: null 
}">

    <!-- ==================== الترويسة والأزرار التفاعلية ==================== -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></span>
                <h2 class="text-2xl font-black text-slate-800">مرحباً بك في لوحة تحكم المركز </h2>
            </div>
            <p class="text-sm text-slate-500 font-medium mt-1">
                اليوم: <span class="font-bold text-slate-700">{{ now()->translatedFormat('l، d F Y') }}</span> | نظام الجدولة وغرف التأهيل تعمل بانتظام.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('children.create') }}" class="px-4 py-2.5 bg-white border border-slate-200/90 rounded-2xl text-xs font-bold text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition shadow-xs flex items-center gap-2">
                <i class="fa-solid fa-user-plus text-emerald-600 text-sm"></i>
                <span>تسجيل طفل جديد</span>
            </a>

            <a href="{{ route('finances.create') }}" class="px-4 py-2.5 bg-white border border-slate-200/90 rounded-2xl text-xs font-bold text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition shadow-xs flex items-center gap-2">
                <i class="fa-solid fa-file-invoice-dollar text-teal-600 text-sm"></i>
                <span>سند قبض / باقة جديدة</span>
            </a>

            <a href="{{ route('reports.index') }}" class="px-4 py-2.5 bg-brand-50 border border-brand-200 rounded-2xl text-xs font-bold text-brand-700 hover:bg-brand-100 transition shadow-xs flex items-center gap-2">
                <i class="fa-solid fa-file-export text-brand-600 text-sm"></i>
                <span>تقرير اليوم الشامل (PDF)</span>
            </a>
        </div>
    </div>

    <!-- ==================== 1. بطاقات المؤشرات الإحصائية (KPI Cards) ==================== -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- كارت جلسات اليوم -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm relative overflow-hidden group hover:border-brand-300 transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">جلسات اليوم</p>
                    <h3 class="text-3xl font-black text-slate-800 mt-2">{{ $stats['today_sessions_total'] }}</h3>
                </div>
                <div class="w-13 h-13 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl group-hover:scale-110 transition">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center gap-2 text-xs font-bold flex-wrap">
                <span class="text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg">{{ $stats['today_sessions_done'] }} اكتملت</span>
                <span class="text-amber-700 bg-amber-50 px-2.5 py-1 rounded-lg">{{ $stats['today_sessions_pending'] }} متبقية</span>
                <span class="text-rose-700 bg-rose-50 px-2.5 py-1 rounded-lg">{{ $stats['today_sessions_absent'] }} غياب</span>
            </div>
        </div>

        <!-- كارت الأطفال النشطين -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm relative overflow-hidden group hover:border-emerald-300 transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">الأطفال بالخطة التأهيلية</p>
                    <h3 class="text-3xl font-black text-slate-800 mt-2">{{ $stats['active_children_count'] }}</h3>
                </div>
                <div class="w-13 h-13 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl group-hover:scale-110 transition">
                    <i class="fa-solid fa-children"></i>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center gap-1.5 text-xs text-emerald-600 font-bold">
                <i class="fa-solid fa-arrow-trend-up"></i>
                <span>+{{ $stats['new_children_month'] }} طفل تم تسجيلهم هذا الشهر</span>
            </div>
        </div>

        <!-- كارت الأخصائيين المتواجدين -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm relative overflow-hidden group hover:border-purple-300 transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">الأخصائيين المتواجدين</p>
                    <h3 class="text-3xl font-black text-slate-800 mt-2">
                        {{ $stats['active_specialists'] }} 
                        <span class="text-base text-slate-400 font-normal">/ {{ $stats['total_specialists'] }}</span>
                    </h3>
                </div>
                <div class="w-13 h-13 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-2xl group-hover:scale-110 transition">
                    <i class="fa-solid fa-user-doctor"></i>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center gap-2 text-xs text-purple-700 font-bold">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                <span>{{ $stats['today_sessions_active'] }} جلسات مباشرة الآن</span>
            </div>
        </div>

        <!-- كارت الخزينة والإيراد اليومي -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm relative overflow-hidden group hover:border-teal-300 transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">إيراد الخزينة اليوم</p>
                    <h3 class="text-3xl font-black text-teal-700 mt-2">{{ number_format($stats['today_revenue']) }} <span class="text-sm font-bold text-slate-400">ج.م</span></h3>
                </div>
                <div class="w-13 h-13 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-2xl group-hover:scale-110 transition">
                    <i class="fa-solid fa-cash-register"></i>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500 font-semibold">
                <span>إجمالي الشهر: <strong>{{ number_format($stats['month_revenue']) }} ج.م</strong></span>
                <span class="text-emerald-600 font-bold">مستقر</span>
            </div>
        </div>

    </div>
    <!-- ==================== 2. رادار غرف التأهيل الحية (Live Rooms Dispatcher) ==================== -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <span class="flex h-3 w-3 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-rose-500"></span>
                </span>
                <h3 class="font-extrabold text-lg text-slate-800">حالة غرف التأهيل والجلسات الجارية الآن (Live Rooms)</h3>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs text-slate-400 font-bold hidden sm:inline">تحديث فوري للغرف والأخصائيين</span>
                <a href="{{ route('settings.index') }}" onclick="localStorage.setItem('settingsTab', 'dropdowns')" class="px-3 py-1.5 bg-brand-50 text-brand-700 rounded-xl text-xs font-bold hover:bg-brand-100 transition shadow-xs flex items-center gap-1.5 border border-brand-200">
                    <i class="fa-solid fa-plus text-[10px]"></i>
                    إضافة قاعة جديدة
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($rooms as $room)
            <div class="bg-white rounded-3xl p-5 border {{ $room['status'] === 'busy' ? 'border-amber-200 shadow-amber-500/5 bg-gradient-to-b from-amber-50/50 via-white to-white' : 'border-slate-200/80' }} shadow-sm transition hover:shadow-md">
                
                <div class="flex items-center justify-between">
                    <h4 class="font-extrabold text-sm text-slate-800">{{ $room['name'] }}</h4>
                    @if($room['status'] === 'busy')
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black bg-amber-100 text-amber-800 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-600 animate-ping"></span> جارية الآن
                        </span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black bg-emerald-100 text-emerald-800">
                            متاحة وشاغرة
                        </span>
                    @endif
                </div>
                <p class="text-[11px] text-slate-400 font-semibold mt-0.5">{{ $room['category'] }}</p>

                @if($room['status'] === 'busy')
                <div class="mt-4 pt-3 border-t border-amber-100 space-y-3">
                    <div class="flex items-center gap-3">
                        <img src="{{ $room['child']['avatar'] }}" alt="avatar" class="w-10 h-10 rounded-2xl bg-amber-100/60 p-1">
                        <div class="min-w-0">
                            <p class="text-xs font-black text-slate-800 truncate">{{ $room['child']['name'] }}</p>
                            <p class="text-[10px] text-slate-500 font-medium truncate">{{ $room['specialist'] }}</p>
                        </div>
                    </div>

                    <!-- شريط التقدم الزمني للجلسة -->
                    <div>
                        <div class="flex justify-between text-[11px] font-bold text-amber-800 mb-1">
                            <span><i class="fa-regular fa-clock ml-1"></i> متبقي {{ $room['remaining_minutes'] }} دقيقة</span>
                            <span>{{ $room['end_time'] }}</span>
                        </div>
                        <div class="w-full bg-amber-100 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-amber-500 h-1.5 rounded-full" style="width: {{ $room['progress_percentage'] }}%"></div>
                        </div>
                    </div>
                </div>
                @else
                <div class="mt-4 pt-3 border-t border-slate-100 text-xs text-slate-500 space-y-2">
                    <div class="flex items-center gap-1.5 text-emerald-700 font-bold mb-1">
                        <i class="fa-regular fa-calendar-plus"></i>
                        <span>الموعد القادم:</span>
                    </div>
                    @if($room['next_session_time'] !== 'لا يوجد موعد قادم اليوم')
                        <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100 space-y-1.5">
                            <div class="flex items-center justify-between font-bold">
                                <span class="text-slate-800">{{ $room['next_session_time'] }}</span>
                                <span class="text-[10px] text-brand-600 bg-brand-50 px-1.5 py-0.5 rounded">{{ $room['next_session_title'] }}</span>
                            </div>
                            <div class="flex items-center justify-between text-[10px] text-slate-500 font-semibold border-t border-slate-200/60 pt-1.5 mt-1.5">
                                <span class="flex items-center gap-1"><i class="fa-solid fa-user-doctor text-slate-400"></i> {{ $room['specialist'] }}</span>
                                <span class="flex items-center gap-1 truncate max-w-[80px]" title="{{ $room['child_name'] }}"><i class="fa-solid fa-child text-slate-400"></i> {{ $room['child_name'] }}</span>
                            </div>
                        </div>
                    @else
                        <p class="text-[11px] text-slate-500 bg-slate-50 p-2 rounded-xl border border-slate-100 text-center font-semibold">لا يوجد موعد قادم اليوم</p>
                    @endif
                </div>
                @endif

            </div>
            @endforeach
        </div>
    </div>
    <!-- ==================== 3. جدول جلسات اليوم وملاحظات أولياء الأمور ==================== -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- جدول جلسات اليوم (يمتد لعمودين) -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-100 shadow-sm p-6 space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h3 class="font-extrabold text-lg text-slate-800">جدول جلسات اليوم</h3>
                    <p class="text-xs text-slate-400 font-semibold">متابعة الحضور، تسجيل التقارير الطبية، ورفع الفيديوهات للأهل</p>
                </div>

                <!-- أزرار التصفية السريعة -->
                <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-2xl text-xs font-bold">
                    <button @click="selectedFilter = 'all'" :class="selectedFilter === 'all' ? 'bg-white text-slate-800 shadow-xs' : 'text-slate-500 hover:text-slate-700'" class="px-3 py-1.5 rounded-xl transition">الكل</button>
                    <button @click="selectedFilter = 'speech'" :class="selectedFilter === 'speech' ? 'bg-white text-blue-700 shadow-xs' : 'text-slate-500 hover:text-slate-700'" class="px-3 py-1.5 rounded-xl transition">تخاطب</button>
                    <button @click="selectedFilter = 'sensory'" :class="selectedFilter === 'sensory' ? 'bg-white text-purple-700 shadow-xs' : 'text-slate-500 hover:text-slate-700'" class="px-3 py-1.5 rounded-xl transition">تكامل حسي</button>
                    <button @click="selectedFilter = 'skills'" :class="selectedFilter === 'skills' ? 'bg-white text-emerald-700 shadow-xs' : 'text-slate-500 hover:text-slate-700'" class="px-3 py-1.5 rounded-xl transition">مهارات</button>
                </div>
            </div>

            <!-- الجدول -->
            <div class="overflow-x-auto">
                <table class="w-full text-right text-sm">
                    <thead>
                        <tr class="text-[11px] text-slate-400 font-extrabold border-b border-slate-100 pb-3">
                            <th class="py-3 px-3">الطفل والتشخيص</th>
                            <th class="py-3 px-3">الأخصائي والغرفة</th>
                            <th class="py-3 px-3">الموعد</th>
                            <th class="py-3 px-3">الحالة</th>
                            <th class="py-3 px-3 text-center">التقرير والفيديو</th>
                            <th class="py-3 px-3 text-left">إجراءات سريعة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @foreach($todaySessions as $session)
                        <tr class="hover:bg-slate-50/80 transition group">
                            
                            <!-- الطفل -->
                            <td class="py-4 px-3">
                                <div class="font-extrabold text-slate-800 flex items-center gap-2">
                                    <span>{{ $session['child_name'] }}</span>
                                    <span class="font-mono bg-slate-100 text-slate-600 text-[10px] px-1.5 py-0.5 rounded-md font-bold">{{ $session['child_code'] }}</span>
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5 truncate max-w-xs" title="{{ $session['diagnosis'] }}">
                                    {{ $session['diagnosis'] }}
                                </div>
                            </td>

                            <!-- الأخصائي -->
                            <td class="py-4 px-3">
                                <div class="text-slate-800 font-bold text-xs">{{ $session['specialist_name'] }}</div>
                                <div class="text-[11px] text-brand-600 font-semibold mt-0.5">{{ $session['room_name'] }}</div>
                            </td>

                            <!-- الموعد -->
                            <td class="py-4 px-3 font-mono text-xs text-slate-600 whitespace-nowrap">
                                {{ $session['time_slot'] }}
                            </td>

                            <!-- الحالة -->
                            <td class="py-4 px-3 whitespace-nowrap">
                                @if($session['status'] === 'completed')
                                    <span class="px-2.5 py-1 rounded-xl text-xs font-black bg-emerald-100 text-emerald-800 inline-flex items-center gap-1.5">
                                        <i class="fa-solid fa-check text-[10px]"></i> تمت بنجاح
                                    </span>
                                @elseif($session['status'] === 'in_progress')
                                    <span class="px-2.5 py-1 rounded-xl text-xs font-black bg-amber-100 text-amber-800 inline-flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-600 animate-ping"></span> جارية الآن
                                    </span>
                                @elseif($session['status'] === 'scheduled')
                                    <span class="px-2.5 py-1 rounded-xl text-xs font-black bg-blue-50 text-blue-700">
                                        مجدولة
                                    </span>
                                @elseif($session['status'] === 'absent')
                                    <span class="px-2.5 py-1 rounded-xl text-xs font-black bg-rose-100 text-rose-800">
                                        غياب بعذر
                                    </span>
                                @endif
                            </td>

                            <!-- التقرير والفيديو -->
                            <td class="py-4 px-3 text-center">
                                <div class="flex items-center justify-center gap-2.5">
                                    @if($session['has_report'])
                                        <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs shadow-xs" title="تم تسجيل تقرير الجلسة">
                                            <i class="fa-solid fa-file-circle-check text-sm"></i>
                                        </span>
                                    @else
                                        <a href="{{ route('doctor.sessions.create', ['child_id' => $session['child_id'] ?? 1]) }}" class="w-8 h-8 rounded-xl bg-slate-100 text-slate-400 hover:bg-brand-50 hover:text-brand-600 flex items-center justify-center text-xs transition" title="كتابة تقرير الجلسة">
                                            <i class="fa-regular fa-file-lines text-sm"></i>
                                        </a>
                                    @endif

                                    @if($session['has_video'])
                                        <span class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xs shadow-xs" title="تم رفع فيديو لولي الأمر ({{ $session['video_duration'] }})">
                                            <i class="fa-solid fa-video text-sm"></i>
                                        </span>
                                    @else
                                        <a href="{{ route('doctor.sessions.create', ['child_id' => $session['child_id'] ?? 1]) }}" class="w-8 h-8 rounded-xl bg-slate-100 text-slate-400 hover:bg-purple-50 hover:text-purple-600 flex items-center justify-center text-xs transition" title="رفع فيديو للطفل في الجلسة">
                                            <i class="fa-solid fa-video-slash text-sm"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>

                            <!-- إجراءات -->
                            <td class="py-4 px-3 text-left">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('children.show', $session['child_id'] ?? 1) }}" class="px-2.5 py-1 rounded-xl bg-slate-100 hover:bg-brand-600 hover:text-white text-slate-600 text-xs font-bold transition">
                                        تفاصيل
                                    </a>
                                    <a href="https://wa.me/2{{ preg_replace('/[^0-9]/', '', $session['parent_phone'] ?? '') }}" target="_blank" class="w-7 h-7 rounded-xl bg-green-50 text-green-600 hover:bg-green-600 hover:text-white flex items-center justify-center text-xs transition" title="تواصل واتساب مع الأب">
                                        <i class="fa-brands fa-whatsapp text-sm"></i>
                                    </a>
                                </div>
                            </td>

                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- شريط معلومات الباقة -->
            <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400 font-semibold">
                <span>عرض {{ count($todaySessions) }} من أصل {{ $stats['today_sessions_total'] }} جلسة مجدولة لليوم</span>
                <a href="{{ route('calendar.index') }}" class="text-brand-600 font-bold hover:underline">عرض الجدول بالكامل مع الأخصائيين ←</a>
            </div>
        </div>
        <!-- العمود الجانبي: ملاحظات الأهل وتنبيهات الباقات والواتساب -->
        <div class="space-y-6">

            <!-- 1. حائط ملاحظات أولياء الأمور المباشرة -->
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-sm">
                            <i class="fa-solid fa-comments"></i>
                        </div>
                        <h3 class="font-extrabold text-base text-slate-800">ملاحظات أولياء الأمور</h3>
                    </div>
                    <span class="text-xs bg-purple-50 text-purple-700 px-2.5 py-0.5 rounded-full font-bold">{{ $stats['unread_parent_notes'] }} جديدة</span>
                </div>

                <div class="space-y-3">
                    @foreach($parentNotes as $note)
                    <div class="p-4 rounded-2xl bg-slate-50/80 border border-slate-100 space-y-2 hover:border-purple-200 transition">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-1.5">
                                <span class="text-xs font-black text-slate-800">{{ $note['parent_name'] }}</span>
                                <span class="text-[10px] text-slate-400">({{ $note['parent_type'] }})</span>
                            </div>
                            <span class="text-[10px] text-slate-400 font-semibold">{{ $note['time_ago'] }}</span>
                        </div>
                        
                        <p class="text-xs text-slate-600 leading-relaxed font-medium bg-white p-2.5 rounded-xl border border-slate-100">
                            {{ $note['note'] }}
                        </p>
                        
                        <div class="pt-1 flex items-center justify-between text-[11px]">
                            <span class="text-slate-400">الدكتور المتابع: <strong class="text-slate-700">{{ $note['specialist_name'] }}</strong></span>
                            <a href="{{ route('doctor.portal') }}" class="text-brand-600 font-bold hover:underline flex items-center gap-1">
                                <span>رد الدكتور</span>
                                <i class="fa-solid fa-reply text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- 2. تنبيه تجديد الباقات (رصيد منخفض) -->
            <div class="bg-gradient-to-br from-amber-50/90 to-orange-50/60 border border-amber-200/80 rounded-3xl p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 text-amber-900 font-black text-sm">
                        <i class="fa-solid fa-triangle-exclamation text-amber-600 text-base"></i>
                        <span>تنبيه تجديد باقات الجلسات</span>
                    </div>
                    <span class="text-[11px] bg-amber-200/80 text-amber-900 px-2 py-0.5 rounded-full font-bold">{{ count($lowBalancePackages) }} أطفال</span>
                </div>

                <div class="space-y-2.5">
                    @foreach($lowBalancePackages as $item)
                    <div class="bg-white p-3.5 rounded-2xl border border-amber-100 shadow-xs space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-black text-slate-800">{{ $item['child_name'] }}</span>
                            <span class="text-[11px] font-black text-rose-600 bg-rose-50 px-2 py-0.5 rounded-md">متبقي {{ $item['remaining_sessions'] }} من {{ $item['total_sessions'] }}</span>
                        </div>
                        <p class="text-[11px] text-slate-500 truncate">{{ $item['package_name'] }}</p>
                        <div class="pt-1 border-t border-slate-100 flex items-center justify-between text-[11px]">
                            <span class="text-slate-400 font-mono">{{ $item['parent_phone'] }}</span>
                            <a href="https://wa.me/2{{ $item['parent_phone'] }}" target="_blank" class="text-emerald-600 font-bold hover:underline flex items-center gap-1">
                                <i class="fa-brands fa-whatsapp"></i>
                                <span>إرسال رابط التجديد</span>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- 3. نشاط رسائل الواتساب الآلية اليوم -->
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-5 space-y-3">
                <div class="flex items-center justify-between text-xs font-black text-slate-800">
                    <span class="flex items-center gap-1.5 text-emerald-700">
                        <i class="fa-brands fa-whatsapp text-base"></i>
                        <span>رسائل تذكير الواتساب اليوم</span>
                    </span>
                    <span class="text-slate-400">{{ $whatsappStats['sent_reminders'] }} رسالة</span>
                </div>
                <div class="grid grid-cols-3 gap-2 text-center text-xs">
                    <div class="bg-emerald-50 p-2 rounded-xl text-emerald-800 font-bold">
                        <p class="text-base font-black">{{ $whatsappStats['confirmed_by_parent'] }}</p>
                        <p class="text-[10px] text-emerald-600">أكدوا الحضور</p>
                    </div>
                    <div class="bg-amber-50 p-2 rounded-xl text-amber-800 font-bold">
                        <p class="text-base font-black">{{ $whatsappStats['rescheduled'] }}</p>
                        <p class="text-[10px] text-amber-600">طلبوا تأجيل</p>
                    </div>
                    <div class="bg-slate-100 p-2 rounded-xl text-slate-700 font-bold">
                        <p class="text-base font-black">{{ $whatsappStats['waiting_reply'] }}</p>
                        <p class="text-[10px] text-slate-500">في الانتظار</p>
                    </div>
                </div>
            </div>

        </div>

    </div>
    <!-- ==================== نافذة تفاعلية: كتابة تقرير الجلسة (Modal) ==================== -->
    <div x-show="reportModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.outside="reportModalOpen = false" class="bg-white rounded-3xl shadow-2xl border border-slate-100 max-w-lg w-full p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center">
                        <i class="fa-solid fa-file-signature"></i>
                    </div>
                    <div>
                        <h4 class="font-black text-base text-slate-800">تسجيل تقرير الجلسة السريع</h4>
                        <p class="text-xs text-brand-600 font-bold" x-text="'الطفل: ' + selectedChild"></p>
                    </div>
                </div>
                <button @click="reportModalOpen = false" class="text-slate-400 hover:text-slate-600 text-lg">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="space-y-3 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">حالة واستجابة الطفل في الجلسة:</label>
                    <div class="grid grid-cols-3 gap-2 font-bold">
                        <label class="flex items-center justify-center gap-1.5 p-2 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 cursor-pointer">
                            <input type="radio" name="mood" checked class="accent-emerald-600">
                            <span>ممتاز ومتعاون</span>
                        </label>
                        <label class="flex items-center justify-center gap-1.5 p-2 rounded-xl bg-amber-50 text-amber-800 border border-amber-200 cursor-pointer">
                            <input type="radio" name="mood" class="accent-amber-600">
                            <span>متوسط / مشتت</span>
                        </label>
                        <label class="flex items-center justify-center gap-1.5 p-2 rounded-xl bg-rose-50 text-rose-800 border border-rose-200 cursor-pointer">
                            <input type="radio" name="mood" class="accent-rose-600">
                            <span>مقاوم / غاضب</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">ملاحظة لولي الأمر (تظهر في تطبيق الأهل):</label>
                    <textarea rows="2" placeholder="اكتب ما تم إنجازه اليوم وما تم التدريب عليه..." class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-brand-500 text-xs font-medium"></textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">تمرين منزلي مطلوب من ولي الأمر:</label>
                    <input type="text" placeholder="مثال: تكرار نطق صوت حرف الكاف 10 مرات قبل النوم بالمرآة" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-brand-500 text-xs font-medium">
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button @click="reportModalOpen = false" class="px-4 py-2 rounded-xl text-slate-500 font-bold hover:bg-slate-100 text-xs">إلغاء</button>
                <button @click="reportModalOpen = false; alert('تم حفظ التقرير وإرسال إشعار لولي الأمر بنجاح ')" class="px-5 py-2 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs shadow-md shadow-brand-600/30">حفظ وإرسال للأهل</button>
            </div>
        </div>
    </div>

    <!-- ==================== نافذة تفاعلية: رفع فيديو للطفل (Video Modal) ==================== -->
    <div x-show="videoModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.outside="videoModalOpen = false" class="bg-white rounded-3xl shadow-2xl border border-slate-100 max-w-md w-full p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                        <i class="fa-solid fa-video"></i>
                    </div>
                    <div>
                        <h4 class="font-black text-base text-slate-800">رفع فيديو الجلسة لولي الأمر</h4>
                        <p class="text-xs text-purple-600 font-bold" x-text="'الطفل: ' + selectedChild"></p>
                    </div>
                </div>
                <button @click="videoModalOpen = false" class="text-slate-400 hover:text-slate-600 text-lg">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="space-y-3 text-xs">
                <div class="border-2 border-dashed border-purple-200 rounded-2xl p-6 text-center bg-purple-50/30 hover:bg-purple-50 transition cursor-pointer">
                    <i class="fa-solid fa-cloud-arrow-up text-3xl text-purple-500 mb-2"></i>
                    <p class="font-bold text-slate-700">اسحب مقطع الفيديو هنا أو اضغط للاختيار</p>
                    <p class="text-[10px] text-slate-400 mt-1">يدعم MP4, MOV (مقاطع قصيرة 10-45 ثانية لتسليط الضوء على الإنجاز)</p>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">عنوان الفيديو أو وصف الإنجاز:</label>
                    <input type="text" placeholder="مثال: نطق صوت /ك/ بمساعدة بصرية بنجاح" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-purple-500 text-xs font-medium">
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button @click="videoModalOpen = false" class="px-4 py-2 rounded-xl text-slate-500 font-bold hover:bg-slate-100 text-xs">إلغاء</button>
                <button @click="videoModalOpen = false; alert('تم رفع الفيديو وحفظه في ملف الطفل بنجاح ')" class="px-5 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs shadow-md shadow-purple-600/30">رفع ونشر لولي الأمر</button>
            </div>
        </div>
    </div>

</div>
@endsection
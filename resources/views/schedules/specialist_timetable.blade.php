@extends('layouts.app')

@section('title', 'كالندر وجدول مواعيد الأخصائي: ' . $selectedSpecialistName)

@section('content')
<div class="space-y-8" x-data="specialistCalendarApp()">

    <!-- ==================== 1. الترويسة ومبدل الأخصائيين ==================== -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl flex items-center justify-center text-white text-lg shadow-md" style="background-color: #0d9488;">
                    <i class="fa-solid fa-calendar-days"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-800">كالندر وجدول مواعيد جلسات الأخصائي</h2>
                    <p class="text-xs text-slate-500 font-semibold mt-0.5">تقويم شهري تفاعلي للمواعيد، تسجيل الحضور والغياب، وتوثيق الجلسات بالفيديو</p>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <!-- مبدل الأخصائي السريع -->
            @if(count($specialists) > 1)
            <form action="{{ route('doctor.timetable') }}" method="GET" class="flex items-center gap-2">
                <span class="text-xs font-bold text-slate-500">الأخصائي:</span>
                <select name="specialist" onchange="this.form.submit()" class="p-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold shadow-xs outline-none focus:border-teal-500">
                    @foreach($specialists as $sp)
                    <option value="{{ $sp->name }}" {{ $selectedSpecialistName === $sp->name ? 'selected' : '' }}>
                        {{ $sp->name }} ({{ $sp->specialization }})
                    </option>
                    @endforeach
                </select>
            </form>
            @else
            <div class="flex items-center gap-2 bg-slate-50 p-2.5 rounded-2xl border border-slate-200">
                <span class="text-xs font-bold text-slate-500">الأخصائي:</span>
                <span class="text-xs font-bold text-teal-700">{{ $selectedSpecialistName }}</span>
            </div>
            @endif

            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'user')
            <!-- زر إضافة موعد جديد -->
            <button type="button" @click="openAddModal('{{ date('Y-m-d') }}')" class="px-5 py-2.5 rounded-2xl text-white font-extrabold text-xs shadow-md hover:opacity-95 active:scale-95 transition flex items-center gap-2" style="background-color: #0d9488;">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>+ حجز موعد جلسة</span>
            </button>
            @endif
        </div>
    </div>

    <!-- رسائل النجاح إن وجدت -->
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl text-xs font-bold flex items-center gap-3 animate-in fade-in">
        <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <!-- رسائل الخطأ تظهر كـ Popup أنيق -->
    @if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'عفواً، لا يمكن إتمام الإجراء!',
                text: '{{ session('error') }}',
                confirmButtonColor: '#f43f5e',
                confirmButtonText: '<i class="fa-solid fa-check"></i> حسناً، فهمت',
                customClass: {
                    popup: 'rounded-3xl',
                    confirmButton: 'rounded-2xl px-6 py-2.5 font-bold shadow-md'
                }
            });
        });
    </script>
    @endif

    @if($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let errorHtml = '<ul style="text-align: right; list-style: none; padding: 0; margin: 0; color: #9f1239; font-size: 0.9em; font-weight: 600;">';
            @foreach($errors->all() as $error)
                errorHtml += '<li style="margin-bottom: 8px;"><i class="fa-solid fa-triangle-exclamation" style="margin-left: 6px;"></i>{{ $error }}</li>';
            @endforeach
            errorHtml += '</ul>';

            Swal.fire({
                icon: 'warning',
                title: 'توجد أخطاء في البيانات!',
                html: errorHtml,
                confirmButtonColor: '#f43f5e',
                confirmButtonText: '<i class="fa-solid fa-check"></i> حسناً',
                customClass: {
                    popup: 'rounded-3xl',
                    confirmButton: 'rounded-2xl px-6 py-2.5 font-bold shadow-md'
                }
            });
        });
    </script>
    @endif

    <!-- ==================== 2. الكالندر الشهري التفاعلي للأخصائي (Interactive Visual Calendar) ==================== -->
    <div class="bg-white rounded-3xl p-6 md:p-8 border border-slate-100 shadow-sm space-y-6">
        
        <!-- شريط تحكم التقويم (أزرار التنقل والشهور وعرض اليوم) -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
            
            <div class="flex items-center gap-3">
                <div class="flex items-center bg-slate-100 rounded-2xl p-1 shadow-2xs">
                    <button type="button" @click="prevMonth()" class="p-2 hover:bg-slate-50 text-slate-700 rounded-xl transition shadow-2xs" title="الشهر السابق">
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </button>
                    <button type="button" @click="goToToday()" class="px-3 py-1.5 hover:bg-slate-50 text-slate-800 text-xs font-bold rounded-xl transition shadow-2xs">
                        اليوم
                    </button>
                    <button type="button" @click="nextMonth()" class="p-2 hover:bg-slate-50 text-slate-700 rounded-xl transition shadow-2xs" title="الشهر القادم">
                        <i class="fa-solid fa-chevron-left text-xs"></i>
                    </button>
                </div>

                <h3 class="text-xl font-black text-slate-900 flex items-center gap-2">
                    <span x-text="monthNames[currentMonth]"></span>
                    <span class="font-mono text-teal-700" x-text="currentYear"></span>
                </h3>
            </div>

            <!-- أزرار التبديل والفلاتر -->
            <div class="flex items-center gap-2">
                <!-- شريط البحث -->
                <div class="relative w-48">
                    <input type="text" x-model="searchQuery" placeholder="بحث بالطفل أو الأخصائي..." class="w-full pl-8 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold outline-none focus:ring-2 focus:ring-teal-500/20">
                    <i class="fa-solid fa-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                </div>

                <div class="flex items-center bg-slate-100 rounded-2xl p-1 text-xs font-bold">
                    <button type="button" @click="viewMode = 'calendar'" :class="viewMode === 'calendar' ? 'bg-slate-50 text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-900'" class="px-3.5 py-1.5 rounded-xl transition flex items-center gap-1.5">
                        <i class="fa-solid fa-table-cells"></i>
                        <span>عرض الكالندر</span>
                    </button>
                    <button type="button" @click="viewMode = 'list'" :class="viewMode === 'list' ? 'bg-slate-50 text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-900'" class="px-3.5 py-1.5 rounded-xl transition flex items-center gap-1.5">
                        <i class="fa-solid fa-list-ul"></i>
                        <span>عرض القائمة</span>
                    </button>
                </div>
            </div>

        </div>

        <!-- ==================== شبكة الكالندر الشهري (Month Grid) ==================== -->
        <div x-show="viewMode === 'calendar'" class="space-y-2">
            
            <!-- عناوين أيام الأسبوع (السبت -> الجمعة) -->
            <div class="grid grid-cols-7 gap-2 text-center text-xs font-extrabold text-slate-400 py-2 border-b border-slate-100">
                <template x-for="day in dayNames" :key="day">
                    <div x-text="day" class="py-1"></div>
                </template>
            </div>

            <!-- خلايا الأيام داخل الشهر -->
            <div class="grid grid-cols-7 gap-2">
                
                <!-- الأيام الفارغة قبل بداية الشهر -->
                <template x-for="blank in blankDays" :key="'blank-'+blank">
                    <div class="min-h-[110px] sm:min-h-[130px] p-2 bg-slate-50/40 rounded-2xl border border-slate-100/60 opacity-40"></div>
                </template>

                <!-- أيام الشهر الفعلية -->
                <template x-for="day in daysInMonth" :key="'day-'+day">
                    <div @click="handleDayClick(day)" 
                         :class="{
                            'ring-2 ring-amber-400 bg-amber-50/30 border-amber-300': isToday(day),
                            'bg-slate-50 border-slate-200 opacity-60 hover:opacity-100': isPast(day) && !isToday(day),
                            'bg-white border-slate-200 hover:border-teal-400': !isToday(day) && !isPast(day)
                         }" 
                         class="min-h-[110px] sm:min-h-[130px] p-2 rounded-2xl border transition shadow-2xs flex flex-col justify-between group cursor-pointer hover:shadow-md">
                        
                        <!-- رقم اليوم في رأس الخلية -->
                        <div class="flex items-center justify-between">
                            <span :class="isToday(day) ? 'bg-amber-500 text-white font-black' : (isPast(day) ? 'text-slate-400 font-bold' : 'text-slate-700 font-extrabold')" 
                                  class="w-7 h-7 rounded-xl flex items-center justify-center text-xs font-mono shadow-2xs" 
                                  x-text="day"></span>
                            
                            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'user')
                            <button type="button" x-show="!isPast(day)" @click.stop="openAddModalForDay(day)" class="opacity-0 group-hover:opacity-100 w-6 h-6 rounded-lg bg-teal-50 text-teal-700 hover:bg-teal-600 hover:text-white transition flex items-center justify-center text-[10px]" title="إضافة موعد بهذا اليوم">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                            @endif
                        </div>

                        <!-- قائمة الجلسات داخل هذا اليوم (Chips) -->
                        <div class="space-y-1 my-1 overflow-y-auto max-h-[85px]">
                            <template x-for="sess in getSessionsForDay(day)" :key="sess.id">
                                <div @click.stop="openSessionDetail(sess)" 
                                     :class="{
                                        'bg-orange-500 text-white border-orange-600 line-through opacity-90': sess.status === 'cancelled',
                                        'bg-emerald-500 text-white border-emerald-600': sess.attendance_status === 'attended' && sess.status !== 'cancelled',
                                        'bg-rose-500 text-white border-rose-600': sess.attendance_status === 'absent' && sess.status !== 'cancelled',
                                        'bg-blue-500 text-white border-blue-600': sess.attendance_status === 'pending' && sess.status !== 'cancelled'
                                     }" 
                                     class="p-1.5 rounded-xl border text-[10px] font-bold truncate transition hover:scale-102 hover:shadow-xs flex items-center gap-1 shadow-2xs">
                                    <span class="w-1.5 h-1.5 rounded-full shrink-0 bg-white opacity-80"></span>
                                    <span class="font-mono text-[9px] shrink-0" x-text="sess.start_time.substring(0, 5)"></span>
                                    <span class="truncate" x-text="sess.status === 'cancelled' ? 'اعتذار: ' + sess.child_name : sess.child_name"></span>
                                </div>
                            </template>
                        </div>

                        <!-- شريط سفلي مصغر لعدد الجلسات إن وجدت -->
                        <div class="text-[9px] font-bold text-slate-400 text-left">
                            <span x-show="getSessionsForDay(day).length > 0" x-text="getSessionsForDay(day).length + ' جلسات'" class="text-teal-700"></span>
                        </div>

                    </div>
                </template>

            </div>

        </div>

        <!-- ==================== عرض القائمة (List View) ==================== -->
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
                            <h5 class="font-black text-sm text-slate-900" x-text="sess.child_name"></h5>
                            <p class="text-[11px] text-slate-500 font-medium mt-0.5" x-text="sess.session_title + ' • ' + sess.room_name"></p>
                        </div>

                        <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs font-bold">
                            <a :href="sess.whatsapp_reminder_url" target="_blank" class="text-emerald-700 hover:underline flex items-center gap-1">
                                <i class="fa-brands fa-whatsapp text-emerald-600"></i>
                                <span>تذكير واتساب</span>
                            </a>

                            <button type="button" @click="openSessionDetail(sess)" class="text-slate-600 hover:text-teal-700">
                                تفاصيل الجلسة &larr;
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

    <!-- ==================== 3. مودال تفاصيل الجلسة وإجراءات الأخصائي ==================== -->
    <div x-show="detailModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 space-y-6 shadow-2xl border border-slate-100" @click.away="detailModalOpen = false">
            
            <template x-if="selectedSession">
                <div class="space-y-6">
                    
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-700 flex items-center justify-center text-xl shadow-xs">
                                <i class="fa-solid fa-calendar-check"></i>
                            </div>
                            <div>
                                <h3 class="font-black text-lg text-slate-900" x-text="'جلسة: ' + selectedSession.child_name"></h3>
                                <p class="text-xs text-slate-500 font-mono font-bold" x-text="selectedSession.session_date + ' • ' + selectedSession.formatted_time_range"></p>
                            </div>
                        </div>

                        <button type="button" @click="detailModalOpen = false" class="p-2 text-slate-400 hover:text-slate-700 rounded-xl">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <!-- بيانات الجلسة -->
                    <div class="space-y-3 text-xs">
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">
                            <p class="text-slate-800 font-bold flex items-center gap-2">
                                <i class="fa-solid fa-stethoscope text-teal-600"></i>
                                <span x-text="'نوع الجلسة: ' + selectedSession.session_title"></span>
                            </p>
                            <p class="text-slate-600 font-medium flex items-center gap-2">
                                <i class="fa-solid fa-door-open text-purple-600"></i>
                                <span x-text="'القاعة / الغرفة: ' + selectedSession.room_name"></span>
                            </p>
                            <p class="text-slate-600 font-medium flex items-center gap-2">
                                <i class="fa-solid fa-user-doctor text-blue-600"></i>
                                <span x-text="'الأخصائي المعالج: ' + selectedSession.specialist_name"></span>
                            </p>
                        </div>

                        <div x-show="selectedSession.notes" class="p-3 bg-amber-50 rounded-2xl border border-amber-200 text-slate-700 text-xs italic">
                            <span class="font-bold text-slate-800">ملاحظات:</span> <span x-text="selectedSession.notes"></span>
                        </div>
                    </div>

                    <!-- أزرار الإجراءات السريعة -->
                    <div class="space-y-3 pt-2">
                        
                        <div class="space-y-3 pt-2" x-show="selectedSession.attendance_status === 'pending'">
                            <div class="flex items-center gap-2" x-show="isSessionToday(selectedSession)">
                                <!-- تسجيل حضور وبدء الجلسة -->
                                <a :href="'/doctor-portal/log?child_id=' + selectedSession.child_id" class="flex-1 py-3 px-3 rounded-2xl bg-emerald-600 text-white font-bold text-xs shadow-md hover:bg-emerald-700 transition flex items-center justify-center gap-1.5">
                                    <i class="fa-solid fa-notes-medical"></i>
                                    <span>تسجيل حضور وبدء الجلسة</span>
                                </a>

                                <!-- تسجيل غياب -->
                                <form :action="'/schedules/' + selectedSession.id + '/attendance'" method="POST" class="flex-1">
                                    @csrf
                                    <input type="hidden" name="attendance_status" value="absent">
                                    <button type="submit" class="w-full py-3 px-3 rounded-2xl bg-rose-50 text-rose-700 hover:bg-rose-600 hover:text-white border border-rose-200 font-bold text-xs transition flex items-center justify-center gap-1.5">
                                        <i class="fa-solid fa-xmark"></i>
                                        <span>تسجيل غياب</span>
                                    </button>
                                </form>

                                <!-- تذكير واتساب -->
                                <a :href="selectedSession.whatsapp_reminder_url" target="_blank" class="p-3 rounded-2xl bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white border border-emerald-200 transition" title="إرسال تذكير واتساب">
                                    <i class="fa-brands fa-whatsapp text-lg"></i>
                                </a>
                            </div>

                            <div class="flex items-center gap-2" x-show="isSessionPast(selectedSession)">
                                <div class="flex-1 py-3 px-3 rounded-2xl bg-slate-100 border border-slate-200 text-slate-500 font-bold text-xs flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-clock-rotate-left"></i>
                                    <span>انتهى وقت الجلسة دون تسجيل حضور أو إجراء</span>
                                </div>
                            </div>

                            <!-- If future -->
                            <div class="flex items-center gap-2" x-show="isSessionFuture(selectedSession)">
                                <div class="flex-1 py-3 px-3 rounded-2xl bg-teal-50 border border-teal-200 text-teal-700 font-bold text-xs flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-calendar-day"></i>
                                    <span>جلسة قادمة (تُسجل في نفس يوم الجلسة)</span>
                                </div>
                            </div>
                        </div>

                        <!-- If attended -->
                        <div class="flex items-center gap-2" x-show="selectedSession.attendance_status === 'attended'">
                            <div class="flex-1 py-3 px-3 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 font-bold text-xs flex items-center justify-center gap-2">
                                <i class="fa-solid fa-check-circle"></i>
                                <span>تم الحضور والجلسة مسجلة</span>
                            </div>
                            <a :href="selectedSession.whatsapp_reminder_url" target="_blank" class="p-3 rounded-2xl bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white border border-emerald-200 transition" title="إرسال تذكير واتساب" x-show="!isSessionPast(selectedSession)">
                                <i class="fa-brands fa-whatsapp text-lg"></i>
                            </a>
                        </div>

                        <!-- If absent -->
                        <div class="flex items-center gap-2" x-show="selectedSession.attendance_status === 'absent'">
                            <div class="flex-1 py-3 px-3 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 font-bold text-xs flex items-center justify-center gap-2">
                                <i class="fa-solid fa-times-circle"></i>
                                <span>تم تسجيل الغياب</span>
                            </div>
                            <a :href="selectedSession.whatsapp_reminder_url" target="_blank" class="p-3 rounded-2xl bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white border border-emerald-200 transition" title="إرسال تذكير واتساب" x-show="!isSessionPast(selectedSession)">
                                <i class="fa-brands fa-whatsapp text-lg"></i>
                            </a>
                        </div>

                        @if(auth()->user()->role === 'specialist')
                        <!-- الاعتذار عن الجلسة (للأخصائي) -->
                        <div class="text-center pt-2 border-t border-slate-100" x-show="selectedSession.status !== 'cancelled' && selectedSession.attendance_status === 'pending' && !isSessionPast(selectedSession)">
                            <form :action="'/doctor-portal/timetable/apologize-session/' + selectedSession.id" method="POST" onsubmit="return confirm('هل أنت متأكد من رغبتك في الاعتذار عن هذه الجلسة؟ سيتم إشعار الإدارة وولي الأمر بذلك.')">
                                @csrf
                                <button type="submit" class="text-rose-500 hover:text-rose-700 text-xs font-bold hover:underline flex items-center justify-center gap-1.5 w-full">
                                    <i class="fa-solid fa-triangle-exclamation"></i> اعتذار عن هذه الجلسة لظروف طارئة
                                </button>
                            </form>
                        </div>
                        @endif

                        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'user')
                        <!-- حذف الموعد -->
                        <div class="text-center pt-2 border-t border-slate-100" x-show="!isSessionPast(selectedSession)">
                            <form :action="'/schedules/' + selectedSession.id" method="POST" onsubmit="return confirm('هل أنت متأكد من رغبتك في حذف هذا الموعد من الجدول؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-500 hover:text-rose-700 text-xs font-bold hover:underline">
                                    <i class="fa-regular fa-trash-can ml-1"></i> حذف هذا الموعد من الكالندر
                                </button>
                            </form>
                        </div>
                        @endif

                    </div>

                </div>
            </template>

        </div>
    </div>

    <!-- ==================== 4. مودال جدولة موعد جلسة لطفل ==================== -->
    <div x-show="addModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-xl w-full p-6 md:p-8 space-y-6 shadow-2xl border border-slate-100" @click.away="addModalOpen = false">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-white text-lg" style="background-color: #0d9488;">
                        <i class="fa-solid fa-calendar-plus"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-lg text-slate-900">تحديد وجدولة موعد جلسة</h3>
                        <p class="text-xs text-slate-500 font-semibold" x-text="'للأخصائي: ' + specialistName"></p>
                    </div>
                </div>

                <button type="button" @click="addModalOpen = false" class="p-2 text-slate-400 hover:text-slate-700 rounded-xl">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form action="{{ route('schedules.store') }}" method="POST" class="space-y-4 text-xs font-medium">
                @csrf
                <input type="hidden" name="specialist_name" :value="specialistName">
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    
                    <!-- اختيار الطفل -->
                    <div class="sm:col-span-2">
                        <label class="block font-bold text-slate-700 mb-1.5">اختر الطفل <span class="text-rose-500">*</span></label>
                        <select name="child_id" x-model="selectedChildId" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold">
                            <option value="">-- اختر الطفل من القائمة --</option>
                            @foreach($allChildren as $ch)
                            <option value="{{ $ch->id }}">{{ $ch->name }} (كود: {{ $ch->code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- نوع وعنوان الجلسة -->
                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5">نوع وعنوان الجلسة <span class="text-rose-500">*</span></label>
                        <select name="session_title" x-model="sessionTitle" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold">
                            @foreach($sessionTypes as $st)
                            <option value="{{ $st }}">{{ $st }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- تاريخ الجلسة -->
                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5">تاريخ الجلسة <span class="text-rose-500">*</span></label>
                        <input type="date" name="session_date" x-model="sessionDate" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold">
                    </div>

                    <!-- القاعة أو الغرفة -->
                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5">القاعة / الغرفة <span class="text-rose-500">*</span></label>
                        <select name="room_name" x-model="roomName" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold">
                            @foreach($rooms as $rm)
                            <option value="{{ $rm }}">{{ $rm }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- وقت البدء -->
                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5">وقت بدء الجلسة <span class="text-rose-500">*</span></label>
                        <input type="time" name="start_time" x-model="startTime" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold font-mono">
                    </div>

                    <!-- وقت الانتهاء -->
                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5">وقت انتهاء الجلسة</label>
                        <input type="time" name="end_time" x-model="endTime" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold font-mono">
                    </div>

                    <!-- تكرار أسبوعي -->
                    <div class="sm:col-span-2 p-3 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                        <div>
                            <span class="font-bold text-slate-800 block">تكرار الموعد أسبوعياً (Recurring Weekly)</span>
                            <span class="text-[11px] text-slate-400">سيتم إنشاء نفس الموعد تلقائياً في نفس اليوم والساعة للأسابيع القادمة</span>
                        </div>
                        <input type="checkbox" name="is_recurring" value="1" x-model="isRecurring" class="w-5 h-5 rounded accent-teal-600">
                    </div>

                    <!-- ملاحظات -->
                    <div class="sm:col-span-2">
                        <label class="block font-bold text-slate-700 mb-1.5">ملاحظات إضافية للجلسة (اختياري):</label>
                        <textarea name="notes" x-model="notes" rows="2" placeholder="اكتب أي تعليمات للأهل أو تجهيزات للقاعة..." class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white leading-relaxed"></textarea>
                    </div>

                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" @click="addModalOpen = false" class="px-5 py-2.5 rounded-2xl text-slate-500 font-bold hover:bg-slate-100 text-xs">
                        إلغاء
                    </button>
                    <button type="submit" class="px-8 py-3 rounded-2xl text-white font-black text-xs shadow-lg hover:opacity-95 transition flex items-center gap-2" style="background-color: #0d9488;">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>حفظ وتثبيت الموعد بالجدول</span>
                    </button>
                </div>

            </form>

        </div>
    </div>

    <!-- ==================== 5. Day Summary & Apologize Modal ==================== -->
    <div x-show="daySummaryModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 space-y-6 shadow-2xl border border-slate-100" @click.away="daySummaryModalOpen = false">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shadow-xs">
                        <i class="fa-solid fa-sun"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-lg text-slate-900" x-text="'ملخص يوم: ' + sessionDate"></h3>
                        <p class="text-xs text-slate-500 font-mono font-bold" x-text="'الأخصائي: ' + specialistName"></p>
                    </div>
                </div>
                <button type="button" @click="daySummaryModalOpen = false" class="p-2 text-slate-400 hover:text-slate-700 rounded-xl">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <template x-if="daySessions.length > 0">
                <div class="space-y-6 text-xs">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                            <span class="block text-slate-500 font-bold mb-1">إجمالي ساعات العمل</span>
                            <span class="text-xl font-black text-slate-800" x-text="dayTotalHours"></span>
                        </div>
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                            <span class="block text-slate-500 font-bold mb-1">أوقات الفراغ (Gaps)</span>
                            <span class="text-xl font-black text-slate-800" x-text="dayTotalGaps"></span>
                        </div>
                    </div>

                    <div>
                        <h4 class="font-bold text-slate-800 mb-3 border-b pb-2">الأطفال المجدولين اليوم (تواصل سريع)</h4>
                        <div class="max-h-40 overflow-y-auto space-y-2 pr-2">
                            <template x-for="sess in daySessions" :key="sess.id">
                                <div class="flex items-center justify-between p-2 rounded-xl border border-slate-100" :class="sess.status === 'cancelled' ? 'bg-rose-50 opacity-70' : 'bg-white'">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-clock text-slate-400"></i>
                                        <span class="font-mono font-bold text-slate-700" x-text="sess.start_time.substring(0,5) + ' - ' + sess.end_time.substring(0,5)"></span>
                                        <span class="font-black text-slate-900" x-text="sess.child_name"></span>
                                        <span x-show="sess.status === 'cancelled'" class="text-[9px] bg-rose-100 text-rose-700 px-2 py-0.5 rounded-full font-bold">ملغية</span>
                                    </div>
                                    <a :href="sess.whatsapp_reminder_url" target="_blank" class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center hover:bg-emerald-500 hover:text-white transition" title="واتساب">
                                        <i class="fa-brands fa-whatsapp text-lg"></i>
                                    </a>
                                </div>
                            </template>
                        </div>
                    </div>
                    
                    <template x-if="!dayIsAllCancelled">
                        <div class="bg-rose-50 border border-rose-200 rounded-2xl p-4 flex flex-col gap-3" x-show="!isPast(parseInt(sessionDate.split('-')[2]))">
                            <p class="font-bold text-rose-800">حالة طوارئ أو طلب إجازة؟</p>
                            <p class="text-rose-600 text-[11px]">عند الضغط على الزر أدناه سيتم إلغاء جميع جلساتك لهذا اليوم فوراً، وتسجيل غياب تلقائي للأطفال، وإرسال تنبيه عاجل للإدارة لإبلاغ أولياء الأمور.</p>
                            <form action="/doctor-portal/timetable/apologize-day" method="POST" onsubmit="return confirm('هل أنت متأكد من إلغاء جميع مواعيدك لهذا اليوم؟ هذا الإجراء لا يمكن التراجع عنه بسهولة!')">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="specialist_name" :value="specialistName">
                                <input type="hidden" name="session_date" :value="sessionDate">
                                <button type="submit" class="w-full py-2.5 bg-rose-600 text-white font-black rounded-xl hover:bg-rose-700 shadow-md">
                                    <i class="fa-solid fa-triangle-exclamation ml-1"></i>
                                    اعتذار عن عمل اليوم بالكامل
                                </button>
                            </form>
                        </div>
                    </template>
                </div>
            </template>
            <template x-if="daySessions.length === 0">
                <div class="text-center py-10 text-slate-400 font-bold text-sm">
                    لا توجد أي جلسات مجدولة في هذا اليوم.
                </div>
            </template>
        </div>
    </div>
</div>

@push('scripts')
<script>
function specialistCalendarApp() {
    return {
        viewMode: 'calendar',
        searchQuery: '',
        currentYear: new Date().getFullYear(),
        currentMonth: new Date().getMonth(),
        monthNames: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'],
        dayNames: ['السبت', 'الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة'],
        
        allSessions: {!! json_encode($calendarEvents) !!},

        blankDays: [],
        daysInMonth: [],
        daySummaryModalOpen: false,
        daySessions: [],
        dayTotalHours: '0',
        dayTotalGaps: '0',
        dayIsAllCancelled: false,
        detailModalOpen: false,
        selectedSession: null,
        addModalOpen: false,
        selectedChildId: '',
        specialistName: '{{ $selectedSpecialistName }}',
        sessionTitle: 'جلسة تخاطب ونطق فردي',
        sessionDate: '{{ date('Y-m-d') }}',
        startTime: '10:00',
        endTime: '10:45',
        roomName: 'غرفة التخاطب 1',
        isRecurring: false,
        notes: '',

        init() {
            this.calculateCalendar();
        },

        calculateCalendar() {
            let firstDay = new Date(this.currentYear, this.currentMonth, 1).getDay();
            // Saturday is index 0 in Arabic RTL calendar:
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

        isPast(day) {
            let date = new Date(this.currentYear, this.currentMonth, day);
            let today = new Date();
            today.setHours(0,0,0,0);
            return date < today;
        },

        getTodayString() {
            let today = new Date();
            let y = today.getFullYear();
            let m = String(today.getMonth() + 1).padStart(2, '0');
            let d = String(today.getDate()).padStart(2, '0');
            return `${y}-${m}-${d}`;
        },

        isSessionPast(sess) {
            return sess.session_date < this.getTodayString();
        },

        isSessionFuture(sess) {
            return sess.session_date > this.getTodayString();
        },

        isSessionToday(sess) {
            return sess.session_date === this.getTodayString();
        },

        getSessionsForDay(day) {
            let dateStr = this.formatDate(day);
            return this.allSessions.filter(s => {
                let matchDate = s.session_date === dateStr;
                let matchSearch = true;
                if (this.searchQuery.trim() !== '') {
                    let q = this.searchQuery.trim().toLowerCase();
                    matchSearch = (s.child_name && s.child_name.toLowerCase().includes(q)) || 
                                  (s.specialist_name && s.specialist_name.toLowerCase().includes(q)) ||
                                  (s.session_title && s.session_title.toLowerCase().includes(q));
                }
                return matchDate && matchSearch;
            });
        },

        get filteredMonthSessions() {
            let mStr = String(this.currentMonth + 1).padStart(2, '0');
            let prefix = `${this.currentYear}-${mStr}`;
            return this.allSessions.filter(s => {
                let matchMonth = s.session_date.startsWith(prefix);
                let matchSearch = true;
                if (this.searchQuery.trim() !== '') {
                    let q = this.searchQuery.trim().toLowerCase();
                    matchSearch = (s.child_name && s.child_name.toLowerCase().includes(q)) || 
                                  (s.specialist_name && s.specialist_name.toLowerCase().includes(q)) ||
                                  (s.session_title && s.session_title.toLowerCase().includes(q));
                }
                return matchMonth && matchSearch;
            });
        },

        handleDayClick(day) {
            let sessions = this.getSessionsForDay(day);
            this.sessionDate = this.formatDate(day);
            if (sessions.length > 0) {
                this.daySessions = sessions;
                this.calculateDaySummary();
                this.daySummaryModalOpen = true;
            } else {
                if (!this.isPast(day)) {
                    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'user')
                    this.openAddModalForDay(day);
                    @endif
                }
            }
        },

        calculateDaySummary() {
            if (this.daySessions.length === 0) return;
            
            // Sort sessions by start_time
            let sorted = [...this.daySessions].sort((a, b) => a.start_time.localeCompare(b.start_time));
            
            let totalMinutes = 0;
            let gapMinutes = 0;
            this.dayIsAllCancelled = true;

            for (let i = 0; i < sorted.length; i++) {
                if (sorted[i].status !== 'cancelled') {
                    this.dayIsAllCancelled = false;
                }
                
                let start = new Date(this.sessionDate + 'T' + sorted[i].start_time);
                let end = new Date(this.sessionDate + 'T' + sorted[i].end_time);
                totalMinutes += (end - start) / 60000;

                if (i < sorted.length - 1) {
                    let nextStart = new Date(this.sessionDate + 'T' + sorted[i+1].start_time);
                    if (nextStart > end) {
                        gapMinutes += (nextStart - end) / 60000;
                    }
                }
            }

            let formatTime = (mins) => {
                let h = Math.floor(mins / 60);
                let m = mins % 60;
                return (h > 0 ? h + ' ساعة ' : '') + (m > 0 ? m + ' دقيقة' : (h === 0 ? '0' : ''));
            };

            this.dayTotalHours = formatTime(totalMinutes);
            this.dayTotalGaps = gapMinutes > 0 ? formatTime(gapMinutes) : 'لا يوجد فراغات';
        },

        openAddModalForDay(day) {
            this.sessionDate = this.formatDate(day);
            this.addModalOpen = true;
        },

        openAddModal(dateStr) {
            this.sessionDate = dateStr || '{{ date('Y-m-d') }}';
            this.addModalOpen = true;
        },

        openSessionDetail(sess) {
            this.selectedSession = sess;
            this.detailModalOpen = true;
        }
    }
}
</script>
@endpush
@endsection





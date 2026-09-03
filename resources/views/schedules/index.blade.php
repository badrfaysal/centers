@extends('layouts.app')

@section('title', 'جدول وكالندر الجلسات العام')

@section('content')
<div class="space-y-8" x-data="masterCalendarApp()">

    <!-- ==================== 1. الترويسة وأزرار التحكم ==================== -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl flex items-center justify-center text-white text-lg shadow-md" style="background-color: #0d9488;">
                    <i class="fa-solid fa-calendar-days"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-800">جدول وكالندر الجلسات وتوزيع الأخصائيين والغرف</h2>
                    <p class="text-xs text-slate-500 font-semibold mt-0.5">تقويم شهري وإداري تفاعلي، منع تعارض القاعات، وإرسال تذكيرات للأهل</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('doctor.timetable') }}" class="px-4 py-2.5 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-2xl text-xs font-bold transition flex items-center gap-1.5 border border-blue-100">
                <i class="fa-solid fa-user-doctor text-xs"></i>
                <span>جدول الأخصائي الشخصي</span>
            </a>

            <button type="button" @click="openAddModal('{{ date('Y-m-d') }}')" class="px-5 py-2.5 rounded-2xl text-white font-extrabold text-xs shadow-lg hover:opacity-95 active:scale-95 transition flex items-center gap-2" style="background-color: #0d9488;">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>+ جدولة موعد جلسة جديدة</span>
            </button>
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

    <!-- ==================== 2. كروت إحصائيات الجلسات والمواعيد ==================== -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-extrabold text-amber-800 uppercase">جلسات ومواعيد اليوم</p>
                <h3 class="text-3xl font-black text-amber-900 mt-1">{{ $todayCount }} <span class="text-xs text-slate-400 font-normal">جلسة مجدولة</span></h3>
                <p class="text-[10px] text-amber-700 font-bold mt-0.5">{{ date('Y-m-d') }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-calendar-day"></i>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-extrabold text-slate-400 uppercase">جلسات هذا الأسبوع</p>
                <h3 class="text-3xl font-black text-teal-800 mt-1">{{ $thisWeekCount }} <span class="text-xs text-slate-400 font-normal">جلسة بالأسبوع</span></h3>
                <p class="text-[10px] text-teal-700 font-bold mt-0.5">مواعيد مؤكدة</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-calendar-week"></i>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-extrabold text-slate-400 uppercase">إجمالي الجلسات المجدولة</p>
                <h3 class="text-3xl font-black text-purple-800 mt-1">{{ $totalScheduledCount }} <span class="text-xs text-slate-400 font-normal">جلسة مستقبلية</span></h3>
                <p class="text-[10px] text-purple-600 font-bold mt-0.5">قيد التنفيذ والمتابعة</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-clipboard-check"></i>
            </div>
        </div>
    </div>

    <!-- ==================== 3. الكالندر الشهري التفاعلي العام للمركز ==================== -->
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

            <!-- أزرار التبديل والفلاتر -->
            <div class="flex flex-wrap items-center gap-2">
                <!-- فلترة سريعة بالأخصائي داخل الكالندر -->
                <select x-model="filterSpecialist" class="p-2 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold outline-none">
                    <option value="all">كل الأخصائيين</option>
                    @foreach($specialists as $sp)
                    <option value="{{ $sp->name }}">{{ $sp->name }}</option>
                    @endforeach
                </select>

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
        </div>

        <!-- ==================== شبكة الكالندر الشهري العام (Month Grid) ==================== -->
        <div x-show="viewMode === 'calendar'" class="space-y-2">
            
            <div class="grid grid-cols-7 gap-2 text-center text-xs font-extrabold text-slate-400 py-2 border-b border-slate-100">
                <template x-for="day in dayNames" :key="day">
                    <div x-text="day" class="py-1"></div>
                </template>
            </div>

            <div class="grid grid-cols-7 gap-2">
                <template x-for="blank in blankDays" :key="'blank-'+blank">
                    <div class="min-h-[110px] sm:min-h-[130px] p-2 bg-slate-50/40 rounded-2xl border border-slate-100/60 opacity-40"></div>
                </template>

                <template x-for="day in daysInMonth" :key="'day-'+day">
                    <div @click="handleDayClick(day)" 
                         :class="{
                            'ring-2 ring-amber-400 bg-amber-50/30 border-amber-300': isToday(day),
                            'bg-white border-slate-200 hover:border-teal-400': !isToday(day)
                         }" 
                         class="min-h-[110px] sm:min-h-[130px] p-2 rounded-2xl border transition shadow-2xs flex flex-col justify-between group cursor-pointer hover:shadow-md">
                        
                        <div class="flex items-center justify-between">
                            <span :class="isToday(day) ? 'bg-amber-500 text-white font-black' : 'text-slate-700 font-extrabold'" 
                                  class="w-7 h-7 rounded-xl flex items-center justify-center text-xs font-mono shadow-2xs" 
                                  x-text="day"></span>
                            
                            <button type="button" @click.stop="openAddModalForDay(day)" class="opacity-0 group-hover:opacity-100 w-6 h-6 rounded-lg bg-teal-50 text-teal-700 hover:bg-teal-600 hover:text-white transition flex items-center justify-center text-[10px]" title="إضافة موعد">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </div>

                        <div class="space-y-1 my-1 overflow-y-auto max-h-[85px]">
                            <template x-for="sess in getSessionsForDay(day)" :key="sess.id">
                                <div @click.stop="openSessionDetail(sess)" 
                                     :class="{
                                        'bg-orange-500 text-white border-orange-600 line-through opacity-90': sess.status === 'cancelled',
                                        'bg-emerald-500 text-white border-emerald-600': sess.attendance_status === 'attended' && sess.status !== 'cancelled',
                                        'bg-rose-500 text-white border-rose-600': sess.attendance_status === 'absent' && sess.status !== 'cancelled',
                                        'bg-blue-500 text-white border-blue-600': sess.attendance_status === 'pending' && sess.status !== 'cancelled'
                                     }" 
                                     class="p-1.5 rounded-xl border text-[10px] font-bold truncate transition hover:scale-102 flex items-center gap-1 shadow-2xs">
                                    <span class="w-1.5 h-1.5 rounded-full shrink-0 bg-white opacity-80"></span>
                                    <span class="font-mono text-[9px] shrink-0" x-text="sess.start_time.substring(0, 5)"></span>
                                    <span class="truncate" x-text="sess.status === 'cancelled' ? 'اعتذار: ' + sess.child_name : sess.child_name + ' (' + sess.specialist_name + ')'"></span>
                                </div>
                            </template>
                        </div>

                        <div class="text-[9px] font-bold text-slate-400 text-left">
                            <span x-show="getSessionsForDay(day).length > 0" x-text="getSessionsForDay(day).length + ' جلسات'" class="text-teal-700"></span>
                        </div>
                    </div>
                </template>
            </div>

        </div>

        <!-- ==================== عرض القائمة العام ==================== -->
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
                            <p class="text-[11px] text-slate-500 font-medium mt-0.5" x-text="'الأخصائي: ' + sess.specialist_name + ' • ' + sess.room_name"></p>
                        </div>

                        <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs font-bold">
                            <a :href="sess.whatsapp_reminder_url" target="_blank" class="text-emerald-700 hover:underline flex items-center gap-1">
                                <i class="fa-brands fa-whatsapp text-emerald-600"></i>
                                <span>تذكير الأهل بالواتساب</span>
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

    <!-- ==================== مودال تفاصيل الجلسة ==================== -->
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
                                <h3 class="font-black text-lg text-slate-900" x-text="selectedSession.status === 'cancelled' ? 'اعتذار: ' + selectedSession.child_name : 'جلسة: ' + selectedSession.child_name"></h3>
                                <p class="text-xs text-slate-500 font-mono font-bold" x-text="selectedSession.session_date + ' | ' + selectedSession.formatted_time_range"></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <!-- Edit Button -->
                            <button type="button" @click="openEditModal(selectedSession)" class="p-2 text-teal-600 hover:bg-teal-50 rounded-xl" title="تعديل الموعد">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <!-- Delete Button -->
                            <form :action="'/schedules/' + selectedSession.id" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الموعد لإتاحة المكان لطفل آخر؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-rose-500 hover:bg-rose-50 rounded-xl" title="حذف الموعد">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                            <!-- Close Button -->
                            <button type="button" @click="detailModalOpen = false" class="p-2 text-slate-400 hover:text-slate-700 rounded-xl">
                                <i class="fa-solid fa-xmark text-lg"></i>
                            </button>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2 text-xs">
                        <p class="text-slate-800 font-bold flex items-center gap-2">
                            <i class="fa-solid fa-stethoscope text-teal-600"></i>
                            <span x-text="'نوع الجلسة: ' + selectedSession.session_title"></span>
                        </p>
                        <p class="text-slate-600 font-medium flex items-center gap-2">
                            <i class="fa-solid fa-user-doctor text-blue-600"></i>
                            <span x-text="'الأخصائي المعالج: ' + selectedSession.specialist_name"></span>
                        </p>
                        <p class="text-slate-600 font-medium flex items-center gap-2">
                            <i class="fa-solid fa-door-open text-purple-600"></i>
                            <span x-text="'القاعة / الغرفة: ' + selectedSession.room_name"></span>
                        </p>
                    </div>

                    <div class="pt-2" x-show="selectedSession.status !== 'cancelled'">
                        <!-- If pending -->
                        <div class="flex items-center gap-2" x-show="selectedSession.attendance_status === 'pending'">
                            <form :action="'/schedules/' + selectedSession.id + '/attendance'" method="POST" class="flex-1">
                                @csrf
                                <input type="hidden" name="attendance_status" value="attended">
                                <button type="submit" class="w-full py-2.5 px-3 rounded-2xl bg-emerald-600 text-white font-bold text-xs shadow-xs hover:bg-emerald-700 transition flex items-center justify-center gap-1.5">
                                    <i class="fa-solid fa-check"></i>
                                    <span>حضر</span>
                                </button>
                            </form>

                            <form :action="'/schedules/' + selectedSession.id + '/attendance'" method="POST" class="flex-1">
                                @csrf
                                <input type="hidden" name="attendance_status" value="absent">
                                <button type="submit" class="w-full py-2.5 px-3 rounded-2xl bg-rose-50 text-rose-700 hover:bg-rose-600 hover:text-white border border-rose-200 font-bold text-xs transition flex items-center justify-center gap-1.5">
                                    <i class="fa-solid fa-xmark"></i>
                                    <span>غياب</span>
                                </button>
                            </form>

                            <a :href="selectedSession.whatsapp_reminder_url" target="_blank" class="p-2.5 rounded-2xl bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white border border-emerald-200 transition" title="إرسال تذكير واتساب">
                                <i class="fa-brands fa-whatsapp text-lg"></i>
                            </a>
                        </div>

                        <!-- If attended -->
                        <div class="flex items-center gap-2" x-show="selectedSession.attendance_status === 'attended'">
                            <div class="flex-1 py-2.5 px-3 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 font-bold text-xs flex items-center justify-center gap-2">
                                <i class="fa-solid fa-check-circle"></i>
                                <span>تم تسجيل الحضور</span>
                            </div>
                            <a :href="selectedSession.whatsapp_reminder_url" target="_blank" class="p-2.5 rounded-2xl bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white border border-emerald-200 transition" title="إرسال تذكير واتساب">
                                <i class="fa-brands fa-whatsapp text-lg"></i>
                            </a>
                        </div>

                        <!-- If absent -->
                        <div class="flex items-center gap-2" x-show="selectedSession.attendance_status === 'absent'">
                            <div class="flex-1 py-2.5 px-3 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 font-bold text-xs flex items-center justify-center gap-2">
                                <i class="fa-solid fa-times-circle"></i>
                                <span>تم تسجيل الغياب</span>
                            </div>
                            <a :href="selectedSession.whatsapp_reminder_url" target="_blank" class="p-2.5 rounded-2xl bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white border border-emerald-200 transition" title="إرسال تذكير واتساب">
                                <i class="fa-brands fa-whatsapp text-lg"></i>
                            </a>
                        </div>
                    </div>

                    <div class="text-center pt-2 border-t border-slate-100">
                        <form :action="'/schedules/' + selectedSession.id" method="POST" onsubmit="return confirm('هل أنت متأكد من رغبتك في حذف هذا الموعد من الجدول؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-500 hover:text-rose-700 text-xs font-bold hover:underline">
                                <i class="fa-regular fa-trash-can ml-1"></i> حذف هذا الموعد
                            </button>
                        </form>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- ==================== مودال جدولة موعد جلسة جديدة ==================== -->
    <div x-show="addModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-xl w-full p-6 md:p-8 space-y-6 shadow-2xl border border-slate-100" @click.away="addModalOpen = false">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-white text-lg" style="background-color: #0d9488;">
                        <i class="fa-solid fa-calendar-plus"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-lg text-slate-900" x-text="isEditMode ? 'تعديل موعد الجلسة' : 'حجز موعد جلسة جديدة'"></h3>
                        <p class="text-xs text-slate-500 font-semibold mt-0.5">تحديد الطفل، الأخصائي، التاريخ، والوقت والقاعة</p>
                    </div>
                </div>

                <button type="button" @click="addModalOpen = false" class="p-2 text-slate-400 hover:text-slate-700 rounded-xl">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form :action="isEditMode ? '/schedules/' + editSessionId : '{{ route('schedules.store') }}'" method="POST" class="space-y-4 text-xs font-medium"><template x-if="isEditMode"><input type="hidden" name="_method" value="PUT"></template>
                @csrf
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block font-bold text-slate-700 mb-1.5">اختر الطفل <span class="text-rose-500">*</span></label>
                        <select name="child_id" x-model="childId" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold">
                            <option value="">-- اختر الطفل من القائمة --</option>
                            @foreach($children as $ch)
                            <option value="{{ $ch->id }}">{{ $ch->name }} (كود: {{ $ch->code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5">الأخصائي المعالج <span class="text-rose-500">*</span></label>
                        <select name="specialist_name" x-model="specialistName" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold">
                            @foreach($specialists as $sp)
                            <option value="{{ $sp->name }}">{{ $sp->name }} ({{ $sp->specialization }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5">نوع وعنوان الجلسة <span class="text-rose-500">*</span></label>
                        <select name="session_title" x-model="sessionTitle" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold">
                            @foreach($sessionTypes as $st)
                            <option value="{{ $st }}">{{ $st }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5">تاريخ الجلسة <span class="text-rose-500">*</span></label>
                        <input type="date" name="session_date" x-model="sessionDate" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5">القاعة / الغرفة <span class="text-rose-500">*</span></label>
                        <select name="room_name" x-model="roomName" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold">
                            @foreach($rooms as $rm)
                            <option value="{{ $rm }}">{{ $rm }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5">وقت بدء الجلسة <span class="text-rose-500">*</span></label>
                        <input type="time" name="start_time" x-model="startTime" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold font-mono">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5">وقت انتهاء الجلسة</label>
                        <input type="time" name="end_time" x-model="endTime" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold font-mono">
                    </div>

                    <div class="sm:col-span-2 p-3 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                        <div>
                            <span class="font-bold text-slate-800 block">تكرار الموعد أسبوعياً (Recurring Weekly)</span>
                            <span class="text-[11px] text-slate-400">سيتم إنشاء نفس الموعد تلقائياً في نفس اليوم والساعة للأسابيع القادمة</span>
                        </div>
                        <input type="checkbox" name="is_recurring" value="1" x-model="isRecurring" class="w-5 h-5 rounded accent-teal-600">
                    </div>

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
                        <span>حفظ وجدولة الموعد</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
function masterCalendarApp() {
    return {
        viewMode: 'calendar',
        filterSpecialist: 'all',
        currentYear: new Date().getFullYear(),
        currentMonth: new Date().getMonth(),
        monthNames: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'],
        dayNames: ['السبت', 'الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة'],
        
        allSessions: {!! json_encode($calendarEvents) !!},

        blankDays: [],
        daysInMonth: [],
        detailModalOpen: false,
        selectedSession: null,
        addModalOpen: false,
        isEditMode: false,
        editSessionId: null,
        childId: '',
        specialistName: '{{ $specialists->first()->name ?? '' }}',
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
            return this.allSessions.filter(s => {
                let matchDate = s.session_date === dateStr;
                let matchSpec = this.filterSpecialist === 'all' || s.specialist_name === this.filterSpecialist;
                return matchDate && matchSpec;
            });
        },

        get filteredMonthSessions() {
            let mStr = String(this.currentMonth + 1).padStart(2, '0');
            let prefix = `${this.currentYear}-${mStr}`;
            return this.allSessions.filter(s => {
                let matchMonth = s.session_date.startsWith(prefix);
                let matchSpec = this.filterSpecialist === 'all' || s.specialist_name === this.filterSpecialist;
                return matchMonth && matchSpec;
            });
        },

        handleDayClick(day) {
            let sessions = this.getSessionsForDay(day);
            if (sessions.length > 0) {
                this.openSessionDetail(sessions[0]);
            } else {
                this.openAddModalForDay(day);
            }
        },

        openAddModalForDay(day) {
            this.isEditMode = false;
            this.sessionDate = this.formatDate(day);
            this.addModalOpen = true;
        },

        openAddModal(dateStr) {
            this.isEditMode = false;
            this.sessionDate = dateStr || '{{ date('Y-m-d') }}';
            this.addModalOpen = true;
        },

        openEditModal(sess) {
            this.isEditMode = true;
            this.editSessionId = sess.id;
            this.childId = sess.child_id;
            this.specialistName = sess.specialist_name;
            this.sessionTitle = sess.session_title;
            this.sessionDate = sess.session_date;
            this.startTime = sess.start_time.substring(0, 5);
            this.endTime = sess.end_time ? sess.end_time.substring(0, 5) : '';
            this.roomName = sess.room_name;
            this.notes = sess.notes || '';
            this.detailModalOpen = false;
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










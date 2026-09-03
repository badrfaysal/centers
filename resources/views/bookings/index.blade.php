@extends('layouts.app')

@section('title', 'طلبات الحجز والمواعيد')

@section('content')
<div class="space-y-8" x-data="{ 
    scheduleModalOpen: false,
    selectedBooking: null,
    scheduledDate: '',
    scheduledTime: '10:00',
    specialistName: '',
    roomName: 'غرفة التخاطب 1',
    sessionPrice: 250,
    paymentStatus: 'unpaid',
    adminNotes: '',
    openScheduleModal(b) {
        this.selectedBooking = b;
        this.scheduledDate = b.scheduled_at ? b.scheduled_at.substring(0, 10) : '{{ date('Y-m-d', strtotime('+1 day')) }}';
        this.scheduledTime = b.scheduled_at ? b.scheduled_at.substring(11, 16) : '10:00';
        this.specialistName = b.specialist_name || '{{ $specialists->first()->name ?? '' }}';
        this.roomName = b.room || 'غرفة التخاطب 1';
        this.sessionPrice = b.session_price || 250;
        this.paymentStatus = b.payment_status || 'unpaid';
        this.adminNotes = b.admin_notes || '';
        this.scheduleModalOpen = true;
    }
}">

    <!-- ==================== 1. الترويسة وأزرار التنقل ==================== -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl flex items-center justify-center text-white text-lg shadow-md bg-teal-600">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-2xl font-black text-slate-800">إدارة ومتابعة طلبات الحجز والمواعيد</h2>
                        @if($pendingCount > 0)
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-teal-100 text-teal-800 border border-teal-200">
                            {{ $pendingCount }} طلب بانتظار التأكيد
                        </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 font-semibold mt-0.5">مراجعة طلبات التقييم الأولي الواردة من الموقع العام، الموافقة وتحديد الموعد والأخصائي</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('website') }}#booking" target="_blank" class="px-4 py-2.5 bg-white border border-slate-200 rounded-2xl text-xs font-bold text-slate-700 hover:bg-slate-50 transition shadow-xs flex items-center gap-2">
                <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                <span>معاينة فورم الحجز بالموقع</span>
            </a>
        </div>
    </div>

    <!-- رسائل النجاح إن وجدت -->
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl text-xs font-bold flex items-center gap-3 animate-in fade-in">
        <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <!-- ==================== 2. كروت الإحصائيات والتنبيهات ==================== -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        
        <div class="bg-white rounded-3xl p-5 border shadow-sm flex items-center justify-between {{ $pendingCount > 0 ? 'border-teal-300 ring-2 ring-teal-400/20 bg-teal-50/20' : 'border-slate-100' }}">
            <div>
                <p class="text-[11px] font-extrabold uppercase {{ $pendingCount > 0 ? 'text-teal-700' : 'text-slate-400' }}">بانتظار تحديد الموعد</p>
                <h3 class="text-3xl font-black mt-1 {{ $pendingCount > 0 ? 'text-teal-700' : 'text-slate-800' }}">{{ $pendingCount }}</h3>
                <p class="text-[10px] font-bold text-slate-400 mt-0.5">طلبات جديدة واردة</p>
            </div>
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl {{ $pendingCount > 0 ? 'bg-teal-100 text-teal-700' : 'bg-slate-100 text-slate-400' }}">
                <i class="fa-solid fa-clock"></i>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-extrabold text-slate-400 uppercase">حجوزات مؤكدة بمواعيد</p>
                <h3 class="text-3xl font-black text-emerald-700 mt-1">{{ $confirmedCount }}</h3>
                <p class="text-[10px] text-emerald-600 font-bold mt-0.5">تم تحديد موعد وأخصائي</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-extrabold text-slate-400 uppercase">جلسات مكتملة التقييم</p>
                <h3 class="text-3xl font-black text-purple-700 mt-1">{{ $completedCount }}</h3>
                <p class="text-[10px] text-purple-600 font-bold mt-0.5">تم تقييم الطفل بنجاح</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-extrabold text-slate-400 uppercase">إجمالي طلبات الحجز</p>
                <h3 class="text-3xl font-black text-slate-800 mt-1">{{ $totalCount }}</h3>
                <p class="text-[10px] text-slate-400 font-bold mt-0.5">منذ إطلاق الموقع</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-list-check"></i>
            </div>
        </div>

    </div>

    <!-- ==================== 3. شريط الفلاتر والبحث ==================== -->
    <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm space-y-4">
        
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2 text-xs font-bold">
                <a href="{{ route('bookings.index') }}" class="px-3.5 py-2 rounded-xl transition {{ !request()->has('status') ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    الكل ({{ $totalCount }})
                </a>

                <a href="{{ route('bookings.index', ['status' => 'pending']) }}" class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ request('status') === 'pending' ? 'bg-teal-700 text-white font-black' : 'bg-teal-50 text-teal-800 hover:bg-teal-100 border border-teal-200' }}">
                    <span>بانتظار التأكيد ({{ $pendingCount }})</span>
                </a>

                <a href="{{ route('bookings.index', ['status' => 'confirmed']) }}" class="px-3.5 py-2 rounded-xl transition flex items-center gap-1 {{ request('status') === 'confirmed' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-800 hover:bg-emerald-100 border border-emerald-200' }}">
                    <i class="fa-solid fa-calendar-check text-xs"></i>
                    <span>مواعيد مؤكدة ({{ $confirmedCount }})</span>
                </a>

                <a href="{{ route('bookings.index', ['status' => 'completed']) }}" class="px-3.5 py-2 rounded-xl transition {{ request('status') === 'completed' ? 'bg-purple-600 text-white' : 'bg-purple-50 text-purple-800 hover:bg-purple-100 border border-purple-200' }}">
                    مكتملة التقييم ({{ $completedCount }})
                </a>
            </div>
        </div>

        <form action="{{ route('bookings.index') }}" method="GET" class="flex gap-2 text-xs">
            <div class="relative flex-1">
                <i class="fa-solid fa-magnifying-glass absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث باسم الطفل، اسم ولي الأمر، كود الحجز (BK-2001)، أو رقم الهاتف..." class="w-full pl-3 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-semibold">
            </div>
            <button type="submit" class="px-5 py-2.5 rounded-2xl text-white bg-teal-600 hover:bg-teal-700 font-bold transition flex items-center gap-1.5 shrink-0">
                <span>بحث</span>
            </button>
            @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('bookings.index') }}" class="px-3.5 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold transition flex items-center justify-center" title="إلغاء الفلترة">
                <i class="fa-solid fa-rotate-left"></i>
            </a>
            @endif
        </form>

    </div>

    <!-- ==================== 4. قائمة كروت طلبات الحجز ==================== -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        @forelse($bookings as $bk)
        <div class="bg-white rounded-3xl p-6 border shadow-sm space-y-4 transition hover:border-slate-300 {{ $bk->status === 'pending' ? 'border-teal-200 bg-teal-50/10' : 'border-slate-100' }} flex flex-col justify-between">
            
            <div class="space-y-4">
                
                <!-- رأس الكارت -->
                <div class="flex items-start justify-between gap-3 pb-3 border-b border-slate-100">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-xs font-black px-2 py-0.5 rounded-md bg-slate-100 text-teal-700">{{ $bk->booking_code }}</span>
                            <h4 class="font-black text-base text-slate-900">الطفل: {{ $bk->child_name }}</h4>
                            <span class="text-xs font-bold text-slate-500">({{ $bk->child_age }})</span>
                        </div>
                        <p class="text-xs text-slate-500 font-medium mt-1">
                            ولي الأمر: <strong class="text-slate-800 font-bold">{{ $bk->parent_name }}</strong> • هاتف: <span class="font-mono font-bold text-slate-700">{{ $bk->phone }}</span>
                        </p>
                    </div>

                    <!-- شارة الحالة -->
                    <div>
                        @if($bk->status === 'pending')
                            <span class="px-2.5 py-1 rounded-xl text-[11px] font-black bg-teal-100 text-teal-800 flex items-center gap-1">
                                <i class="fa-solid fa-clock text-[10px]"></i> بانتظار التأكيد
                            </span>
                        @elseif($bk->status === 'confirmed')
                            <span class="px-2.5 py-1 rounded-xl text-[11px] font-black bg-emerald-100 text-emerald-800 flex items-center gap-1">
                                <i class="fa-solid fa-calendar-check text-[10px]"></i> موعد مؤكد
                            </span>
                        @elseif($bk->status === 'completed')
                            <span class="px-2.5 py-1 rounded-xl text-[11px] font-black bg-purple-100 text-purple-800 flex items-center gap-1">
                                <i class="fa-solid fa-check text-[10px]"></i> مكتمل التقييم
                            </span>
                        @else
                            <span class="px-2.5 py-1 rounded-xl text-[11px] font-black bg-slate-100 text-slate-600">
                                ملغي
                            </span>
                        @endif
                    </div>
                </div>

                <!-- تفاصيل الخدمة والملاحظات -->
                <div class="space-y-2 text-xs">
                    <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
                        <div class="flex items-center gap-1.5 font-bold text-slate-800">
                            <i class="fa-solid fa-stethoscope text-teal-600 text-xs"></i>
                            <span>الخدمة المطلوبة: <strong>{{ $bk->service }}</strong></span>
                        </div>
                        @if($bk->notes)
                        <p class="text-slate-600 font-medium text-[11px] leading-relaxed pt-1 border-t border-slate-200">
                            "{{ $bk->notes }}"
                        </p>
                        @endif
                    </div>

                    <!-- في حال تم تحديد الموعد مسبقاً -->
                    @if($bk->status === 'confirmed' && $bk->scheduled_at)
                    <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-950 font-medium space-y-1 text-xs">
                        <div class="flex items-center justify-between font-bold">
                            <span class="flex items-center gap-1.5 text-emerald-900">
                                <i class="fa-solid fa-calendar-day text-emerald-600"></i>
                                <span>الموعد المحدد: <strong>{{ $bk->scheduled_at->format('Y-m-d') }} في تمام {{ $bk->scheduled_at->format('h:i A') }}</strong></span>
                            </span>
                        </div>
                        <p class="text-[11px] text-emerald-800 flex flex-wrap items-center gap-2">
                            <span>الأخصائي: <strong>{{ $bk->specialist_name }}</strong></span> • 
                            <span>القاعة: <strong>{{ $bk->room }}</strong></span> • 
                            <span>سعر الجلسة: <strong class="text-teal-900 font-bold font-mono">{{ $bk->session_price }} ج.م</strong></span> • 
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 border">{{ $bk->payment_status === 'paid' ? 'مدفوع' : 'دفع عند الحضور' }}</span>
                        </p>
                    </div>
                    @endif
                </div>

            </div>

            <!-- أزرار الإجراءات السفلية -->
            <div class="pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2 text-xs font-bold">
                
                <div class="flex items-center gap-2">
                    
                    <!-- زر الموافقة وتحديد الموعد (يفتح المودال) -->
                    <button type="button" @click="openScheduleModal({{ json_encode($bk) }})" class="px-4 py-2 rounded-xl bg-teal-600 text-white font-bold transition flex items-center gap-1.5 shadow-xs hover:bg-teal-700">
                        <i class="fa-solid fa-calendar-plus text-xs"></i>
                        <span>{{ $bk->status === 'confirmed' ? 'تعديل الموعد والأخصائي' : 'الموافقة وتحديد موعد الجلسة' }}</span>
                    </button>

                    <!-- زر رفض الطلب -->
                    @if($bk->status === 'pending')
                    <form action="{{ route('bookings.status', $bk) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من رفض هذا الطلب؟')" class="inline">
                        @csrf
                        <input type="hidden" name="status" value="cancelled">
                        <button type="submit" class="px-3 py-2 rounded-xl bg-rose-50 text-rose-600 font-bold transition flex items-center gap-1 border border-rose-200 hover:bg-rose-100 shadow-xs">
                            <i class="fa-solid fa-xmark text-sm"></i>
                            <span>رفض الطلب</span>
                        </button>
                    </form>
                    @endif

                    <!-- زر واتساب لإرسال رسالة التأكيد المعتمدة -->
                    <a href="{{ $bk->whatsapp_confirmation_url }}" target="_blank" class="px-3 py-2 rounded-xl bg-emerald-500 text-white hover:bg-emerald-600 transition flex items-center gap-1 shadow-sm" title="إرسال تأكيد الموعد عبر واتساب">
                        <i class="fa-brands fa-whatsapp text-sm"></i>
                        <span>تأكيد بالواتساب</span>
                    </a>

                </div>

                <div class="flex items-center gap-2">
                    
                    <!-- زر تحويل إلى ملف طفل -->
                    <a href="{{ route('children.create', ['name' => $bk->child_name, 'phone' => $bk->phone, 'notes' => $bk->notes, 'main_specialist' => $bk->specialist_name]) }}" class="px-3 py-2 rounded-xl bg-purple-600 text-white hover:bg-purple-700 transition flex items-center gap-1 shadow-sm" title="إنشاء بروفايل وملف طفل رسمي">
                        <i class="fa-solid fa-user-plus text-xs"></i>
                        <span>+ ملف طفل</span>
                    </a>

                    <!-- حذف الطلب -->
                    <form action="{{ route('bookings.destroy', $bk) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من رغبتك في حذف هذا الطلب؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 transition" title="حذف">
                            <i class="fa-regular fa-trash-can"></i>
                        </button>
                    </form>
                </div>

            </div>

        </div>
        @empty
        <div class="col-span-2 py-16 text-center text-slate-400 text-xs bg-white rounded-3xl border border-slate-100 p-8 space-y-3">
            <i class="fa-solid fa-calendar-xmark text-4xl text-slate-300"></i>
            <p class="font-extrabold text-sm text-slate-700">لا توجد طلبات حجز مسجلة تطابق معايير البحث.</p>
            <p class="text-slate-400">أي طلب حجز يقدمه ولي الأمر عبر الموقع العام سيظهر هنا فوراً!</p>
        </div>
        @endforelse
    </div>

    <!-- الترقيم -->
    <div class="p-4 bg-white rounded-2xl border border-slate-100">
        {{ $bookings->links() }}
    </div>

    <!-- ==================== 5. مودال الموافقة وتحديد موعد الجلسة والأخصائي ==================== -->
    <div x-show="scheduleModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-xl w-full p-6 md:p-8 space-y-6 shadow-2xl border border-slate-100" @click.away="scheduleModalOpen = false">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-white text-lg bg-teal-600">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-lg text-slate-900">الموافقة وتحديد موعد الجلسة والتقييم</h3>
                        <p class="text-xs text-slate-500 font-semibold" x-text="'للطفل: ' + (selectedBooking ? selectedBooking.child_name : '') + ' • كود: ' + (selectedBooking ? selectedBooking.booking_code : '')"></p>
                    </div>
                </div>

                <button type="button" @click="scheduleModalOpen = false" class="p-2 text-slate-400 hover:text-slate-700 rounded-xl">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- نموذج الموافقة وتحديد الموعد -->
            <template x-if="selectedBooking">
                <form :action="'/bookings/' + selectedBooking.id + '/approve'" method="POST" class="space-y-4 text-xs font-medium">
                    @csrf
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        
                        <!-- تاريخ الجلسة -->
                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">تاريخ الموعد <span class="text-rose-500">*</span></label>
                            <input type="date" name="scheduled_date" x-model="scheduledDate" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold">
                        </div>

                        <!-- وقت وساعة الجلسة -->
                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">وقت الجلسة <span class="text-rose-500">*</span></label>
                            <input type="time" name="scheduled_time" x-model="scheduledTime" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold font-mono">
                        </div>

                        <!-- حقل مركب يرسل scheduled_at كاملاً -->
                        <input type="hidden" name="scheduled_at" :value="scheduledDate + ' ' + scheduledTime">

                        <!-- الأخصائي المعالج المخصص -->
                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">الأخصائي المعالج المخصص <span class="text-rose-500">*</span></label>
                            <select name="specialist_name" x-model="specialistName" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold">
                                @foreach($specialists as $sp)
                                <option value="{{ $sp->name }}">{{ $sp->name }} ({{ $sp->specialization }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- القاعة أو الغرفة -->
                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">القاعة / الغرفة المخصصة <span class="text-rose-500">*</span></label>
                            <select name="room" x-model="roomName" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold">
                                @foreach($rooms as $rm)
                                <option value="{{ $rm }}">{{ $rm }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- سعر ورسوم الجلسة -->
                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">سعر ورسوم الجلسة (ج.م) <span class="text-rose-500">*</span></label>
                            <input type="number" step="0.01" name="session_price" x-model="sessionPrice" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-mono font-bold text-sm text-teal-700">
                        </div>

                        <!-- حالة الدفع -->
                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">حالة الدفع <span class="text-rose-500">*</span></label>
                            <select name="payment_status" x-model="paymentStatus" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold">
                                <option value="unpaid">دفع عند الحضور بالاستقبال</option>
                                <option value="paid">تم السداد مقدماً</option>
                                <option value="deposit">تم دفع عربون حجز</option>
                            </select>
                        </div>

                        <!-- ملاحظات وتوجيهات إدارية -->
                        <div class="sm:col-span-2">
                            <label class="block font-bold text-slate-700 mb-1.5">ملاحظات وتوجيهات الإدارة للاستقبال:</label>
                            <textarea name="admin_notes" x-model="adminNotes" rows="2" placeholder="اكتب أي توجيهات للأخصائي أو موظف الاستقبال..." class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white leading-relaxed"></textarea>
                        </div>

                    </div>

                    <!-- أزرار الإجراءات -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button type="button" @click="scheduleModalOpen = false" class="px-5 py-2.5 rounded-2xl text-slate-500 font-bold hover:bg-slate-100 text-xs">
                            إلغاء
                        </button>
                        <button type="submit" class="px-7 py-3 rounded-2xl text-white font-black text-xs shadow-lg bg-teal-600 hover:bg-teal-700 transition flex items-center gap-2">
                            <i class="fa-solid fa-check"></i>
                            <span>تأكيد الموعد واعتماد الحجز</span>
                        </button>
                    </div>

                </form>
            </template>

        </div>
    </div>

</div>
@endsection

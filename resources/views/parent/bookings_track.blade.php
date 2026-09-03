<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>متابعة حالة الحجز والمواعيد | بوابة ولي الأمر</title>
    
    <!-- Google Cairo Arabic Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Cairo', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <!-- FontAwesome Free 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>

    @php
        $centerSettings = \App\Http\Controllers\SettingController::getSettings();
    @endphp

    <style>
        :root {
            --brand-primary: {{ $centerSettings['primary_color'] ?? '#0d9488' }};
            --brand-secondary: {{ $centerSettings['secondary_color'] ?? '#6366f1' }};
            --brand-accent: {{ $centerSettings['accent_color'] ?? '#f59e0b' }};
        }
        body { font-family: 'Cairo', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased min-h-screen flex flex-col">

    <!-- ==================== الشريط العلوي ==================== -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 h-20 flex items-center justify-between">
            
            <a href="{{ route('website') }}" class="flex items-center gap-3 group">
                <div class="w-11 h-11 rounded-2xl flex items-center justify-center text-white text-xl shadow-md transition group-hover:scale-105" style="background: linear-gradient(135deg, #0d9488 0%, color-mix(in srgb, #0d9488 85%, #000) 100%);">
                    <i class="fa-solid {{ $centerSettings['logo_icon'] ?? 'fa-brain' }}"></i>
                </div>
                <div>
                    <h1 class="font-black text-sm text-slate-900 leading-tight">{{ $centerSettings['center_name'] ?? 'مركز الأمل للتأهيل' }}</h1>
                    <p class="text-[10px] font-bold" style="color: #0d9488;">بوابة أولياء الأمور ومتابعة الحجوزات</p>
                </div>
            </a>

            <div class="flex items-center gap-2">
                <a href="{{ route('parent.portal') }}" class="px-4 py-2 rounded-2xl bg-purple-50 text-purple-700 hover:bg-purple-100 text-xs font-bold transition flex items-center gap-1.5 border border-purple-100">
                    <i class="fa-solid fa-video text-xs"></i>
                    <span class="hidden sm:inline">فيديوهات وتقارير الجلسات</span>
                    <span class="sm:hidden">الفيديوهات</span>
                </a>

                <a href="{{ route('website') }}" class="px-4 py-2 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition flex items-center gap-1.5">
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                    <span>الموقع العام</span>
                </a>
            </div>

        </div>
    </header>

    <!-- ==================== المحتوى الرئيسي ==================== -->
    <main class="flex-1 max-w-5xl w-full mx-auto p-4 sm:p-6 lg:p-8 space-y-8">
        
        <!-- رسائل النجاح والتنبيهات -->
        @if(session('booking_success'))
        <div class="p-5 rounded-3xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-bold flex items-center gap-3 shadow-xs animate-in fade-in">
            <i class="fa-solid fa-circle-check text-emerald-600 text-2xl shrink-0"></i>
            <div>
                <h4 class="font-black text-sm">تم تسجيل طلب الحجز بنجاح</h4>
                <p class="text-emerald-800 mt-0.5">{{ session('booking_success') }}</p>
            </div>
        </div>
        @endif

        <!-- كارت تسجيل الدخول / البحث برقم الهاتف -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <h2 class="text-xl font-black text-slate-900">متابعة حالة حجوزات ومواعيد طفلك</h2>
                    <p class="text-xs text-slate-500 font-medium">أدخل رقم الهاتف الذي سجلت به في نموذج الحجز لعرض حالة الطلب، الموعد المحدد، وسعر الجلسة فور اعتماده</p>
                </div>
            </div>

            <form action="{{ route('parent.bookings.lookup') }}" method="POST" class="flex flex-col sm:flex-row gap-2.5">
                @csrf
                <div class="relative flex-1">
                    <i class="fa-solid fa-phone absolute right-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="phone" value="{{ $phone }}" required placeholder="أدخل رقم الهاتف المسجل به (مثال: 01012345678)..." class="w-full pl-4 pr-11 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white focus:border-teal-500 font-mono font-bold text-sm">
                </div>
                <button type="submit" class="px-8 py-3.5 rounded-2xl text-white font-black text-xs shadow-md hover:opacity-95 transition flex items-center justify-center gap-2" style="background-color: #0d9488;">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <span>عرض حجوزاتي</span>
                </button>
            </form>
        </div>

        <!-- ==================== عرض قائمة الحجوزات ==================== -->
        @if($phone)
        <div class="space-y-6">
            
            <div class="flex items-center justify-between">
                <h3 class="font-black text-lg text-slate-900">سجل طلبات الحجز الخاصة برقم: <span class="font-mono text-teal-700 font-bold">{{ $phone }}</span></h3>
                <span class="text-xs font-bold text-slate-500">{{ $bookings->count() }} طلب مسجل</span>
            </div>

            <div class="space-y-5">
                @forelse($bookings as $bk)
                <div class="bg-white rounded-3xl p-6 sm:p-8 border shadow-sm space-y-6 transition {{ $bk->status === 'confirmed' ? 'border-emerald-300 ring-2 ring-emerald-400/20' : 'border-slate-200' }}">
                    
                    <!-- رأس الكارت -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
                        <div>
                            <div class="flex items-center gap-2.5">
                                <span class="font-mono text-xs font-black px-2.5 py-1 rounded-xl bg-slate-100 text-slate-700" style="color: #0d9488;">{{ $bk->booking_code }}</span>
                                <h4 class="font-black text-lg text-slate-900">الطفل: {{ $bk->child_name }}</h4>
                                <span class="text-xs text-slate-500 font-bold">({{ $bk->child_age }})</span>
                            </div>
                            <p class="text-xs text-slate-500 font-medium mt-1">
                                الخدمة المطلوبة: <strong class="text-slate-800">{{ $bk->service }}</strong> • تاريخ تقديم الطلب: <span class="font-mono">{{ $bk->created_at ? $bk->created_at->format('Y-m-d') : '' }}</span>
                            </p>
                        </div>

                        <!-- شارة الحالة الكبيرة -->
                        <div>
                            @if($bk->status === 'confirmed')
                                <span class="px-4 py-2 rounded-2xl text-xs font-black bg-emerald-100 text-emerald-900 border border-emerald-300 flex items-center gap-2">
                                    <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                                    <span>تم تأكيد وقبول الموعد بنجاح</span>
                                </span>
                            @elseif($bk->status === 'pending')
                                <span class="px-4 py-2 rounded-2xl text-xs font-black bg-teal-100 text-teal-900 border border-teal-200 flex items-center gap-2">
                                    <i class="fa-solid fa-clock text-teal-600 text-sm"></i>
                                    <span>طلبك قيد المراجعة وتحديد الموعد</span>
                                </span>
                            @elseif($bk->status === 'completed')
                                <span class="px-4 py-2 rounded-2xl text-xs font-black bg-purple-100 text-purple-900 flex items-center gap-2">
                                    <i class="fa-solid fa-check-double text-purple-600 text-sm"></i>
                                    <span>تم تنفيذ الجلسة بنجاح</span>
                                </span>
                            @else
                                <span class="px-4 py-2 rounded-2xl text-xs font-black bg-rose-100 text-rose-800 flex items-center gap-1.5 border border-rose-200">
                                    <i class="fa-solid fa-xmark text-rose-600"></i>
                                    مرفوض / ملغي
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- ==================== تفاصيل الموعد والسعر عند التأكيد ==================== -->
                    @if($bk->status === 'confirmed' && $bk->scheduled_at)
                    <div class="rounded-3xl p-6 bg-gradient-to-br from-emerald-500/10 via-teal-500/5 to-slate-50 border border-emerald-200 space-y-5">
                        
                        <div class="flex items-center gap-2 text-emerald-950 font-black text-sm">
                            <i class="fa-solid fa-calendar-check text-emerald-600 text-lg"></i>
                            <span>بيانات موعد الجلسة والتقييم المعتمد:</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
                            
                            <!-- 1. تاريخ ووقت الموعد -->
                            <div class="p-4 rounded-2xl bg-white border border-emerald-200/80 shadow-2xs space-y-1">
                                <span class="text-[10px] font-extrabold text-slate-400 uppercase block">الموعد المحدد:</span>
                                <p class="font-black text-slate-900 text-sm">{{ $bk->scheduled_at->format('Y-m-d') }}</p>
                                <p class="text-xs font-mono font-black text-emerald-700">الساعة {{ $bk->scheduled_at->format('h:i A') }}</p>
                            </div>

                            <!-- 2. الأخصائي المعالج المخصص -->
                            <div class="p-4 rounded-2xl bg-white border border-emerald-200/80 shadow-2xs space-y-1">
                                <span class="text-[10px] font-extrabold text-slate-400 uppercase block">الأخصائي المعالج:</span>
                                <p class="font-black text-slate-900 text-sm">{{ $bk->specialist_name ?? 'الاستشاري المتابع' }}</p>
                                <p class="text-[11px] font-bold text-purple-700">استشاري تأهيل معتمد</p>
                            </div>

                            <!-- 3. القاعة المخصصة -->
                            <div class="p-4 rounded-2xl bg-white border border-emerald-200/80 shadow-2xs space-y-1">
                                <span class="text-[10px] font-extrabold text-slate-400 uppercase block">القاعة / الغرفة:</span>
                                <p class="font-black text-slate-900 text-sm">{{ $bk->room ?? 'غرفة التقييم' }}</p>
                                <p class="text-[11px] font-bold text-slate-500">مجهزة بأحدث الوسائل</p>
                            </div>

                            <!-- 4. سعر ورسوم الجلسة -->
                            <div class="p-4 rounded-2xl bg-white border-2 border-teal-500 shadow-xs space-y-1 text-center sm:text-right">
                                <span class="text-[10px] font-extrabold text-slate-400 uppercase block">سعر ورسوم الجلسة:</span>
                                <p class="font-black text-teal-800 text-lg font-mono">
                                    {{ $bk->session_price }} <span class="text-xs font-sans">{{ $centerSettings['currency'] ?? 'ج.م' }}</span>
                                </p>
                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-black {{ $bk->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-900' }}">
                                    {{ $bk->payment_status === 'paid' ? 'تم السداد' : 'الدفع عند الحضور بالاستقبال' }}
                                </span>
                            </div>

                        </div>

                        <!-- إرشادات الحضور -->
                        <div class="p-4 rounded-2xl bg-white/80 border border-slate-200 text-xs text-slate-600 space-y-1">
                            <p class="font-bold text-slate-800 flex items-center gap-1.5">
                                <i class="fa-solid fa-circle-info text-teal-600"></i>
                                <span>إرشادات وتعليمات الحضور:</span>
                            </p>
                            <p class="leading-relaxed">
                                يرجى الحضور قبل موعد الجلسة بـ 10 دقائق لإتمام إجراءات الاستقبال، وإحضار أي تقارير طبية أو مقاييس ذكاء سابقة إن وجدت.
                            </p>
                        </div>

                    </div>
                    @elseif($bk->status === 'cancelled')
                    <!-- في حالة كان الطلب ملغي/مرفوض -->
                    <div class="rounded-3xl p-6 bg-rose-50 border border-rose-200 text-xs space-y-2 text-rose-700">
                        <div class="flex items-center gap-2 font-black text-rose-900 text-sm">
                            <i class="fa-solid fa-circle-xmark text-rose-600 text-lg"></i>
                            <span>نعتذر، تم إلغاء أو رفض طلب الحجز</span>
                        </div>
                        <p class="leading-relaxed font-bold">
                            عفواً، لم نتمكن من تأكيد حجزك في الوقت الحالي (قد يكون لعدم توفر مواعيد مناسبة أو بناءً على طلبكم).<br>
                            يرجى التواصل مع فريق الاستقبال عبر واتساب أو الهاتف لمزيد من التفاصيل ولتنسيق موعد آخر يناسبكم.
                        </p>
                    </div>
                    @else
                    <!-- في حالة كان الطلب قيد المراجعة -->
                    <div class="rounded-3xl p-6 bg-slate-50 border border-slate-200 text-xs space-y-2 text-slate-600">
                        <div class="flex items-center gap-2 font-bold text-slate-800">
                            <i class="fa-solid fa-hourglass-half text-teal-600"></i>
                            <span>طلبك مسجل في قائمة الانتظار والمراجعة</span>
                        </div>
                        <p class="leading-relaxed">
                            يقوم فريق الاستقبال حالياً بمراجعة طلبك وتنسيق جدول الأخصائيين، وسيتم إرسال رسالة واتساب وتحديث هذه الصفحة فور تحديد الموعد المناسب وسعر الجلسة.
                        </p>
                    </div>
                    @endif

                    <!-- أزرار التواصل والدعم لولي الأمر -->
                    <div class="pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3 text-xs">
                        <div class="flex items-center gap-2">
                            <a href="https://wa.me/2{{ $centerSettings['whatsapp'] ?? '01012345678' }}?text={{ urlencode('مرحباً، أستفسر عن طلب الحجز كود: ' . $bk->booking_code . ' للطفل: ' . $bk->child_name) }}" target="_blank" class="px-4 py-2 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white font-bold transition flex items-center gap-1.5 border border-emerald-200">
                                <i class="fa-brands fa-whatsapp text-sm"></i>
                                <span>مراسلة الاستقبال على واتساب</span>
                            </a>

                            <a href="tel:{{ $centerSettings['phone'] ?? '01012345678' }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition flex items-center gap-1.5">
                                <i class="fa-solid fa-phone text-xs"></i>
                                <span>اتصال هاتفي بالمركز</span>
                            </a>
                        </div>

                        <span class="text-[11px] text-slate-400 font-mono">كود الحجز: {{ $bk->booking_code }}</span>
                    </div>

                </div>
                @empty
                <div class="p-12 text-center bg-white rounded-3xl border border-slate-200 text-slate-400 text-xs font-bold space-y-2">
                    <i class="fa-solid fa-calendar-xmark text-4xl text-slate-300"></i>
                    <p class="text-sm text-slate-700">لا توجد طلبات حجز مسجلة برقم الهاتف هذا.</p>
                    <a href="{{ route('website') }}#booking" class="inline-block px-4 py-2 rounded-xl text-white font-bold text-xs shadow-xs" style="background-color: #0d9488;">
                        + حجز جلسة تقييم أولي الآن
                    </a>
                </div>
                @endforelse
            </div>

        </div>
        @endif

    </main>

    <!-- التذييل -->
    <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-400 font-medium">
        {{ $centerSettings['center_name'] ?? 'مركز الأمل للتخاطب والتأهيل' }} — بوابة أولياء الأمور © {{ date('Y') }}.
    </footer>

</body>
</html>



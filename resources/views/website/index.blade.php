<!DOCTYPE html>
<html lang="ar" dir="rtl" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $centerSettings['center_name'] ?? 'مركز الأمل للتخاطب والتأهيل' }} | الموقع الرسمي</title>
    
    <!-- Google Cairo Arabic Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <?php
        function hexToRgbWebsite($hex) {
            $hex = ltrim($hex, '#');
            if (strlen($hex) == 3) {
                $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
            }
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
            return ["r" => $r, "g" => $g, "b" => $b];
        }
        function mixColorsWebsite($color1, $color2, $weight) {
            $w = $weight / 100;
            $r = round($color1['r'] * $w + $color2['r'] * (1 - $w));
            $g = round($color1['g'] * $w + $color2['g'] * (1 - $w));
            $b = round($color1['b'] * $w + $color2['b'] * (1 - $w));
            return "$r $g $b";
        }
        function generatePaletteWebsite($hex) {
            $base = hexToRgbWebsite($hex);
            $white = ["r" => 255, "g" => 255, "b" => 255];
            $black = ["r" => 0, "g" => 0, "b" => 0];
            return [
                50 => mixColorsWebsite($base, $white, 10),
                100 => mixColorsWebsite($base, $white, 20),
                200 => mixColorsWebsite($base, $white, 40),
                300 => mixColorsWebsite($base, $white, 60),
                400 => mixColorsWebsite($base, $white, 80),
                500 => mixColorsWebsite($base, $white, 100),
                600 => mixColorsWebsite($base, $black, 80),
                700 => mixColorsWebsite($base, $black, 60),
                800 => mixColorsWebsite($base, $black, 40),
                900 => mixColorsWebsite($base, $black, 20),
                950 => mixColorsWebsite($base, $black, 10),
            ];
        }
        $primaryColor = $centerSettings['primary_color'] ?? '#0d9488';
        $secondaryColor = $centerSettings['secondary_color'] ?? '#6366f1';
        $accentColor = $centerSettings['accent_color'] ?? '#f59e0b';
        
        $primaryPalette = generatePaletteWebsite($primaryColor);
        $secondaryPalette = generatePaletteWebsite($secondaryColor);
        $accentPalette = generatePaletteWebsite($accentColor);
        
        $primaryRgb = $primaryPalette[500];
        $secondaryRgb = $secondaryPalette[500];
        $accentRgb = $accentPalette[500];
    ?>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Cairo', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            primary: 'rgb(var(--brand-primary) / <alpha-value>)',
                            secondary: 'rgb(var(--brand-secondary) / <alpha-value>)',
                            accent: 'rgb(var(--brand-accent) / <alpha-value>)',
                        },
                        teal: {
                            50: 'rgb(var(--brand-primary-50) / <alpha-value>)',
                            100: 'rgb(var(--brand-primary-100) / <alpha-value>)',
                            200: 'rgb(var(--brand-primary-200) / <alpha-value>)',
                            300: 'rgb(var(--brand-primary-300) / <alpha-value>)',
                            400: 'rgb(var(--brand-primary-400) / <alpha-value>)',
                            500: 'rgb(var(--brand-primary) / <alpha-value>)',
                            600: 'rgb(var(--brand-primary-600) / <alpha-value>)',
                            700: 'rgb(var(--brand-primary-700) / <alpha-value>)',
                            800: 'rgb(var(--brand-primary-800) / <alpha-value>)',
                            900: 'rgb(var(--brand-primary-900) / <alpha-value>)',
                            950: 'rgb(var(--brand-primary-950) / <alpha-value>)',
                        },
                        indigo: {
                            50: 'rgb(var(--brand-secondary-50) / <alpha-value>)',
                            100: 'rgb(var(--brand-secondary-100) / <alpha-value>)',
                            200: 'rgb(var(--brand-secondary-200) / <alpha-value>)',
                            300: 'rgb(var(--brand-secondary-300) / <alpha-value>)',
                            400: 'rgb(var(--brand-secondary-400) / <alpha-value>)',
                            500: 'rgb(var(--brand-secondary) / <alpha-value>)',
                            600: 'rgb(var(--brand-secondary-600) / <alpha-value>)',
                            700: 'rgb(var(--brand-secondary-700) / <alpha-value>)',
                            800: 'rgb(var(--brand-secondary-800) / <alpha-value>)',
                            900: 'rgb(var(--brand-secondary-900) / <alpha-value>)',
                            950: 'rgb(var(--brand-secondary-950) / <alpha-value>)',
                        },
                        amber: {
                            50: 'rgb(var(--brand-accent-50) / <alpha-value>)',
                            100: 'rgb(var(--brand-accent-100) / <alpha-value>)',
                            200: 'rgb(var(--brand-accent-200) / <alpha-value>)',
                            300: 'rgb(var(--brand-accent-300) / <alpha-value>)',
                            400: 'rgb(var(--brand-accent-400) / <alpha-value>)',
                            500: 'rgb(var(--brand-accent) / <alpha-value>)',
                            600: 'rgb(var(--brand-accent-600) / <alpha-value>)',
                            700: 'rgb(var(--brand-accent-700) / <alpha-value>)',
                            800: 'rgb(var(--brand-accent-800) / <alpha-value>)',
                            900: 'rgb(var(--brand-accent-900) / <alpha-value>)',
                            950: 'rgb(var(--brand-accent-950) / <alpha-value>)',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- FontAwesome Free 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>

    <!-- تخصيص الألوان الديناميكية على مستوى الموقع بالكامل (White-Label Dynamic Branding) -->
    <style>
        :root {
            --brand-primary: {{ $primaryRgb }};
            --brand-secondary: {{ $secondaryRgb }};
            --brand-accent: {{ $accentRgb }};
            
            <?php foreach([50, 100, 200, 300, 400, 600, 700, 800, 900, 950] as $s): ?>
            --brand-primary-<?= $s ?>: <?= $primaryPalette[$s] ?>;
            --brand-secondary-<?= $s ?>: <?= $secondaryPalette[$s] ?>;
            --brand-accent-<?= $s ?>: <?= $accentPalette[$s] ?>;
            <?php endforeach; ?>

            --brand-primary-hex: {{ $primaryColor }};
            --brand-secondary-hex: {{ $secondaryColor }};
            --brand-accent-hex: {{ $accentColor }};
        }
        body { font-family: 'Cairo', sans-serif; }
        [x-cloak] { display: none !important; }

        /* تخصيص السايد بار والأزرار العامة بالموقع */

        .hero-glow {
            background: radial-gradient(circle at 80% 20%, color-mix(in srgb, var(--brand-primary-hex) 12%, transparent) 0%, transparent 60%);
        }

        .card-hover-glow {
            transition: all 0.3s ease;
        }
        .card-hover-glow:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 30px -10px color-mix(in srgb, var(--brand-primary-hex) 20%, transparent);
            border-color: color-mix(in srgb, var(--brand-primary-hex) 40%, transparent);
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-teal-500 selection:text-white" x-data="{ mobileMenuOpen: false }">

    <!-- ==================== 1. الشريط العلوي للتنقل (Navbar) ==================== -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200/80 transition shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            
            <!-- الشعار وهوية المركز -->
            <a href="#hero" class="flex items-center gap-3.5 group">
                <div class="w-11 h-11 rounded-2xl flex items-center justify-center text-white text-xl shadow-lg transition-transform group-hover:scale-105" style="background: linear-gradient(135deg, var(--brand-primary-hex, #0d9488) 0%, color-mix(in srgb, var(--brand-primary-hex, #0d9488) 80%, #000) 100%);">
                    <i class="fa-solid {{ $centerSettings['logo_icon'] ?? 'fa-brain' }}"></i>
                </div>
                <div>
                    <h1 class="font-black text-base text-slate-900 leading-tight">{{ $centerSettings['center_name'] ?? 'مركز الأمل للتخاطب والتأهيل' }}</h1>
                    <p class="text-[11px] font-extrabold" style="color: var(--brand-primary-hex, #0d9488);">{{ $centerSettings['center_slogan'] ?? 'للتأهيل وتنمية المهارات وعلاج النطق' }}</p>
                </div>
            </a>

            <!-- روابط القائمة الرئيسية للكمبيوتر -->
            <nav class="hidden lg:flex items-center gap-7 text-xs font-extrabold text-slate-600">
                <a href="#hero" class="hover:text-slate-900 transition">الرئيسية</a>
                <a href="#about" class="hover:text-slate-900 transition">عن المركز</a>
                <a href="#gallery" class="hover:text-slate-900 transition">مرافق وقاعات المركز</a>
                <a href="#programs" class="hover:text-slate-900 transition">برامجنا التأهيلية</a>
                <a href="#specialists" class="hover:text-slate-900 transition">فريق الأخصائيين</a>
                <a href="#testimonials" class="hover:text-slate-900 transition">آراء الأهالي</a>
                <a href="#booking" class="hover:text-slate-900 transition">حجز تقييم</a>
                <a href="{{ route('parent.bookings.track') }}" class="hover:text-teal-600 font-black text-teal-700 transition">متابعة طلب الحجز</a>
            </nav>

            <!-- أزرار الإجراءات وبوابة الدخول -->
            <div class="hidden sm:flex items-center gap-3">
                <a href="{{ route('parent.portal') }}" class="px-4 py-2.5 rounded-2xl bg-purple-50 text-purple-700 hover:bg-purple-100 text-xs font-bold transition flex items-center gap-1.5 border border-purple-100 shadow-2xs">
                    <i class="fa-solid fa-hands-holding-child text-sm"></i>
                    <span>بوابة ولي الأمر</span>
                </a>

                <a href="{{ route('dashboard') }}" class="px-5 py-2.5 rounded-2xl text-white font-black text-xs shadow-lg hover:opacity-95 active:scale-95 transition flex items-center gap-2" style="background: linear-gradient(135deg, var(--brand-primary-hex, #0d9488) 0%, color-mix(in srgb, var(--brand-primary-hex, #0d9488) 85%, #000) 100%);">
                    <i class="fa-solid fa-lock text-xs"></i>
                    <span>لوحة تحكم المركز</span>
                </a>
            </div>

            <!-- زر القائمة بالموبايل -->
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2.5 text-slate-600 hover:bg-slate-100 rounded-2xl">
                <i class="fa-solid fa-bars text-xl"></i>
            </button>

        </div>

        <!-- القائمة المنسدلة بالموبايل -->
        <div x-show="mobileMenuOpen" x-cloak class="lg:hidden bg-white border-b border-slate-200 px-6 py-5 space-y-4 text-xs font-bold shadow-xl">
            <a href="#hero" @click="mobileMenuOpen = false" class="block py-2 text-slate-800">الرئيسية</a>
            <a href="#about" @click="mobileMenuOpen = false" class="block py-2 text-slate-800">عن المركز</a>
            <a href="#gallery" @click="mobileMenuOpen = false" class="block py-2 text-slate-800">مرافق وقاعات المركز</a>
            <a href="#programs" @click="mobileMenuOpen = false" class="block py-2 text-slate-800">برامجنا التأهيلية</a>
            <a href="#specialists" @click="mobileMenuOpen = false" class="block py-2 text-slate-800">فريق الأخصائيين</a>
            <a href="#testimonials" @click="mobileMenuOpen = false" class="block py-2 text-slate-800">آراء الأهالي</a>
            <a href="#booking" @click="mobileMenuOpen = false" class="block py-2 text-slate-800">حجز تقييم أولي</a>
            <a href="{{ route('parent.bookings.track') }}" @click="mobileMenuOpen = false" class="block py-2 text-teal-700 font-black">متابعة طلب الحجز</a>
            
            <div class="pt-4 border-t border-slate-100 flex flex-col gap-2">
                <a href="{{ route('parent.portal') }}" class="w-full py-2.5 rounded-xl bg-purple-50 text-purple-700 text-center font-bold">بوابة أولياء الأمور</a>
                <a href="{{ route('dashboard') }}" class="w-full py-2.5 rounded-xl text-white text-center font-black" style="background-color: var(--brand-primary-hex, #0d9488);">دخول لوحة التحكم</a>
            </div>
        </div>
    </header>

    <!-- ==================== 2. الواجهة الرئيسية (Hero Section: 2 Columns - النصوص يمين والصورة شمال) ==================== -->
    <section id="hero" class="relative overflow-hidden pt-10 pb-16 lg:pt-16 lg:pb-24 hero-glow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-center">
                
                <!-- ==================== العمود الأيمن: النصوص والشارات وأزرار الحجز ==================== -->
                <div class="lg:col-span-7 space-y-6 text-right">
                    
                    <!-- شارة الترحيب -->
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-black bg-white shadow-xs border border-slate-200" style="color: var(--brand-primary-hex, #0d9488);">
                        <span class="w-2 h-2 rounded-full animate-ping" style="background-color: var(--brand-primary-hex, #0d9488);"></span>
                        <span>المركز الرائد والأحدث للتأهيل اللغوي والحركي والسلوكي للأطفال</span>
                    </div>

                    <!-- العنوان الرئيسي العريض -->
                    <h1 class="text-3xl sm:text-5xl lg:text-5xl font-black text-slate-900 tracking-tight leading-tight sm:leading-tight">
                        نبني مستقبلاً أفضل لطفلك.. خطوة بخطوة نحو <span class="underline decoration-wavy decoration-teal-400" style="color: var(--brand-primary-hex, #0d9488);">النطق والتفوق</span>
                    </h1>

                    <!-- الوصف المختصر -->
                    <p class="text-sm sm:text-base text-slate-600 font-medium leading-relaxed max-w-2xl">
                        نقدم برامج علاجية وتأهيلية فردية متطورة (IEP) تحت إشراف نخبة من استشاريي التخاطب والتكامل الحسي وتعديل السلوك، مع متابعة حية بالفيديو وتطبيق مخصص لولي الأمر.
                    </p>

                    <!-- أزرار الحجز والدخول -->
                    <div class="flex flex-col sm:flex-row items-center gap-3 pt-2">
                        <a href="#booking" class="w-full sm:w-auto px-8 py-4 rounded-2xl text-white font-black text-sm shadow-xl hover:opacity-95 active:scale-95 transition flex items-center justify-center gap-2" style="background: linear-gradient(135deg, var(--brand-primary-hex, #0d9488) 0%, color-mix(in srgb, var(--brand-primary-hex, #0d9488) 85%, #000) 100%);">
                            <i class="fa-solid fa-calendar-check text-base"></i>
                            <span>احجز جلسة تقييم أولي لطفلك</span>
                        </a>

                        <a href="{{ route('parent.portal') }}" class="w-full sm:w-auto px-7 py-4 rounded-2xl bg-white hover:bg-slate-50 text-slate-800 font-extrabold text-sm border border-slate-200 shadow-sm transition flex items-center justify-center gap-2">
                            <i class="fa-solid fa-video text-purple-600"></i>
                            <span>متابعة فيديوهات طفلك بالبوابة</span>
                        </a>
                    </div>

                    <!-- شبكة إحصائيات وبطاقات الثقة (4 بطاقات) -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5 pt-6 text-center">
                        <div class="p-3.5 rounded-2xl bg-white border border-slate-100 shadow-xs">
                            <h4 class="text-2xl font-black text-slate-900" style="color: var(--brand-primary-hex, #0d9488);">+500</h4>
                            <p class="text-[11px] font-bold text-slate-500 mt-0.5">طفل تم تأهيلهم بنجاح</p>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-white border border-slate-100 shadow-xs">
                            <h4 class="text-2xl font-black text-purple-700">100%</h4>
                            <p class="text-[11px] font-bold text-slate-500 mt-0.5">خطط فردية مخصصة (IEP)</p>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-white border border-slate-100 shadow-xs">
                            <h4 class="text-2xl font-black text-blue-700">+10</h4>
                            <p class="text-[11px] font-bold text-slate-500 mt-0.5">استشاريين وأخصائيين</p>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-white border border-slate-100 shadow-xs">
                            <h4 class="text-2xl font-black text-amber-500">4.9/5</h4>
                            <p class="text-[11px] font-bold text-slate-500 mt-0.5">تقييم ورضا أولياء الأمور</p>
                        </div>
                    </div>

                </div>

                <!-- ==================== العمود الأيسر: صورة المركز المحددة من الإعدادات ==================== -->
                <div class="lg:col-span-5 relative">
                    
                    <!-- خلفية إشعاعية متوهجة -->
                    <div class="absolute -inset-2 rounded-3xl opacity-30 blur-2xl transition duration-500" style="background: linear-gradient(135deg, var(--brand-primary-hex, #0d9488), var(--brand-secondary-hex, #6366f1));"></div>

                    <!-- كارت الصورة الرئيسي -->
                    <div class="relative rounded-3xl overflow-hidden shadow-2xl border-4 border-white bg-slate-900 group">
                        
                        <!-- الصورة الفعلية من الإعدادات -->
                        <img src="{{ $centerSettings['hero_image'] ?? 'https://images.unsplash.com/photo-1576765608535-5f04d1e3f289?auto=format&fit=crop&w=1200&q=80' }}" 
                             alt="{{ $centerSettings['center_name'] ?? 'مركز التأهيل' }}" 
                             class="w-full h-80 sm:h-96 lg:h-[430px] object-cover group-hover:scale-105 transition duration-700 ease-out">

                        <!-- تدرج لوني خفيف على الصورة -->
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-black/20"></div>

                        <!-- بادج عائم علوي: اعتماد طبي رسمي -->
                        <div class="absolute top-4 right-4 bg-white/95 backdrop-blur-md px-3.5 py-1.5 rounded-2xl text-[11px] font-black text-slate-800 shadow-lg border border-slate-100 flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <span>اعتماد طبي رسمي وترخيص معتمد</span>
                        </div>

                        <!-- بادج عائم سفلي: قاعات حسية Sensory Room ومتابعة بالفيديو -->
                        <div class="absolute bottom-4 inset-x-4 bg-slate-900/90 backdrop-blur-md p-4 rounded-2xl border border-white/10 text-white space-y-1">
                            <div class="flex items-center justify-between text-xs font-black">
                                <span class="flex items-center gap-1.5" style="color: var(--brand-primary-hex, #0d9488);">
                                    <i class="fa-solid fa-shapes"></i>
                                    <span>قاعات حسية Sensory Room متطورة</span>
                                </span>
                                <span class="text-[10px] text-emerald-400 font-mono">متابعة بالفيديو للأهل</span>
                            </div>
                            <p class="text-[11px] text-slate-300 font-medium">أحدث أجهزة التكامل الحسي والتفريغ الحركي وتنمية المهارات</p>
                        </div>

                    </div>

                </div>

            </div>

        </div>
    </section>

    <!-- ==================== 3. عن المركز ورؤيتنا (About Section) ==================== -->
    <section id="about" class="py-16 bg-white border-y border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                
                <div class="space-y-6">
                    <div class="inline-block px-3.5 py-1 rounded-xl text-xs font-black bg-teal-50 text-teal-800 border border-teal-100">
                        عن المركز ورؤيتنا
                    </div>

                    <h2 class="text-3xl sm:text-4xl font-black text-slate-900 leading-snug">
                        نجمع بين العلم الحديث، الأجهزة المتطورة، والاهتمام الإنساني بكل طفل
                    </h2>

                    <p class="text-sm text-slate-600 font-medium leading-relaxed">
                        في <strong>{{ $centerSettings['center_name'] ?? 'مركز الأمل' }}</strong>، نؤمن بأن كل طفل يمتلك قدرات فريدة قابلة للتطور متى ما وجد التشخيص العلمي الصحيح والبيئة التفاعلية المحفزة.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs font-bold text-slate-800 pt-2">
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-start gap-3">
                            <i class="fa-solid fa-circle-check text-emerald-600 text-lg mt-0.5"></i>
                            <div>
                                <h4 class="font-black text-sm text-slate-900">تقييم سريري شامل</h4>
                                <p class="text-[11px] text-slate-500 font-medium mt-0.5">اختبارات ذكاء ومقاييس لغوية وسلوكية معتمدة</p>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-start gap-3">
                            <i class="fa-solid fa-circle-check text-emerald-600 text-lg mt-0.5"></i>
                            <div>
                                <h4 class="font-black text-sm text-slate-900">قاعات حسية Sensory Room</h4>
                                <p class="text-[11px] text-slate-500 font-medium mt-0.5">أحدث أجهزة التكامل الحسي والتفريغ الحركي</p>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-start gap-3">
                            <i class="fa-solid fa-circle-check text-emerald-600 text-lg mt-0.5"></i>
                            <div>
                                <h4 class="font-black text-sm text-slate-900">تطبيق ولي الأمر بالفيديو</h4>
                                <p class="text-[11px] text-slate-500 font-medium mt-0.5">مشاهدة تطور طفلك وتلقي التمارين المنزلية</p>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-start gap-3">
                            <i class="fa-solid fa-circle-check text-emerald-600 text-lg mt-0.5"></i>
                            <div>
                                <h4 class="font-black text-sm text-slate-900">إشراف مخ وأعصاب</h4>
                                <p class="text-[11px] text-slate-500 font-medium mt-0.5">تكامل طبي مع استشاريي المخ والأعصاب</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- بطاقة بصرية تفاعلية -->
                <div class="relative">
                    <div class="rounded-3xl overflow-hidden bg-gradient-to-tr from-slate-900 via-slate-800 to-teal-950 p-8 sm:p-10 text-white space-y-6 shadow-2xl relative">
                        <div class="w-16 h-16 rounded-2xl bg-white/10 flex items-center justify-center text-3xl text-teal-400">
                            <i class="fa-solid fa-heart-pulse"></i>
                        </div>
                        <h3 class="text-2xl font-black leading-snug">
                            "هدفنا أن نرى ابتسامة طفلك وهو يتحدث بثقة ويتفاعل مع العالم من حوله"
                        </h3>
                        <p class="text-xs text-slate-300 leading-relaxed font-medium">
                            نظام متكامل يضمن استمرارية التدريب بين جلسات المركز والبيت لتحقيق أسرع نتائج ملحوظة في وقت قياسي.
                        </p>
                        
                        <div class="pt-4 border-t border-white/10 flex items-center justify-between text-xs">
                            <span class="font-extrabold text-teal-300">الترخيص والاعتماد الطبي الرسمي</span>
                            <span class="font-mono text-[11px] text-slate-400">MED-LIC-2026</span>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- ==================== 4. معرض صور ومرافق المركز (Gallery Section) ==================== -->
    @if(!empty($centerSettings['gallery_images']))
    <section id="gallery" class="py-16 bg-slate-100/70 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
            <div class="text-center max-w-2xl mx-auto space-y-2">
                <span class="px-3.5 py-1 rounded-xl text-xs font-black bg-white text-teal-800 border border-slate-200">
                    جولة داخل المركز
                </span>
                <h2 class="text-3xl font-black text-slate-900">مرافق وقاعات التأهيل المجهزة</h2>
                <p class="text-xs text-slate-500 font-medium">بيئة آمنة، مبهجة، ومصممة خصيصاً لتشجيع الأطفال على الاستكشاف والتفاعل</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                @foreach($centerSettings['gallery_images'] as $idx => $gPhoto)
                <div class="h-64 rounded-3xl overflow-hidden border-2 border-white shadow-md group relative">
                    <img src="{{ $gPhoto }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition duration-300 flex items-end p-5">
                        <span class="text-white text-xs font-bold">قاعات التأهيل الحديثة</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- ==================== 5. البرامج والتخصصات التأهيلية ==================== -->
    <section id="programs" class="py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            
            <div class="text-center max-w-2xl mx-auto space-y-3">
                <span class="px-3.5 py-1 rounded-xl text-xs font-black bg-purple-50 text-purple-700 border border-purple-100">
                    تخصصاتنا وبرامجنا التأهيلية
                </span>
                <h2 class="text-3xl font-black text-slate-900">برامج متخصصة مصممة لكل حالة بدقة</h2>
                <p class="text-xs text-slate-500 font-medium">نطبق أحدث البروتوكولات المعتمدة عالمياً في علاج النطق والتكامل الحسي وتعديل السلوك</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($programs as $prog)
                <div class="bg-white rounded-3xl p-7 border border-slate-200/80 shadow-xs space-y-5 card-hover-glow flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl shadow-xs" style="background-color: var(--brand-primary-hex, #0d9488)15; color: var(--brand-primary-hex, #0d9488);">
                            <i class="fa-solid {{ $prog['icon'] }}"></i>
                        </div>

                        <h3 class="font-black text-base text-slate-900 leading-snug">{{ $prog['title'] }}</h3>
                        <p class="text-xs text-slate-600 font-medium leading-relaxed">{{ $prog['desc'] }}</p>

                        <div class="space-y-2 pt-2 border-t border-slate-100">
                            @foreach($prog['features'] as $feat)
                            <div class="flex items-center gap-2 text-xs font-bold text-slate-700">
                                <i class="fa-solid fa-check text-[10px]" style="color: var(--brand-primary-hex, #0d9488);"></i>
                                <span>{{ $feat }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <a href="#booking" class="mt-4 pt-3 border-t border-slate-100 font-black text-xs flex items-center justify-between group" style="color: var(--brand-primary-hex, #0d9488);">
                        <span>حجز تقييم لهذا البرنامج</span>
                        <i class="fa-solid fa-arrow-left text-xs transition-transform group-hover:-translate-x-1"></i>
                    </a>
                </div>
                @endforeach
            </div>

        </div>
    </section>

    <!-- ==================== 6. فريق الأخصائيين المعتمدين ==================== -->
    <section id="specialists" class="py-20 bg-white border-y border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            
            <div class="text-center max-w-2xl mx-auto space-y-3">
                <span class="px-3.5 py-1 rounded-xl text-xs font-black bg-cyan-50 text-cyan-800 border border-cyan-100">
                    كادر طبي متميز
                </span>
                <h2 class="text-3xl font-black text-slate-900">نخبة من استشاريي وأخصائيي التأهيل</h2>
                <p class="text-xs text-slate-500 font-medium">فريق متكامل ذو كفاءة وخبرة عالية وشغف كبير في رعاية وتأهيل الأطفال</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($specialists as $sp)
                <div class="bg-slate-50 rounded-3xl p-6 border border-slate-200/80 text-center space-y-4 card-hover-glow">
                    <img src="{{ $sp->avatar_url }}" alt="{{ $sp->name }}" class="w-24 h-24 rounded-3xl object-cover ring-4 ring-white shadow-md mx-auto">
                    
                    <div class="space-y-1">
                        <h4 class="font-black text-base text-slate-900">{{ $sp->name }}</h4>
                        <p class="text-xs font-bold" style="color: var(--brand-primary-hex, #0d9488);">{{ $sp->job_title }}</p>
                        <p class="text-[11px] font-bold text-purple-700">{{ $sp->specialization }}</p>
                    </div>

                    <p class="text-[11px] text-slate-500 font-medium leading-relaxed line-clamp-2">
                        {{ $sp->qualification ?? 'خبرة ' . $sp->experience_years . ' سنوات في التأهيل وعلاج النطق' }}
                    </p>

                    <div class="pt-3 border-t border-slate-200 flex items-center justify-center gap-2">
                        <span class="px-3 py-1 bg-white rounded-xl text-[10px] font-bold text-slate-700 border border-slate-200">
                            {{ $sp->experience_years }} سنوات خبرة
                        </span>
                    </div>
                </div>
                @endforeach
            </div>

        </div>
    </section>

    <!-- ==================== 7. آراء وتجارب أولياء الأمور ==================== -->
    <section id="testimonials" class="py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            
            <div class="text-center max-w-2xl mx-auto space-y-3">
                <span class="px-3.5 py-1 rounded-xl text-xs font-black bg-amber-50 text-amber-800 border border-amber-200">
                    قصص نجاح ملهمة
                </span>
                <h2 class="text-3xl font-black text-slate-900">ماذا يقول أولياء الأمور عن تجربتهم معنا؟</h2>
                <p class="text-xs text-slate-500 font-medium">آراء وتجارب حقيقية تعكس ثقة الأسر في نتائج برامجنا التأهيلية</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($testimonials as $tst)
                <div class="bg-white rounded-3xl p-7 border border-slate-200/80 shadow-xs space-y-4 card-hover-glow flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="flex items-center text-amber-400 text-sm">
                            @for($i = 1; $i <= ($tst->rating ?? 5); $i++)
                                <i class="fa-solid fa-star"></i>
                            @endfor
                        </div>
                        <p class="text-xs text-slate-700 font-medium leading-relaxed italic">
                            "{{ $tst->feedback }}"
                        </p>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                        <div>
                            <h4 class="font-black text-sm text-slate-900">{{ $tst->parent_name }}</h4>
                            <span class="text-[10px] text-teal-700 font-bold">ولي أمر معتمد</span>
                        </div>
                        <div class="w-9 h-9 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center text-sm">
                            <i class="fa-solid fa-quote-left"></i>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

        </div>
    </section>

    <!-- ==================== 8. نموذج حجز تقييم أولي (Booking Section) ==================== -->
    <section id="booking" class="py-20 bg-white border-t border-slate-200">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="bg-slate-50 rounded-3xl p-8 sm:p-12 border border-slate-200 shadow-xl space-y-8">
                
                <div class="text-center space-y-2">
                    <div class="inline-block px-3.5 py-1 rounded-xl text-xs font-black bg-emerald-50 text-emerald-800 border border-emerald-200">
                        احجز موعد لطفلك الآن
                    </div>
                    <h2 class="text-3xl font-black text-slate-900">طلب استشارة وجلسة تقييم أولي</h2>
                    <p class="text-xs text-slate-500 font-medium">املأ البيانات وسيتواصل معك فريق الاستقبال هاتفياً لتحديد الموعد المناسب</p>
                </div>

                @if(session('booking_success'))
                <div class="p-4 rounded-2xl bg-emerald-100 border border-emerald-300 text-emerald-900 font-bold text-xs flex items-center gap-3 animate-in fade-in">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-xl"></i>
                    <span>{{ session('booking_success') }}</span>
                </div>
                @endif

                <form action="{{ route('website.book') }}" method="POST" class="space-y-5 text-xs font-medium">
                    @csrf
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">اسم ولي الأمر <span class="text-rose-500">*</span></label>
                            <input type="text" name="parent_name" required placeholder="مثال: أ. محمد عبد الله" class="w-full p-3.5 bg-white border border-slate-200 rounded-2xl outline-none focus:border-teal-500 font-bold">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">رقم الهاتف / الواتساب <span class="text-rose-500">*</span></label>
                            <input type="text" name="phone" required placeholder="010XXXXXXXX" class="w-full p-3.5 bg-white border border-slate-200 rounded-2xl outline-none focus:border-teal-500 font-mono font-bold">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">اسم الطفل <span class="text-rose-500">*</span></label>
                            <input type="text" name="child_name" required placeholder="مثال: يوسف" class="w-full p-3.5 bg-white border border-slate-200 rounded-2xl outline-none focus:border-teal-500 font-bold">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">عمر الطفل <span class="text-rose-500">*</span></label>
                            <input type="text" name="child_age" required placeholder="مثال: 4 سنوات و 6 أشهر" class="w-full p-3.5 bg-white border border-slate-200 rounded-2xl outline-none focus:border-teal-500 font-bold">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block font-bold text-slate-700 mb-1.5">البرنامج أو الخدمة المطلوبة <span class="text-rose-500">*</span></label>
                            <select name="service" required class="w-full p-3.5 bg-white border border-slate-200 rounded-2xl outline-none focus:border-teal-500 font-bold">
                                @foreach(\App\Http\Controllers\SettingController::getDropdownList("services") as $srv)
                                <option value="{{ $srv }}">{{ $srv }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block font-bold text-slate-700 mb-1.5">ملاحظات إضافية عن حالة الطفل (اختياري)</label>
                            <textarea name="notes" rows="3" placeholder="اكتب ما تلاحظه على طفلك أو أي تشخيصات سابقة..." class="w-full p-3.5 bg-white border border-slate-200 rounded-2xl outline-none focus:border-teal-500 leading-relaxed"></textarea>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-4 rounded-2xl text-white font-black text-sm shadow-xl hover:opacity-95 active:scale-95 transition flex items-center justify-center gap-2" style="background: linear-gradient(135deg, var(--brand-primary-hex, #0d9488) 0%, color-mix(in srgb, var(--brand-primary-hex, #0d9488) 85%, #000) 100%);">
                        <i class="fa-solid fa-paper-plane text-base"></i>
                        <span>إرسال طلب الحجز الآن</span>
                    </button>

                </form>

            </div>

        </div>
    </section>

    <!-- ==================== 9. التذييل ومعلومات التواصل (Footer) ==================== -->
    <footer class="bg-slate-950 text-slate-300 pt-16 pb-12 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 text-xs">
                
                <!-- هوية المركز -->
                <div class="space-y-4 md:col-span-2">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-white text-lg" style="background-color: var(--brand-primary-hex, #0d9488);">
                            <i class="fa-solid {{ $centerSettings['logo_icon'] ?? 'fa-brain' }}"></i>
                        </div>
                        <div>
                            <h3 class="font-black text-base text-white">{{ $centerSettings['center_name'] ?? 'مركز الأمل للتخاطب والتأهيل' }}</h3>
                            <p class="text-[11px] text-teal-400 font-bold">{{ $centerSettings['center_slogan'] ?? 'للتأهيل وتنمية المهارات' }}</p>
                        </div>
                    </div>

                    <p class="text-slate-400 leading-relaxed max-w-md">
                        مركز متخصص في رعاية وتأهيل الأطفال، علاج اضطرابات النطق والكلام، التكامل الحسي، وتعديل السلوك بأحدث التقنيات والخطط الفردية المعتمدة.
                    </p>

                    <div class="flex items-center gap-3 text-lg text-slate-400 pt-2">
                        <a href="#" class="w-9 h-9 rounded-xl bg-white/5 hover:bg-white/10 flex items-center justify-center hover:text-white transition"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" class="w-9 h-9 rounded-xl bg-white/5 hover:bg-white/10 flex items-center justify-center hover:text-white transition"><i class="fa-brands fa-instagram"></i></a>
                        <a href="https://wa.me/2{{ $centerSettings['phone'] ?? '01000000000' }}" target="_blank" class="w-9 h-9 rounded-xl bg-emerald-500/20 text-emerald-400 hover:bg-emerald-500/30 flex items-center justify-center transition"><i class="fa-brands fa-whatsapp"></i></a>
                    </div>
                </div>

                <!-- روابط سريعة -->
                <div class="space-y-3">
                    <h4 class="font-black text-sm text-white">روابط سريعة</h4>
                    <ul class="space-y-2 text-slate-400">
                        <li><a href="#about" class="hover:text-white transition">عن المركز</a></li>
                        <li><a href="#gallery" class="hover:text-white transition">مرافق وقاعات المركز</a></li>
                        <li><a href="#programs" class="hover:text-white transition">البرامج والتخصصات</a></li>
                        <li><a href="#specialists" class="hover:text-white transition">فريق الأخصائيين</a></li>
                        <li><a href="#booking" class="hover:text-white transition">حجز استشارة</a></li>
                        <li><a href="{{ route('parent.bookings.track') }}" class="hover:text-teal-400 transition">متابعة طلب الحجز</a></li>
                        <li><a href="{{ route('parent.portal') }}" class="hover:text-white transition">بوابة أولياء الأمور</a></li>
                        <li><a href="{{ route('dashboard') }}" class="hover:text-white transition">لوحة تحكم الإدارة</a></li>
                    </ul>
                </div>

                <!-- أوقات العمل والاتصال -->
                <div class="space-y-3">
                    <h4 class="font-black text-sm text-white">معلومات الاتصال</h4>
                    <ul class="space-y-2 text-slate-400">
                        <li class="flex items-center gap-2">
                            <i class="fa-solid fa-phone text-teal-400"></i>
                            <span class="font-mono">{{ $centerSettings['phone'] ?? '01012345678' }}</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fa-solid fa-clock text-teal-400"></i>
                            <span>السبت - الخميس: 9:00 ص - 9:00 م</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fa-solid fa-location-dot text-teal-400"></i>
                            <span>{{ $centerSettings['address'] ?? 'القاهرة - مصر' }}</span>
                        </li>
                    </ul>
                </div>

            </div>

            <!-- حقوق الملكية -->
            <div class="pt-8 border-t border-slate-800 text-center text-[11px] text-slate-500 font-medium">
                جميع الحقوق محفوظة © {{ date('Y') }} {{ $centerSettings['center_name'] ?? 'مركز الأمل' }} — مدعوم بنظام نبض التأهيل الإلكتروني.
            </div>

        </div>
    </footer>

</body>
</html>

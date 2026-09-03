<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'نظام إدارة مراكز التخاطب والتأهيل') | {{ $centerSettings['center_name'] ?? 'مركز الأمل' }}</title>
    
    <!-- Google Cairo Arabic Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <?php
        // Convert HEX to RGB and generate palette shades
        function hexToRgb($hex) {
            $hex = ltrim($hex, '#');
            if (strlen($hex) == 3) {
                $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
            }
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
            return ["r" => $r, "g" => $g, "b" => $b];
        }
        function mixColors($color1, $color2, $weight) {
            $w = $weight / 100;
            $r = round($color1['r'] * $w + $color2['r'] * (1 - $w));
            $g = round($color1['g'] * $w + $color2['g'] * (1 - $w));
            $b = round($color1['b'] * $w + $color2['b'] * (1 - $w));
            return "$r $g $b";
        }
        function generatePalette($hex) {
            $base = hexToRgb($hex);
            $white = ["r" => 255, "g" => 255, "b" => 255];
            $black = ["r" => 0, "g" => 0, "b" => 0];
            return [
                50 => mixColors($base, $white, 10),
                100 => mixColors($base, $white, 20),
                200 => mixColors($base, $white, 40),
                300 => mixColors($base, $white, 60),
                400 => mixColors($base, $white, 80),
                500 => mixColors($base, $white, 100),
                600 => mixColors($base, $black, 80),
                700 => mixColors($base, $black, 60),
                800 => mixColors($base, $black, 40),
                900 => mixColors($base, $black, 20),
                950 => mixColors($base, $black, 10),
            ];
        }

        $primaryColor = $centerSettings['primary_color'] ?? '#0d9488';
        $secondaryColor = $centerSettings['secondary_color'] ?? '#6366f1';
        $accentColor = $centerSettings['accent_color'] ?? '#f59e0b';
        
        $primaryPalette = generatePalette($primaryColor);
        $secondaryPalette = generatePalette($secondaryColor);
        $accentPalette = generatePalette($accentColor);
        
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
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Alpine.js for Modern UI Interactions -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>

    <!-- تخصيص الألوان الديناميكية على مستوى النظام والسايد بار بالكامل (White-Label Dynamic Theme) -->
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

        /* تخصيص السايد بار الديناميكي المتفاعل مع ألوان المركز */
        .sidebar-dynamic {
            background: linear-gradient(180deg, #090e17 0%, #0f172a 50%, color-mix(in srgb, var(--brand-primary-hex) 18%, #0b1324) 100%) !important;
            border-left: 1px solid color-mix(in srgb, var(--brand-primary-hex) 25%, transparent) !important;
        }

        .sidebar-header-dynamic {
            background: linear-gradient(90deg, color-mix(in srgb, var(--brand-primary-hex) 15%, transparent) 0%, rgba(15, 23, 42, 0.6) 100%) !important;
            border-bottom: 1px solid color-mix(in srgb, var(--brand-primary-hex) 25%, transparent) !important;
        }

        .sidebar-logo-glow {
            background: linear-gradient(135deg, var(--brand-primary-hex) 0%, color-mix(in srgb, var(--brand-primary-hex) 70%, #000) 100%) !important;
            box-shadow: 0 8px 25px -4px color-mix(in srgb, var(--brand-primary-hex) 55%, transparent) !important;
        }

        .sidebar-link-active {
            background: linear-gradient(135deg, var(--brand-primary-hex) 0%, color-mix(in srgb, var(--brand-primary-hex) 85%, #000) 100%) !important;
            box-shadow: 0 8px 22px -5px color-mix(in srgb, var(--brand-primary-hex) 50%, transparent) !important;
            color: #ffffff !important;
            font-weight: 800 !important;
        }

        .sidebar-link-inactive {
            color: #94a3b8;
            transition: all 0.2s ease-in-out;
        }

        .sidebar-link-inactive:hover {
            background-color: color-mix(in srgb, var(--brand-primary-hex) 18%, transparent) !important;
            color: #ffffff !important;
            transform: translateX(-4px);
        }

        .sidebar-badge-dynamic {
            background-color: color-mix(in srgb, var(--brand-primary-hex) 25%, transparent) !important;
            color: color-mix(in srgb, var(--brand-primary-hex) 85%, #ffffff) !important;
            border: 1px solid color-mix(in srgb, var(--brand-primary-hex) 35%, transparent);
        }

        .sidebar-footer-dynamic {
            background: linear-gradient(180deg, transparent 0%, color-mix(in srgb, var(--brand-primary-hex) 15%, rgba(15, 23, 42, 0.8)) 100%) !important;
            border-top: 1px solid color-mix(in srgb, var(--brand-primary-hex) 25%, transparent) !important;
        }
        
        /* Subtle Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col" x-data="{ sidebarOpen: false }">

    <?php
        $globalNewParentNotesCount = \App\Models\ParentMessage::whereNull('doctor_reply')->count();
        $globalNewParentNotes = \App\Models\ParentMessage::with('child')->whereNull('doctor_reply')->latest()->take(5)->get();

        $globalSpecialistsCount = \App\Models\Specialist::count();
        $globalPendingBookingsCount = \App\Models\ConsultationBooking::where('status', 'pending')->count();
        $globalTodaySessionsCount = \App\Models\SessionSchedule::whereDate('session_date', \Carbon\Carbon::today())->count();
    ?>

    <div class="flex flex-1 min-h-screen">
        
        <!-- ==================== القائمة الجانبية (Sidebar) ==================== -->
        <aside class="w-72 sidebar-dynamic text-slate-200 hidden lg:flex flex-col justify-between shrink-0 shadow-2xl z-30 sticky top-0 h-screen overflow-y-auto">
            <div>
                <!-- الشعار وهوية المركز الديناميكية -->
                <a href="{{ route('dashboard') }}" class="h-20 flex items-center px-6 gap-3.5 sidebar-header-dynamic group">
                    <div class="w-11 h-11 rounded-2xl sidebar-logo-glow flex items-center justify-center text-white text-xl transition-transform group-hover:scale-105">
                        <i class="fa-solid {{ $centerSettings['logo_icon'] ?? 'fa-brain' }}"></i>
                    </div>
                    <div class="overflow-hidden">
                        <h1 class="font-black text-sm text-white tracking-wide truncate">{{ $centerSettings['center_name'] ?? 'مركز الأمل للتخاطب' }}</h1>
                        <p class="text-[10px] font-extrabold truncate" style="color: color-mix(in srgb, var(--brand-primary-hex) 70%, #fff);">{{ $centerSettings['center_slogan'] ?? 'للتأهيل وتنمية المهارات' }}</p>
                    </div>
                </a>

                <!-- قائمة الروابط النشطة -->
                <div class="p-4 space-y-6">
                    <div>
                        <p class="px-3 text-[10px] font-extrabold uppercase tracking-wider mb-2" style="color: color-mix(in srgb, var(--brand-primary-hex) 55%, #64748b);">القائمة الرئيسية</p>
                        <nav class="space-y-1.5 text-sm font-semibold">
                            
                            <!-- شاشة الانتظار للمركز -->
                            <a href="{{ route('center.screen') }}" target="_blank" class="flex items-center gap-3.5 px-3.5 py-3 rounded-2xl transition bg-white/5 text-slate-300 hover:bg-white/10 hover:text-white border border-white/10 mb-2">
                                <i class="fa-solid fa-tv w-5 text-center text-base text-indigo-400"></i>
                                <span>شاشة المركز (الانتظار)</span>
                                <i class="fa-solid fa-arrow-up-right-from-square mr-auto text-[11px] text-slate-400"></i>
                            </a>

                            <!-- الموقع الإلكتروني العام -->
                            @if(!Auth::check() || Auth::user()->role !== 'parent')
                            <a href="{{ route('website') }}" target="_blank" class="flex items-center gap-3.5 px-3.5 py-3 rounded-2xl transition bg-white/5 text-slate-300 hover:bg-white/10 hover:text-white border border-white/10 mb-2">
                                <i class="fa-solid fa-globe w-5 text-center text-base text-emerald-400"></i>
                                <span>الموقع الإلكتروني العام</span>
                                <i class="fa-solid fa-arrow-up-right-from-square mr-auto text-[11px] text-slate-400"></i>
                            </a>
                            @endif

                        @if(Auth::check() && Auth::user()->role === 'parent')
                            <?php
                                $newVideosCount = 0;
                                $newMediaCount = 0;
                                
                                $parentChild = \App\Models\Child::where('user_id', Auth::id())->first();
                                if(!$parentChild) {
                                    $parentChild = \App\Models\Child::where('national_id', Auth::user()->username)->first();
                                }
                                
                                if($parentChild) {
                                    $newVideosCount = \App\Models\TherapySession::where('child_id', $parentChild->id)
                                        ->whereNotNull('video_path')
                                        ->where('created_at', '>=', now()->subDays(3))
                                        ->count();
                                        
                                    $newMediaCount = \App\Models\ChildMedia::where('child_id', $parentChild->id)
                                        ->where('created_at', '>=', now()->subDays(3))
                                        ->count();
                                }
                            ?>
                            <!-- بوابة ولي الأمر - قوائم تفاعلية -->
                            <a href="{{ route('parent.portal') }}?tab=calendar" class="flex items-center gap-3.5 px-3.5 py-3 rounded-2xl transition {{ request('tab', 'calendar') === 'calendar' ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                                <i class="fa-solid fa-calendar-days w-5 text-center text-base {{ request('tab', 'calendar') === 'calendar' ? 'text-white' : 'text-amber-400' }}"></i>
                                <span>مواعيد الجلسات</span>
                            </a>
                            <a href="{{ route('parent.portal') }}?tab=videos" class="flex items-center gap-3.5 px-3.5 py-3 rounded-2xl transition {{ request('tab') === 'videos' ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                                <i class="fa-solid fa-video w-5 text-center text-base {{ request('tab') === 'videos' ? 'text-white' : 'text-teal-400' }}"></i>
                                <span>فيديوهات وتقارير</span>
                                @if(isset($newVideosCount) && $newVideosCount > 0)
                                    <span class="mr-auto px-2 py-0.5 rounded-full bg-rose-500 text-white text-[10px] font-black animate-pulse">{{ $newVideosCount }}</span>
                                @endif
                            </a>
                            <a href="{{ route('parent.portal') }}?tab=goals" class="flex items-center gap-3.5 px-3.5 py-3 rounded-2xl transition {{ request('tab') === 'goals' ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                                <i class="fa-solid fa-bullseye w-5 text-center text-base {{ request('tab') === 'goals' ? 'text-white' : 'text-rose-400' }}"></i>
                                <span>الأهداف العلاجية</span>
                            </a>
                            <a href="{{ route('parent.portal') }}?tab=messages" class="flex items-center gap-3.5 px-3.5 py-3 rounded-2xl transition {{ request('tab') === 'messages' ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                                <i class="fa-solid fa-envelope w-5 text-center text-base {{ request('tab') === 'messages' ? 'text-white' : 'text-indigo-400' }}"></i>
                                <span>مراسلة الأخصائي</span>
                            </a>
                            <a href="{{ route('parent.portal') }}?tab=rating" class="flex items-center gap-3.5 px-3.5 py-3 rounded-2xl transition {{ request('tab') === 'rating' ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                                <i class="fa-solid fa-star w-5 text-center text-base {{ request('tab') === 'rating' ? 'text-white' : 'text-amber-400' }}"></i>
                                <span>تقييم الجلسات</span>
                            </a>
                            <a href="{{ route('parent.portal') }}?tab=reports" class="flex items-center gap-3.5 px-3.5 py-3 rounded-2xl transition {{ request('tab') === 'reports' ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                                <i class="fa-solid fa-dumbbell w-5 text-center text-base {{ request('tab') === 'reports' ? 'text-white' : 'text-emerald-400' }}"></i>
                                <span>التمارين المنزلية</span>
                            </a>
                            <a href="{{ route('parent.portal') }}?tab=media" class="flex items-center gap-3.5 px-3.5 py-3 rounded-2xl transition {{ request('tab') === 'media' ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                                <i class="fa-solid fa-photo-film w-5 text-center text-base {{ request('tab') === 'media' ? 'text-white' : 'text-purple-400' }}"></i>
                                <span>مكتبة الملفات</span>
                                @if(isset($newMediaCount) && $newMediaCount > 0)
                                    <span class="mr-auto px-2 py-0.5 rounded-full bg-rose-500 text-white text-[10px] font-black animate-pulse">{{ $newMediaCount }}</span>
                                @endif
                            </a>
                        @elseif(Auth::check() && Auth::user()->role === 'specialist')
                            <!-- بوابة الأخصائي -->
                            <a href="{{ route('doctor.portal') }}" class="flex items-center gap-3.5 px-3.5 py-3 rounded-2xl transition {{ request()->routeIs('doctor.portal') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                                <i class="fa-solid fa-user-doctor w-5 text-center text-base {{ request()->routeIs('doctor.portal') ? 'text-white' : 'text-blue-400' }}"></i>
                                <span>بوابة الأخصائي (تسجيل)</span>
                            </a>
                            <a href="{{ route('doctor.timetable') }}" class="flex items-center gap-3.5 px-3.5 py-3 rounded-2xl transition {{ request()->routeIs('doctor.timetable') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                                <i class="fa-solid fa-calendar-check w-5 text-center text-base {{ request()->routeIs('doctor.timetable') ? 'text-white' : 'text-amber-400' }}"></i>
                                <span>جدول مواعيدي اليوم</span>
                            </a>
                            <a href="{{ route('children.index') }}" class="flex items-center gap-3.5 px-3.5 py-3 rounded-2xl transition {{ request()->routeIs('children.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                                <i class="fa-solid fa-child-reaching w-5 text-center text-base {{ request()->routeIs('children.*') ? 'text-white' : 'text-emerald-400' }}"></i>
                                <span>ملفات الأطفال (IEP)</span>
                            </a>
                            <!-- مكتبة الملفات والفيديوهات -->
                            <a href="{{ route('media.index') }}" class="flex items-center gap-3.5 px-3.5 py-3 rounded-2xl transition {{ request()->routeIs('media.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                                <i class="fa-solid fa-photo-film w-5 text-center text-base {{ request()->routeIs('media.*') ? 'text-white' : 'text-purple-400' }}"></i>
                                <span>الملفات والفيديوهات</span>
                            </a>
                        @else
                            <!-- لوحة التحكم -->
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-3.5 px-3.5 py-3 rounded-2xl transition {{ request()->routeIs('dashboard') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                                <i class="fa-solid fa-chart-pie w-5 text-center text-base {{ request()->routeIs('dashboard') ? 'text-white' : '' }}"></i>
                                <span>لوحة التحكم</span>
                            </a>

                            <!-- شاشة الـ QR للحضور السريع -->
                            <a href="{{ route('attendance.screen') }}" target="_blank" class="flex items-center gap-3.5 px-3.5 py-3 rounded-2xl transition bg-purple-50/10 text-purple-200 hover:bg-purple-500/20 hover:text-white border border-purple-500/30">
                                <i class="fa-solid fa-qrcode w-5 text-center text-base text-purple-400"></i>
                                <span>شاشة الحضور (QR)</span>
                            </a>

                            <!-- شاشة النداء الصوتي للمركز -->
                            <a href="{{ route('center.screen') }}" target="_blank" class="flex items-center gap-3.5 px-3.5 py-3 rounded-2xl transition bg-blue-50/10 text-blue-200 hover:bg-blue-500/20 hover:text-white border border-blue-500/30 mt-2">
                                <i class="fa-solid fa-volume-high w-5 text-center text-base text-blue-400"></i>
                                <span>شاشة النداء الصوتي</span>
                            </a>

                            <!-- جدول وكالندر الجلسات العام -->
                            <a href="{{ route('calendar.index') }}" class="flex items-center gap-3.5 px-3.5 py-3 rounded-2xl transition {{ request()->routeIs('calendar.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                                <i class="fa-solid fa-calendar-days w-5 text-center text-base {{ request()->routeIs('calendar.*') ? 'text-white' : 'text-amber-400' }}"></i>
                                <span>كالندر وجدول الجلسات</span>
                            </a>

                            <!-- مكتبة الملفات والفيديوهات -->
                            <a href="{{ route('media.index') }}" class="flex items-center gap-3.5 px-3.5 py-3 rounded-2xl transition {{ request()->routeIs('media.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                                <i class="fa-solid fa-photo-film w-5 text-center text-base {{ request()->routeIs('media.*') ? 'text-white' : 'text-purple-400' }}"></i>
                                <span>الملفات والفيديوهات</span>
                            </a>

                            <!-- طلبات الحجز والمواعيد -->
                            <a href="{{ route('bookings.index') }}" class="flex items-center gap-3.5 px-3.5 py-3 rounded-2xl transition {{ request()->routeIs('bookings.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                                <i class="fa-solid fa-calendar-check w-5 text-center text-base {{ request()->routeIs('bookings.*') ? 'text-white' : 'text-teal-400' }}"></i>
                                <span>طلبات الحجز والمواعيد</span>
                            </a>

                            <!-- قائمة الانتظار -->
                            <a href="{{ route('waitlists.index') }}" class="flex items-center gap-3.5 px-3.5 py-3 rounded-2xl transition {{ request()->routeIs('waitlists.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                                <i class="fa-solid fa-hourglass-half w-5 text-center text-base {{ request()->routeIs('waitlists.*') ? 'text-white' : 'text-amber-400' }}"></i>
                                <span>قائمة الانتظار</span>
                            </a>

                            <!-- ملفات الأطفال -->
                            <a href="{{ route('children.index') }}" class="flex items-center gap-3.5 px-3.5 py-3 rounded-2xl transition {{ request()->routeIs('children.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                                <i class="fa-solid fa-child-reaching w-5 text-center text-base {{ request()->routeIs('children.*') ? 'text-white' : 'text-emerald-400' }}"></i>
                                <span>ملفات الأطفال (IEP)</span>
                            </a>

                            <!-- فريق الأخصائيين والتأهيل -->
                            <a href="{{ route('specialists.index') }}" class="flex items-center gap-3.5 px-3.5 py-3 rounded-2xl transition {{ request()->routeIs('specialists.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                                <i class="fa-solid fa-user-tie w-5 text-center text-base {{ request()->routeIs('specialists.*') ? 'text-white' : 'text-cyan-400' }}"></i>
                                <span>فريق الأخصائيين</span>
                            </a>

                            <!-- بوابة الأخصائي / جدول وتوثيق الجلسات -->
                            <a href="{{ route('doctor.timetable') }}" class="flex items-center gap-3.5 px-3.5 py-3 rounded-2xl transition {{ request()->routeIs('doctor.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                                <i class="fa-solid fa-user-doctor w-5 text-center text-base {{ request()->routeIs('doctor.*') ? 'text-white' : 'text-blue-400' }}"></i>
                                <span>جدول وبوابة الأخصائي</span>
                            </a>

                            <!-- ملاحظات وشكاوى أولياء الأمور -->
                            <a href="{{ route('admin.parent-notes.index') }}" class="flex items-center gap-3.5 px-3.5 py-3 rounded-2xl transition relative group {{ request()->routeIs('admin.parent-notes.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                                <i class="fa-solid fa-envelope-open-text w-5 text-center text-base {{ request()->routeIs('admin.parent-notes.*') ? 'text-white' : 'text-amber-400' }}"></i>
                                <span>ملاحظات وشكاوى الأهل</span>
                            </a>

                            <!-- الماليات والفواتير -->
                            <a href="{{ route('finances.index') }}" class="flex items-center gap-3.5 px-3.5 py-3 rounded-2xl transition {{ request()->routeIs('finances.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                                <i class="fa-solid fa-file-invoice-dollar w-5 text-center text-base {{ request()->routeIs('finances.*') ? 'text-white' : 'text-emerald-400' }}"></i>
                                <span>الماليات والفواتير</span>
                            </a>

                            <!-- ديون أولياء الأمور -->
                            <a href="{{ route('debts.index') }}" class="flex items-center gap-3.5 px-3.5 py-3 rounded-2xl transition {{ request()->routeIs('debts.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                                <i class="fa-solid fa-hand-holding-dollar w-5 text-center text-base {{ request()->routeIs('debts.*') ? 'text-white' : 'text-slate-400' }}"></i>
                                <span>ديون أولياء الأمور</span>
                            </a>

                            <!-- إدارة المصروفات -->
                            <a href="{{ route('expenses.index') }}" class="flex items-center gap-3.5 px-3.5 py-3 rounded-2xl transition {{ request()->routeIs('expenses.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                                <i class="fa-solid fa-money-bill-transfer w-5 text-center text-base {{ request()->routeIs('expenses.*') ? 'text-white' : 'text-rose-400' }}"></i>
                                <span>المصروفات</span>
                            </a>

                            <!-- المرتبات والموظفين -->
                            <a href="{{ route('payroll.index') }}" class="flex items-center gap-3.5 px-3.5 py-3 rounded-2xl transition {{ request()->routeIs('payroll.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                                <i class="fa-solid fa-money-check-dollar w-5 text-center text-base {{ request()->routeIs('payroll.*') ? 'text-white' : 'text-emerald-400' }}"></i>
                                <span>المرتبات والموظفين</span>
                            </a>

                            <!-- التقارير -->
                            <a href="{{ route('reports.index') }}" class="flex items-center gap-3.5 px-3.5 py-3 rounded-2xl transition {{ request()->routeIs('reports.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                                <i class="fa-solid fa-chart-line w-5 text-center text-base {{ request()->routeIs('reports.*') ? 'text-white' : 'text-indigo-400' }}"></i>
                                <span>التقارير والإحصائيات</span>
                            </a>

                            <!-- إعدادات وهوية المركز -->
                            <a href="{{ route('settings.index') }}" class="flex items-center gap-3.5 px-3.5 py-3 rounded-2xl transition {{ request()->routeIs('settings.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                                <i class="fa-solid fa-palette w-5 text-center text-base {{ request()->routeIs('settings.*') ? 'text-white' : 'text-pink-400' }}"></i>
                                <span>إعدادات وهوية المركز</span>
                            </a>
                        @endif

                        </nav>
                    </div>
                </div>
            </div>

            <!-- ملف المستخدم في الأسفل -->
            <div class="p-4 sidebar-footer-dynamic">
                @if(auth()->guard()->check())
                <div class="flex items-center gap-3 mb-3">
                    <img src="https://api.dicebear.com/7.x/avataaars/svg?seed={{ Auth::user()->username }}" alt="User" class="w-10 h-10 rounded-xl bg-slate-800 ring-2" style="--tw-ring-color: var(--brand-primary-hex, #0d9488);">
                    <div class="overflow-hidden">
                        <p class="text-sm font-bold text-white truncate">{{ Auth::user()->name }}</p>
                        <p class="text-[11px] font-extrabold" style="color: color-mix(in srgb, var(--brand-primary-hex) 80%, #fff);">
                            {{ Auth::user()->role === 'admin' ? 'مدير النظام' : (Auth::user()->role === 'specialist' ? 'أخصائي' : 'ولي أمر') }}

                        </p>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full py-2 bg-slate-800/50 hover:bg-rose-500/80 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i> تسجيل الخروج
                    </button>
                </form>
                @endif
            </div>
        </aside>

        <!-- ==================== المحتوى الرئيسي (Main Content) ==================== -->
        <div class="flex-1 flex flex-col min-w-0">
            
            <!-- الشريط العلوي (Top Header) -->
            <header class="h-20 bg-white border-b border-slate-200 px-4 md:px-8 flex items-center justify-between sticky top-0 z-20 shadow-xs">
                
                <!-- زر القائمة في الموبايل + البحث السريع -->
                <div class="flex items-center gap-3 flex-1 max-w-xl">
                    <button class="lg:hidden p-2 text-slate-600 hover:bg-slate-100 rounded-xl" @click="sidebarOpen = !sidebarOpen">
                        <i class="fa-solid fa-bars text-xl"></i>
                    </button>
                    
                    @if(Auth::check() && Auth::user()->role !== 'parent')
                    <form action="{{ route('children.index') }}" method="GET" class="relative w-full">
                        <i class="fa-solid fa-magnifying-glass absolute right-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" 
                               name="search"
                               placeholder="بحث سريع: كود الطفل (مثال: CH-1001)، اسم الطفل، أو هاتف ولي الأمر..." 
                               class="w-full pl-4 pr-11 py-2.5 bg-slate-100/90 rounded-2xl text-sm border border-transparent focus:bg-white focus:ring-4 outline-none transition" style="--tw-ring-color: color-mix(in srgb, var(--brand-primary-hex) 20%, transparent); border-color: transparent;">
                    </form>
                    @endif
                </div>

                <!-- أزرار الإجراءات السريعة والإشعارات -->
                <div class="flex items-center gap-3">
                    @if(Auth::check() && Auth::user()->role !== 'parent')
                    
                    <!-- زر كالندر اليوم السريع -->
                    <a href="{{ route('calendar.index') }}" class="hidden sm:flex items-center gap-2 px-3.5 py-2 bg-amber-50 text-amber-800 hover:bg-amber-100 rounded-2xl text-xs font-bold transition border border-amber-200">
                        <i class="fa-solid fa-calendar-day text-amber-600"></i>
                        <span>جدول اليوم ({{ $globalTodaySessionsCount }})</span>
                    </a>

                    <!-- زر شاشة الانتظار (المركز) -->
                    <a href="{{ route('center.screen') }}" target="_blank" class="hidden sm:flex items-center gap-2 px-3.5 py-2 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-2xl text-xs font-bold transition border border-indigo-100 shadow-sm">
                        <i class="fa-solid fa-tv text-xs"></i>
                        <span>شاشة المركز</span>
                    </a>

                    <!-- زر زيارة الموقع الإلكتروني العام -->
                    <a href="{{ route('website') }}" target="_blank" class="hidden sm:flex items-center gap-2 px-3.5 py-2 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-2xl text-xs font-bold transition border border-emerald-100 shadow-sm">
                        <i class="fa-solid fa-globe text-xs"></i>
                        <span>الموقع العام</span>
                    </a>

                    <!-- زر تنبيه طلبات الحجز الجديدة -->
                    @if($globalPendingBookingsCount > 0)
                    <a href="{{ route('bookings.index') }}" class="flex items-center gap-2 px-3.5 py-2 rounded-2xl bg-teal-50 text-teal-800 border border-teal-200 text-xs font-black shadow-xs hover:bg-teal-100 transition">
                        <i class="fa-solid fa-calendar-check text-teal-600"></i>
                        <span>{{ $globalPendingBookingsCount }} طلبات حجز</span>
                    </a>
                    @endif

                    <!-- زر تنبيه الملاحظات الجديدة للأهل في الهيدر -->
                    @if($globalNewParentNotesCount > 0)
                    <a href="{{ route('admin.parent-notes.index') }}" class="flex items-center gap-2 px-3.5 py-2 rounded-2xl bg-rose-50 text-rose-700 border border-rose-200 text-xs font-black shadow-xs hover:bg-rose-100 transition">
                        <i class="fa-solid fa-bell text-rose-600"></i>
                        <span>{{ $globalNewParentNotesCount }} ملاحظات جديدة</span>
                    </a>
                    @endif

                    <!-- جرس الإشعارات (دروب منيو) -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" @click.away="open = false" class="relative w-11 h-11 rounded-2xl bg-slate-100 text-slate-600 flex items-center justify-center hover:bg-slate-200 transition focus:outline-none" title="{{ $globalNewParentNotesCount > 0 ? $globalNewParentNotesCount . ' ملاحظات جديدة' : 'لا توجد إشعارات جديدة' }}">
                            <i class="fa-regular fa-bell text-lg"></i>
                            @if($globalNewParentNotesCount > 0)
                                <span class="absolute top-2 right-2 w-3 h-3 bg-rose-500 rounded-full ring-2 ring-white animate-pulse"></span>
                            @endif
                        </button>
                        
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                             x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                             class="absolute left-0 mt-3 w-80 bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden z-50 origin-top-left"
                             x-cloak>
                             
                            <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                                <h3 class="font-black text-slate-700">الإشعارات</h3>
                                @if($globalNewParentNotesCount > 0)
                                    <span class="bg-rose-100 text-rose-700 text-[10px] px-2 py-0.5 rounded-full font-bold">{{ $globalNewParentNotesCount }} جديد</span>
                                @endif
                            </div>

                            <div class="max-h-[320px] overflow-y-auto overscroll-contain">
                                @forelse($globalNewParentNotes as $note)
                                    <a href="{{ route('admin.parent-notes.index') }}" class="block p-4 border-b border-slate-50 hover:bg-slate-50 transition relative">
                                        @if($note->is_urgent)
                                            <div class="absolute left-4 top-4 w-2 h-2 rounded-full bg-rose-500 animate-ping"></div>
                                            <div class="absolute left-4 top-4 w-2 h-2 rounded-full bg-rose-500"></div>
                                        @else
                                            <div class="absolute left-4 top-4 w-2 h-2 rounded-full bg-slate-300"></div>
                                        @endif
                                        <div class="flex items-start gap-3">
                                            <div class="w-10 h-10 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                                                <i class="fa-solid fa-envelope-open-text text-sm"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h4 class="text-xs font-bold text-slate-700 mb-1 truncate">{{ $note->subject ?? 'ملاحظة من ولي الأمر' }}</h4>
                                                <p class="text-[11px] text-slate-500 truncate mb-1">من: {{ $note->parent_name }}</p>
                                                <span class="text-[9px] text-slate-400"><i class="fa-regular fa-clock ml-1"></i>{{ $note->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </a>
                                @empty
                                    <div class="p-8 text-center flex flex-col items-center justify-center">
                                        <i class="fa-solid fa-bell-slash text-3xl text-slate-200 mb-3"></i>
                                        <p class="text-xs font-bold text-slate-500">لا توجد إشعارات جديدة</p>
                                    </div>
                                @endforelse
                            </div>
                            
                            <a href="{{ route('admin.parent-notes.index') }}" class="block p-3 text-center text-xs font-bold text-rose-600 hover:bg-rose-50 hover:text-rose-700 transition bg-white border-t border-slate-100">
                                عرض كل الملاحظات الإدارية
                            </a>
                        </div>
                    </div>
                    @endif
                </div>

            </header>

            <!-- جسم الصفحة الرئيسي -->
            <main class="p-4 md:p-8 space-y-8 flex-1">
                @yield('content')
            </main>

            <!-- التذييل (Footer) -->
            <footer class="bg-white border-t border-slate-200/80 px-8 py-4 text-center text-xs text-slate-400 font-medium">
                {{ $centerSettings['center_name'] ?? 'مركز الأمل للتخاطب والتأهيل' }} — مدعوم بنظام نبض التأهيل © 2026.
            </footer>

        </div>

    </div>

    <!-- Notification Sound -->
    <audio id="urgentSound" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" preload="auto"></audio>
    <audio id="bookingSound" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" preload="auto"></audio>

    <script>
    // ==================== فتح قفل الصوت عند أول تفاعل مع الصفحة ====================
    (function() {
        let audioUnlocked = false;
        function unlockAudio() {
            if (audioUnlocked) return;
            // Play and immediately pause all audio elements to unlock them
            document.querySelectorAll('audio').forEach(a => {
                a.play().then(() => { a.pause(); a.currentTime = 0; }).catch(() => {});
            });
            // Unlock Web Audio API context
            if (window._notifAudioCtx) window._notifAudioCtx.resume();
            audioUnlocked = true;
        }
        ['click', 'touchstart', 'keydown', 'scroll'].forEach(evt => {
            document.addEventListener(evt, unlockAudio, { once: false, passive: true });
        });

        // Web Audio API — beep generator (works without external files)
        try {
            window._notifAudioCtx = new (window.AudioContext || window.webkitAudioContext)();
        } catch(e) {}

        window.playNotificationBeep = function() {
            // Try HTML audio first
            let snd = document.getElementById('bookingSound');
            let played = false;
            if (snd) {
                snd.volume = 1.0;
                snd.currentTime = 0;
                let p = snd.play();
                if (p) p.then(() => { played = true; }).catch(() => {});
            }

            // Always also play Web Audio beep as backup (louder & reliable)
            try {
                let ctx = window._notifAudioCtx;
                if (!ctx) return;
                if (ctx.state === 'suspended') ctx.resume();

                function beep(freq, startTime, duration) {
                    let osc = ctx.createOscillator();
                    let gain = ctx.createGain();
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.type = 'sine';
                    osc.frequency.value = freq;
                    gain.gain.setValueAtTime(0.6, startTime);
                    gain.gain.exponentialRampToValueAtTime(0.01, startTime + duration);
                    osc.start(startTime);
                    osc.stop(startTime + duration);
                }

                let now = ctx.currentTime;
                // Pleasant 3-tone chime: Do - Mi - Sol
                beep(523, now, 0.25);        // C5
                beep(659, now + 0.28, 0.25); // E5
                beep(784, now + 0.56, 0.4);  // G5

                // Repeat after 1.5 seconds
                beep(523, now + 1.5, 0.25);
                beep(659, now + 1.78, 0.25);
                beep(784, now + 2.06, 0.4);
            } catch(e) {}
        };
    })();
    </script>

    <script>
        @if(Auth::check() && in_array(Auth::user()->role, ['admin', 'specialist', 'reception']))
        document.addEventListener('DOMContentLoaded', function() {
            let notifiedIds = JSON.parse(localStorage.getItem('notifiedUrgentIds') || '[]');

            function checkUrgentNotifications() {
                fetch('{{ route("api.urgent-notifications") }}')
                    .then(res => res.json())
                    .then(data => {
                        let notifications = data.notifications;
                        if (notifications && notifications.length > 0) {
                            let tempIgnored = JSON.parse(sessionStorage.getItem('temporarilyIgnoredUrgentIds') || '{}');
                            let newNotifications = notifications.filter(n => {
                                if (notifiedIds.includes(n.id)) return false;
                                if (tempIgnored[n.id] && Date.now() < tempIgnored[n.id]) return false;
                                return true;
                            });
                            
                            if (newNotifications.length > 0) {
                                // Play Sound
                                document.getElementById('urgentSound').play().catch(e => console.log('Audio play failed:', e));
                                
                                // Show Big Alert for the most recent one
                                let latest = newNotifications[0];
                                
                                let isDayApology = latest.title && latest.title.includes('اعتذار طارئ عن يوم عمل');
                                let headerHtml = '';
                                
                                if (isDayApology) {
                                    let specName = latest.sender.replace('الأخصائي: ', '');
                                    headerHtml = `<div style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 10px; margin-bottom: 20px; font-size: 1.8em; font-weight: bold; border: 2px solid #f5c6cb;">الأخصائي: <span style="color: #d9534f;">${specName}</span></div>`;
                                } else {
                                    headerHtml = `<div style="background-color: #fff3cd; color: #856404; padding: 15px; border-radius: 10px; margin-bottom: 20px; font-size: 1.8em; font-weight: bold; border: 2px solid #ffeeba;">الطفل: <span style="color: #d9534f;">${latest.child_name || ''}</span></div>`;
                                }

                                Swal.fire({
                                    icon: 'warning',
                                    title: 'إشعار عاجل!',
                                    html: `
                                        ${headerHtml}
                                        <span style="font-size: 1.2em; color: #333; font-weight: bold;">${latest.title}</span><br><br>
                                        <span style="font-size: 1.1em; color: #555;">${latest.body}</span><br>
                                        ${latest.affected_html || ''}
                                        <br>
                                        <small style="color: #777;">تم الإرسال بواسطة: ${latest.sender}</small>
                                    `,
                                    showDenyButton: !isDayApology,
                                    showCancelButton: true,
                                    confirmButtonText: 'حسناً، فهمت',
                                    denyButtonText: 'تسكين طفل آخر',
                                    cancelButtonText: 'ذكرني لاحقاً',
                                    confirmButtonColor: '#0d9488',
                                    denyButtonColor: '#f59e0b',
                                    cancelButtonColor: '#64748b',
                                    width: '600px',
                                    backdrop: `rgba(0,0,0,0.6)`
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        // حسناً، فهمت
                                        notifiedIds.push(latest.id);
                                        if (notifiedIds.length > 100) notifiedIds = notifiedIds.slice(-100);
                                        localStorage.setItem('notifiedUrgentIds', JSON.stringify(notifiedIds));
                                    } else if (result.isDenied) {
                                        // تسكين طفل آخر
                                        notifiedIds.push(latest.id);
                                        if (notifiedIds.length > 100) notifiedIds = notifiedIds.slice(-100);
                                        localStorage.setItem('notifiedUrgentIds', JSON.stringify(notifiedIds));
                                        window.location.href = '{{ route("calendar.index") }}';
                                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                                        // ذكرني لاحقاً (بعد 15 دقيقة)
                                        let tempIgnored = JSON.parse(sessionStorage.getItem('temporarilyIgnoredUrgentIds') || '{}');
                                        tempIgnored[latest.id] = Date.now() + (15 * 60 * 1000);
                                        sessionStorage.setItem('temporarilyIgnoredUrgentIds', JSON.stringify(tempIgnored));
                                    }
                                });

                                // Mark all other fetched notifications as notified so they don't pile up
                                if (newNotifications.length > 1) {
                                    for (let i = 1; i < newNotifications.length; i++) {
                                        notifiedIds.push(newNotifications[i].id);
                                    }
                                    if (notifiedIds.length > 100) notifiedIds = notifiedIds.slice(-100);
                                    localStorage.setItem('notifiedUrgentIds', JSON.stringify(notifiedIds));
                                }
                            }
                        }
                    })
                    .catch(err => console.error('Error fetching notifications:', err));
            }

            // Check immediately, then every 10 seconds
            checkUrgentNotifications();
            setInterval(checkUrgentNotifications, 10000);

            @if(in_array(Auth::user()->role, ['admin', 'reception']))
            // ==================== إشعارات طلبات الحجز الجديدة (Real-time) ====================
            let notifiedBookingIds = JSON.parse(localStorage.getItem('notifiedBookingIds') || '[]');
            let snoozedBookingIds = JSON.parse(sessionStorage.getItem('snoozedBookingIds') || '{}');

            function playBookingSound() {
                if (window.playNotificationBeep) {
                    window.playNotificationBeep();
                }
            }

            function checkNewBookings() {
                fetch('{{ route("api.new-bookings") }}')
                    .then(res => res.json())
                    .then(data => {
                        if (data.bookings && data.bookings.length > 0) {
                            // Clean expired snoozes
                            let now = Date.now();
                            Object.keys(snoozedBookingIds).forEach(k => {
                                if (now >= snoozedBookingIds[k]) delete snoozedBookingIds[k];
                            });
                            sessionStorage.setItem('snoozedBookingIds', JSON.stringify(snoozedBookingIds));

                            let newOnes = data.bookings.filter(b => {
                                if (notifiedBookingIds.includes(b.id)) return false;
                                if (snoozedBookingIds[b.id] && Date.now() < snoozedBookingIds[b.id]) return false;
                                return true;
                            });

                            if (newOnes.length > 0) {
                                playBookingSound();

                                let latest = newOnes[0];

                                let bookingsListHtml = '';
                                if (newOnes.length > 1) {
                                    bookingsListHtml = '<div style="margin-top:12px; padding:10px; background:#f0fdf4; border-radius:12px; border:1px solid #bbf7d0; text-align:right;">';
                                    bookingsListHtml += '<span style="font-size:0.85em; font-weight:800; color:#166534;">+ ' + (newOnes.length - 1) + ' طلبات حجز أخرى بانتظار المراجعة</span>';
                                    bookingsListHtml += '</div>';
                                }

                                Swal.fire({
                                    icon: 'info',
                                    title: '🔔 طلب حجز جديد!',
                                    html: `
                                        <div style="background:#eff6ff; padding:15px; border-radius:12px; border:1px solid #bfdbfe; margin-bottom:15px; text-align:right;">
                                            <div style="font-size:1.3em; font-weight:900; color:#1e40af; margin-bottom:8px;">
                                                <i class="fa-solid fa-child-reaching" style="margin-left:6px;"></i>
                                                ${latest.child_name}
                                            </div>
                                            <div style="font-size:0.95em; color:#334155; font-weight:600;">
                                                <i class="fa-solid fa-user" style="margin-left:4px; color:#6366f1;"></i>
                                                ولي الأمر: ${latest.parent_name}
                                            </div>
                                            <div style="font-size:0.9em; color:#475569; margin-top:4px;">
                                                <i class="fa-solid fa-phone" style="margin-left:4px; color:#0d9488;"></i>
                                                ${latest.phone}
                                            </div>
                                            <div style="font-size:0.9em; color:#475569; margin-top:4px;">
                                                <i class="fa-solid fa-stethoscope" style="margin-left:4px; color:#f59e0b;"></i>
                                                ${latest.service}
                                            </div>
                                        </div>
                                        <div style="font-size:0.8em; color:#94a3b8; text-align:right;">
                                            <i class="fa-regular fa-clock" style="margin-left:4px;"></i>
                                            ${latest.created_at} • كود: ${latest.code}
                                        </div>
                                        ${bookingsListHtml}
                                    `,
                                    showDenyButton: true,
                                    showCancelButton: true,
                                    confirmButtonText: '<i class="fa-solid fa-calendar-check"></i> فتح طلبات الحجز',
                                    denyButtonText: '<i class="fa-solid fa-clock"></i> ذكرني لاحقاً',
                                    cancelButtonText: 'حسناً، فهمت',
                                    confirmButtonColor: '#0d9488',
                                    denyButtonColor: '#f59e0b',
                                    cancelButtonColor: '#64748b',
                                    width: '520px',
                                    backdrop: 'rgba(0,0,0,0.5)',
                                    allowOutsideClick: false,
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        // فتح طلبات الحجز — mark as notified
                                        newOnes.forEach(b => notifiedBookingIds.push(b.id));
                                        if (notifiedBookingIds.length > 200) notifiedBookingIds = notifiedBookingIds.slice(-200);
                                        localStorage.setItem('notifiedBookingIds', JSON.stringify(notifiedBookingIds));
                                        window.location.href = '{{ route("bookings.index") }}';
                                    } else if (result.isDenied) {
                                        // ذكرني لاحقاً — snooze 10 minutes
                                        newOnes.forEach(b => {
                                            snoozedBookingIds[b.id] = Date.now() + (10 * 60 * 1000);
                                        });
                                        sessionStorage.setItem('snoozedBookingIds', JSON.stringify(snoozedBookingIds));
                                    } else {
                                        // حسناً، فهمت — mark as notified permanently
                                        newOnes.forEach(b => notifiedBookingIds.push(b.id));
                                        if (notifiedBookingIds.length > 200) notifiedBookingIds = notifiedBookingIds.slice(-200);
                                        localStorage.setItem('notifiedBookingIds', JSON.stringify(notifiedBookingIds));
                                    }
                                });
                            }
                        }
                    })
                    .catch(err => console.error('Error fetching new bookings:', err));
            }

            // Check immediately, then every 10 seconds for real-time feel
            checkNewBookings();
            setInterval(checkNewBookings, 10000);
            @endif
        });
        @endif
    </script>

    <?php echo $__env->yieldPushContent('modals'); ?>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH D:\Projects\Centers\Centers\resources\views\layouts\app.blade.php ENDPATH**/ ?>
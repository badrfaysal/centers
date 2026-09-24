<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير طبي وتأهيلي شامل — {{ $child->name }} ({{ $child->code }})</title>
    
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
    
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>

    <style>
        :root {
            --brand-primary: {{ $centerSettings['primary_color'] ?? '#0d9488' }};
            --brand-secondary: {{ $centerSettings['secondary_color'] ?? '#6366f1' }};
        }
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        [x-cloak] { display: none !important; }

        /* تنسيقات ورقة الطباعة A4 المعتمدة القياسية */
        @page {
            size: A4 portrait;
            margin: 8mm 10mm 8mm 10mm;
        }

        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .page-container {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                margin: 0 !important;
                max-width: 100% !important;
                width: 100% !important;
            }
            .page-break-inside-avoid {
                page-break-inside: avoid;
                break-inside: avoid;
            }
        }

        .header-line {
            height: 3px;
            background: linear-gradient(90deg, #0d9488 0%, #0d9488 100%);
        }
    </style>
</head>
<body class="py-6 px-4" x-data="{ 
    showOptions: true,
    sections: {
        header: true,
        demographics: true,
        photo: true,
        medical: true,
        iep: true,
        sessions: true,
        signatures: true
    },
    setPreset(type) {
        if (type === 'all') {
            this.sections = { header: true, demographics: true, photo: true, medical: true, iep: true, sessions: true, signatures: true };
        } else if (type === 'parent') {
            this.sections = { header: true, demographics: true, photo: true, medical: false, iep: true, sessions: true, signatures: true };
        } else if (type === 'medical') {
            this.sections = { header: true, demographics: true, photo: false, medical: true, iep: false, sessions: false, signatures: true };
        } else if (type === 'iep_only') {
            this.sections = { header: true, demographics: true, photo: true, medical: false, iep: true, sessions: false, signatures: false };
        }
    }
}">

    <!-- ==================== شريط التحكم واختيار أقسام الطباعة (يختفي في الطباعة) ==================== -->
    <div class="no-print max-w-4xl mx-auto mb-6 bg-white p-5 rounded-3xl shadow-xl border border-slate-200 space-y-4 sticky top-4 z-50">
        
        <div class="flex flex-wrap items-center justify-between gap-4 pb-3 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl flex items-center justify-center text-white text-lg shadow-md" style="background-color: #0d9488;">
                    <i class="fa-solid fa-sliders"></i>
                </div>
                <div>
                    <h3 class="font-black text-sm text-slate-800">تخصيص واختيار أقسام التقرير المطبوع (A4)</h3>
                    <p class="text-xs text-slate-400 font-semibold">حدد أو الغِ تحديد أي قسم ترغب في إظهاره أو إخفائه من الورقة المطبوعة</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button onclick="window.print()" class="px-7 py-3 rounded-2xl text-white font-black text-xs shadow-lg hover:opacity-95 active:scale-95 transition flex items-center gap-2" style="background: linear-gradient(135deg, #0d9488 0%, #0f172a 100%);">
                    <i class="fa-solid fa-print text-sm text-amber-400"></i>
                    <span>طباعة التقرير المخصص (A4)</span>
                </button>
                
                <a href="{{ route('children.show', $child) }}" class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl text-xs font-bold transition">
                    إغلاق والعودة
                </a>
            </div>
        </div>

        <!-- أزرار النماذج الجاهزة السريعة (Presets) -->
        <div class="flex flex-wrap items-center gap-2 text-xs font-bold">
            <span class="text-slate-400 text-[11px] ml-1">نماذج سريعة جاهزة:</span>
            <button type="button" @click="setPreset('all')" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                 تقرير شامل كامل
            </button>
            <button type="button" @click="setPreset('parent')" class="px-3 py-1.5 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-700 transition border border-purple-100">
                 تقرير ولي الأمر والتمارين
            </button>
            <button type="button" @click="setPreset('medical')" class="px-3 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 transition border border-blue-100">
                 تقرير طبي سريري للمخ والأعصاب
            </button>
            <button type="button" @click="setPreset('iep_only')" class="px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 transition border border-emerald-100">
                 خطة الأهداف فقط
            </button>
        </div>

        <!-- مربعات الاختيار التفاعلية لتحديد كل قسم (Section Toggles) -->
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-2.5 pt-2 text-xs font-bold">
            
            <!-- 1. الترويسة -->
            <label class="p-2.5 rounded-2xl border cursor-pointer transition flex items-center gap-2 select-none" :class="sections.header ? 'bg-teal-50/80 border-teal-300 text-teal-950 ring-1 ring-teal-300' : 'bg-slate-50 border-slate-200 text-slate-400'">
                <input type="checkbox" x-model="sections.header" class="rounded text-teal-600 focus:ring-0">
                <span class="truncate">ترويسة المركز</span>
            </label>

            <!-- 2. بيانات الطفل -->
            <label class="p-2.5 rounded-2xl border cursor-pointer transition flex items-center gap-2 select-none" :class="sections.demographics ? 'bg-teal-50/80 border-teal-300 text-teal-950 ring-1 ring-teal-300' : 'bg-slate-50 border-slate-200 text-slate-400'">
                <input type="checkbox" x-model="sections.demographics" class="rounded text-teal-600 focus:ring-0">
                <span class="truncate">بيانات الطفل</span>
            </label>

            <!-- 3. صورة الطفل -->
            <label class="p-2.5 rounded-2xl border cursor-pointer transition flex items-center gap-2 select-none" :class="sections.photo ? 'bg-teal-50/80 border-teal-300 text-teal-950 ring-1 ring-teal-300' : 'bg-slate-50 border-slate-200 text-slate-400'">
                <input type="checkbox" x-model="sections.photo" class="rounded text-teal-600 focus:ring-0">
                <span class="truncate">صورة الطفل</span>
            </label>

            <!-- 4. التشخيصات والملف الطبي -->
            <label class="p-2.5 rounded-2xl border cursor-pointer transition flex items-center gap-2 select-none" :class="sections.medical ? 'bg-teal-50/80 border-teal-300 text-teal-950 ring-1 ring-teal-300' : 'bg-slate-50 border-slate-200 text-slate-400'">
                <input type="checkbox" x-model="sections.medical" class="rounded text-teal-600 focus:ring-0">
                <span class="truncate">الملف الطبي والأدوية</span>
            </label>

            <!-- 5. الخطة العلاجية -->
            <label class="p-2.5 rounded-2xl border cursor-pointer transition flex items-center gap-2 select-none" :class="sections.iep ? 'bg-teal-50/80 border-teal-300 text-teal-950 ring-1 ring-teal-300' : 'bg-slate-50 border-slate-200 text-slate-400'">
                <input type="checkbox" x-model="sections.iep" class="rounded text-teal-600 focus:ring-0">
                <span class="truncate">الخطة العلاجية</span>
            </label>

            <!-- 6. ملخص الجلسات -->
            <label class="p-2.5 rounded-2xl border cursor-pointer transition flex items-center gap-2 select-none" :class="sections.sessions ? 'bg-teal-50/80 border-teal-300 text-teal-950 ring-1 ring-teal-300' : 'bg-slate-50 border-slate-200 text-slate-400'">
                <input type="checkbox" x-model="sections.sessions" class="rounded text-teal-600 focus:ring-0">
                <span class="truncate">الجلسات والواجب</span>
            </label>

            <!-- 7. التوقيعات والأختام -->
            <label class="p-2.5 rounded-2xl border cursor-pointer transition flex items-center gap-2 select-none" :class="sections.signatures ? 'bg-teal-50/80 border-teal-300 text-teal-950 ring-1 ring-teal-300' : 'bg-slate-50 border-slate-200 text-slate-400'">
                <input type="checkbox" x-model="sections.signatures" class="rounded text-teal-600 focus:ring-0">
                <span class="truncate">التوقيع والختم</span>
            </label>

        </div>

    </div>

    <!-- ==================== مستند التقرير الطبي A4 (The A4 Document) ==================== -->
    <div class="page-container max-w-[210mm] mx-auto bg-white p-8 sm:p-10 rounded-3xl shadow-xl border border-slate-200 space-y-4 text-slate-800">
        
        <!-- 1. ترويسة التقرير الرسمية للمركز (Official Header) -->
        <div x-show="sections.header" class="space-y-2">
            <div class="flex items-center justify-between pb-3 border-b-2 border-slate-800">
                
                <!-- يمين: بيانات المركز -->
                <div class="space-y-0.5 text-right">
                    <h1 class="font-black text-lg text-slate-900 leading-tight">{{ $centerSettings['center_name'] ?? 'مركز الأمل للتخاطب والتأهيل' }}</h1>
                    <p class="text-[11px] font-extrabold" style="color: #0d9488;">{{ $centerSettings['center_slogan'] ?? 'مركز معتمد للتأهيل وتنمية المهارات وعلاج النطق' }}</p>
                    <p class="text-[10px] text-slate-500 font-medium font-mono">هاتف: {{ $centerSettings['phone'] ?? '01000000000' }} • ترخيص رقم: 2026/MED/104</p>
                </div>

                <!-- وسط: شعار وهوية المركز -->
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-white text-2xl shadow-sm border border-slate-300 shrink-0" style="background: linear-gradient(135deg, #0d9488 0%, #0f172a 100%);">
                    <i class="fa-solid {{ $centerSettings['logo_icon'] ?? 'fa-brain' }}"></i>
                </div>

                <!-- يسار: بيانات إصدار التقرير -->
                <div class="space-y-1 text-left font-mono text-[10px]">
                    <p><span class="text-slate-400 font-sans font-bold">تاريخ التقرير:</span> <strong>{{ date('Y/m/d') }}</strong></p>
                    <p><span class="text-slate-400 font-sans font-bold">رقم المرجع:</span> <strong class="text-slate-800">{{ 'RPT-' . $child->code . '-' . date('Y') }}</strong></p>
                    <p class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-sans font-bold inline-block border border-slate-300">ملف طبي رسمي</p>
                </div>

            </div>

            <!-- شريط التدرج اللوني الفاصل -->
            <div class="header-line rounded-full"></div>
        </div>

        <!-- شريط عنوان التقرير -->
        <div class="text-center py-2 px-4 rounded-xl bg-slate-900 text-white space-y-0.5">
            <h2 class="font-black text-sm tracking-wide">تقرير تأهيلي وطبي سريري شامل (Comprehensive Clinical Report)</h2>
            <p class="text-[10px] text-slate-300 font-medium">الخطة الفردية، التقييم السريري، ونسب التطور وملاحظات الجلسات التأهيلية</p>
        </div>

        <!-- ==================== 2. بطاقة الهوية والبيانات الأساسية للطفل ==================== -->
        <div x-show="sections.demographics" class="border border-slate-300 rounded-2xl p-4 bg-slate-50/70 space-y-3">
            <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                <span class="font-black text-xs text-slate-900 flex items-center gap-1.5">
                    <i class="fa-solid fa-id-card text-teal-700"></i>
                    <span>أولاً: بيانات الطفل الأساسية والهوية</span>
                </span>
                <span class="font-mono text-xs font-black px-2.5 py-0.5 rounded-lg bg-white border border-slate-300" style="color: #0d9488;">
                    كود الطفل: {{ $child->code }}
                </span>
            </div>

            <div class="grid grid-cols-4 gap-3 text-[11px] items-center">
                
                <!-- صورة الطفل (يمكن إظهارها أو إخفاؤها) -->
                <template x-if="sections.photo">
                    <div class="col-span-1 flex flex-col items-center justify-center p-2 bg-white rounded-xl border border-slate-200">
                        <img src="{{ $child->avatar_url }}" alt="child" class="w-20 h-20 rounded-xl object-cover ring-1 ring-slate-200">
                        <span class="text-[9px] font-bold text-slate-400 mt-1">{{ $child->status === 'active' ? 'نشط بالخطة' : 'معلق' }}</span>
                    </div>
                </template>

                <!-- جدول التفاصيل -->
                <div :class="sections.photo ? 'col-span-3' : 'col-span-4'" class="grid grid-cols-2 gap-2 font-medium">
                    <div class="p-2 bg-white rounded-lg border border-slate-200">
                        <span class="text-slate-400 block text-[10px] font-bold">اسم الطفل الرباعي:</span>
                        <strong class="text-slate-900 text-xs font-black">{{ $child->name }}</strong>
                    </div>

                    <div class="p-2 bg-white rounded-lg border border-slate-200">
                        <span class="text-slate-400 block text-[10px] font-bold">العمر الزمني / العقلي:</span>
                        <strong class="text-slate-900">{{ $child->age_text }}</strong> • <span class="text-purple-700 font-bold">العقلي: {{ $child->mental_age ?? 'غير محدد' }}</span>
                    </div>

                    <div class="p-2 bg-white rounded-lg border border-slate-200">
                        <span class="text-slate-400 block text-[10px] font-bold">تاريخ الميلاد والنوع:</span>
                        <strong class="text-slate-800">{{ $child->birth_date ? $child->birth_date->format('Y/m/d') : 'غير محدد' }}</strong> ({{ $child->gender === 'male' ? 'ذكر' : 'أنثى' }})
                    </div>

                    <div class="p-2 bg-white rounded-lg border border-slate-200">
                        <span class="text-slate-400 block text-[10px] font-bold">الأخصائي المعالج المتابع:</span>
                        <strong class="text-slate-800">{{ $child->main_specialist ?? 'د. أحمد يسري' }}</strong>
                    </div>

                    <div class="col-span-2 p-2 bg-white rounded-lg border border-slate-200">
                        <span class="text-slate-400 block text-[10px] font-bold">بيانات ولي الأمر والاتصال:</span>
                        <strong class="text-slate-800">{{ $child->parent_name }}</strong> ({{ $child->parent_relation }}) — هاتف: <span class="font-mono font-bold">{{ $child->phone }}</span> • الطوارئ: <span class="font-mono">{{ $child->emergency_phone ?? 'لا يوجد' }}</span>
                    </div>
                </div>

            </div>
        </div>

        <!-- ==================== 3. التشخيصات الطبية والمخ والأعصاب ==================== -->
        <div x-show="sections.medical" class="border border-slate-300 rounded-2xl p-4 bg-white space-y-3 page-break-inside-avoid">
            <span class="font-black text-xs text-slate-900 flex items-center gap-1.5 border-b border-slate-200 pb-2">
                <i class="fa-solid fa-stethoscope text-purple-700"></i>
                <span>ثانياً: التشخيصات الطبية المعتمدة وملف المخ والأعصاب والأدوية</span>
            </span>

            <!-- سطور التشخيصات المستقلة -->
            <div class="space-y-1">
                <span class="text-[10px] font-bold text-slate-400 block">سطور التشخيصات السريرية المعتمدة:</span>
                <div class="grid grid-cols-2 gap-1.5">
                    @foreach($child->diagnoses_list as $idx => $dg)
                    <div class="p-2 rounded-xl bg-purple-50 border border-purple-200 text-purple-950 font-extrabold text-[11px] flex items-center gap-2">
                        <span class="w-4 h-4 rounded-full bg-purple-200 text-purple-900 flex items-center justify-center text-[9px] font-bold shrink-0">{{ $idx + 1 }}</span>
                        <span>{{ $dg }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- جدول الأدوية والمخ والأعصاب واختبارات الذكاء -->
            <div class="grid grid-cols-2 gap-2 text-[11px] pt-1">
                <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-200 space-y-1">
                    <span class="text-slate-500 font-bold block text-[10px]">طبيب المخ والأعصاب المتابع:</span>
                    <p class="font-bold text-slate-900">{{ $child->neurologist_name ?? 'لا يوجد طبيب مخ وأعصاب مسجل' }}</p>
                </div>

                <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-200 space-y-1">
                    <span class="text-slate-500 font-bold block text-[10px]">الأدوية والعلاجات الحالية:</span>
                    <p class="font-bold text-slate-900">{{ $child->current_medications ?? 'لا يتناول أدوية حالياً' }}</p>
                </div>

                <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-200 space-y-1">
                    <span class="text-slate-500 font-bold block text-[10px]">سجل اختبارات ومقاييس الذكاء السابقة:</span>
                    <p class="font-semibold text-slate-800">{{ $child->iq_tests_history ?? 'لم تسجل اختبارات ذكاء سابقة' }}</p>
                </div>

                <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-200 space-y-1">
                    <span class="text-slate-500 font-bold block text-[10px]">الأجهزة المساعدة والتاريخ المرضي:</span>
                    <p class="font-semibold text-slate-800">{{ $child->assistive_devices ?? 'لا توجد أجهزة مساعدة' }} • {{ $child->medical_notes ?? 'تاريخ طبي طبيعي' }}</p>
                </div>
            </div>

            <!-- سجل الاختبارات والتقييمات المسجلة -->
            @if($child->tests->count() > 0)
            <div class="space-y-1.5 pt-1">
                <span class="text-[10px] font-black text-slate-500 flex items-center gap-1">
                    <i class="fa-solid fa-clipboard-check text-indigo-600"></i>
                    سجل الاختبارات والتقييمات النفسية والعقلية المسجلة ({{ $child->tests->count() }} اختبار):
                </span>
                <table class="w-full text-right text-[10px] border-collapse">
                    <thead>
                        <tr class="bg-indigo-50 text-indigo-800 font-bold border-b border-indigo-200">
                            <th class="py-1.5 px-2 w-8">#</th>
                            <th class="py-1.5 px-2">اسم الاختبار / المقياس</th>
                            <th class="py-1.5 px-2 w-24">تاريخ الاختبار</th>
                            <th class="py-1.5 px-2 w-24">الدرجة / النتيجة</th>
                            <th class="py-1.5 px-2">ملاحظات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-800 font-medium">
                        @foreach($child->tests as $ti => $test)
                        <tr>
                            <td class="py-1.5 px-2 text-slate-400 font-mono">{{ $ti + 1 }}</td>
                            <td class="py-1.5 px-2 font-bold text-slate-900">{{ $test->test_name }}</td>
                            <td class="py-1.5 px-2 font-mono">{{ $test->test_date->format('Y/m/d') }}</td>
                            <td class="py-1.5 px-2">
                                @if($test->score)
                                    <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-800 font-bold border border-emerald-200">{{ $test->score }}</span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="py-1.5 px-2 text-slate-600">{{ $test->notes ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        <!-- ==================== 4. الخطة العلاجية الفردية ==================== -->
        <div x-show="sections.iep" class="border border-slate-300 rounded-2xl p-4 bg-white space-y-3 page-break-inside-avoid">
            <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                <span class="font-black text-xs text-slate-900 flex items-center gap-1.5">
                    <i class="fa-solid fa-bullseye text-teal-700"></i>
                    <span>ثالثاً: الخطة العلاجية الفردية ونسب إتقان الأهداف</span>
                </span>
                <span class="text-[10px] font-bold text-slate-500">معدل الإنجاز العام: 80%</span>
            </div>

            <!-- جدول الأهداف -->
            <table class="w-full text-right text-[11px] border-collapse">
                <thead>
                    <tr class="bg-slate-100 text-slate-700 border-b border-slate-300 font-black">
                        <th class="py-2 px-3">#</th>
                        <th class="py-2 px-3">الهدف التأهيلي المحدد</th>
                        <th class="py-2 px-3">المجال</th>
                        <th class="py-2 px-3">الأخصائي</th>
                        <th class="py-2 px-3 text-center">نسبة الإتقان</th>
                        <th class="py-2 px-3 text-center">حالة الهدف</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 font-semibold text-slate-800">
                    @foreach($iepGoals as $idx => $goal)
                    <tr>
                        <td class="py-2 px-3 text-slate-400 font-mono">{{ $idx + 1 }}</td>
                        <td class="py-2 px-3 font-bold text-slate-900">{{ $goal['title'] }}</td>
                        <td class="py-2 px-3 text-slate-600">{{ $goal['category'] }}</td>
                        <td class="py-2 px-3 text-slate-600">{{ $goal['specialist'] }}</td>
                        <td class="py-2 px-3 text-center font-bold font-mono text-teal-700">
                            {{ $goal['progress'] }}%
                        </td>
                        <td class="py-2 px-3 text-center">
                            @if($goal['status'] === 'achieved')
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">مكتمل </span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-800 border border-blue-200">قيد التدريب</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- ==================== 5. تقارير الأخصائيين التفصيلية عن الجلسات ==================== -->
        <div x-show="sections.sessions" class="border border-slate-300 rounded-2xl p-4 bg-white space-y-4 page-break-inside-avoid">
            <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                <span class="font-black text-xs text-slate-900 flex items-center gap-1.5">
                    <i class="fa-solid fa-notes-medical text-amber-600"></i>
                    <span>رابعاً: تقارير الأخصائيين التفصيلية عن الجلسات المنفذة</span>
                </span>
                <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-lg border border-slate-200">
                    إجمالي الجلسات الموثقة: {{ count($recentSessions) }}
                </span>
            </div>

            @foreach($recentSessions as $idx => $sess)
            <div class="rounded-xl border border-slate-200 overflow-hidden page-break-inside-avoid">
                
                <!-- رأس الجلسة -->
                <div class="flex items-center justify-between px-4 py-2.5 text-white text-[11px] font-bold" style="background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);">
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center text-[10px] font-black">{{ $idx + 1 }}</span>
                        <span>جلسة {{ $sess['date'] }}</span>
                        <span class="opacity-80">{{ $sess['time'] }} • {{ $sess['room'] }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded-lg bg-white/20 text-[10px]">استجابة الطفل: {{ $sess['mood'] }}</span>
                        <span class="px-2 py-0.5 rounded-lg bg-white/20 text-[10px]">الأخصائي: {{ $sess['specialist'] }}</span>
                    </div>
                </div>

                <div class="p-4 space-y-3">
                    <!-- تقرير الأخصائي المفصّل -->
                    <div class="space-y-1">
                        <span class="text-[10px] font-black text-slate-500 flex items-center gap-1">
                            <i class="fa-solid fa-file-medical text-teal-600"></i>
                            تقرير وملاحظات الأخصائي المفصّلة عن الجلسة:
                        </span>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-[11px] text-slate-800 font-medium leading-relaxed">
                            {{ $sess['notes'] }}
                        </div>
                    </div>

                    <!-- الأهداف التي تم تقييمها -->
                    @if(!empty($sess['goals']) && is_array($sess['goals']) && count($sess['goals']) > 0)
                    <div class="space-y-1.5">
                        <span class="text-[10px] font-black text-slate-500 flex items-center gap-1">
                            <i class="fa-solid fa-bullseye text-indigo-600"></i>
                            الأهداف العلاجية التي تم التدريب عليها وتقييمها:
                        </span>
                        <table class="w-full text-right text-[10px] border-collapse">
                            <thead>
                                <tr class="bg-slate-100 text-slate-600 font-bold border-b border-slate-200">
                                    <th class="py-1.5 px-2 w-8">#</th>
                                    <th class="py-1.5 px-2">الهدف</th>
                                    <th class="py-1.5 px-2 text-center w-28">نسبة الإتقان</th>
                                    <th class="py-1.5 px-2 text-center w-16">الحالة</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($sess['goals'] as $gi => $g)
                                @php
                                    $gText = is_array($g) ? ($g['text'] ?? 'هدف') : $g;
                                    $gPct = is_array($g) ? (int)($g['percentage'] ?? 0) : 0;
                                @endphp
                                <tr>
                                    <td class="py-1.5 px-2 text-slate-400 font-mono">{{ $gi + 1 }}</td>
                                    <td class="py-1.5 px-2 font-bold text-slate-800">{{ $gText }}</td>
                                    <td class="py-1.5 px-2">
                                        <div class="flex items-center gap-1.5">
                                            <div class="flex-1 h-1.5 bg-slate-200 rounded-full overflow-hidden">
                                                <div class="h-full rounded-full {{ $gPct >= 100 ? 'bg-emerald-500' : ($gPct >= 50 ? 'bg-teal-500' : 'bg-amber-500') }}" style="width: {{ $gPct }}%"></div>
                                            </div>
                                            <span class="font-mono font-black text-slate-700 w-8 text-center">{{ $gPct }}%</span>
                                        </div>
                                    </td>
                                    <td class="py-1.5 px-2 text-center">
                                        @if($gPct >= 100)
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-100 text-emerald-800">✓ مكتمل</span>
                                        @else
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-blue-50 text-blue-700">قيد التدريب</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif

                    <!-- التمرين المنزلي -->
                    @if(!empty($sess['home_exercise']))
                    <div class="space-y-1">
                        <span class="text-[10px] font-black text-amber-700 flex items-center gap-1">
                            <i class="fa-solid fa-house-user"></i>
                            التمرين / الواجب المنزلي المطلوب من ولي الأمر:
                        </span>
                        <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-[11px] text-amber-950 font-semibold leading-relaxed">
                            {{ $sess['home_exercise'] }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <!-- ==================== 6. التوقيعات الرسمية والأختام والاعتماد ==================== -->
        <div x-show="sections.signatures" class="border-2 border-slate-800 rounded-2xl p-4 bg-slate-50/50 page-break-inside-avoid space-y-4">
            <div class="text-center pb-2 border-b border-slate-300">
                <p class="font-black text-xs text-slate-900">الاعتماد والتوقيعات الرسمية لإدارة المركز</p>
            </div>

            <div class="grid grid-cols-3 gap-6 text-center text-xs font-bold pt-1">
                
                <!-- توقيع الأخصائي المعالج -->
                <div class="space-y-8">
                    <p class="text-slate-700">الأخصائي المعالج المتابع</p>
                    <p class="font-black text-slate-900 font-mono pt-4 border-t border-dashed border-slate-400">{{ $child->main_specialist ?? 'د. أحمد يسري' }}</p>
                </div>

                <!-- توقيع المشرف الطبي -->
                <div class="space-y-8">
                    <p class="text-slate-700">المشرف الفني والطبي</p>
                    <p class="font-black text-slate-900 pt-4 border-t border-dashed border-slate-400">د. حسام فؤاد</p>
                </div>

                <!-- ختم إدارة المركز -->
                <div class="space-y-4 flex flex-col items-center justify-center">
                    <p class="text-slate-700">ختم واعتماد المركز الرسمي</p>
                    <div class="w-16 h-16 rounded-full border-2 border-dashed border-slate-400 flex items-center justify-center text-[10px] text-slate-400 font-bold rotate-[-12deg]">
                        (ختم المركز)
                    </div>
                </div>

            </div>
        </div>

        <!-- تذييل التقرير الرسمي -->
        <div class="pt-2 text-center text-[9px] text-slate-400 font-medium border-t border-slate-200">
            وثيقة تأهيلية وطبية سريرية رسمية صادرة ومعتمدة من نظام إدارة مراكز التخاطب والتأهيل — طُبعت بتاريخ: {{ date('Y-m-d H:i') }}
        </div>

    </div>

</body>
</html>



@extends('layouts.app')

@section('title', 'إعدادات وهوية المركز')

@section('content')
<div class="space-y-8" x-data="{ 
    tab: localStorage.getItem('settingsTab') || 'branding',
    centerName: '{{ $settings['center_name'] }}',
    centerSlogan: '{{ $settings['center_slogan'] }}',
    primaryColor: '{{ $settings['primary_color'] }}',
    secondaryColor: '{{ $settings['secondary_color'] }}',
    accentColor: '{{ $settings['accent_color'] }}',
    logoIcon: '{{ $settings['logo_icon'] }}',
    currency: '{{ $settings['currency'] }}',
    defaultMin: '{{ $settings['default_session_min'] }}',
    heroPreview: '{{ $settings['hero_image'] ?? '' }}',
    init() {
        this.$watch('tab', val => localStorage.setItem('settingsTab', val));
    },
    handleHero(event) {
        const file = event.target.files[0];
        if (file) {
            this.heroPreview = URL.createObjectURL(file);
        }
    },
    setPalette(p, s, a) {
        this.primaryColor = p;
        this.secondaryColor = s;
        this.accentColor = a;
    }
}">

    <!-- ترويسة الصفحة -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-white text-lg shadow-md transition-colors" :style="'background-color: ' + primaryColor">
                    <i class="fa-solid fa-palette"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-800">إعدادات وهوية المركز (White-Label)</h2>
                    <p class="text-xs text-slate-500 font-semibold mt-0.5">تخصيص اسم المركز، الشعار، الصور، والألوان التي تُطبق على كامل صفحات النظام والسايد بار والموقع العام</p>
                </div>
            </div>
        </div>

        @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-2.5 rounded-2xl text-xs font-bold flex items-center gap-2 animate-in fade-in">
            <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
            <span>{{ session('success') }}</span>
        </div>
        @endif
    </div>

    <!-- نموذج حفظ الإعدادات الرئيسي -->
    <form id="main-settings-form" action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- العمود الأيمن والوسط: التبويبات والحقول (2 أعمدة) -->
            <div class="lg:col-span-2 space-y-6">

                <!-- شريط التبويبات -->
                <!-- شريط التبويبات -->
                <div class="flex flex-wrap items-center gap-1.5 p-1.5 bg-slate-100 rounded-2xl">
                    <button type="button" @click="tab = 'branding'" 
                            :class="tab === 'branding' ? 'bg-white shadow-sm text-slate-900 font-black' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50 font-bold'" 
                            class="px-4 py-2.5 rounded-xl text-xs transition-all duration-200 flex items-center gap-2 flex-1 justify-center sm:flex-none sm:justify-start">
                        <i class="fa-solid fa-paintbrush" :style="tab === 'branding' ? 'color: ' + primaryColor : ''"></i>
                        <span>الهوية</span>
                    </button>

                    <button type="button" @click="tab = 'photos'" 
                            :class="tab === 'photos' ? 'bg-white shadow-sm text-slate-900 font-black' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50 font-bold'" 
                            class="px-4 py-2.5 rounded-xl text-xs transition-all duration-200 flex items-center gap-2 flex-1 justify-center sm:flex-none sm:justify-start">
                        <i class="fa-solid fa-camera" :style="tab === 'photos' ? 'color: ' + primaryColor : ''"></i>
                        <span>الصور</span>
                    </button>

                    <button type="button" @click="tab = 'general'" 
                            :class="tab === 'general' ? 'bg-white shadow-sm text-slate-900 font-black' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50 font-bold'" 
                            class="px-4 py-2.5 rounded-xl text-xs transition-all duration-200 flex items-center gap-2 flex-1 justify-center sm:flex-none sm:justify-start">
                        <i class="fa-solid fa-building-columns" :style="tab === 'general' ? 'color: ' + primaryColor : ''"></i>
                        <span>بيانات التواصل</span>
                    </button>

                    <button type="button" @click="tab = 'whatsapp'" 
                            :class="tab === 'whatsapp' ? 'bg-white shadow-sm text-slate-900 font-black' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50 font-bold'" 
                            class="px-4 py-2.5 rounded-xl text-xs transition-all duration-200 flex items-center gap-2 flex-1 justify-center sm:flex-none sm:justify-start">
                        <i class="fa-brands fa-whatsapp" :style="tab === 'whatsapp' ? 'color: ' + primaryColor : ''"></i>
                        <span>واتساب</span>
                    </button>

                    <button type="button" @click="tab = 'pricing'" 
                            :class="tab === 'pricing' ? 'bg-white shadow-sm text-slate-900 font-black' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50 font-bold'" 
                            class="px-4 py-2.5 rounded-xl text-xs transition-all duration-200 flex items-center gap-2 flex-1 justify-center sm:flex-none sm:justify-start">
                        <i class="fa-solid fa-money-bill-wave" :style="tab === 'pricing' ? 'color: ' + primaryColor : ''"></i>
                        <span>تسعير الجلسات</span>
                    </button>
                    
                    <button type="button" @click="tab = 'dropdowns'" 
                            :class="tab === 'dropdowns' ? 'bg-white shadow-sm text-slate-900 font-black' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50 font-bold'" 
                            class="px-4 py-2.5 rounded-xl text-xs transition-all duration-200 flex items-center gap-2 flex-1 justify-center sm:flex-none sm:justify-start">
                        <i class="fa-solid fa-list-ul" :style="tab === 'dropdowns' ? 'color: ' + primaryColor : ''"></i>
                        <span>القوائم</span>
                    </button>
                </div>

                <!-- تبويب 1: الهوية البصرية والألوان -->
                <div x-show="tab === 'branding'" class="space-y-6">
                    
                    <!-- باليتات ألوان جاهزة بضغطة زر -->
                    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-extrabold text-sm text-slate-800">نماذج ألوان جاهزة متناسقة (Color Presets)</h4>
                                <p class="text-xs text-slate-400 font-semibold mt-0.5">اختر نموذج متناسق وسيتم تلوين السايد بار والأزرار فورياً</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <!-- باليتة 1: تيل عصري -->
                            <button type="button" @click="setPalette('#0d9488', '#6366f1', '#f59e0b')" class="p-3 rounded-2xl border border-slate-200 hover:border-slate-400 transition text-right group">
                                <div class="flex items-center gap-1.5 mb-2">
                                    <span class="w-5 h-5 rounded-full bg-teal-600 shadow-xs"></span>
                                    <span class="w-4 h-4 rounded-full bg-indigo-500 shadow-xs"></span>
                                    <span class="w-3 h-3 rounded-full bg-amber-500 shadow-xs"></span>
                                </div>
                                <p class="text-xs font-bold text-slate-800">التيل العصري</p>
                                <p class="text-[10px] text-slate-400">تأهيل ونطق طبي</p>
                            </button>

                            <!-- باليتة 2: أزرق طبي ملكي -->
                            <button type="button" @click="setPalette('#0284c7', '#3b82f6', '#10b981')" class="p-3 rounded-2xl border border-slate-200 hover:border-slate-400 transition text-right group">
                                <div class="flex items-center gap-1.5 mb-2">
                                    <span class="w-5 h-5 rounded-full bg-sky-600 shadow-xs"></span>
                                    <span class="w-4 h-4 rounded-full bg-blue-500 shadow-xs"></span>
                                    <span class="w-3 h-3 rounded-full bg-emerald-500 shadow-xs"></span>
                                </div>
                                <p class="text-xs font-bold text-slate-800">الأزرق الملكي</p>
                                <p class="text-[10px] text-slate-400">عيادات ومراكز كبرى</p>
                            </button>

                            <!-- باليتة 3: الزمردي التأهيلي -->
                            <button type="button" @click="setPalette('#059669', '#0d9488', '#f59e0b')" class="p-3 rounded-2xl border border-slate-200 hover:border-slate-400 transition text-right group">
                                <div class="flex items-center gap-1.5 mb-2">
                                    <span class="w-5 h-5 rounded-full bg-emerald-600 shadow-xs"></span>
                                    <span class="w-4 h-4 rounded-full bg-teal-600 shadow-xs"></span>
                                    <span class="w-3 h-3 rounded-full bg-amber-500 shadow-xs"></span>
                                </div>
                                <p class="text-xs font-bold text-slate-800">الزمردي الصحي</p>
                                <p class="text-[10px] text-slate-400">تنمية مهارات وتخاطب</p>
                            </button>

                            <!-- باليتة 4: البنفسجي الإبداعي -->
                            <button type="button" @click="setPalette('#7c3aed', '#ec4899', '#f59e0b')" class="p-3 rounded-2xl border border-slate-200 hover:border-slate-400 transition text-right group">
                                <div class="flex items-center gap-1.5 mb-2">
                                    <span class="w-5 h-5 rounded-full bg-purple-600 shadow-xs"></span>
                                    <span class="w-4 h-4 rounded-full bg-pink-500 shadow-xs"></span>
                                    <span class="w-3 h-3 rounded-full bg-amber-500 shadow-xs"></span>
                                </div>
                                <p class="text-xs font-bold text-slate-800">البنفسجي الإبداعي</p>
                                <p class="text-[10px] text-slate-400">تعديل سلوك وأطفال</p>
                            </button>
                        </div>
                    </div>

                    <!-- اختيار الألوان وتحديد الكود -->
                    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-6">
                        <h4 class="font-extrabold text-sm text-slate-800">تخصيص الألوان الدقيقة (Color Pickers)</h4>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            
                            <!-- اللون الرئيسي -->
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-3">
                                <label class="block font-bold text-xs text-slate-700">
                                    اللون الرئيسي للسايد بار والأزرار (Primary) <span class="text-rose-500">*</span>
                                </label>
                                <p class="text-[11px] text-slate-400 font-medium">يصبغ السايد بار، زر الحجز، الروابط النشطة، وترويسات الجداول.</p>
                                <div class="flex items-center gap-3">
                                    <input type="color" x-model="primaryColor" name="primary_color" class="w-12 h-12 rounded-xl cursor-pointer border-2 border-white shadow-md bg-transparent">
                                    <input type="text" x-model="primaryColor" name="primary_color" class="flex-1 px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-mono font-bold uppercase outline-none focus:border-slate-400">
                                </div>
                            </div>

                            <!-- اللون الفرعي -->
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-3">
                                <label class="block font-bold text-xs text-slate-700">
                                    اللون الفرعي والشارات (Secondary / Accent) <span class="text-rose-500">*</span>
                                </label>
                                <p class="text-[11px] text-slate-400 font-medium">للشارات والبادجات التنبيهية والتأثيرات الفرعية.</p>
                                <div class="flex items-center gap-3">
                                    <input type="color" x-model="secondaryColor" name="secondary_color" class="w-12 h-12 rounded-xl cursor-pointer border-2 border-white shadow-md bg-transparent">
                                    <input type="text" x-model="secondaryColor" name="secondary_color" class="flex-1 px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-mono font-bold uppercase outline-none focus:border-slate-400">
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- اسم المركز والشعار والأيقونة -->
                    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-5">
                        <h4 class="font-extrabold text-sm text-slate-800">بيانات الهوية والشعار</h4>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 text-xs font-medium">
                            <div class="sm:col-span-2">
                                <label class="block font-bold text-slate-700 mb-1.5">اسم المركز الرسمي <span class="text-rose-500">*</span></label>
                                <input type="text" x-model="centerName" name="center_name" required placeholder="مثال: مركز الأمل للتخاطب والتأهيل" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold text-sm">
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block font-bold text-slate-700 mb-1.5">الوصف / السلوجان المختصر للسايد بار</label>
                                <input type="text" x-model="centerSlogan" name="center_slogan" placeholder="مثال: للتخاطب وتنمية المهارات والتأهيل الشامل" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-semibold">
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 mb-1.5">أيقونة الشعار في السايد بار</label>
                                <select x-model="logoIcon" name="logo_icon" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold">
                                    <option value="fa-brain">عقل ودماغ (fa-brain)</option>
                                    <option value="fa-child-reaching">طفل وتأهيل (fa-child-reaching)</option>
                                    <option value="fa-heart-pulse">نبض ورعاية (fa-heart-pulse)</option>
                                    <option value="fa-hands-holding-child">أيدي راعية (fa-hands-holding-child)</option>
                                    <option value="fa-star">نجمة الأمل (fa-star)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 mb-1.5">العملة الافتراضية للفواتير والرواتب</label>
                                <input type="text" x-model="currency" name="currency" required placeholder="ج.م أو ر.س أو د.إ" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold">
                            </div>
                        </div>
                    </div>

                </div>

                <!-- تبويب 2: صور ومرافق المركز للموقع العام -->
                <div x-show="tab === 'photos'" class="space-y-6">
                    
                    <!-- صورة الواجهة الرئيسية (Hero Image) -->
                    <div class="bg-white rounded-3xl p-6 md:p-8 border border-slate-100 shadow-sm space-y-6">
                        <div>
                            <h4 class="font-extrabold text-sm text-slate-800">صورة الواجهة الرئيسية للموقع العام (Hero Banner Image)</h4>
                            <p class="text-xs text-slate-400 font-semibold mt-0.5">تظهر هذه الصورة على يسار الواجهة الرئيسية للموقع العام للمركز بجانب العنوان وأزرار الحجز</p>
                        </div>

                        <!-- معاينة ورفع الصورة الرئيسية -->
                        <div class="flex flex-col sm:flex-row items-center gap-6 p-5 rounded-3xl bg-slate-50 border border-slate-200/80">
                            <div class="relative w-full sm:w-64 h-40 rounded-2xl overflow-hidden border-2 border-dashed border-slate-300 bg-slate-200 flex items-center justify-center shrink-0 shadow-md">
                                <template x-if="heroPreview">
                                    <img :src="heroPreview" alt="Hero Preview" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!heroPreview">
                                    <div class="text-center text-slate-400 p-3">
                                        <i class="fa-solid fa-image text-3xl mb-1"></i>
                                        <p class="text-[10px] font-bold">لا توجد صورة محددة</p>
                                    </div>
                                </template>
                            </div>

                            <div class="space-y-3 flex-1 text-center sm:text-right text-xs">
                                <div>
                                    <label class="block font-bold text-slate-800 text-xs">رفع صورة من جهازك:</label>
                                    <p class="text-[11px] text-slate-400 mt-0.5">يفضل صورة بجودة عالية للأطفال أثناء الجلسات أو لقاعات المركز (JPG, PNG بحد أقصى 10MB)</p>
                                </div>
                                <label class="inline-block px-5 py-2.5 bg-white hover:bg-slate-100 border border-slate-300 rounded-xl text-xs font-bold text-slate-700 cursor-pointer shadow-xs transition">
                                    <i class="fa-solid fa-cloud-arrow-up ml-1 text-teal-600"></i>
                                    <span>اختر صورة رئيسية من الجهاز</span>
                                    <input type="file" name="hero_image" accept="image/*" @change="handleHero" class="hidden">
                                </label>

                                <div class="pt-2 border-t border-slate-200/60">
                                    <label class="block font-bold text-slate-700 text-[11px] mb-1">أو أدخل رابط صورة خارجي (Image URL):</label>
                                    <input type="text" name="hero_image_url" x-model="heroPreview" placeholder="https://..." class="w-full p-2.5 bg-white border border-slate-200 rounded-xl outline-none text-xs font-mono">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- معرض صور قاعات ومرافق المركز الإضافية -->
                    <div class="bg-white rounded-3xl p-6 md:p-8 border border-slate-100 shadow-sm space-y-6">
                        <div>
                            <h4 class="font-extrabold text-sm text-slate-800">معرض صور مرافق وقاعات المركز (Facilities Gallery)</h4>
                            <p class="text-xs text-slate-400 font-semibold mt-0.5">ارفع صوراً لقاعة التكامل الحسي Sensory Room، غرف التخاطب، وأدوات التأهيل</p>
                        </div>

                        <!-- الصور الحالية بالمعرض -->
                        @if(!empty($settings['gallery_images']))
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach($settings['gallery_images'] as $gImg)
                            <div class="relative h-28 rounded-2xl overflow-hidden border border-slate-200 group shadow-xs">
                                <img src="{{ $gImg }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            </div>
                            @endforeach
                        </div>
                        @endif

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-2">
                            <label class="block font-bold text-slate-800">إضافة صور جديدة للمعرض:</label>
                            <input type="file" name="gallery_photos[]" multiple accept="image/*" class="w-full p-2.5 bg-white border border-slate-200 rounded-xl text-xs">
                            <p class="text-[10px] text-slate-400">يمكنك تحديد عدة صور معاً لرفعها دفعة واحدة.</p>
                        </div>
                    </div>

                </div>

                <!-- تبويب 3: بيانات التواصل والعنوان -->
                <div x-show="tab === 'general'" class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-5 text-xs font-medium">
                    <h4 class="font-extrabold text-sm text-slate-800">بيانات التواصل والفروع</h4>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">رقم هاتف المركز الرئيسي</label>
                            <input type="text" name="phone" value="{{ $settings['phone'] }}" placeholder="01012345678" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-mono">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">رقم الواتساب الرسمي</label>
                            <input type="text" name="whatsapp" value="{{ $settings['whatsapp'] }}" placeholder="01012345678" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-mono">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">البريد الإلكتروني</label>
                            <input type="email" name="email" value="{{ $settings['email'] }}" placeholder="info@center.com" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">مدة الجلسة الافتراضية (بالدقائق)</label>
                            <input type="number" name="default_session_min" x-model="defaultMin" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block font-bold text-slate-700 mb-1.5">عنوان المركز / المقر</label>
                            <input type="text" name="address" value="{{ $settings['address'] }}" placeholder="مثال: القاهرة - مدينة نصر - شارع عباس العقاد" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white">
                        </div>
                    </div>
                </div>

                <!-- تبويب 4: أتمتة رسائل الواتساب -->
                <div x-show="tab === 'whatsapp'" class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-5 text-xs font-medium">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-extrabold text-sm text-slate-800">إعدادات رسائل تذكير الواتساب التلقائية</h4>
                            <p class="text-[11px] text-slate-400 font-semibold mt-0.5">تُرسل الرسائل آلياً لأولياء الأمور قبل موعد الجلسة</p>
                        </div>
                        <label class="flex items-center gap-2 font-bold cursor-pointer">
                            <input type="checkbox" name="whatsapp_auto_send" value="1" {{ $settings['whatsapp_auto_send'] ? 'checked' : '' }} class="w-4 h-4 rounded accent-emerald-600">
                            <span class="text-emerald-700">تفعيل الإرسال التلقائي</span>
                        </label>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5">قالب رسالة التذكير (مع المتغيرات التلقائية):</label>
                        <textarea name="whatsapp_template" rows="5" class="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-mono text-xs leading-relaxed">{{ $settings['whatsapp_template'] }}</textarea>
                    </div>

                    <div class="p-3 bg-slate-100 rounded-2xl text-[11px] text-slate-600 font-semibold space-y-1">
                                <p class="font-bold text-slate-800"><i class="fa-solid fa-code ml-1"></i> المتغيرات المتاحة للاستخدام في الرسالة:</p>
                        <p class="text-slate-500"><code>{ولي_الأمر}</code> اسم الأب أو الأم • <code>{الطفل}</code> اسم الطفل • <code>{الموعد}</code> وقت وتاريخ الجلسة • <code>{الأخصائي}</code> اسم المعالج • <code>{الغرفة}</code> رقم الغرفة • <code>{اسم_المركز}</code> اسم المركز.</p>
                    </div>
                </div>

                <!-- تبويب القوائم -->
                <div x-show="tab === 'dropdowns'" class="space-y-6" style="display: none;">
                    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-4">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h4 class="font-extrabold text-sm text-slate-800">إدارة القوائم المنسدلة في النظام</h4>
                                <p class="text-xs text-slate-400 font-semibold mt-0.5">أدخل كل خيار في سطر جديد. ستنعكس هذه الخيارات في جميع شاشات النظام فوراً.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-bold text-slate-700 text-xs mb-1.5">التخصصات (Specializations)</label>
                                <textarea name="dropdown_specializations" rows="6" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold text-xs" dir="auto">{{ implode("\n", \App\Http\Controllers\SettingController::getDropdownList('specializations')) }}</textarea>
                            </div>
                            
                            <div>
                                <label class="block font-bold text-slate-700 text-xs mb-1.5">القاعات (Rooms)</label>
                                <textarea name="dropdown_rooms" rows="6" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold text-xs" dir="auto">{{ implode("\n", \App\Http\Controllers\SettingController::getDropdownList('rooms')) }}</textarea>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 text-xs mb-1.5">أنواع الجلسات (Session Types)</label>
                                <textarea name="dropdown_session_types" rows="6" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold text-xs" dir="auto">{{ implode("\n", \App\Http\Controllers\SettingController::getDropdownList('session_types')) }}</textarea>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 text-xs mb-1.5">الخدمات في الموقع (Website Services)</label>
                                <textarea name="dropdown_services" rows="6" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold text-xs" dir="auto">{{ implode("\n", \App\Http\Controllers\SettingController::getDropdownList('services')) }}</textarea>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 text-xs mb-1.5">تصنيفات المصروفات (Expense Categories)</label>
                                <textarea name="dropdown_expense_categories" rows="6" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold text-xs" dir="auto">{{ implode("\n", \App\Http\Controllers\SettingController::getDropdownList('expense_categories')) }}</textarea>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 text-xs mb-1.5">تصنيف الحالة الرئيسي للطفل (Diagnoses)</label>
                                <textarea name="dropdown_diagnoses" rows="6" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold text-xs" dir="auto">{{ implode("\n", \App\Http\Controllers\SettingController::getDropdownList('diagnoses')) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- تبويب 5: أسعار جلسات الأخصائيين -->
                <div x-show="tab === 'pricing'" class="space-y-6">
                    </form>{{-- إغلاق فورم الإعدادات الرئيسي لأن أسعار الجلسات في فورم مستقل --}}

                    <form action="{{ route('settings.session-prices') }}" method="POST" class="space-y-6">
                        @csrf

                        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-5">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-extrabold text-sm text-slate-800">تحديد سعر الجلسة لكل أخصائي</h4>
                                    <p class="text-xs text-slate-400 font-semibold mt-0.5">السعر المحدد هنا سيظهر تلقائياً في شاشة الماليات عند إنشاء فاتورة جديدة</p>
                                </div>
                                <div class="flex items-center gap-2 text-xs font-bold text-slate-500">
                                    <i class="fa-solid fa-circle-info text-blue-400"></i>
                                    <span>العملة: {{ $settings['currency'] }}</span>
                                </div>
                            </div>

                            @if($specialists->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="border-b border-slate-200">
                                            <th class="text-right py-3 px-4 font-extrabold text-slate-700 text-xs">#</th>
                                            <th class="text-right py-3 px-4 font-extrabold text-slate-700 text-xs">الأخصائي</th>
                                            <th class="text-right py-3 px-4 font-extrabold text-slate-700 text-xs">التخصص</th>
                                            <th class="text-right py-3 px-4 font-extrabold text-slate-700 text-xs">نوع الراتب</th>
                                            <th class="text-right py-3 px-4 font-extrabold text-slate-700 text-xs">سعر الجلسة ({{ $settings['currency'] }})</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach($specialists as $index => $specialist)
                                        <tr class="hover:bg-slate-50 transition">
                                            <td class="py-3 px-4 text-xs text-slate-400 font-bold">{{ $index + 1 }}</td>
                                            <td class="py-3 px-4">
                                                <div class="flex items-center gap-3">
                                                    <img src="{{ $specialist->avatar_url }}" alt="{{ $specialist->name }}" class="w-9 h-9 rounded-xl object-cover bg-slate-200">
                                                    <div>
                                                        <p class="font-bold text-sm text-slate-800">{{ $specialist->name }}</p>
                                                        <p class="text-[11px] text-slate-400 font-semibold">{{ $specialist->code }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-3 px-4 text-xs font-semibold text-slate-600">{{ $specialist->specialization }}</td>
                                            <td class="py-3 px-4">
                                                <span class="px-2.5 py-1 text-[11px] rounded-full font-bold
                                                    {{ $specialist->salary_type === 'per_session' ? 'bg-teal-50 text-teal-700' : ($specialist->salary_type === 'monthly' ? 'bg-blue-50 text-blue-700' : 'bg-amber-50 text-amber-700') }}">
                                                    {{ $specialist->salary_type === 'per_session' ? 'بالجلسة' : ($specialist->salary_type === 'monthly' ? 'شهري' : 'نسبة') }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-4">
                                                <input type="number" 
                                                       name="prices[{{ $specialist->id }}]" 
                                                       value="{{ $specialist->session_rate }}" 
                                                       min="0" 
                                                       step="0.01"
                                                       class="w-32 px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold outline-none focus:bg-white focus:border-teal-400 transition">
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="pt-4 flex items-center justify-end">
                                <button type="submit" class="px-6 py-3 rounded-2xl text-white font-extrabold text-sm shadow-xl hover:opacity-90 active:scale-95 transition flex items-center gap-2.5 bg-brand-primary">
                                    <i class="fa-solid fa-floppy-disk text-base"></i>
                                    <span>حفظ أسعار الجلسات</span>
                                </button>
                            </div>
                            @else
                            <div class="text-center py-10">
                                <i class="fa-solid fa-user-tie text-4xl text-slate-300 mb-3"></i>
                                <p class="text-sm font-bold text-slate-400">لا يوجد أخصائيون مسجلون حالياً</p>
                                <a href="{{ route('specialists.create') }}" class="inline-block mt-3 px-4 py-2 bg-teal-600 text-white rounded-xl text-xs font-bold hover:bg-teal-700 transition">
                                    <i class="fa-solid fa-plus ml-1"></i> إضافة أخصائي جديد
                                </a>
                            </div>
                            @endif
                        </div>
                    </form>

                </div>

                <!-- زر الحفظ النهائي -->
                <div class="pt-4 flex items-center justify-end">
                    <button type="submit" form="main-settings-form" class="px-8 py-3.5 rounded-2xl text-white font-extrabold text-sm shadow-xl hover:opacity-90 active:scale-95 transition flex items-center gap-2.5" :style="'background-color: ' + primaryColor">
                        <i class="fa-solid fa-floppy-disk text-base"></i>
                        <span>حفظ وتطبيق التعديلات والصور على النظام والموقع العام</span>
                    </button>
                </div>

            </div>

            <!-- العمود الأيسر: المعاينة الحية الفورية للسايد بار والعناصر (Live Sidebar Preview) -->
            <div class="space-y-4">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                    <h4 class="font-extrabold text-sm text-slate-800">معاينة حية لشكل السايد بار (Sidebar Preview)</h4>
                </div>

                <!-- كارت المعاينة التفاعلي للسايد بار -->
                <div class="rounded-3xl p-5 border border-slate-800 shadow-2xl space-y-5 sticky top-28 text-white" :style="'background: linear-gradient(180deg, #090e17 0%, #0f172a 60%, ' + primaryColor + '30 100%); border-color: ' + primaryColor + '40'">
                    
                    <!-- الهيدر المصغر للسايد بار -->
                    <div class="flex items-center gap-3 pb-4 border-b" :style="'border-color: ' + primaryColor + '30'">
                        <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-white text-lg shadow-lg" :style="'background: linear-gradient(135deg, ' + primaryColor + ' 0%, #000 100%); box-shadow: 0 8px 20px -3px ' + primaryColor + '80'">
                            <i class="fa-solid" :class="logoIcon"></i>
                        </div>
                        <div class="overflow-hidden">
                            <h3 class="font-black text-xs text-white truncate" x-text="centerName"></h3>
                            <p class="text-[10px] font-bold truncate" :style="'color: ' + primaryColor" x-text="centerSlogan"></p>
                        </div>
                    </div>

                    <!-- نموذج لعناصر السايد بار التفاعلية -->
                    <div class="space-y-2 text-xs">
                        <p class="text-[10px] font-extrabold uppercase" :style="'color: ' + primaryColor">شكل روابط السايد بار:</p>
                        
                        <!-- الرابط النشط -->
                        <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-extrabold text-white shadow-lg" :style="'background: linear-gradient(135deg, ' + primaryColor + ' 0%, #000 100%); box-shadow: 0 8px 20px -4px ' + primaryColor + '70'">
                            <i class="fa-solid fa-chart-pie w-4 text-center"></i>
                            <span>لوحة التحكم (نشط)</span>
                        </div>

                        <!-- رابط غير نشط عادي -->
                        <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-bold text-slate-400 hover:text-white transition">
                            <i class="fa-solid fa-globe w-4 text-center text-emerald-400"></i>
                            <span>الموقع العام</span>
                            <span class="mr-auto px-2 py-0.5 text-[9px] rounded-full font-bold" :style="'background-color: ' + primaryColor + '30; color: #fff; border: 1px solid ' + primaryColor + '50'">عرض</span>
                        </div>

                        <!-- رابط ملفات الأطفال -->
                        <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-bold text-slate-400 hover:text-white transition">
                            <i class="fa-solid fa-child-reaching w-4 text-center text-emerald-400"></i>
                            <span>ملفات الأطفال</span>
                            <span class="mr-auto px-2 py-0.5 text-[9px] rounded-full font-bold text-emerald-300 bg-emerald-500/20">142</span>
                        </div>
                    </div>

                    <!-- مثال على زر الإجراء بالصفحة -->
                    <div class="pt-3 border-t" :style="'border-color: ' + primaryColor + '30'">
                        <p class="text-[10px] font-bold text-slate-400 mb-2">زر الإجراءات السريعة بالصفحة:</p>
                        <button type="button" class="w-full py-2 px-3 rounded-xl text-white font-bold text-xs shadow-md transition flex items-center justify-center gap-2" :style="'background-color: ' + primaryColor">
                            <i class="fa-solid fa-plus text-xs"></i>
                            <span>حجز موعد جلسة</span>
                        </button>
                    </div>

                </div>
            </div>

        </div>

    </form>

</div>
@endsection
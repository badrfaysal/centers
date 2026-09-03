@extends('layouts.app')

@section('title', 'تسجيل طفل جديد')

@section('content')
<div class="space-y-8" x-data="{ 
    gender: 'male',
    diagnosisCategory: 'speech',
    packageType: 'evaluation',
    photoPreview: null,
    diagnoses: [''],
    addDiagnosis() {
        this.diagnoses.push('');
    },
    removeDiagnosis(index) {
        if (this.diagnoses.length > 1) {
            this.diagnoses.splice(index, 1);
        } else {
            this.diagnoses[0] = '';
        }
    },
    previewImage(event) {
        const file = event.target.files[0];
        if (file) {
            this.photoPreview = URL.createObjectURL(file);
        }
    }
}">

    <!-- الترويسة وأزرار التنقل -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-2xl flex items-center justify-center text-white text-xl shadow-lg" style="background-color: #0d9488;">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <div>
                <h2 class="text-2xl font-black text-slate-800">تسجيل وفتح ملف طفل جديد</h2>
                <p class="text-xs text-slate-500 font-semibold mt-0.5">تسجيل البيانات الشخصية، الطبية، دكتور المخ والأعصاب، واختبارات الذكاء والأدوية</p>
            </div>
        </div>

        <a href="{{ route('children.index') }}" class="px-4 py-2.5 bg-white border border-slate-200 rounded-2xl text-xs font-bold text-slate-700 hover:bg-slate-50 transition shadow-xs flex items-center gap-2">
            <i class="fa-solid fa-arrow-right text-slate-400"></i>
            <span>العودة لقائمة الأطفال</span>
        </a>
    </div>

    <!-- رسائل الأخطاء إن وجدت -->
    @if (isset($errors) && $errors->any())
    <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-2xl text-xs font-bold space-y-1">
        <p class="flex items-center gap-1.5"><i class="fa-solid fa-triangle-exclamation"></i> يرجى مراجعة الحقول التالية:</p>
        <ul class="list-disc pr-5 font-medium">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- فورم التسجيل الرئيسي -->
    <form action="{{ route('children.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf

        <div class="bg-white rounded-3xl p-6 md:p-8 border border-slate-100 shadow-sm space-y-8">
            
            <!-- الشريط العلوي للكود وحالة الملف وصورة الطفل -->
            <div class="flex flex-col md:flex-row items-center justify-between gap-6 p-5 rounded-3xl bg-slate-50 border border-slate-100">
                
                <!-- رفع ومعاينة صورة الطفل -->
                <div class="flex items-center gap-4">
                    <div class="relative group">
                        <template x-if="photoPreview">
                            <img :src="photoPreview" alt="child photo" class="w-18 h-18 rounded-2xl object-cover ring-4 ring-white shadow-md">
                        </template>
                        <template x-if="!photoPreview">
                            <div class="w-18 h-18 rounded-2xl bg-slate-200 flex flex-col items-center justify-center text-slate-400 border-2 border-dashed border-slate-300">
                                <i class="fa-solid fa-camera text-xl mb-1"></i>
                                <span class="text-[9px] font-bold">صورة الطفل</span>
                            </div>
                        </template>
                        <label class="absolute -bottom-2 -left-2 w-7 h-7 rounded-xl bg-white shadow-md border border-slate-200 flex items-center justify-center cursor-pointer hover:bg-slate-50 transition" title="رفع أو التقاط صورة">
                            <i class="fa-solid fa-cloud-arrow-up text-xs text-brand-primary" style="color: #0d9488;"></i>
                            <input type="file" name="photo" accept="image/*" @change="previewImage" class="hidden">
                        </label>
                    </div>

                    <div>
                        <p class="font-extrabold text-sm text-slate-800">صورة الطفل الشخصية</p>
                        <p class="text-[11px] text-slate-400">التقط صورة من الكاميرا أو ارفع صورة واضحة للطفل</p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <span class="text-[11px] font-extrabold text-slate-400 block">كود الطفل التلقائي:</span>
                        <span class="font-mono px-3 py-1 bg-white border border-slate-200 rounded-xl text-sm font-black text-slate-800 shadow-xs inline-block mt-0.5">
                            {{ $nextCode }}
                        </span>
                        <input type="hidden" name="code" value="{{ $nextCode }}">
                    </div>

                    <div class="text-right">
                        <span class="text-[11px] font-extrabold text-slate-400 block">حالة الملف:</span>
                        <select name="status" class="px-3 py-1.5 bg-white border border-slate-200 rounded-xl text-xs font-bold outline-none mt-0.5 shadow-xs">
                            <option value="active"> نشط في الخطة التأهيلية</option>
                            <option value="on_hold"> معلق / انتظار</option>
                            <option value="discharged"> منتهي / متخرج</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- ==================== 1. البيانات الشخصية والعمر ==================== -->
            <div class="space-y-4">
                <div class="flex items-center gap-2 border-b border-slate-100 pb-2">
                    <i class="fa-solid fa-child text-sm" style="color: #0d9488;"></i>
                    <h3 class="font-black text-sm text-slate-800">1. البيانات الشخصية والعمر (الزمني والعقلي)</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-4 gap-5 text-xs font-medium">
                    
                    <div class="sm:col-span-2">
                        <label class="block font-bold text-slate-700 mb-1.5">الاسم الرباعي للطفل <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" required value="{{ old('name') }}" placeholder="مثال: سيف الدين محمود السعدني" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold text-sm">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5">النوع (الجنس) <span class="text-rose-500">*</span></label>
                        <div class="grid grid-cols-2 gap-2 font-bold">
                            <label class="flex items-center justify-center gap-1.5 p-2.5 rounded-xl border cursor-pointer transition" :class="gender === 'male' ? 'bg-blue-50 border-blue-300 text-blue-800' : 'bg-slate-50 border-slate-200 text-slate-600'">
                                <input type="radio" name="gender" value="male" x-model="gender" class="hidden">
                                <i class="fa-solid fa-mars"></i>
                                <span>ذكر</span>
                            </label>
                            <label class="flex items-center justify-center gap-1.5 p-2.5 rounded-xl border cursor-pointer transition" :class="gender === 'female' ? 'bg-pink-50 border-pink-300 text-pink-800' : 'bg-slate-50 border-slate-200 text-slate-600'">
                                <input type="radio" name="gender" value="female" x-model="gender" class="hidden">
                                <i class="fa-solid fa-venus"></i>
                                <span>أنثى</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5">تاريخ الميلاد (العمر الزمني) <span class="text-rose-500">*</span></label>
                        <input type="date" name="birth_date" required value="{{ old('birth_date') }}" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5">العمر العقلي / اللغوي (إن وجد)</label>
                        <input type="text" name="mental_age" value="{{ old('mental_age') }}" placeholder="مثال: 3 سنوات ونصف أو 40 شهر" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold text-slate-800">
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block font-bold text-slate-700 mb-1.5">عنوان السكن والمنطقة</label>
                        <input type="text" name="address" value="{{ old('address') }}" placeholder="مثال: مدينة نصر - الحي السابع - شارع الطيران" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-medium">
                    </div>

                </div>
            </div>

            <!-- ==================== 2. بيانات ولي الأمر والتواصل ==================== -->
            <div class="space-y-4 pt-4 border-t border-slate-100">
                <div class="flex items-center gap-2 border-b border-slate-100 pb-2">
                    <i class="fa-solid fa-users text-sm" style="color: #0d9488;"></i>
                    <h3 class="font-black text-sm text-slate-800">2. بيانات ولي الأمر والتواصل (الواتساب)</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-4 gap-5 text-xs font-medium">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5">الرقم القومي (لولي الأمر) <span class="text-rose-500">*</span></label>
                        <input type="text" name="national_id" required value="{{ old('national_id') }}" maxlength="14" placeholder="14 رقم" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5">اسم ولي الأمر <span class="text-rose-500">*</span></label>
                        <input type="text" name="parent_name" required value="{{ old('parent_name') }}" placeholder="الاسم" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5">صلة القرابة <span class="text-rose-500">*</span></label>
                        <select name="parent_relation" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold">
                            <option value="الأب">الأب</option>
                            <option value="الأم">الأم</option>
                            <option value="الوصي / ولي الأمر">الوصي / ولي الأمر</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5">رقم هاتف ولي الأمر (الواتساب) <span class="text-rose-500">*</span></label>
                        <input type="text" name="phone" required value="{{ old('phone') }}" placeholder="01012345678" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-mono font-bold">
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block font-bold text-slate-700 mb-1.5">رقم هاتف إضافي (للطوارئ)</label>
                        <input type="text" name="emergency_phone" value="{{ old('emergency_phone') }}" placeholder="رقم هاتف آخر أو هاتف المنزل" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-mono">
                    </div>
                </div>
            </div>

            <!-- ==================== 3. البيانات الطبية وسطور التشخيص المستقلة ==================== -->
            <div class="space-y-5 pt-4 border-t border-slate-100">
                <div class="flex items-center gap-2 border-b border-slate-100 pb-2">
                    <i class="fa-solid fa-brain text-sm" style="color: #0d9488;"></i>
                    <h3 class="font-black text-sm text-slate-800">3. التشخيص الطبي وسطور التشخيصات المستقلة</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 text-xs font-medium">
                    
                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5">تصنيف الحالة الرئيسي <span class="text-rose-500">*</span></label>
                        <select name="diagnosis_category" x-model="diagnosisCategory" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold">
                            @foreach($diagnoses as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5">الأخصائي المعالج المتابع بالمركز</label>
                        <select name="main_specialist" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold">
                            <option value="">-- لم يتم تعيين أخصائي بعد --</option>
                            @foreach($specialists as $spec)
                            <option value="{{ $spec }}">{{ $spec }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- سطور التشخيص المستقلة (Dynamic Multi-Row Diagnosis List) -->
                    <div class="sm:col-span-2 p-5 rounded-3xl bg-slate-50/80 border border-slate-200/80 space-y-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="block font-black text-slate-800 text-xs">
                                    سطور التشخيصات الطبية والتأهيلية (تشخيصات مستقلة) <span class="text-rose-500">*</span>
                                </label>
                                <p class="text-[11px] text-slate-400">أضف كل تشخيص أو اضطراب في سطر مستقل لسهولة المتابعة وخطة العلاج</p>
                            </div>

                            <button type="button" @click="addDiagnosis()" class="px-3.5 py-1.5 rounded-xl text-white font-bold text-xs shadow-xs hover:opacity-90 active:scale-95 transition flex items-center gap-1.5" style="background-color: #0d9488;">
                                <i class="fa-solid fa-plus text-xs"></i>
                                <span>إضافة سطر تشخيص</span>
                            </button>
                        </div>

                        <!-- قائمة السطور الديناميكية -->
                        <div class="space-y-2.5">
                            <template x-for="(item, index) in diagnoses" :key="index">
                                <div class="flex items-center gap-2.5 animate-in fade-in duration-150">
                                    <span class="w-7 h-7 rounded-xl bg-white border border-slate-200 text-slate-700 text-xs font-black flex items-center justify-center shrink-0 shadow-xs" x-text="index + 1"></span>
                                    
                                    <input type="text" 
                                           name="diagnoses[]" 
                                           x-model="diagnoses[index]" 
                                           required
                                           :placeholder="'مثال: ' + (index === 0 ? 'طيف توحد (ASD) خفيف إلى متوسط' : (index === 1 ? 'تأخر نمو لغوي تعبيري واستقبالي' : 'اضطراب معالجة حسية وتشتت انتباه'))" 
                                           class="flex-1 p-3 bg-white border border-slate-200 rounded-2xl outline-none focus:border-slate-400 font-semibold text-xs text-slate-800">

                                    <button type="button" 
                                            @click="removeDiagnosis(index)" 
                                            class="w-10 h-10 rounded-2xl bg-white border border-slate-200 text-slate-400 hover:text-rose-600 hover:border-rose-200 hover:bg-rose-50 flex items-center justify-center text-sm transition shrink-0" 
                                            title="حذف هذا التشخيص">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5">دكتور المخ والأعصاب المتابع (إن وجد)</label>
                        <input type="text" name="neurologist_name" value="{{ old('neurologist_name') }}" placeholder="مثال: د. مجدي يوسف - استشاري مخ وأعصاب أطفال" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-semibold">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5">باقة الجلسات المبدئية</label>
                        <select name="package_type" x-model="packageType" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold">
                            @foreach($packages as $pkey => $plabel)
                            <option value="{{ $pkey }}">{{ $plabel }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block font-bold text-slate-700 mb-1.5">اختبارات ومقاييس الذكاء السابقة (IQ Assessments)</label>
                        <textarea name="iq_tests_history" rows="2" placeholder="مثال: تم عمل مقياس ستانفورد بينيه (الدرجة 82)، مقياس جيليام للتوحد (درجة احتمال خفيف)، اختبار اللغة المعرب..." class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white"></textarea>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block font-bold text-slate-700 mb-1.5">الأدوية والعلاجات التي يتناولها بانتظام (Current Medications)</label>
                        <textarea name="current_medications" rows="2" placeholder="مثال: كونسيرتا 18 مجم صباحاً، دواء تيجريتول، مكمل أوميجا 3، فيتامين د..." class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white"></textarea>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5">أجهزة مساعدة مستخدمة</label>
                        <input type="text" name="assistive_devices" value="{{ old('assistive_devices') }}" placeholder="مثال: قوقعة إلكترونية، سماعة طبية، نظارة" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5">التاريخ المرضي وملاحظات الحمل والولادة</label>
                        <input type="text" name="medical_notes" value="{{ old('medical_notes') }}" placeholder="نقص أكسجين عند الولادة، عمليات جراحية، إلخ" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white">
                    </div>

                </div>
            </div>

            <!-- أزرار الحفظ والإلغاء -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('children.index') }}" class="px-6 py-3 rounded-2xl text-slate-500 font-bold hover:bg-slate-100 text-xs transition">
                    إلغاء
                </a>
                <button type="submit" class="px-8 py-3.5 rounded-2xl text-white font-black text-sm shadow-lg hover:opacity-95 active:scale-95 transition flex items-center gap-2" style="background-color: #0d9488;">
                    <i class="fa-solid fa-check"></i>
                    <span>حفظ وتسجيل ملف الطفل</span>
                </button>
            </div>

        </div>

    </form>

</div>
@endsection


@extends('layouts.app')

@section('title', 'تسجيل أخصائي تأهيل جديد')

@section('content')
<div class="space-y-8" x-data="{ 
    photoPreview: null,
    salaryType: 'per_session',
    handlePhoto(event) {
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
                <h2 class="text-2xl font-black text-slate-800">إضافة وتسجيل أخصائي جديد</h2>
                <p class="text-xs text-slate-500 font-semibold mt-0.5">تسجيل بيانات الأخصائي المعالج، التخصص، المؤهل، ونظام العمل والمحاسبة</p>
            </div>
        </div>

        <a href="{{ route('specialists.index') }}" class="px-4 py-2.5 bg-white border border-slate-200 rounded-2xl text-xs font-bold text-slate-700 hover:bg-slate-50 transition shadow-xs flex items-center gap-2">
            <i class="fa-solid fa-arrow-right"></i>
            <span>العودة لقائمة الأخصائيين</span>
        </a>
    </div>

    <!-- رسائل الأخطاء إن وجدت -->
    @if (isset($errors) && $errors->any())
    <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-2xl text-xs font-bold space-y-1 animate-in fade-in">
        <p class="flex items-center gap-1.5"><i class="fa-solid fa-triangle-exclamation"></i> يرجى تصحيح الأخطاء التالية:</p>
        <ul class="list-disc pr-5 font-medium">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- نموذج التسجيل -->
    <form action="{{ route('specialists.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf

        <!-- ==================== 1. البيانات الأساسية وصورة الأخصائي ==================== -->
        <div class="bg-white rounded-3xl p-6 md:p-8 border border-slate-100 shadow-sm space-y-6">
            
            <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                <i class="fa-solid fa-id-badge text-sm" style="color: #0d9488;"></i>
                <h3 class="font-black text-sm text-slate-800">1. البيانات الشخصية والمهنية الأساسية</h3>
            </div>

            <!-- رفع ومعاينة صورة الأخصائي -->
            <div class="flex flex-col sm:flex-row items-center gap-6 p-4 rounded-2xl bg-slate-50/70 border border-slate-200/70">
                <div class="relative group">
                    <template x-if="photoPreview">
                        <img :src="photoPreview" alt="Preview" class="w-24 h-24 rounded-3xl object-cover ring-4 ring-white shadow-md">
                    </template>
                    <template x-if="!photoPreview">
                        <div class="w-24 h-24 rounded-3xl bg-slate-200 flex flex-col items-center justify-center text-slate-400 border-2 border-dashed border-slate-300">
                            <i class="fa-solid fa-camera text-2xl mb-1"></i>
                            <span class="text-[9px] font-bold">صورة شخصية</span>
                        </div>
                    </template>
                </div>

                <div class="space-y-2 text-center sm:text-right">
                    <label class="block font-extrabold text-slate-800 text-xs">صورة الأخصائي الشخصية (اختياري)</label>
                    <p class="text-[11px] text-slate-400">تظهر في بروفايل الأخصائي وبوابة ولي الأمر عند توثيق الجلسات (JPG, PNG بحد أقصى 5MB)</p>
                    <label class="inline-block px-4 py-2 bg-white hover:bg-slate-100 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 cursor-pointer shadow-2xs transition">
                        <i class="fa-solid fa-upload ml-1"></i>
                        <span>اختر صورة من الجهاز</span>
                        <input type="file" name="photo" accept="image/*" @change="handlePhoto" class="hidden">
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 text-xs font-medium">
                
                <!-- كود الأخصائي -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5">كود الأخصائي (توليد تلقائي) <span class="text-rose-500">*</span></label>
                    <input type="text" name="code" value="{{ old('code', $nextCode) }}" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-mono font-bold">
                </div>

                <!-- الرقم القومي -->

                <!-- اسم الأخصائي -->
                <div class="sm:col-span-1">
                    <label class="block font-bold text-slate-700 mb-1.5">الاسم الرباعي <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="مثال: د. أحمد..." class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold">
                </div>

                <!-- التخصص الرئيسي -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5">التخصص الرئيسي <span class="text-rose-500">*</span></label>
                    <select name="specialization" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold">
                        @foreach($specializations as $key => $title)
                        <option value="{{ $key }}" {{ old('specialization') === $key ? 'selected' : '' }}>{{ $title }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- المسمى الوظيفي -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5">المسمى الوظيفي <span class="text-rose-500">*</span></label>
                    <input type="text" name="job_title" value="{{ old('job_title', 'أخصائي تخاطب ونطق أول') }}" required placeholder="مثال: أخصائي أول / استشاري تأهيل" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold">
                </div>

                <!-- سنوات الخبرة -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5">سنوات الخبرة العملية <span class="text-rose-500">*</span></label>
                    <input type="number" name="experience_years" value="{{ old('experience_years', 5) }}" min="0" max="50" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold font-mono">
                </div>

                <!-- الهاتف والواتساب -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5">رقم الهاتف / الواتساب <span class="text-rose-500">*</span></label>
                    <input type="text" name="phone" value="{{ old('phone') }}" required placeholder="010XXXXXXXX" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-mono font-bold">
                </div>

                <!-- البريد الإلكتروني -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5">البريد الإلكتروني (اختياري)</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="doctor@center.com" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-mono">
                </div>

                <!-- رقم ترخيص مزاولة المهنة -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5">رقم ترخيص مزاولة المهنة (إن وجد)</label>
                    <input type="text" name="license_number" value="{{ old('license_number') }}" placeholder="مثال: MED-LIC-2026" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-mono">
                </div>

                <!-- المؤهل الدراسي -->
                <div class="sm:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1.5">المؤهل الأكاديمي والشهادات التخصصية</label>
                    <input type="text" name="qualification" value="{{ old('qualification') }}" placeholder="مثال: ماجستير التخاطب وعلاج اضطرابات النطق والكلام - جامعة عين شمس" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-medium">
                </div>

                <!-- حالة الحساب -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5">حالة الأخصائي <span class="text-rose-500">*</span></label>
                    <select name="status" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold">
                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>نشط ومتاح للجلسات </option>
                        <option value="on_leave" {{ old('status') === 'on_leave' ? 'selected' : '' }}>في إجازة </option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>غير نشط / موقوف </option>
                    </select>
                </div>

            </div>

        </div>

        <!-- ==================== 2. إعدادات الغرفة ونظام العمل والمحاسبة ==================== -->
        <div class="bg-white rounded-3xl p-6 md:p-8 border border-slate-100 shadow-sm space-y-6">
            
            <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                <i class="fa-solid fa-calendar-days text-sm" style="color: #0d9488;"></i>
                <h3 class="font-black text-sm text-slate-800">2. جدول العمل والغرفة الافتراضية ونظام المحاسبة</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 text-xs font-medium">
                
                <!-- الغرفة الافتراضية -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5">الغرفة / القاعة الافتراضية للجلسات</label>
                    <select name="default_room" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold">
                        @foreach($rooms as $rm)
                        <option value="{{ $rm }}" {{ old('default_room') === $rm ? 'selected' : '' }}>{{ $rm }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- نظام المحاسبة والراتب -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5">نظام المحاسبة والماليات <span class="text-rose-500">*</span></label>
                    <select name="salary_type" x-model="salaryType" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold">
                        <option value="monthly">راتب شهري ثابت</option>
                        <option value="per_session">مبلغ ثابت لكل جلسة منفذة</option>
                    </select>
                </div>

                <!-- قيمة الجلسة أو النسبة أو الراتب -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5">
                        <span x-text="salaryType === 'per_session' ? 'قيمة الجلسة للأخصائي (ج.م)' : 'الراتب الشهري الثابت (ج.م)'"></span>
                    </label>
                    <input type="number" step="0.01" name="session_rate" value="{{ old('session_rate', 150) }}" placeholder="0.00" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-mono font-bold text-sm text-teal-700">
                </div>

                <!-- أيام العمل الأسبوعية المتاحة -->
                <div class="sm:col-span-3 space-y-2">
                    <label class="block font-bold text-slate-700 text-xs">أيام العمل الأسبوعية المتاحة للأخصائي بالمركز:</label>
                    <div class="grid grid-cols-2 sm:grid-cols-7 gap-2">
                        @foreach($daysList as $day)
                        <label class="p-2.5 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-center gap-2 cursor-pointer hover:bg-slate-100 transition select-none font-bold text-xs">
                            <input type="checkbox" name="work_days[]" value="{{ $day }}" checked class="rounded text-teal-600 focus:ring-0">
                            <span>{{ $day }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <!-- نبذة تعريفية وسيرة ذاتية -->
                <div class="sm:col-span-3 space-y-1.5">
                    <label class="block font-bold text-slate-700 text-xs">نبذة وسيرة ذاتية مختصرة عن الأخصائي:</label>
                    <textarea name="bio" rows="3" placeholder="اكتب نبذة عن خبرات الأخصائي وأهم البرامج العلاجية والتأهيلية التي ينفذها..." class="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-medium text-xs leading-relaxed">{{ old('bio') }}</textarea>
                </div>

            </div>

            <!-- أزرار الإجراءات -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('specialists.index') }}" class="px-6 py-3 rounded-2xl text-slate-500 font-bold hover:bg-slate-100 text-xs transition">
                    إلغاء
                </a>
                <button type="submit" class="px-9 py-3.5 rounded-2xl text-white font-black text-xs shadow-xl hover:opacity-95 active:scale-95 transition flex items-center gap-2" style="background: linear-gradient(135deg, #0d9488 0%, color-mix(in srgb, #0d9488 85%, #000) 100%);">
                    <i class="fa-solid fa-floppy-disk text-sm"></i>
                    <span>حفظ وتسجيل الأخصائي</span>
                </button>
            </div>

        </div>

    </form>

</div>
@endsection



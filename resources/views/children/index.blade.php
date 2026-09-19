@extends('layouts.app')

@section('title', 'ملفات الأطفال')

@section('content')
<div class="space-y-8" x-data="{
    selectedChild: null,
    selectedDiagnoses: [],
    showDetailsModal: false,
    openModal(child) {
        this.selectedChild = child;
        try {
            this.selectedDiagnoses = JSON.parse(child.initial_diagnosis);
            if (!Array.isArray(this.selectedDiagnoses)) {
                this.selectedDiagnoses = [child.initial_diagnosis];
            }
        } catch(e) {
            this.selectedDiagnoses = child.initial_diagnosis ? child.initial_diagnosis.split('\n') : [];
        }
        this.showDetailsModal = true;
    }
}">

    <!-- الترويسة وأزرار الإجراءات -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-white text-lg shadow-md" style="background-color: #0d9488;">
                    <i class="fa-solid fa-child-reaching"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-800">ملفات الأطفال المسجلين (IEP)</h2>
                    <p class="text-xs text-slate-500 font-semibold mt-0.5">إدارة بيانات الأطفال، التشخيصات المستقلة، دكتور المخ والأعصاب، والأدوية</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('children.create') }}" class="px-5 py-2.5 rounded-2xl text-white font-extrabold text-xs shadow-lg hover:opacity-95 active:scale-95 transition flex items-center gap-2" style="background-color: #0d9488;">
                <i class="fa-solid fa-plus text-sm"></i>
                <span>تسجيل طفل جديد</span>
            </a>
        </div>
    </div>

    <!-- رسالة النجاح عند إضافة طفل -->
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl text-xs font-bold flex items-center gap-3 animate-in fade-in">
        <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <!-- بطاقات إحصائيات سريعة للأطفال -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-extrabold text-slate-400 uppercase">إجمالي الأطفال المسجلين</p>
                <h3 class="text-2xl font-black text-slate-800 mt-1">{{ $totalChildren }} <span class="text-xs text-slate-400 font-normal">أطفال</span></h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-children"></i>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-extrabold text-slate-400 uppercase">الأطفال بالخطة النشطة</p>
                <h3 class="text-2xl font-black text-emerald-700 mt-1">{{ $activeChildren }} <span class="text-xs text-slate-400 font-normal">طفل</span></h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-user-check"></i>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-extrabold text-slate-400 uppercase">تذكيرات الواتساب النشطة</p>
                <h3 class="text-2xl font-black text-teal-700 mt-1">{{ $activeChildren }} <span class="text-xs text-slate-400 font-normal">مفعل</span></h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-xl">
                <i class="fa-brands fa-whatsapp"></i>
            </div>
        </div>
    </div>

    <!-- شريط البحث والفلترة -->
    <x-filter-bar 
        search-placeholder="ابحث باسم الطفل، كود الطفل، أو هاتف ولي الأمر..."
        route="{{ route('children.index') }}"
        :filters="[
            'diagnosis_category' => [
                'label' => 'التصنيف',
                'options' => [
                    'speech' => 'تخاطب ونطق',
                    'autism' => 'طيف توحد (ASD)',
                    'adhd' => 'فرط حركة وتشتت (ADHD)',
                    'hearing' => 'ضعف سمعي وقوقعة'
                ]
            ],
            'status' => [
                'label' => 'الحالة',
                'options' => [
                    'active' => 'نشط',
                    'inactive' => 'غير نشط'
                ]
            ]
        ]"
        :sort-options="[
            'created_at' => 'تاريخ الإضافة',
            'name' => 'اسم الطفل',
            'birth_date' => 'تاريخ الميلاد'
        ]"
    />

    <!-- جدول قائمة الأطفال -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="text-[11px] text-slate-400 font-extrabold border-b border-slate-100 bg-slate-50/50">
                        <th class="py-4 px-4">صورة وكود وبيانات الطفل</th>
                        <th class="py-4 px-4">العمر (الزمني / العقلي)</th>
                        <th class="py-4 px-4">ولي الأمر والتواصل</th>
                        <th class="py-4 px-4">التشخيصات المستقلة</th>
                        <th class="py-4 px-4">الأخصائي المعالج</th>
                        <th class="py-4 px-4 text-center">إحصائيات الحضور</th>
                        <th class="py-4 px-4">الحالة</th>
                        <th class="py-4 px-4 text-left">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($children as $child)
                    <tr class="hover:bg-slate-50/80 transition group">
                        
                        <!-- صورة وكود واسم الطفل -->
                        <td class="py-4 px-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $child->avatar_url }}" alt="avatar" class="w-11 h-11 rounded-2xl object-cover ring-2 ring-slate-100 p-0.5 shadow-xs">
                                <div>
                                    <a href="{{ route('children.show', $child) }}" class="font-extrabold text-slate-800 hover:underline flex items-center gap-1.5">
                                        <span>{{ $child->name }}</span>
                                    </a>
                                    <span class="font-mono text-[11px] font-bold" style="color: #0d9488;">{{ $child->code }}</span>
                                </div>
                            </div>
                        </td>

                        <!-- العمر والنوع -->
                        <td class="py-4 px-4 whitespace-nowrap text-xs text-slate-600">
                            <p class="font-bold text-slate-800">زمني: {{ $child->age_text }}</p>
                            @if($child->mental_age)
                            <p class="text-[11px] font-semibold text-purple-600">عقلي: {{ $child->mental_age }}</p>
                            @endif
                            <p class="text-[10px] text-slate-400 mt-0.5">
                                @if($child->gender === 'female')
                                    <i class="fa-solid fa-venus text-pink-500 ml-1"></i> أنثى
                                @else
                                    <i class="fa-solid fa-mars text-blue-500 ml-1"></i> ذكر
                                @endif
                            </p>
                        </td>

                        <!-- ولي الأمر -->
                        <td class="py-4 px-4 whitespace-nowrap text-xs">
                            <p class="font-bold text-slate-800">{{ $child->parent_name }} <span class="text-[10px] text-slate-400 font-normal">({{ $child->parent_relation }})</span></p>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span class="font-mono text-slate-500 font-bold">{{ $child->phone }}</span>
                                <a href="https://wa.me/2{{ $child->phone }}" target="_blank" class="w-5 h-5 rounded-md bg-green-50 text-green-600 hover:bg-green-600 hover:text-white flex items-center justify-center text-[10px] transition" title="واتساب مباشر">
                                    <i class="fa-brands fa-whatsapp"></i>
                                </a>
                            </div>
                        </td>

                        <!-- سطور التشخيصات المستقلة -->
                        <td class="py-4 px-4 text-xs max-w-xs">
                            <div class="space-y-1">
                                @foreach($child->diagnoses_list as $diag)
                                <div class="px-2.5 py-1 rounded-xl text-[11px] font-bold bg-slate-100 text-slate-800 flex items-center gap-1.5 w-fit">
                                    <span class="w-1.5 h-1.5 rounded-full" style="background-color: #0d9488;"></span>
                                    <span>{{ $diag }}</span>
                                </div>
                                @endforeach
                            </div>

                            @if($child->neurologist_name)
                            <p class="text-[11px] text-blue-700 font-medium mt-1.5 truncate">
                                <i class="fa-solid fa-user-doctor text-[9px]"></i> {{ $child->neurologist_name }}
                            </p>
                            @endif
                        </td>

                        <!-- الأخصائي -->
                        <td class="py-4 px-4 text-xs">
                            <p class="font-bold text-slate-700">{{ $child->main_specialist ?? 'غير محدد' }}</p>
                        </td>

                        <!-- إحصائيات الحضور -->
                        <td class="py-4 px-4 text-xs">
                            @php
                                $c_schedules = $child->sessionSchedules()->get();
                                $c_att = $c_schedules->where('attendance_status', 'attended')->count();
                                $c_exc = $c_schedules->filter(function($s) { return $s->status === 'cancelled' || str_contains($s->notes ?? '', 'اعتذار'); })->count();
                                $c_abs = max(0, $c_schedules->where('attendance_status', 'absent')->count() - $c_exc);
                                $c_tot = $c_att + $c_abs + $c_exc;
                                $c_att_pct = $c_tot > 0 ? round(($c_att / $c_tot) * 100) : 0;
                                $c_abs_pct = $c_tot > 0 ? round(($c_abs / $c_tot) * 100) : 0;
                            @endphp
                            @if($c_tot > 0)
                                <div class="flex flex-col gap-1 items-center">
                                    <div class="flex items-center gap-1.5 w-full max-w-[80px]" title="حضور">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                        <div class="w-full bg-slate-100 rounded-full h-2">
                                            <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $c_att_pct }}%"></div>
                                        </div>
                                        <span class="font-bold text-slate-700 text-[10px] w-8">{{ $c_att_pct }}%</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 w-full max-w-[80px]" title="غياب">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 shrink-0"></span>
                                        <div class="w-full bg-slate-100 rounded-full h-2">
                                            <div class="bg-rose-500 h-2 rounded-full" style="width: {{ $c_abs_pct }}%"></div>
                                        </div>
                                        <span class="font-bold text-slate-700 text-[10px] w-8">{{ $c_abs_pct }}%</span>
                                    </div>
                                </div>
                            @else
                                <div class="text-center">
                                    <span class="text-[10px] text-slate-400 font-bold bg-slate-100 px-2 py-1 rounded-lg">لا يوجد</span>
                                </div>
                            @endif
                        </td>

                        <!-- الحالة -->
                        <td class="py-4 px-4 whitespace-nowrap">
                            @if($child->status === 'active')
                                <span class="px-2.5 py-1 rounded-xl text-xs font-black bg-emerald-100 text-emerald-800 inline-flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> نشط
                                </span>
                            @elseif($child->status === 'on_hold')
                                <span class="px-2.5 py-1 rounded-xl text-xs font-black bg-amber-100 text-amber-800">
                                    معلق
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-xl text-xs font-black bg-slate-100 text-slate-600">
                                    منتهي
                                </span>
                            @endif
                        </td>

                        <!-- إجراءات -->
                        <td class="py-4 px-4 text-left whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('children.show', $child) }}" class="px-3 py-1.5 rounded-xl text-white text-xs font-bold transition flex items-center gap-1 shadow-xs hover:opacity-90" style="background-color: #0d9488;" title="عرض بروفايل الطفل الشامل">
                                    <i class="fa-solid fa-address-card text-[11px]"></i>
                                    <span>عرض الملف</span>
                                </a>
                                <a href="{{ route('children.edit', $child) }}" class="px-3 py-1.5 rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white text-xs font-bold transition flex items-center gap-1 shadow-2xs" title="تعديل بيانات الطفل">
                                    <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                                    <span>تعديل</span>
                                </a>
                                <button type="button" @click="openModal({{ json_encode($child) }})" class="px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition" title="معاينة سريعة">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </div>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400 text-xs">
                            <i class="fa-solid fa-child-reaching text-3xl mb-2 text-slate-300"></i>
                            <p class="font-bold">لا يوجد أطفال مسجلين حالياً تطابق معايير البحث.</p>
                            <a href="{{ route('children.create') }}" class="mt-3 inline-block font-extrabold hover:underline" style="color: #0d9488;">+ تسجيل طفل جديد الآن</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- الترقيم والصفحات -->
        <div class="p-4 border-t border-slate-100">
            {{ $children->links() }}
        </div>
    </div>

    <!-- نافذة المعاينة الطبية السريعة (Quick Clinical Preview Modal) -->
    <div x-show="showDetailsModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.outside="showDetailsModal = false" class="bg-white rounded-3xl shadow-2xl border border-slate-100 max-w-lg w-full p-6 space-y-5 animate-in fade-in zoom-in-95 duration-150">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-3">
                    <img :src="selectedChild ? (selectedChild.photo_path ? '/storage/' + selectedChild.photo_path : (selectedChild.avatar || 'https://api.dicebear.com/7.x/bottts/svg?seed=' + encodeURIComponent(selectedChild.name))) : ''" class="w-12 h-12 rounded-2xl object-cover bg-slate-100 p-0.5 shadow-sm">
                    <div>
                        <h4 class="font-black text-base text-slate-800" x-text="selectedChild ? selectedChild.name : ''"></h4>
                        <p class="text-xs font-mono font-bold" style="color: #0d9488;" x-text="selectedChild ? selectedChild.code : ''"></p>
                    </div>
                </div>
                <button @click="showDetailsModal = false" class="text-slate-400 hover:text-slate-600 text-lg">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="space-y-3.5 text-xs">
                
                <div class="grid grid-cols-2 gap-3 p-3 bg-slate-50 rounded-2xl">
                    <div>
                        <span class="text-slate-400 font-bold block text-[11px]">تاريخ الميلاد (الزمني):</span>
                        <span class="font-black text-slate-800" x-text="selectedChild ? selectedChild.birth_date : ''"></span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-bold block text-[11px]">العمر العقلي / اللغوي:</span>
                        <span class="font-black text-purple-700" x-text="selectedChild && selectedChild.mental_age ? selectedChild.mental_age : 'غير مسجل'"></span>
                    </div>
                </div>

                <!-- سطور التشخيصات المستقلة داخل المودال -->
                <div class="space-y-1.5">
                    <span class="text-slate-400 font-bold block text-[11px]">قائمة التشخيصات الطبية المستقلة:</span>
                    <div class="space-y-1.5">
                        <template x-for="(dItem, dIdx) in selectedDiagnoses" :key="dIdx">
                            <div class="flex items-center gap-2 p-2.5 rounded-xl bg-purple-50/70 border border-purple-100 text-purple-950 font-bold">
                                <span class="w-5 h-5 rounded-lg bg-purple-200 text-purple-800 flex items-center justify-center text-[10px]" x-text="dIdx + 1"></span>
                                <span x-text="dItem"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="space-y-1">
                    <span class="text-slate-400 font-bold block text-[11px]"><i class="fa-solid fa-user-doctor ml-1 text-blue-600"></i> طبيب المخ والأعصاب المتابع:</span>
                    <p class="p-2.5 rounded-xl bg-blue-50 text-blue-900 font-bold" x-text="selectedChild && selectedChild.neurologist_name ? selectedChild.neurologist_name : 'لا يوجد طبيب مخ وأعصاب مسجل'"></p>
                </div>

                <div class="space-y-1">
                    <span class="text-slate-400 font-bold block text-[11px]"><i class="fa-solid fa-pills ml-1 text-amber-600"></i> الأدوية والعلاجات الحالية:</span>
                    <p class="p-2.5 rounded-xl bg-amber-50 text-amber-900 font-semibold" x-text="selectedChild && selectedChild.current_medications ? selectedChild.current_medications : 'لا يتناول أدوية حالية'"></p>
                </div>

                <div class="space-y-1">
                    <span class="text-slate-400 font-bold block text-[11px]"><i class="fa-solid fa-brain ml-1 text-purple-600"></i> اختبارات ومقاييس الذكاء السابقة:</span>
                    <p class="p-2.5 rounded-xl bg-purple-50 text-purple-900 font-semibold" x-text="selectedChild && selectedChild.iq_tests_history ? selectedChild.iq_tests_history : 'لم تسجل اختبارات ذكاء سابقة'"></p>
                </div>

            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end">
                <button @click="showDetailsModal = false" class="px-6 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs">إغلاق</button>
            </div>

        </div>
    </div>

</div>
@endsection


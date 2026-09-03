@extends('layouts.app')

@section('title', 'فريق الأخصائيين والتأهيل')

@section('content')
<div class="space-y-8">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl flex items-center justify-center text-white text-lg shadow-md" style="background-color: #0d9488;">
                    <i class="fa-solid fa-user-tie"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-800">فريق الأخصائيين والتأهيل</h2>
                    <p class="text-xs text-slate-500 font-semibold mt-0.5">إدارة بيانات الأطباء والأخصائيين</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('specialists.create') }}" class="px-5 py-2.5 rounded-2xl text-white font-extrabold text-xs shadow-lg hover:opacity-95 active:scale-95 transition flex items-center gap-2" style="background-color: #0d9488;">
                <i class="fa-solid fa-user-plus text-sm"></i>
                <span>+ إضافة أخصائي جديد</span>
            </a>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl text-xs font-bold flex items-center gap-3 animate-in fade-in">
        <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-extrabold text-slate-400 uppercase">إجمالي الأخصائيين</p>
                <h3 class="text-2xl font-black text-slate-800 mt-1">{{ $totalCount }}</h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-cyan-50 text-cyan-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-users-viewfinder"></i>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-extrabold text-slate-400 uppercase">أخصائيين نشطين</p>
                <h3 class="text-2xl font-black text-emerald-700 mt-1">{{ $activeCount }}</h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-user-check"></i>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-extrabold text-slate-400 uppercase">التخصصات المتاحة</p>
                <h3 class="text-2xl font-black text-purple-700 mt-1">{{ count($specializations) }}</h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-brain"></i>
            </div>
        </div>
    </div>

    <!-- Filters -->
    @php
        $filters = [
            'specialization' => [
                'label' => 'التخصص',
                'options' => $specializations
            ],
            'status' => [
                'label' => 'الحالة',
                'options' => [
                    'active' => 'نشط',
                    'inactive' => 'غير نشط'
                ]
            ]
        ];
    @endphp
    
    <x-filter-bar 
        search-placeholder="ابحث باسم الأخصائي، أو الكود..."
        route="{{ route('specialists.index') }}"
        :filters="$filters"
        :sort-options="[
            'created_at' => 'تاريخ الإضافة',
            'name' => 'اسم الأخصائي',
            'children_count' => 'عدد الأطفال',
            'therapy_sessions_count' => 'عدد الجلسات'
        ]"
    />

    <!-- قائمة الأخصائيين (جدول) -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-500">
                        <th class="px-6 py-4 font-semibold">الأخصائي</th>
                        <th class="px-6 py-4 font-semibold">التخصص / المسمى</th>
                        <th class="px-6 py-4 font-semibold">الغرفة / الخبرة</th>
                        <th class="px-6 py-4 font-semibold">أيام العمل</th>
                        <th class="px-6 py-4 font-semibold">الحالة</th>
                        <th class="px-6 py-4 font-semibold text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($specialists as $sp)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $sp->avatar_url }}" alt="{{ $sp->name }}" class="w-10 h-10 rounded-xl object-cover ring-2 ring-slate-100">
                                <div>
                                    <a href="{{ route('specialists.show', $sp) }}" class="font-extrabold text-slate-900 hover:underline block">{{ $sp->name }}</a>
                                    <span class="text-[10px] font-bold text-slate-500">{{ $sp->code }} | <a href="https://wa.me/2{{ $sp->phone }}" target="_blank" class="hover:text-emerald-500 transition"><i class="fa-brands fa-whatsapp"></i> {{ $sp->phone }}</a></span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-xl text-[10px] font-black bg-purple-50 text-purple-800 border border-purple-100 mb-1 inline-block">
                                {{ $sp->specialization }}
                            </span>
                            <p class="text-[10px] font-bold text-slate-500">{{ $sp->job_title }}</p>
                        </td>
                        <td class="px-6 py-4 text-[11px] font-medium text-slate-600">
                            <div class="space-y-1">
                                <p><i class="fa-solid fa-door-open text-slate-400 w-4"></i> {{ $sp->default_room ?? 'غير محددة' }}</p>
                                <p><i class="fa-solid fa-briefcase text-slate-400 w-4"></i> {{ $sp->experience_years }} سنوات خبرة</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if(!empty($sp->work_days) && is_array($sp->work_days))
                            <div class="flex flex-wrap gap-1 max-w-[150px]">
                                @foreach($sp->work_days as $d)
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600">{{ $d }}</span>
                                @endforeach
                            </div>
                            @else
                            <span class="text-xs text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-xs font-bold">
                            @if($sp->status === 'active')
                                <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800">نشط</span>
                            @elseif($sp->status === 'on_leave')
                                <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-800">إجازة</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full bg-rose-100 text-rose-800">موقوف</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center items-center gap-2">
                                <a href="{{ route('specialists.edit', $sp) }}" class="px-2.5 py-1.5 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white transition tooltip" title="تعديل">
                                    <i class="fa-solid fa-pen text-[11px]"></i>
                                </a>
                                <a href="{{ route('specialists.show', $sp) }}" class="px-2.5 py-1.5 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-600 hover:text-white transition tooltip" title="عرض الملف">
                                    <i class="fa-solid fa-eye text-[11px]"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                            <i class="fa-solid fa-user-doctor text-4xl mb-3 opacity-30"></i>
                            <p class="font-bold text-sm">لا يوجد أخصائيين يطابقون بحثك.</p>
                            <a href="{{ route('specialists.create') }}" class="inline-block mt-4 px-5 py-2.5 rounded-2xl text-white font-bold text-xs shadow-md transition" style="background-color: #0d9488;">+ إضافة أخصائي جديد</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="p-4 bg-white rounded-2xl border border-slate-100">
        {{ $specialists->links() }}
    </div>

</div>
@endsection



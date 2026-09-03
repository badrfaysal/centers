@extends('layouts.app')

@section('title', 'قائمة الانتظار')

@section('content')
<div x-data="{ showAddModal: false }" class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-6 rounded-3xl shadow-sm border border-slate-200">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-500 shadow-inner">
                <i class="fa-solid fa-hourglass-half text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">قائمة الانتظار (Waiting List)</h1>
                <p class="text-slate-500 text-sm mt-1">إدارة الأطفال المنتظرين دورهم لكل أخصائي</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <button @click="showAddModal = true" class="bg-amber-500 text-white px-6 py-3 rounded-2xl font-bold hover:bg-amber-600 transition-colors shadow-lg shadow-amber-500/30 flex items-center gap-2">
                <i class="fa-solid fa-plus"></i>
                إضافة للقائمة
            </button>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="bg-emerald-50 border-r-4 border-emerald-500 p-4 rounded-2xl flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-emerald-500 text-xl"></i>
            <p class="text-emerald-700 font-medium">{{ session('success') }}</p>
        </div>
    @endif
    @if($errors->any())
        <div class="bg-rose-50 border-r-4 border-rose-500 p-4 rounded-2xl flex items-center gap-3">
            <i class="fa-solid fa-triangle-exclamation text-rose-500 text-xl"></i>
            <p class="text-rose-700 font-medium">{{ $errors->first() }}</p>
        </div>
    @endif

    <!-- Main Table View -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        
        <!-- Filters -->
        <div class="p-4 border-b border-slate-100 bg-slate-50">
            <form action="{{ route('waitlists.index') }}" method="GET" class="flex items-center gap-3 w-full max-w-sm">
                <select name="specialist_id" class="flex-1 px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-bold focus:outline-none" onchange="this.form.submit()">
                    <option value="all">كل الأخصائيين</option>
                    @foreach($specialists as $specialist)
                        <option value="{{ $specialist->id }}" {{ request('specialist_id') == $specialist->id ? 'selected' : '' }}>{{ $specialist->name }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-500">
                        <th class="px-6 py-4 font-semibold">الدور</th>
                        <th class="px-6 py-4 font-semibold">اسم الطفل</th>
                        <th class="px-6 py-4 font-semibold">الأخصائي المطلوب</th>
                        <th class="px-6 py-4 font-semibold">الأولوية</th>
                        <th class="px-6 py-4 font-semibold">تاريخ الإضافة</th>
                        <th class="px-6 py-4 font-semibold">ملاحظات</th>
                        <th class="px-6 py-4 font-semibold text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($waitlists as $index => $item)
                        @if(request()->filled('specialist_id') && request('specialist_id') !== 'all' && request('specialist_id') != $item->specialist_id)
                            @continue
                        @endif
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="w-8 h-8 bg-amber-100 text-amber-700 font-black text-xs rounded-lg flex items-center justify-center">
                                    #{{ $index + 1 }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-800">{{ $item->child->name }}</p>
                                <p class="text-xs text-slate-500">{{ $item->child->code }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <img src="{{ $item->specialist->photo_path ? asset('storage/' . $item->specialist->photo_path) : 'https://ui-avatars.com/api/?name='.urlencode($item->specialist->name).'&background=e2e8f0&color=64748b' }}" alt="{{ $item->specialist->name }}" class="w-8 h-8 rounded-lg object-cover">
                                    <div>
                                        <p class="font-bold text-slate-700 text-xs">{{ $item->specialist->name }}</p>
                                        <p class="text-[10px] text-slate-500">{{ $item->specialist->specialization }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($item->priority == 'high')
                                    <span class="px-2.5 py-1 rounded-full bg-rose-100 text-rose-700 text-[10px] font-bold"><i class="fa-solid fa-angles-up ml-1"></i>قصوى</span>
                                @elseif($item->priority == 'normal')
                                    <span class="px-2.5 py-1 rounded-full bg-blue-100 text-blue-700 text-[10px] font-bold"><i class="fa-solid fa-minus ml-1"></i>عادية</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 text-[10px] font-bold"><i class="fa-solid fa-angle-down ml-1"></i>منخفضة</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500">
                                <p>{{ $item->created_at->format('Y-m-d') }}</p>
                                <p class="text-[10px]">منذ {{ $item->created_at->diffForHumans() }}</p>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-600 max-w-xs truncate" title="{{ $item->notes }}">
                                {{ $item->notes ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <form action="{{ route('waitlists.updateStatus', $item->id) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="scheduled">
                                        <button type="submit" class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white transition flex items-center justify-center tooltip" title="تم الحجز له">
                                            <i class="fa-solid fa-calendar-check"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('waitlists.updateStatus', $item->id) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="cancelled">
                                        <button type="submit" onclick="return confirm('هل أنت متأكد من الإلغاء؟')" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white transition flex items-center justify-center tooltip" title="إلغاء من القائمة">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                <i class="fa-solid fa-clipboard-list text-4xl mb-3 opacity-30"></i>
                                <p class="font-bold text-sm">قائمة الانتظار فارغة حالياً.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Modal -->
    <div x-show="showAddModal" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0"
         style="display: none;">
         
        <div x-show="showAddModal" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm"
             @click="showAddModal = false"></div>

        <div x-show="showAddModal" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="bg-white rounded-3xl shadow-xl border border-slate-200 w-full max-w-md relative z-10 overflow-hidden">
            
            <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center text-amber-600">
                        <i class="fa-solid fa-plus"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800">إضافة لقائمة الانتظار</h3>
                </div>
                <button @click="showAddModal = false" class="text-slate-400 hover:text-slate-600 transition-colors w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-100">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form action="{{ route('waitlists.store') }}" method="POST" class="p-6">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">اسم الطفل <span class="text-rose-500">*</span></label>
                        <select name="child_id" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-brand-primary/20 text-slate-700 transition-all font-bold">
                            <option value="">-- اختر الطفل --</option>
                            @foreach($children as $child)
                                <option value="{{ $child->id }}">{{ $child->name }} ({{ $child->code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">الأخصائي المطلوب <span class="text-rose-500">*</span></label>
                        <select name="specialist_id" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-brand-primary/20 text-slate-700 transition-all font-bold">
                            <option value="">-- اختر الأخصائي --</option>
                            @foreach($specialists as $specialist)
                                <option value="{{ $specialist->id }}">{{ $specialist->name }} ({{ $specialist->specialization }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">الأولوية <span class="text-rose-500">*</span></label>
                        <div class="grid grid-cols-3 gap-3">
                            <label class="cursor-pointer">
                                <input type="radio" name="priority" value="high" class="peer sr-only">
                                <div class="text-center py-2 px-3 border border-slate-200 rounded-xl peer-checked:bg-rose-50 peer-checked:border-rose-500 peer-checked:text-rose-600 transition-all">
                                    <i class="fa-solid fa-angles-up mb-1"></i><br>
                                    <span class="text-xs font-bold">قصوى</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="priority" value="normal" checked class="peer sr-only">
                                <div class="text-center py-2 px-3 border border-slate-200 rounded-xl peer-checked:bg-blue-50 peer-checked:border-blue-500 peer-checked:text-blue-600 transition-all">
                                    <i class="fa-solid fa-minus mb-1"></i><br>
                                    <span class="text-xs font-bold">عادية</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="priority" value="low" class="peer sr-only">
                                <div class="text-center py-2 px-3 border border-slate-200 rounded-xl peer-checked:bg-slate-100 peer-checked:border-slate-400 peer-checked:text-slate-600 transition-all">
                                    <i class="fa-solid fa-angle-down mb-1"></i><br>
                                    <span class="text-xs font-bold">منخفضة</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">ملاحظات (اختياري)</label>
                        <textarea name="notes" rows="2" placeholder="مثال: يفضل المواعيد المسائية..." class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-brand-primary/20 text-slate-700 transition-all resize-none"></textarea>
                    </div>
                </div>

                <div class="mt-8 flex items-center justify-end gap-3">
                    <button type="button" @click="showAddModal = false" class="px-6 py-3 rounded-2xl font-bold text-slate-600 hover:bg-slate-100 transition-colors">
                        إلغاء
                    </button>
                    <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white px-8 py-3 rounded-2xl font-bold transition-colors shadow-lg shadow-amber-500/20 flex items-center gap-2">
                        <i class="fa-solid fa-check"></i>
                        إضافة للقائمة
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'قائمة الدور (الانتظار)')

@push('styles')
<style>
    .animated-bg {
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-radius: 24px;
        min-height: 60vh;
    }
    .bg-icon {
        position: absolute;
        color: rgba(15, 23, 42, 0.03);
        animation: float 20s infinite linear;
        z-index: 0;
    }
    .bg-icon:nth-child(1) { top: 5%; left: 5%; font-size: 10rem; animation-duration: 25s; }
    .bg-icon:nth-child(2) { top: 40%; right: 5%; font-size: 15rem; animation-duration: 35s; animation-direction: reverse; }
    .bg-icon:nth-child(3) { bottom: 5%; left: 30%; font-size: 12rem; animation-duration: 30s; }
    
    @keyframes float {
        0% { transform: translateY(0) rotate(0deg); }
        50% { transform: translateY(-30px) rotate(15deg); }
        100% { transform: translateY(0) rotate(0deg); }
    }
    
    .queue-card {
        position: relative;
        z-index: 1;
    }
</style>
@endpush

@section('content')
<div x-data="{ showAddModal: false }" class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-6 rounded-3xl shadow-sm border border-slate-200">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-500 shadow-inner">
                <i class="fa-solid fa-list-ol text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">قائمة الدور (الانتظار)</h1>
                <p class="text-slate-500 text-sm mt-1">ترتيب دخول الأطفال للجلسات عند الأخصائيين</p>
            </div>
        </div>
        <div class="flex items-center gap-3 w-full md:w-auto">
            <form action="{{ route('waitlists.index') }}" method="GET" class="flex-1 md:w-48">
                <select name="specialist_id" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold focus:outline-none" onchange="this.form.submit()">
                    <option value="all">كل الأخصائيين</option>
                    @foreach($specialists as $specialist)
                        <option value="{{ $specialist->id }}" {{ request('specialist_id') == $specialist->id ? 'selected' : '' }}>{{ $specialist->name }}</option>
                    @endforeach
                </select>
            </form>
            <button @click="showAddModal = true" class="bg-indigo-600 text-white px-5 py-3 rounded-2xl font-bold hover:bg-indigo-700 transition-colors shadow-lg shadow-indigo-500/30 flex items-center gap-2 shrink-0">
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

    <!-- Queue Board -->
    <div class="animated-bg p-6 border border-slate-200 shadow-inner">
        <i class="fa-solid fa-users bg-icon"></i>
        <i class="fa-solid fa-clipboard-list bg-icon"></i>
        <i class="fa-regular fa-clock bg-icon"></i>

        <div class="max-w-4xl mx-auto space-y-4">
            @forelse($waitlists as $index => $item)
                @if(request()->filled('specialist_id') && request('specialist_id') !== 'all' && request('specialist_id') != $item->specialist_id)
                    @continue
                @endif
                
                <div class="queue-card bg-white/90 backdrop-blur-md rounded-3xl p-5 border border-white shadow-xl flex flex-col md:flex-row items-center gap-6 transition hover:-translate-y-1">
                    <!-- Turn Number -->
                    <div class="flex-shrink-0 w-20 h-20 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex flex-col items-center justify-center text-white shadow-lg shadow-indigo-500/30">
                        <span class="text-xs font-bold opacity-80 mb-0.5">الدور</span>
                        <span class="text-3xl font-black">#{{ $index + 1 }}</span>
                    </div>
                    
                    <!-- Details -->
                    <div class="flex-1 text-center md:text-right">
                        <h3 class="text-xl font-black text-slate-800">{{ $item->child->name }}</h3>
                        <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 mt-2">
                            <span class="px-3 py-1 rounded-xl bg-slate-100 text-slate-600 text-xs font-bold flex items-center gap-1.5">
                                <i class="fa-solid fa-user-doctor text-slate-400"></i>
                                الأخصائي: {{ $item->specialist->name }}
                            </span>
                            <span class="px-3 py-1 rounded-xl bg-slate-100 text-slate-600 text-xs font-bold flex items-center gap-1.5">
                                <i class="fa-regular fa-clock text-slate-400"></i>
                                منذ {{ $item->created_at->diffForHumans() }}
                            </span>
                        </div>
                        @if($item->notes)
                        <p class="text-xs font-semibold text-slate-500 mt-3 bg-slate-50 p-2 rounded-lg inline-block w-full md:w-auto text-right">
                            <i class="fa-solid fa-quote-right text-slate-300 ml-1"></i>
                            {{ $item->notes }}
                        </p>
                        @endif
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex items-center gap-3 w-full md:w-auto mt-4 md:mt-0">
                        <form action="{{ route('waitlists.updateStatus', $item->id) }}" method="POST" class="flex-1 md:flex-none">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="scheduled">
                            <button type="submit" class="w-full md:w-auto px-6 py-3 rounded-2xl bg-emerald-500 hover:bg-emerald-600 text-white font-black text-sm shadow-lg shadow-emerald-500/30 transition flex items-center justify-center gap-2">
                                <i class="fa-solid fa-door-open"></i>
                                <span>دخل الجلسة</span>
                            </button>
                        </form>
                        
                        <form action="{{ route('waitlists.updateStatus', $item->id) }}" method="POST" class="flex-1 md:flex-none">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="cancelled">
                            <button type="submit" onclick="return confirm('هل أنت متأكد من الإلغاء؟')" class="w-full md:w-auto px-4 py-3 rounded-2xl bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white font-bold text-sm transition flex items-center justify-center gap-2">
                                <i class="fa-solid fa-xmark"></i>
                                <span>إلغاء</span>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center py-20 relative z-10">
                    <div class="w-24 h-24 bg-white/50 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm border border-white">
                        <i class="fa-solid fa-mug-hot text-4xl text-slate-400"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-700">لا يوجد أحد في قائمة الانتظار</h3>
                    <p class="text-slate-500 mt-2">يمكن للأخصائيين أخذ استراحة الآن!</p>
                </div>
            @endforelse
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
                    <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center text-indigo-600">
                        <i class="fa-solid fa-plus"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800">إضافة لقائمة الدور</h3>
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
                    
                    <input type="hidden" name="priority" value="normal">

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">ملاحظات (اختياري)</label>
                        <textarea name="notes" rows="2" placeholder="مثال: يرجى الانتباه أن الطفل منزعج قليلاً..." class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-brand-primary/20 text-slate-700 transition-all resize-none"></textarea>
                    </div>
                </div>

                <div class="mt-8 flex items-center justify-end gap-3">
                    <button type="button" @click="showAddModal = false" class="px-6 py-3 rounded-2xl font-bold text-slate-600 hover:bg-slate-100 transition-colors">
                        إلغاء
                    </button>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-2xl font-bold transition-colors shadow-lg shadow-indigo-500/20 flex items-center gap-2">
                        <i class="fa-solid fa-check"></i>
                        تأكيد وإضافة
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

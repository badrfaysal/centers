@extends('layouts.app')
@section('title', 'التمارين المنزلية: ' . $child->name)
@section('content')
<div class="container mx-auto px-4 py-8 max-w-5xl" x-data="{ tab: 'new' }">
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('parent.portal', $child->code) }}" class="text-gray-500 hover:text-emerald-600 transition">
                <i class="fa-solid fa-arrow-right text-xl"></i>
            </a>
            <div>
                <h1 class="text-3xl font-black text-slate-800 flex items-center gap-3">
                    <i class="fa-solid fa-house-chimney-medical text-emerald-600"></i>
                    التمارين المنزلية
                </h1>
                <p class="text-slate-500 mt-1 text-sm font-semibold">تابع تدريبات النطق الخاصة بطفلك وتفاعل مع الأخصائي</p>
            </div>
        </div>
    </div>

    <!-- Tabs Header -->
    <div class="flex items-center gap-4 mb-6 border-b border-slate-200">
        <button @click="tab = 'new'" 
                :class="tab === 'new' ? 'border-emerald-600 text-emerald-700 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 font-medium'"
                class="pb-3 px-2 border-b-4 transition text-lg flex items-center gap-2">
            تمارين جديدة / قيد المتابعة
            @php
                $newCount = $homeworks->filter(fn($hw) => in_array($hw->status, ['pending', 'reviewed']))->count();
            @endphp
            @if($newCount > 0)
                <span class="bg-rose-500 text-white text-[10px] font-black px-2 py-0.5 rounded-full">{{ $newCount }}</span>
            @endif
        </button>
        <button @click="tab = 'completed'" 
                :class="tab === 'completed' ? 'border-emerald-600 text-emerald-700 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 font-medium'"
                class="pb-3 px-2 border-b-4 transition text-lg flex items-center gap-2">
            تم الانتهاء منها
        </button>
    </div>

    <!-- Tabs Content -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse($homeworks as $hw)
            @php
                $unreadReplies = $hw->messages()->where('sender_type', 'specialist')->whereNull('read_at')->count();
                $isNew = in_array($hw->status, ['pending', 'reviewed']);
            @endphp
            <div x-show="tab === '{{ $isNew ? 'new' : 'completed' }}'" 
                 class="bg-white p-6 rounded-3xl shadow-sm border {{ $unreadReplies > 0 ? 'border-emerald-300 bg-emerald-50/30' : 'border-slate-100' }} hover:shadow-md transition relative overflow-hidden">
                
                @if($hw->status === 'submitted')
                <div class="absolute -right-12 top-6 bg-blue-500 text-white text-[9px] font-black py-1 px-12 transform rotate-45 shadow-sm">
                    تم الرد
                </div>
                @elseif($hw->status === 'reviewed')
                <div class="absolute -right-12 top-6 bg-emerald-500 text-white text-[9px] font-black py-1 px-12 transform rotate-45 shadow-sm">
                    تم التقييم
                </div>
                @endif

                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-xl font-bold text-slate-800">{{ $hw->title }}</h3>
                    @if($unreadReplies > 0)
                        <span class="bg-rose-100 text-rose-700 px-3 py-1 rounded-full text-xs font-bold animate-pulse shadow-sm">يوجد رد جديد</span>
                    @endif
                </div>
                <p class="text-slate-600 line-clamp-2 mb-6 font-medium text-sm">{{ $hw->description }}</p>
                <div class="flex justify-between items-center text-sm text-slate-500 border-t border-slate-100 pt-4">
                    <div class="flex items-center gap-2 font-mono text-xs">
                        <i class="fa-solid fa-clock"></i> {{ $hw->created_at->format('Y-m-d') }}
                    </div>
                    <a href="{{ route('parent.homeworks.show', ['code' => $child->code, 'homework' => $hw->id]) }}" class="{{ $unreadReplies > 0 ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }} px-5 py-2 rounded-xl font-bold transition shadow-sm flex items-center gap-2">
                        @if($unreadReplies > 0 || $hw->status === 'pending')
                            <i class="fa-solid fa-microphone-lines"></i> التفاعل الآن
                        @else
                            <i class="fa-solid fa-eye"></i> مراجعة
                        @endif
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-1 md:col-span-2 bg-slate-50 p-12 text-center rounded-3xl border border-dashed border-slate-200">
                <i class="fa-solid fa-face-smile text-5xl text-slate-300 mb-4"></i>
                <h3 class="text-xl font-bold text-slate-600">لا توجد تمارين حالياً</h3>
                <p class="text-slate-400 mt-2 font-medium">سيظهر هنا أي تدريبات نطق يطلبها الأخصائي</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
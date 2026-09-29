@extends('layouts.app')
@section('title', 'التمارين المنزلية ومتابعة النطق')
@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl" x-data="{ tab: 'new' }">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-black text-slate-800 flex items-center gap-3">
                <i class="fa-solid fa-microphone-lines text-emerald-600"></i>
                التمارين المنزلية وتحليل النطق
            </h1>
            <p class="text-slate-500 mt-2 font-semibold">تابع تدريبات الأطفال واستمع إلى تسجيلات النطق الخاصة بهم</p>
        </div>
        <button onclick="document.getElementById('newHomeworkModal').classList.remove('hidden')" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-xl shadow-lg transition-all flex items-center gap-2 font-bold transform hover:scale-105">
            <i class="fa-solid fa-plus"></i> إضافة تمرين جديد
        </button>
    </div>

    <!-- Tabs Header -->
    <div class="flex items-center gap-4 mb-6 border-b border-slate-200">
        <button @click="tab = 'new'" 
                :class="tab === 'new' ? 'border-emerald-600 text-emerald-700 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 font-medium'"
                class="pb-3 px-2 border-b-4 transition text-lg flex items-center gap-2">
            بانتظار المراجعة
            @php
                $newCount = $homeworks->filter(fn($hw) => $hw->messages()->where('sender_type', 'parent')->whereNull('read_at')->count() > 0)->count();
            @endphp
            @if($newCount > 0)
                <span class="bg-rose-500 text-white text-[10px] font-black px-2 py-0.5 rounded-full">{{ $newCount }}</span>
            @endif
        </button>
        <button @click="tab = 'completed'" 
                :class="tab === 'completed' ? 'border-emerald-600 text-emerald-700 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 font-medium'"
                class="pb-3 px-2 border-b-4 transition text-lg flex items-center gap-2">
            تمت المراجعة / قيد التنفيذ
        </button>
    </div>

    <!-- قائمة التمارين -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 text-sm font-bold text-slate-700">الطفل</th>
                        <th class="px-6 py-4 text-sm font-bold text-slate-700">عنوان التمرين</th>
                        <th class="px-6 py-4 text-sm font-bold text-slate-700">تاريخ الإضافة</th>
                        <th class="px-6 py-4 text-sm font-bold text-slate-700 text-center">الحالة</th>
                        <th class="px-6 py-4 text-sm font-bold text-slate-700 text-center">الإجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($homeworks as $hw)
                        @php
                            $unreadReplies = $hw->messages()->where('sender_type', 'parent')->whereNull('read_at')->count();
                            $isNew = $unreadReplies > 0;
                        @endphp
                        <tr x-show="tab === '{{ $isNew ? 'new' : 'completed' }}'" class="hover:bg-emerald-50/50 transition-colors {{ $isNew ? 'bg-emerald-50/30' : '' }}">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 font-black shadow-sm">
                                        {{ mb_substr($hw->child->name, 0, 1) }}
                                    </div>
                                    <div class="font-black text-slate-800">{{ $hw->child->name }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-slate-800 font-bold">{{ $hw->title }}</div>
                                @if($hw->therapy_session_id)
                                    <span class="text-[10px] text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md mt-1 inline-block font-bold">مرتبط بجلسة</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-600 font-mono text-sm">
                                {{ $hw->created_at->format('Y-m-d') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($isNew)
                                    <span class="bg-rose-100 text-rose-700 px-3 py-1 rounded-full text-xs font-black inline-flex items-center gap-1.5 shadow-sm">
                                        <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                                        تسجيل جديد
                                    </span>
                                @elseif($hw->status === 'reviewed')
                                    <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold">تم الرد</span>
                                @else
                                    <span class="bg-slate-100 text-slate-700 px-3 py-1 rounded-full text-xs font-bold">قيد الانتظار</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('doctor.homeworks.show', $hw->id) }}" class="inline-flex items-center gap-2 {{ $isNew ? 'bg-emerald-600 text-white hover:bg-emerald-700 border-transparent shadow-md' : 'bg-white border border-slate-200 text-slate-700 hover:bg-emerald-50 hover:text-emerald-600 hover:border-emerald-200' }} px-4 py-2.5 rounded-xl text-sm font-bold transition-all transform hover:scale-105">
                                    @if($isNew)
                                        <i class="fa-solid fa-headphones"></i> استماع والرد
                                    @else
                                        <i class="fa-solid fa-eye"></i> عرض المحادثة
                                    @endif
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center text-slate-400 bg-slate-50/50">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fa-solid fa-folder-open text-5xl text-slate-300 mb-4"></i>
                                    <p class="text-lg font-bold text-slate-500">لا يوجد تمارين منزلية حالياً</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
    </div>
</div>

<!-- نافذة إضافة تمرين جديد -->
<div id="newHomeworkModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full overflow-hidden transform transition-all">
        <div class="p-6 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h3 class="text-xl font-black text-slate-800 flex items-center gap-2"><i class="fa-solid fa-plus text-emerald-600"></i> إضافة تمرين جديد</h3>
            <button onclick="document.getElementById('newHomeworkModal').classList.add('hidden')" class="text-slate-400 hover:text-rose-500 transition-colors">
                <i class="fa-solid fa-times text-xl"></i>
            </button>
        </div>
        <form action="{{ route('doctor.homeworks.store') }}" method="POST" class="p-6 space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">اختر الطفل</label>
                <select name="child_id" required class="w-full border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-emerald-500 bg-slate-50">
                    <option value="">-- اختر الطفل --</option>
                    @foreach($children as $child)
                        <option value="{{ $child->id }}">{{ $child->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">عنوان التمرين (مثال: نطق حرف الراء)</label>
                <input type="text" name="title" required class="w-full border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-emerald-500 bg-slate-50">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">التعليمات / الشرح المستهدف</label>
                <textarea name="description" rows="4" class="w-full border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-emerald-500 bg-slate-50" placeholder="اكتب لولي الأمر كيف يمرن الطفل..."></textarea>
            </div>
            <div class="pt-4 flex gap-3">
                <button type="submit" class="flex-1 bg-emerald-600 text-white font-bold py-3.5 rounded-xl hover:bg-emerald-700 transition shadow-lg shadow-emerald-500/30">حفظ وإرسال التمرين</button>
                <button type="button" onclick="document.getElementById('newHomeworkModal').classList.add('hidden')" class="px-6 bg-slate-100 text-slate-700 font-bold py-3.5 rounded-xl hover:bg-slate-200 transition">إلغاء</button>
            </div>
        </form>
    </div>
</div>
@endsection
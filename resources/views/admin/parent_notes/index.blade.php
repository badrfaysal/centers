@extends('layouts.app')

@section('title', 'ملاحظات وشكاوى ومقترحات أولياء الأمور')

@section('content')
<div class="space-y-8" x-data="{ 
    activeTab: '{{ request('tab', 'messages') }}',
    replyModalOpen: false,
    selectedMessage: null,
    openReply(msg) {
        this.selectedMessage = msg;
        this.replyModalOpen = true;
    }
}">

    <!-- ==================== الترويسة وأزرار التحكم ==================== -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-white text-xl shadow-lg relative bg-teal-600">
                    <i class="fa-solid fa-envelope-open-text"></i>
                    @if($newNotesCount > 0)
                        <span class="absolute -top-1 -right-1 w-4 h-4 bg-rose-500 rounded-full ring-2 ring-white animate-ping"></span>
                        <span class="absolute -top-1 -right-1 w-4 h-4 bg-rose-600 rounded-full ring-2 ring-white flex items-center justify-center text-[9px] font-black">!</span>
                    @endif
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-2xl font-black text-slate-800">ملاحظات وشكاوى ومقترحات أولياء الأمور</h2>
                        @if($newNotesCount > 0)
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-rose-100 text-rose-800 border border-rose-200 animate-pulse">
                            {{ $newNotesCount }} ملاحظة جديدة بانتظار الرد
                        </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 font-semibold mt-0.5">متابعة الرسائل المباشرة، الشكاوى، استفسارات الأهالي، وتقييمات خدمات المركز</p>
                </div>
            </div>
        </div>
    </div>

    <!-- رسائل النجاح إن وجدت -->
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl text-xs font-bold flex items-center gap-3 animate-in fade-in">
        <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <!-- ==================== كروت التنبيهات والإحصائيات ==================== -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        
        <!-- 1. ملاحظات جديدة بانتظار الرد (تنبيه أحمر نابض) -->
        <div class="bg-white rounded-3xl p-5 border shadow-sm relative overflow-hidden transition group {{ $newNotesCount > 0 ? 'border-rose-300 ring-2 ring-rose-400/20 bg-rose-50/20' : 'border-slate-100' }}">
            @if($newNotesCount > 0)
                <span class="absolute top-0 right-0 w-24 h-24 bg-rose-500/10 rounded-full blur-xl"></span>
            @endif
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-extrabold uppercase {{ $newNotesCount > 0 ? 'text-rose-600' : 'text-slate-400' }}">
                        بانتظار الرد العاجل:
                    </span>
                    <h3 class="text-3xl font-black mt-1 {{ $newNotesCount > 0 ? 'text-rose-600' : 'text-slate-800' }}">
                        {{ $newNotesCount }}
                    </h3>
                    <p class="text-[10px] font-bold text-slate-400 mt-0.5">رسائل وملاحظات جديدة</p>
                </div>
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl {{ $newNotesCount > 0 ? 'bg-rose-100 text-rose-600 animate-bounce' : 'bg-slate-100 text-slate-400' }}">
                    <i class="fa-solid fa-bell"></i>
                </div>
            </div>
        </div>

        <!-- 2. ملاحظات عاجلة وهامة -->
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-extrabold text-amber-800 uppercase">ملاحظات مصنفة كـ عاجلة:</span>
                <h3 class="text-3xl font-black text-amber-800 mt-1">{{ $urgentNotesCount }}</h3>
                <p class="text-[10px] text-amber-700 font-bold mt-0.5">تحتاج متابعة إدارية </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-bolt"></i>
            </div>
        </div>

        <!-- 3. ملاحظات تم الرد عليها وحلها -->
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-extrabold text-slate-400 uppercase">تم الرد والمعالجة:</span>
                <h3 class="text-3xl font-black text-emerald-700 mt-1">{{ $resolvedNotesCount }}</h3>
                <p class="text-[10px] text-emerald-600 font-bold mt-0.5">ردود معتمدة للأهل </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>

        <!-- 4. تقييمات ومقترحات المركز -->
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-extrabold text-slate-400 uppercase">تقييمات ومقترحات المركز:</span>
                <h3 class="text-3xl font-black text-purple-700 mt-1">{{ $totalRatingsCount }}</h3>
                <p class="text-[10px] text-purple-600 font-bold mt-0.5">آراء وتغذية راجعة </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-star"></i>
            </div>
        </div>

    </div>

    <!-- ==================== شريط الفلاتر والبحث ==================== -->
    <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm space-y-4">
        
        <div class="flex flex-wrap items-center justify-between gap-3">
            
            <!-- أزرار التصفية السريعة -->
            <div class="flex flex-wrap items-center gap-2 text-xs font-bold">
                <a href="{{ route('admin.parent-notes.index') }}" class="px-3.5 py-2 rounded-xl transition {{ !request()->hasAny(['filter', 'recipient']) ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    الكل ({{ $messages->total() }})
                </a>

                <a href="{{ route('admin.parent-notes.index', ['filter' => 'new']) }}" class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ request('filter') === 'new' ? 'bg-rose-600 text-white font-black' : 'bg-rose-50 text-rose-800 hover:bg-rose-100 border border-rose-200' }}">
                    <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
                    <span>جديدة بانتظار الرد ({{ $newNotesCount }})</span>
                </a>

                <a href="{{ route('admin.parent-notes.index', ['filter' => 'urgent']) }}" class="px-3.5 py-2 rounded-xl transition flex items-center gap-1 {{ request('filter') === 'urgent' ? 'bg-amber-600 text-white' : 'bg-amber-50 text-amber-800 hover:bg-amber-100 border border-amber-200' }}">
                    <i class="fa-solid fa-bolt text-xs"></i>
                    <span>عاجلة وهامة ({{ $urgentNotesCount }})</span>
                </a>

                <a href="{{ route('admin.parent-notes.index', ['filter' => 'resolved']) }}" class="px-3.5 py-2 rounded-xl transition {{ request('filter') === 'resolved' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-800 hover:bg-emerald-100 border border-emerald-200' }}">
                    تم الرد والحل ({{ $resolvedNotesCount }})
                </a>

                <a href="{{ route('admin.parent-notes.index', ['recipient' => 'center']) }}" class="px-3.5 py-2 rounded-xl transition {{ request('recipient') === 'center' ? 'bg-purple-600 text-white' : 'bg-purple-50 text-purple-800 hover:bg-purple-100 border border-purple-200' }}">
                    موجهة لإدارة المركز
                </a>
            </div>

            <!-- تبديل إلى تبويب تقييمات المركز -->
            <button type="button" @click="activeTab = activeTab === 'ratings' ? 'messages' : 'ratings'" class="px-4 py-2 rounded-xl border text-xs font-bold transition flex items-center gap-1.5" :class="activeTab === 'ratings' ? 'bg-amber-500 text-white border-amber-500' : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100'">
                <i class="fa-solid fa-star"></i>
                <span x-text="activeTab === 'ratings' ? 'عرض الرسائل والملاحظات' : 'عرض تقييمات ومقترحات المركز (' + {{ $totalRatingsCount }} + ')'"></span>
            </button>

        </div>

        <!-- حقل البحث -->
        <form action="{{ route('admin.parent-notes.index') }}" method="GET" class="flex gap-2 text-xs">
            <div class="relative flex-1">
                <i class="fa-solid fa-magnifying-glass absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث باسم ولي الأمر، كود الطفل (CH-1001)، اسم الطفل، أو نص الملاحظة..." class="w-full pl-3 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-semibold">
            </div>
            <button type="submit" class="px-5 py-2.5 rounded-2xl bg-teal-600 hover:bg-teal-700 text-white font-bold transition flex items-center gap-1.5 shrink-0">
                <span>بحث</span>
            </button>
            @if(request()->hasAny(['search', 'filter', 'recipient']))
            <a href="{{ route('admin.parent-notes.index') }}" class="px-3.5 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold transition flex items-center justify-center" title="إلغاء الفلترة">
                <i class="fa-solid fa-rotate-left"></i>
            </a>
            @endif
        </form>

    </div>

    <!-- ==================== تبويب 1: قائمة رسائل وشكاوى أولياء الأمور ==================== -->
    <div x-show="activeTab === 'messages'" class="space-y-4">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @forelse($messages as $msg)
            <div class="bg-white rounded-3xl p-6 border shadow-sm space-y-4 transition hover:border-slate-300 {{ !$msg->doctor_reply ? 'border-rose-200 bg-rose-50/10' : 'border-slate-100' }}">
                
                <!-- رأس كارت الرسالة -->
                <div class="flex items-start justify-between gap-3 pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <img src="{{ $msg->child ? $msg->child->avatar_url : 'https://api.dicebear.com/7.x/bottts/svg?seed=child' }}" class="w-12 h-12 rounded-2xl object-cover ring-2 ring-slate-100 p-0.5">
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="font-extrabold text-sm text-slate-900">{{ $msg->parent_name }}</h4>
                                @if($msg->child)
                                <span class="text-[11px] text-slate-500 font-bold">(والد: <a href="{{ route('children.show', $msg->child) }}" class="text-teal-700 hover:underline">{{ $msg->child->name }}</a>)</span>
                                @endif
                            </div>
                            
                            <div class="flex flex-wrap items-center gap-2 mt-0.5 text-[10px]">
                                <span class="font-mono text-slate-400">{{ $msg->created_at ? $msg->created_at->diffForHumans() : '' }}</span>
                                <span class="px-2 py-0.5 rounded-md font-bold {{ $msg->recipient_type === 'center' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                    موجه إلى: {{ $msg->recipient_type === 'center' ? 'إدارة المركز' : 'الأخصائي المعالج' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- شارات الحالة والأهمية -->
                    <div class="flex flex-col items-end gap-1 shrink-0">
                        @if($msg->doctor_reply)
                            <span class="px-2.5 py-1 rounded-xl text-[11px] font-black bg-emerald-100 text-emerald-800 flex items-center gap-1">
                                <i class="fa-solid fa-check text-[10px]"></i> تم الرد
                            </span>
                        @else
                            <span class="px-2.5 py-1 rounded-xl text-[11px] font-black bg-rose-100 text-rose-800 flex items-center gap-1.5 shadow-2xs">
                                <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
                                <span>جديدة بانتظار الرد</span>
                            </span>
                        @endif

                        @if($msg->is_urgent)
                            <span class="px-2 py-0.5 rounded-lg text-[10px] font-black bg-amber-100 text-amber-900 flex items-center gap-1">
                                <i class="fa-solid fa-bolt text-amber-600"></i> عاجل وهام
                            </span>
                        @endif
                    </div>
                </div>

                <!-- محتوى رسالة وملاحظة ولي الأمر -->
                <div class="p-4 rounded-2xl bg-purple-50/60 border border-purple-100/80 space-y-1.5 text-xs">
                    @if($msg->subject)
                    <h5 class="font-black text-purple-950 flex items-center gap-1.5">
                        <i class="fa-solid fa-tag text-purple-600 text-[10px]"></i>
                        <span>{{ $msg->subject }}</span>
                    </h5>
                    @endif
                    <p class="text-purple-900 leading-relaxed font-semibold">{{ $msg->message }}</p>
                </div>

                <!-- الرد الرسمي المسجل إن وجد -->
                @if($msg->doctor_reply)
                <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-100 space-y-2 mr-4 text-xs">
                    <div class="flex items-center justify-between font-bold text-[11px]">
                        <span class="text-emerald-950 flex items-center gap-1.5">
                            <i class="fa-solid fa-reply"></i>
                            @if($msg->recipient_type === 'specialist')
                                <span>{{ $msg->replied_by }}:</span>
                            @else
                                <span>رد الإدارة المعتمد ({{ $msg->replied_by }}):</span>
                            @endif
                        </span>
                        <span class="text-emerald-700 font-mono text-[10px]">{{ $msg->replied_at ? $msg->replied_at->diffForHumans() : '' }}</span>
                    </div>
                    <p class="text-emerald-900 leading-relaxed font-semibold">{{ $msg->doctor_reply }}</p>
                </div>
                @endif

                <!-- نموذج الرد الفوري -->
                <form action="{{ route('admin.parent-notes.reply', $msg) }}" method="POST" class="pt-2 space-y-2.5 border-t border-slate-100">
                    @csrf
                    
                    <div class="flex items-center justify-between text-[11px] font-bold">
                        <span class="text-slate-500"><i class="fa-solid fa-pen-nib ml-1 text-teal-600"></i> {{ $msg->doctor_reply ? 'تحديث الرد:' : 'كتابة الرد لولي الأمر:' }}</span>
                        
                        @if($msg->recipient_type === 'specialist')
                            <input type="hidden" name="replied_by" value="الأخصائي {{ $msg->child->main_specialist ?? 'المعالج' }}">
                        @else
                            <select name="replied_by" class="p-1 bg-slate-50 border border-slate-200 rounded-lg text-[10px] font-bold">
                                <option value="إدارة المركز العامة">إدارة المركز العامة</option>
                                <option value="د. أحمد يسري (المشرف الطبي)">د. أحمد يسري (المشرف الطبي)</option>
                                <option value="خدمة العملاء ورعاية الأهالي">خدمة العملاء ورعاية الأهالي</option>
                            </select>
                        @endif
                    </div>

                    <div class="flex gap-2">
                        <textarea name="admin_reply" required rows="2" placeholder="اكتب ردك لولي الأمر ليظهر له فوراً في بوابته..." class="flex-1 p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white text-xs font-medium leading-relaxed"></textarea>
                        
                        <button type="submit" class="px-5 py-2.5 rounded-2xl bg-teal-600 hover:bg-teal-700 text-white font-black text-xs shadow-md transition flex items-center justify-center gap-1.5 shrink-0 self-end">
                            <i class="fa-solid fa-paper-plane text-xs"></i>
                            <span>{{ $msg->doctor_reply ? 'تحديث الرد' : 'إرسال الرد' }}</span>
                        </button>
                    </div>
                </form>

                <!-- أزرار الإجراءات السفلية -->
                <div class="pt-2 flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2">

                        @if($msg->child && $msg->child->phone)
                        <a href="https://wa.me/2{{ $msg->child->phone }}" target="_blank" class="px-3 py-1.5 rounded-xl text-[11px] font-black bg-emerald-500 text-white hover:bg-emerald-600 shadow-sm transition flex items-center gap-1.5">
                            <i class="fa-brands fa-whatsapp text-[12px]"></i>
                            <span class="text-white">مراسلة واتساب</span>
                        </a>
                        @endif
                    </div>

                    <!-- حذف الملاحظة -->
                    <form action="{{ route('admin.parent-notes.destroy', $msg) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من رغبتك في حذف هذه الملاحظة؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-slate-400 hover:text-rose-600 transition text-[11px] font-bold" title="حذف">
                            <i class="fa-regular fa-trash-can"></i>
                        </button>
                    </form>
                </div>

            </div>
            @empty
            <div class="col-span-2 py-16 text-center text-slate-400 text-xs bg-white rounded-3xl border border-slate-100 p-8 space-y-2">
                <i class="fa-solid fa-envelope-circle-check text-4xl text-slate-300"></i>
                <p class="font-extrabold text-sm text-slate-700">لا توجد ملاحظات أو شكاوى مسجلة تطابق معايير البحث.</p>
                <p class="text-slate-400">أي رسالة يكتبها ولي الأمر في بوابته ستظهر هنا فوراً مع تنبيه باللون الأحمر!</p>
            </div>
            @endforelse
        </div>

        <div class="p-4 bg-white rounded-2xl border border-slate-100">
            {{ $messages->links() }}
        </div>

    </div>

    <!-- ==================== تبويب 2: تقييمات ومقترحات أولياء الأمور لإدارة المركز ==================== -->
    <div x-show="activeTab === 'ratings'" class="space-y-6">
        <div>
            <h3 class="font-black text-lg text-slate-800">تقييمات ومقترحات أولياء الأمور السرية لإدارة المركز</h3>
            <p class="text-xs text-slate-400 font-semibold mt-0.5">تقييمات الأهالي لخدمات ومرافق المركز لتحسين جودة الرعاية والتأهيل</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($ratings as $rt)
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-3 text-xs">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h4 class="font-black text-sm text-slate-900">{{ $rt->parent_name }}</h4>
                        <p class="text-[11px] text-slate-400">والد الطفل: {{ $rt->child ? $rt->child->name : '' }} • <span class="font-bold text-teal-700">تقييم خدمة المركز</span></p>
                    </div>

                    <div class="flex items-center text-amber-400 text-sm">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fa-solid fa-star {{ $i <= $rt->rating ? 'text-amber-400' : 'text-slate-200' }}"></i>
                        @endfor
                    </div>
                </div>

                @if($rt->feedback)
                <p class="text-slate-700 font-medium leading-relaxed bg-slate-50 p-3.5 rounded-2xl border border-slate-100">
                    "{{ $rt->feedback }}"
                </p>
                @endif

                <span class="text-[10px] text-slate-400 font-mono block text-left">{{ $rt->created_at ? $rt->created_at->diffForHumans() : '' }}</span>
            </div>
            @empty
            <div class="col-span-2 p-12 text-center bg-white rounded-3xl border border-slate-100 text-slate-400 text-xs font-bold">
                لا توجد تقييمات مسجلة بعد.
            </div>
            @endforelse
        </div>
    </div>

</div>
@endsection

@extends('layouts.app')

@section('title', 'بوابة الأخصائيين والتواصل مع أولياء الأمور')

@section('content')
<div class="space-y-8" x-data="{ 
    activeTab: 'sessions' 
}">

    <!-- ==================== الترويسة والأزرار الإحصائية ==================== -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-white text-lg shadow-md" style="background-color: #0d9488;">
                    <i class="fa-solid fa-user-doctor"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-800">بوابة الأخصائيين والتواصل مع أولياء الأمور</h2>
                    <p class="text-xs text-slate-500 font-semibold mt-0.5">تسجيل الجلسات، الرد على تعليقات الفيديوهات، ومتابعة رسائل واستفسارات الأهل</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('doctor.timetable') }}" class="px-4 py-2.5 bg-amber-50 text-amber-800 hover:bg-amber-100 rounded-2xl text-xs font-bold transition flex items-center gap-2 border border-amber-200 shadow-2xs">
                <i class="fa-solid fa-calendar-days text-amber-600"></i>
                <span>جدول ومواعيد الأخصائي</span>
            </a>

            <a href="{{ route('doctor.sessions.create') }}" class="px-5 py-2.5 rounded-2xl text-white font-extrabold text-xs shadow-lg hover:opacity-95 active:scale-95 transition flex items-center gap-2" style="background-color: #0d9488;">
                <i class="fa-solid fa-notes-medical text-sm"></i>
                <span>تسجيل جلسة جديدة</span>
            </a>
        </div>
    </div>

    <!-- رسائل النجاح إن وجدت -->
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl text-xs font-bold flex items-center gap-3 animate-in fade-in">
        <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <!-- بطاقات المؤشرات الإحصائية للأخصائي -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        
        <!-- 1. إجمالي الجلسات الموثقة -->
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-[11px] font-extrabold text-slate-400 uppercase">الجلسات الموثقة:</span>
                <h3 class="text-2xl font-black text-slate-800">{{ $totalSessionsCount }} <span class="text-xs text-slate-400 font-normal">جلسة</span></h3>
                <p class="text-[10px] text-teal-600 font-bold">مع تقارير مفصلة</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
        </div>

        <!-- 2. رسائل الأهل بانتظار الرد -->
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-[11px] font-extrabold text-slate-400 uppercase">رسائل بانتظار ردك:</span>
                <h3 class="text-2xl font-black text-rose-600">{{ $pendingMessagesCount }} <span class="text-xs text-slate-400 font-normal">رسالة</span></h3>
                <p class="text-[10px] text-rose-500 font-bold">استفسارات أولياء الأمور </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-envelope-open-text"></i>
            </div>
        </div>

        <!-- 3. تعليقات الفيديوهات -->
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-[11px] font-extrabold text-slate-400 uppercase">تعليقات الفيديوهات:</span>
                <h3 class="text-2xl font-black text-purple-700">{{ $totalCommentsCount }} <span class="text-xs text-slate-400 font-normal">تعليق</span></h3>
                <p class="text-[10px] text-purple-600 font-bold">تفاعل أولياء الأمور </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-comments"></i>
            </div>
        </div>

    </div>

    <!-- ==================== شريط التبويبات الرئيسي ==================== -->
    <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 pb-1 text-xs sm:text-sm font-bold">
        
        <!-- تبويب 1: سجل الجلسات -->
        <button type="button" @click="activeTab = 'sessions'" :class="activeTab === 'sessions' ? 'border-b-2 font-black pb-3 text-slate-900' : 'text-slate-400 hover:text-slate-600 pb-3'" :style="activeTab === 'sessions' ? 'border-color: #0d9488; color: #0d9488;' : ''" class="px-3.5 transition flex items-center gap-2">
            <i class="fa-solid fa-calendar-check"></i>
            <span>سجل وتقارير الجلسات</span>
            <span class="px-1.5 py-0.5 text-[10px] rounded-full bg-slate-100 text-slate-700 font-black">{{ $groupedChildren->total() }}</span>
        </button>

        <!-- تبويب 2: تعليقات الفيديوهات -->
        <button type="button" @click="activeTab = 'comments'" :class="activeTab === 'comments' ? 'border-b-2 font-black pb-3 text-slate-900' : 'text-slate-400 hover:text-slate-600 pb-3'" :style="activeTab === 'comments' ? 'border-color: #0d9488; color: #0d9488;' : ''" class="px-3.5 transition flex items-center gap-2">
            <i class="fa-solid fa-video text-purple-600"></i>
            <span>تعليقات أولياء الأمور على الفيديوهات</span>
            <span class="px-1.5 py-0.5 text-[10px] rounded-full bg-purple-100 text-purple-800 font-black">{{ $videoComments->count() }}</span>
        </button>

        <!-- تبويب 3: رسائل واستفسارات الأهل -->
        <button type="button" @click="activeTab = 'messages'" :class="activeTab === 'messages' ? 'border-b-2 font-black pb-3 text-slate-900' : 'text-slate-400 hover:text-slate-600 pb-3'" :style="activeTab === 'messages' ? 'border-color: #0d9488; color: #0d9488;' : ''" class="px-3.5 transition flex items-center gap-2">
            <i class="fa-solid fa-comments text-blue-600"></i>
            <span>رسائل واستفسارات أولياء الأمور</span>
            @if($pendingMessagesCount > 0)
            <span class="px-1.5 py-0.5 text-[10px] rounded-full bg-rose-100 text-rose-800 font-black">{{ $pendingMessagesCount }} جديدة</span>
            @endif
        </button>

    </div>

    <!-- ==================== تبويب 1: سجل وتقارير الجلسات ==================== -->
    <div x-show="activeTab === 'sessions'" class="space-y-6">
        
        <!-- شريط البحث والفلترة -->
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm">
            <form action="{{ route('doctor.portal') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
                <div class="sm:col-span-2 relative">
                    <i class="fa-solid fa-magnifying-glass absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث باسم الطفل، كود الطفل، أو اسم الأخصائي..." class="w-full pl-3 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-semibold">
                </div>

                <div>
                    <select name="specialist" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold" onchange="this.form.submit()">
                        <option value="all">كل الأخصائيين</option>
                        @foreach($specialists as $sp)
                        <option value="{{ $sp }}" {{ request('specialist') === $sp ? 'selected' : '' }}>{{ $sp }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="flex-1 py-2.5 px-4 rounded-2xl text-white font-bold transition flex items-center justify-center gap-1.5" style="background-color: #0d9488;">
                        <i class="fa-solid fa-filter"></i>
                        <span>تصفية</span>
                    </button>
                    @if(request()->hasAny(['search', 'specialist']))
                    <a href="{{ route('doctor.portal') }}" class="py-2.5 px-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold transition flex items-center justify-center" title="إلغاء التصفية">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- قائمة الأطفال وجلساتهم كصفوف -->
        <div class="space-y-4">
            @php $__empty_1 = true; $__currentLoopData = $groupedChildren; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; @endphp
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden" x-data="{ expanded: false }">
                
                <!-- رأس الصف (الطفل) -->
                <div class="p-4 sm:p-5 flex items-center justify-between cursor-pointer hover:bg-slate-50 transition" @click="expanded = !expanded">
                    <div class="flex items-center gap-4">
                        <img src="{{ $child->avatar_url }}" class="w-14 h-14 rounded-2xl object-cover ring-2 ring-slate-100 p-0.5 shadow-sm">
                        <div>
                            <h4 class="font-extrabold text-base text-slate-800">{{ $child->name }}</h4>
                            <div class="flex items-center gap-2 mt-1">
                                <p class="text-xs font-mono font-bold" style="color: #0d9488;">{{ $child->code }}</p>
                                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                <p class="text-[11px] font-bold text-slate-500">{{ $child->therapySessions->count() }} جلسات مسجلة</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-4">
                        <a href="{{ route('children.show', $child) }}" @click.stop class="hidden sm:flex text-xs font-bold text-slate-400 hover:text-brand-primary items-center gap-1">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i> بروفايل الطفل
                        </a>
                        <button class="w-10 h-10 rounded-full bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-slate-100 hover:text-slate-700 transition">
                            <i class="fa-solid fa-chevron-down transition-transform duration-300" :class="expanded ? 'rotate-180' : ''"></i>
                        </button>
                    </div>
                </div>

                <!-- تفاصيل الجلسات المتمددة -->
                <div x-show="expanded" x-collapse>
                    <div class="p-4 sm:p-5 bg-slate-50/50 border-t border-slate-100 space-y-4">
                        @foreach($child->therapySessions as $sess)
                        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
                            <div class="flex flex-wrap items-start justify-between gap-3 mb-3">
                                <div>
                                    <div class="flex items-center gap-2 text-xs font-bold text-slate-500 mb-1">
                                        <i class="fa-regular fa-calendar"></i>
                                        <span>{{ $sess->session_date ? $sess->session_date->format('Y-m-d') : '' }}</span>
                                        <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                                        <i class="fa-regular fa-clock"></i>
                                        <span>{{ $sess->session_time }}</span>
                                    </div>
                                    <p class="text-[11px] text-slate-400 font-bold">الأطصائي: <span class="text-slate-600">{{ $sess->specialist_name }}</span></p>
                                </div>
                                <span class="px-2.5 py-1 rounded-xl text-[10px] font-black bg-emerald-100 text-emerald-800">
                                    {{ $sess->child_mood }}

                                </span>
                            </div>

                            <div class="space-y-2 text-xs">
                                <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                                    <strong class="text-slate-800 block mb-1">تقریر الأطصائي:</strong>
                                    <p class="text-slate-600 leading-relaxed">{{ $sess->clinical_notes }}</p>
                                </div>

                                @if($sess->home_exercise)
                                <div class="bg-amber-50/60 p-3 rounded-xl border border-amber-100">
                                    <strong class="text-amber-900 block mb-1"><i class="fa-solid fa-house-user ml-1 text-amber-600"></i> تمرین منزلي لولي الأمز:</strong>
                                    <p class="text-amber-800 leading-relaxed">{{ $sess->home_exercise }}</p>
                                </div>
                                @endif
                            </div>

                            <div class="mt-3 flex items-center justify-end gap-2 text-[10px] font-bold">
                                @if($sess->video_path)
                                <a href="{{ route('media.index') }}" class="text-purple-600 hover:underline flex items-center gap-1">
                                    <i class="fa-solid fa-video"></i> فيديو مرفوع
                                </a>
                                @endif
                                @if($sess->comments->count() > 0)
                                <span class="text-slate-400 flex items-center gap-1 ml-3">
                                    <i class="fa-regular fa-comments"></i> {{ $sess->comments->count() }} تعليقات
                                </span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): @endphp
            <div class="py-12 text-center text-slate-400 text-xs bg-white rounded-3xl border border-slate-100 p-8">
                <i class="fa-solid fa-notes-medical text-3xl mb-2 text-slate-300"></i>
                <p class="font-bold">لا يوجد أطفال أو جلسات لعرضها ض#f� معا٪ير البحث.</p>
                <a href="{{ route('doctor.sessions.create') }}" class="mt-3 inline-block font-extrabold hover:underline" style="color: #0d9488;">+ تسجیل جلسة جديدة</a>
            </div>
            @endif
        </div>

        <div class="p-4 bg-white rounded-2xl border border-slate-100 mt-6">
            {{ $groupedChildren->links() }}

        </div>/div>
    </div>

    <!-- ==================== تبويب 2: تعليقات أولياء الأمور على الفيديوهات ==================== -->
    <div x-show="activeTab === 'comments'" class="space-y-6">
        <div>
            <h3 class="font-black text-lg text-slate-800">تعليقات ومناقشات أولياء الأمور على فيديوهات الجلسات</h3>
            <p class="text-xs text-slate-400 font-semibold mt-0.5">يمكنك الرد المباشر على أي تعليق كتبه ولي الأمر حول مقاطع تطور طفله</p>
        </div>

        <div class="space-y-4">
            @php $__empty_1 = true; $__currentLoopData = $videoComments->groupBy('therapy_session_id'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sessionId => $groupComments): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; @endphp
            @php $session = $groupComments->first()->session; @endphp
            <div class="bg-white rounded-3xl p-6 md:p-8 border border-slate-100 shadow-sm space-y-5">
                
                <!-- رأس بطاقة الفيديو والجلسة -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <img src="{{ $session && $session->child ? $session->child->avatar_url : '' }}" class="w-10 h-10 rounded-2xl object-cover ring-2 ring-slate-100 p-0.5">
                        <div>
                            <h4 class="font-extrabold text-sm text-slate-800">
                                الطفل: {{ $session && $session->child ? $session->child->name : 'طفل' }}

                            </h4>
                            <p class="text-[11px] text-slate-400">
                                جلسة: {{ $session ? $session->session_date->format('Y-m-d') : '' }} • الفيديو: <strong class="text-purple-700">{{ $session ? ($session->video_title ?? 'مقطع الجلسة') : '' }}</strong>
                            </p>
                        </div>
                    </div>

                    <a href="{{ $session && $session->child ? route('children.show', $session->child) : '#' }}" class="text-xs font-bold hover:underline" style="color: #0d9488;">
                        فتح ملف الطفل
                    </a>
                </div>

                <!-- شجرة التعليقات والردود -->
                <div class="space-y-3">
                    @foreach($groupComments as $comm)
                    <div class="p-4 rounded-2xl {{ $comm->sender_type === 'parent' ? 'bg-purple-50/80 border border-purple-100 text-purple-950' : 'bg-emerald-50/80 border border-emerald-100 text-emerald-950 mr-6' }} space-y-1.5 text-xs">
                        <div class="flex items-center justify-between font-bold text-[11px]">
                            <span class="flex items-center gap-1.5">
                                @if($comm->sender_type === 'parent')
                                    <i class="fa-solid fa-user text-purple-600"></i>
                                    <span>{{ $comm->sender_name }} (ولي الأمر)</span>
                                @else
                                    <i class="fa-solid fa-user-doctor text-emerald-600"></i>
                                    <span>{{ $comm->sender_name }} (الأخصائي المعالج)</span>
                                @endif
                            </span>
                            <span class="text-slate-400 font-mono">{{ $comm->created_at ? $comm->created_at->diffForHumans() : '' }}</span>
                        </div>
                        <p class="font-semibold leading-relaxed">{{ $comm->comment }}</p>
                    </div>
                    @endforeach
                </div>

                <!-- نموذج الرد المباشر للدكتور على تعليقات هذا الفيديو -->
                <form action="{{ route('doctor.comment.reply') }}" method="POST" class="pt-3 border-t border-slate-100 flex gap-2">
                    @php echo csrf_field(); @endphp
                    <input type="hidden" name="therapy_session_id" value="{{ $sessionId }}">
                    <input type="hidden" name="child_id" value="{{ $session ? $session->child_id : '' }}">
                    <input type="hidden" name="sender_name" value="{{ $session ? $session->specialist_name : 'د. أحمد يسري' }}">

                    <input type="text" name="comment" required placeholder="اكتب ردك المهني لولي الأمر على هذا التعليق..." class="flex-1 p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white text-xs font-semibold">
                    <button type="submit" class="px-5 py-3 rounded-2xl text-white font-bold text-xs shadow-md transition hover:opacity-90 flex items-center gap-1.5 shrink-0" style="background-color: #0d9488;">
                        <span>إرسال الرد</span>
                        <i class="fa-solid fa-reply text-xs"></i>
                    </button>
                </form>

            </div>
            @php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): @endphp
            <div class="p-12 text-center bg-white rounded-3xl border border-slate-100 text-slate-400 text-xs font-bold">
                لا توجد تعليقات على الفيديوهات بعد. عندما يكتب أي ولي أمر تعليقاً سيظهر هنا لتتمكن من الرد عليه فوراً!
            </div>
            @endif
        </div>
    </div>

    <!-- ==================== تبويب 3: رسائل واستفسارات أولياء الأمور المباشرة ==================== -->
            <div x-show="activeTab === 'messages'" class="space-y-6">
        <div>
            <h3 class="font-black text-lg text-slate-800">صندوق رسائل واستفسارات أولياء الأمور</h3>
            <p class="text-xs text-slate-400 font-semibold mt-0.5">الرسائل والملاحظات المرسلة من الأهالي الموجهة للأخصائي أو إدارة المركز</p>
        </div>

        <div class="space-y-4">
            @forelse($parentMessages as $childId => $messages)
            @php $child = $messages->first()->child; @endphp
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden" x-data="{ expandedMsg: false }">
                
                <!-- رأس الصف -->
                <div class="p-4 sm:p-5 flex items-center justify-between cursor-pointer hover:bg-slate-50 transition" @click="expandedMsg = !expandedMsg">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4">
                        <img src="{{ $child ? $child->avatar_url : 'https://api.dicebear.com/7.x/bottts/svg?seed=parent' }}" class="w-14 h-14 rounded-2xl object-cover ring-2 ring-slate-100 p-0.5 shadow-sm hidden sm:block">
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="font-extrabold text-base text-slate-800">{{ $messages->first()->parent_name }}</h4>
                                <span class="text-[11px] text-slate-500 font-bold">(والد الطفل: {{ $child ? $child->name : 'غير محدد' }})</span>
                            </div>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="px-2 py-0.5 rounded-lg bg-slate-100 text-slate-600 text-[10px] font-black">{{ $messages->count() }} رسائل</span>
                                @if($messages->whereNull('doctor_reply')->count() > 0)
                                <span class="px-2 py-0.5 rounded-lg bg-rose-100 text-rose-800 text-[10px] font-black flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-600 animate-ping"></span>
                                    {{ $messages->whereNull('doctor_reply')->count() }} بانتظار الرد
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <button class="w-10 h-10 rounded-full bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-slate-100 hover:text-slate-700 transition">
                        <i class="fa-solid fa-chevron-down transition-transform duration-300" :class="expandedMsg ? 'rotate-180' : ''"></i>
                    </button>
                </div>

                <!-- الرسائل المتمددة -->
                <div x-show="expandedMsg" x-collapse>
                    <div class="p-4 sm:p-5 bg-slate-50/50 border-t border-slate-100 space-y-4">
                        @foreach($messages as $pmsg)
                        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs space-y-4">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-50 text-[10px]">
                                <span class="font-mono text-slate-400">
                                    موجه إلى: <strong class="text-slate-700">{{ $pmsg->recipient_type === 'specialist' ? 'الأخصائي المعالج' : 'إدارة المركز' }}</strong>
                                </span>
                                <span class="text-slate-500 font-bold">{{ $pmsg->created_at ? $pmsg->created_at->diffForHumans() : '' }}</span>
                            </div>

                            <div class="p-4 rounded-2xl bg-purple-50/70 border border-purple-100 space-y-1.5">
                                @if($pmsg->subject)
                                <h5 class="font-black text-xs text-purple-950">{{ $pmsg->subject }}</h5>
                                @endif
                                <p class="text-xs text-purple-900 leading-relaxed font-medium">{{ $pmsg->message }}</p>
                            </div>

                            @if($pmsg->doctor_reply)
                            <div class="p-4 rounded-2xl bg-emerald-50/80 border border-emerald-100 space-y-2 mr-6 text-xs">
                                <div class="flex items-center justify-between font-bold text-[11px]">
                                    <span class="text-emerald-950 flex items-center gap-1.5">
                                        <i class="fa-solid fa-reply"></i>
                                        <span>رد الأخصائي ({{ $pmsg->replied_by }}):</span>
                                    </span>
                                    <span class="text-emerald-700 font-mono">{{ $pmsg->replied_at ? $pmsg->replied_at->diffForHumans() : '' }}</span>
                                </div>
                                <p class="text-emerald-900 leading-relaxed font-semibold">{{ $pmsg->doctor_reply }}</p>
                            </div>
                            @endif

                            <!-- فورم الرد -->
                            <form action="{{ route('doctor.message.reply') }}" method="POST" class="pt-2 space-y-2 mr-6">
                                @csrf
                                <input type="hidden" name="message_id" value="{{ $pmsg->id }}">
                                <input type="hidden" name="doctor_name" value="{{ $pmsg->child ? ($pmsg->child->main_specialist ?? 'أ. معالج نفسي') : 'أ. معالج نفسي' }}">

                                <div class="flex flex-col sm:flex-row gap-2">
                                    <textarea name="doctor_reply" required rows="2" placeholder="{{ $pmsg->doctor_reply ? 'تعديل الرد أو إضافة تفاصيل أخرى للرد السابق...' : 'اكتب ردك وتوجيهك الطبي لولي الأمر هنا...' }}" class="flex-1 p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white text-xs font-medium leading-relaxed"></textarea>
                                    <button type="submit" class="px-5 py-2.5 rounded-2xl text-white font-bold text-xs shadow-md transition hover:opacity-90 flex items-center justify-center gap-1.5 shrink-0 self-end" style="background-color: #0d9488;">
                                        <i class="fa-solid fa-paper-plane text-xs"></i>
                                        <span>{{ $pmsg->doctor_reply ? 'تعديل الرد' : 'إرسال الرد للأهل' }}</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @empty
            <div class="py-12 text-center text-slate-400 text-xs bg-white rounded-3xl border border-slate-100 p-8">
                <i class="fa-solid fa-envelope-open text-3xl mb-2 text-slate-300"></i>
                <p class="font-bold">لا توجد رسائل أو استفسارات من أولياء الأمور حالياً.</p>
            </div>
            @endforelse
        </div>
    </div>

</div>
@endsection



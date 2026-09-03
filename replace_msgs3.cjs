const fs = require('fs');

let c = fs.readFileSync('resources/views/doctor/index.blade.php', 'utf8');

const start   = c.indexOf('<div class="space-y-4">\n            @forelse($parentMessages');
const end  = c.indexOf('@endforelse\n        </div>\n    </div>\n\n    <!-- ==================== تبويب 4:');

const replacement = `
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
                                <span class="text-[11px] text-slate-500 font-bold">(
والد الطف؄: {{ $child ? $child->name : 'غير مدد' }})</span>
                            </div>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="px-2 py-0.5 rounded-lg bg-slate-100 text-slate-600 text-[10px] font-black">{{ $messages->count() }} رسائل</span>
                                @if($messages->whereNull('doctor_reply')->count() > 0)
                                <span class="px-2 py-0.5 rounded-lg bg-rose-100 text-rose-800 text-[10px] font-black flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-600 animate-ping"></span>
                                    {{ $messages->whereNull('doctor_reply')->count() }} باوتظار الرد
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
                                    مشه إل: <strong class="text-slate-700">{{ $pmsg->recipient_type === 'specialist' ? 'الأطصائي المعالج' : '�دارة المر�� }}</strong>
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
                                        <span>رد الأطصائي ({{ $pmsg->replied_by }}):</span>
                                    </span>
                                    <span class="text-emerald-700 font-mono">{{ $pmsg->replied_at ? $pmsg->replied_at->diffForHumans() : '' }}</span>
                                </div>
                                <p class="text-emerald-900 leading-relaxed font-semibold">{{ $pmsg->doctor_reply }}</p>
                            </div>
                            @endif

                            <!-- فورص الرح -->
                            <form action="{{ route('doctor.message.reply') }}" method="POST" class="pt-2 space-y-2 mr-6">
                                @csrf
                                <input type="hidden" name="message_id" value="{{ $pmsg->id }}">
                                <input type="hidden" name="doctor_name" value="{{ $pmsg->child ? ($pmsg->child->main_specialist ?? 'ء. معالج نفسل') : 'ء. معالج نفدل' }}">

                                <div class="flex flex-col sm:flex-row gap-2">
                                    <textarea name="doctor_reply" required rows="2" placeholder="{{ $pmsg->doctor_reply ? 'تعديل الرد أب إضاةد تفاصيل أخرة للرد السابف...' : 'اكتت ردك وتوجيهك الطبي لولي الأمر هنا...' }}" class="flex-1 p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white text-xs font-medium leading-relaxed"></textarea>
                                    <button type="submit" class="px-5 py-2.5 rounded-2xl text-white font-bold text-xs shadow-md transition hover:opacity-90 flex items-center justify-center gap-1.5 shrink-0 self-end" style="background-color: var(--brand-primary, #0d9488);">
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
                <p class="font-black">لا توجد رسائل أتط هد أتطياڌ المن</p>
            </div>
            @endforelse
        </div>`;

    if (start > -1 && end > start) {
        let newC = c.substring(0, start) + replacement + c.substring(end + 16);
        fs.writeFileSync('resources/views/doctor/index.blade.php', newC, 'utf8');
        console.log('Replaced successfully');
    } else {
        console.log('Could not find start or end indices');
    }


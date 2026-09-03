const fs = require('fs');

const c = fs.readFileSync('resources/views/doctor/index.blade.php', 'utf8');

const start_str = '        <div class=\"space-y-4\">\n            @forelse(->groupBy(\'child_id\')';
const end_str = '            @endforelse\n        </div>\n    </div>';

const start_idx = c.indexOf(start_str);
const end_idx = c.indexOf(end_str, start_idx) + end_str.length;

const replacement =         <!-- الرسائل مجمعة حسب الطفل -->
        <div class="space-y-4">
            @forelse( as  => )
            @php  = ->first()->child; @endphp
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden" x-data="{ expandedMsg: false }">
                
                <!-- رأس الصف -->
                <div class="p-4 sm:p-5 flex items-center justify-between cursor-pointer hover:bg-slate-50 transition" @click="expandedMsg = !expandedMsg">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4">
                        <img src="{{  ? ->avatar_url : 'https://api.dicebear.com/7.x/bottts/svg?seed=parent' }}" class="w-14 h-14 rounded-2xl object-cover ring-2 ring-slate-100 p-0.5 shadow-sm hidden sm:block">
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="font-extrabold text-base text-slate-800">{{ ->first()->parent_name }}</h4>
                                <span class="text-[11px] text-slate-500 font-bold">(والد الطفل: {{  ? ->name : 'غير محدد' }})</span>
                            </div>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="px-2 py-0.5 rounded-lg bg-slate-100 text-slate-600 text-[10px] font-black">{{ ->count() }} رسائل</span>
                                @if(->whereNull('doctor_reply')->count() > 0)
                                <span class="px-2 py-0.5 rounded-lg bg-rose-100 text-rose-800 text-[10px] font-black flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-600 animate-ping"></span>
                                    {{ ->whereNull('doctor_reply')->count() }} بانتظار الرد
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
                        @foreach( as )
                        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs space-y-4">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-50 text-[10px]">
                                <span class="font-mono text-slate-400">
                                    موجه إلى: <strong class="text-slate-700">{{ ->recipient_type === 'specialist' ? 'الأخصائي المعالج' : 'إدارة المركز' }}</strong>
                                </span>
                                <span class="text-slate-500 font-bold">{{ ->created_at ? ->created_at->diffForHumans() : '' }}</span>
                            </div>

                            <div class="p-4 rounded-2xl bg-purple-50/70 border border-purple-100 space-y-1.5">
                                @if(->subject)
                                <h5 class="font-black text-xs text-purple-950">{{ ->subject }}</h5>
                                @endif
                                <p class="text-xs text-purple-900 leading-relaxed font-medium">{{ ->message }}</p>
                            </div>

                            @if(->doctor_reply)
                            <div class="p-4 rounded-2xl bg-emerald-50/80 border border-emerald-100 space-y-2 mr-6 text-xs">
                                <div class="flex items-center justify-between font-bold text-[11px]">
                                    <span class="text-emerald-950 flex items-center gap-1.5">
                                        <i class="fa-solid fa-reply"></i>
                                        <span>رد الأخصائي ({{ ->replied_by }}):</span>
                                    </span>
                                    <span class="text-emerald-700 font-mono">{{ ->replied_at ? ->replied_at->diffForHumans() : '' }}</span>
                                </div>
                                <p class="text-emerald-900 leading-relaxed font-semibold">{{ ->doctor_reply }}</p>
                            </div>
                            @endif

                            <!-- فورم الرد -->
                            <form action="{{ route('doctor.message.reply') }}" method="POST" class="pt-2 space-y-2 mr-6">
                                @csrf
                                <input type="hidden" name="message_id" value="{{ ->id }}">
                                <input type="hidden" name="doctor_name" value="{{ ->child ? (->child->main_specialist ?? 'أ. معالج نفسي') : 'أ. معالج نفسي' }}">

                                <div class="flex flex-col sm:flex-row gap-2">
                                    <textarea name="doctor_reply" required rows="2" placeholder="{{ ->doctor_reply ? 'تعديل الرد أو إضافة تفاصيل أخرى للرد السابق...' : 'اكتب ردك وتوجيهك الطبي لولي الأمر هنا...' }}" class="flex-1 p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white text-xs font-medium leading-relaxed"></textarea>
                                    <button type="submit" class="px-5 py-2.5 rounded-2xl text-white font-bold text-xs shadow-md transition hover:opacity-90 flex items-center justify-center gap-1.5 shrink-0 self-end" style="background-color: var(--brand-primary, #0d9488);">
                                        <i class="fa-solid fa-paper-plane text-xs"></i>
                                        <span>{{ ->doctor_reply ? 'تعديل الرد' : 'إرسال الرد للأهل' }}</span>
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
    </div>;

if (start_idx !== -1) {
    const new_c = c.substring(0, start_idx) + replacement + c.substring(end_idx);
    fs.writeFileSync('resources/views/doctor/index.blade.php', new_c, 'utf8');
    console.log("Done");
} else {
    console.log("Start not found");
}

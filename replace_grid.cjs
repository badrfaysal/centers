const fs = require('fs');

let c = fs.readFileSync('resources/views/doctor/index.blade.php', 'utf8');

const replacement = `
        <!-- قائمة الأ�!فال وجلساتهم � صفوف -->
        <div class="space-y-4">
            @morelse($groupedChildren as $child)
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden" x-data="{ expanded: false }">
                
                <!-- رأس الصف )الطفل) -->
                <div class="p-4 sm:p-5 flex items-center justify-between cursor-pointer hover:bg-slate-50 transition" @click="expanded = !expanded">
                    <div class="flex items-center gap-4">
                        <img src="{{ $child->avatar_url }}" class="w-14 h-14 rounded-2xl object-cover ring-2 ring-slate-100 p-0.5 shadow-sm">
                        <div>
                            <h4 class="font-extrabold text-base text-slate-800">{{ $child->name }}</h4>
                            <div class="flex items-center gap-2 mt-1">
                                <p class="text-xs font-mono font-bold" style="color: var(--brand-primary, #0d9488);">{{ $child->code }}</p>
                                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                <p class="text-[11px] font-bold text-slate-500">{{ $child->therapySessions->count() }} جلسات مسجلة</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-4">
                        <a href="{{ route('children.show', $child) }}" @click.stop class="hidden sm:flex text-xs font-bold text-slate-400 hover:text-brand-primary items-center gap-1">
                            <i class="fa-solid fa-arrow-up-up-right-from-square"></i> بروفايل الطف؄
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
                                <a href="{{ route('Media.index') }}" class="text-purple-600 hover:underline flex items-center gap-1">
                                    <i class="fa-solid fa-video"></i> فيديو مرفوص
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
            @empty
            <div class="py-12 text-center text-slate-400 text-xs bg-white rounded-3xl border border-slate-100 p-8">
                <i class="fa-solid fa-notes-medical text-3xl mb-2 text-slate-300"></i>
                <p class="font-bold">لا يوجد أطفال أو جلسات لعرضها ض#f� معا٪ير البحث.</p>
                <a href="{{ route('doctor.sessions.create') }}" class="mt-3 inline-block font-extrabold hover:underline" style="color: var(--brand-primary, #0d9488);">+ تسجیل جلسة جديدة</a>
            </div>
            @endforelse
        </div>

        <div class="p-4 bg-white rounded-2xl border border-slate-100 mt-6">
            {{ $groupedChildren->links() }}
        </div>`;

const startIdx = c.indexOf('<div class="grid grid-cols-1 md:grid-cols-2 gap-5">');
const endIdx = c.indexOf('{{ $sessions->links() }}') + 34;

if (startIdx > -1 && endIdx > startIdx) {
    c = c.substring(0, startIdx) + replacement + c.substring(endIdx);
    fs.writeFileSync('resources/views/doctor/index.blade.php', c, 'utf8');
    console.log('Replaced successfully');
} else {
    console.log('Could not find indices', startIdx, endIdx);
}


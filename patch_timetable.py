import re

with open('resources/views/schedules/specialist_timetable.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

modal_html = '''
    <!-- ==================== 5. Day Summary & Apologize Modal ==================== -->
    <div x-show="daySummaryModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 space-y-6 shadow-2xl border border-slate-100" @click.away="daySummaryModalOpen = false">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shadow-xs">
                        <i class="fa-solid fa-sun"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-lg text-slate-900" x-text="'ملخص يوم: ' + sessionDate"></h3>
                        <p class="text-xs text-slate-500 font-mono font-bold" x-text="'الأخصائي: ' + specialistName"></p>
                    </div>
                </div>
                <button type="button" @click="daySummaryModalOpen = false" class="p-2 text-slate-400 hover:text-slate-700 rounded-xl">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <template x-if="daySessions.length > 0">
                <div class="space-y-6 text-xs">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                            <span class="block text-slate-500 font-bold mb-1">إجمالي ساعات العمل</span>
                            <span class="text-xl font-black text-slate-800" x-text="dayTotalHours"></span>
                        </div>
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                            <span class="block text-slate-500 font-bold mb-1">أوقات الفراغ (Gaps)</span>
                            <span class="text-xl font-black text-slate-800" x-text="dayTotalGaps"></span>
                        </div>
                    </div>

                    <div>
                        <h4 class="font-bold text-slate-800 mb-3 border-b pb-2">الأطفال المجدولين اليوم (تواصل سريع)</h4>
                        <div class="max-h-40 overflow-y-auto space-y-2 pr-2">
                            <template x-for="sess in daySessions" :key="sess.id">
                                <div class="flex items-center justify-between p-2 rounded-xl border border-slate-100" :class="sess.status === 'cancelled' ? 'bg-rose-50 opacity-70' : 'bg-white'">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-clock text-slate-400"></i>
                                        <span class="font-mono font-bold text-slate-700" x-text="sess.start_time.substring(0,5) + ' - ' + sess.end_time.substring(0,5)"></span>
                                        <span class="font-black text-slate-900" x-text="sess.child_name"></span>
                                        <span x-show="sess.status === 'cancelled'" class="text-[9px] bg-rose-100 text-rose-700 px-2 py-0.5 rounded-full font-bold">ملغية</span>
                                    </div>
                                    <a :href="sess.whatsapp_reminder_url" target="_blank" class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center hover:bg-emerald-500 hover:text-white transition" title="واتساب">
                                        <i class="fa-brands fa-whatsapp text-lg"></i>
                                    </a>
                                </div>
                            </template>
                        </div>
                    </div>
                    
                    <template x-if="!dayIsAllCancelled">
                        <div class="bg-rose-50 border border-rose-200 rounded-2xl p-4 flex flex-col gap-3">
                            <p class="font-bold text-rose-800">حالة طوارئ أو طلب إجازة؟</p>
                            <p class="text-rose-600 text-[11px]">عند الضغط على الزر أدناه سيتم إلغاء جميع جلساتك لهذا اليوم فوراً، وتسجيل غياب تلقائي للأطفال، وإرسال تنبيه عاجل للإدارة لإبلاغ أولياء الأمور.</p>
                            <form action="/doctor-portal/timetable/apologize-day" method="POST" onsubmit="return confirm('هل أنت متأكد من إلغاء جميع مواعيدك لهذا اليوم؟ هذا الإجراء لا يمكن التراجع عنه بسهولة!')">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="specialist_name" :value="specialistName">
                                <input type="hidden" name="session_date" :value="sessionDate">
                                <button type="submit" class="w-full py-2.5 bg-rose-600 text-white font-black rounded-xl hover:bg-rose-700 shadow-md">
                                    <i class="fa-solid fa-triangle-exclamation ml-1"></i>
                                    اعتذار عن عمل اليوم بالكامل
                                </button>
                            </form>
                        </div>
                    </template>
                </div>
            </template>
            <template x-if="daySessions.length === 0">
                <div class="text-center py-10 text-slate-400 font-bold text-sm">
                    لا توجد أي جلسات مجدولة في هذا اليوم.
                </div>
            </template>
        </div>
    </div>
'''

content = content.replace("@push('scripts')", modal_html + "\\n@push('scripts')")

# Add alpine data
alpine_data = """        daySummaryModalOpen: false,
        daySessions: [],
        dayTotalHours: '0',
        dayTotalGaps: '0',
        dayIsAllCancelled: false,"""

content = content.replace("detailModalOpen: false,", alpine_data + "\\n        detailModalOpen: false,")

# Replace handleDayClick
new_handleDayClick = """        handleDayClick(day) {
            let sessions = this.getSessionsForDay(day);
            this.sessionDate = this.formatDate(day);
            if (sessions.length > 0) {
                this.daySessions = sessions;
                this.calculateDaySummary();
                this.daySummaryModalOpen = true;
            } else {
                this.openAddModalForDay(day);
            }
        },

        calculateDaySummary() {
            if (this.daySessions.length === 0) return;
            
            // Sort sessions by start_time
            let sorted = [...this.daySessions].sort((a, b) => a.start_time.localeCompare(b.start_time));
            
            let totalMinutes = 0;
            let gapMinutes = 0;
            this.dayIsAllCancelled = true;

            for (let i = 0; i < sorted.length; i++) {
                if (sorted[i].status !== 'cancelled') {
                    this.dayIsAllCancelled = false;
                }
                
                let start = new Date(this.sessionDate + 'T' + sorted[i].start_time);
                let end = new Date(this.sessionDate + 'T' + sorted[i].end_time);
                totalMinutes += (end - start) / 60000;

                if (i < sorted.length - 1) {
                    let nextStart = new Date(this.sessionDate + 'T' + sorted[i+1].start_time);
                    if (nextStart > end) {
                        gapMinutes += (nextStart - end) / 60000;
                    }
                }
            }

            let formatTime = (mins) => {
                let h = Math.floor(mins / 60);
                let m = mins % 60;
                return (h > 0 ? h + ' ساعة ' : '') + (m > 0 ? m + ' دقيقة' : (h === 0 ? '0' : ''));
            };

            this.dayTotalHours = formatTime(totalMinutes);
            this.dayTotalGaps = gapMinutes > 0 ? formatTime(gapMinutes) : 'لا يوجد فراغات';
        },"""

content = re.sub(r'handleDayClick\(day\)\s*\{.*?\},\n', new_handleDayClick + '\n', content, flags=re.DOTALL)

with open('resources/views/schedules/specialist_timetable.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)
print("Done")
@extends('layouts.app')

@section('title', 'تسجيل بيانات وتقرير الجلسة')

@section('content')
<div class="space-y-8" x-data="{ 
    selectedChildId: '{{ $selectedChild ? $selectedChild->id : ($children->first() ? $children->first()->id : '') }}',
    children: {{ json_encode($children->map(fn($c) => [
        'id' => $c->id,
        'name' => $c->name,
        'code' => $c->code,
        'age' => $c->age_text,
        'mental_age' => $c->mental_age,
        'avatar' => $c->avatar_url,
        'diagnoses' => $c->diagnoses_list,
        'specialist' => $c->main_specialist,
        'meds' => $c->current_medications,
        'neuro' => $c->neurologist_name,
        'previous_goals' => $c->therapySessions()->latest('session_date')->first()?->goals_evaluated ?? []
    ])) }},
    mood: 'ممتاز ومتعاون ومتحمس',
    goals: [],
    newGoalText: '',
    clinicalNotes: `{!! old('clinical_notes', '') !!}`,
    homeExercise: `{!! old('home_exercise', '') !!}`,
    isRecording: false,
    recordingTarget: 'clinicalNotes',
    interimText: '',
    isHearingAudio: false,
    recognition: null,
    homeworkFiles: [],
    videoFiles: [],
    isSubmitting: false,
    
    toggleRecording(target = 'clinicalNotes') {
        if (this.isRecording) {
            this.stopRecording();
        } else {
            this.recordingTarget = target;
            this.startRecording();
        }
    },
    startRecording() {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (!SpeechRecognition) {
            Swal.fire({
                icon: 'error',
                title: 'غير مدعوم',
                text: 'متصفحك لا يدعم ميزة الإملاء الصوتي. يرجى استخدام متصفح Google Chrome.',
                confirmButtonText: 'حسناً',
                confirmButtonColor: '#0d9488'
            });
            return;
        }
        
        this.recognition = new SpeechRecognition();
        this.recognition.lang = 'ar-EG';
        this.recognition.continuous = true;
        this.recognition.interimResults = true;
        
        this.recognition.onstart = () => {
            this.isRecording = true;
            this.interimText = '';
            this.isHearingAudio = false;
        };
        
        this.recognition.onaudiostart = () => {
            this.isHearingAudio = true;
        };

        this.recognition.onresult = (event) => {
            let finalTranscript = '';
            let interimTranscript = '';
            
            for (let i = event.resultIndex; i < event.results.length; ++i) {
                if (event.results[i].isFinal) {
                    finalTranscript += event.results[i][0].transcript + ' ';
                } else {
                    interimTranscript += event.results[i][0].transcript;
                }
            }
            
            this.interimText = interimTranscript;
            
            if (finalTranscript) {
                if (this[this.recordingTarget] && !this[this.recordingTarget].endsWith(' ') && !this[this.recordingTarget].endsWith('\n')) {
                    this[this.recordingTarget] += ' ';
                }
                this[this.recordingTarget] += finalTranscript;
            }
        };
        
        this.recognition.onerror = (event) => {
            console.error('Speech recognition error:', event.error);
            if (event.error === 'network') {
                Swal.fire({ icon: 'warning', title: 'خطأ في الشبكة', text: 'الإملاء الصوتي يتطلب اتصالاً مستقراً بالإنترنت.' });
                this.stopRecording();
            } else if (event.error === 'not-allowed' || event.error === 'service-not-allowed') {
                Swal.fire({ icon: 'error', title: 'تم حظر الميكروفون', text: 'يرجى إعطاء صلاحية الميكروفون للمتصفح.' });
                this.stopRecording();
            } else if (event.error !== 'no-speech') {
                this.stopRecording();
            }
        };
        
        this.recognition.onend = () => {
            if (this.isRecording) {
                try {
                    this.recognition.start();
                } catch(e) {
                    this.isRecording = false;
                }
            } else {
                this.isRecording = false;
                this.interimText = '';
                this.isHearingAudio = false;
            }
        };
        
        try {
            this.recognition.start();
        } catch(e) {
            console.error(e);
        }
    },
    stopRecording() {
        this.isRecording = false;
        if (this.recognition) {
            this.recognition.stop();
        }
        this.interimText = '';
        this.isHearingAudio = false;
    },
    
    handleHomeworkChange(event) {
        this.homeworkFiles = Array.from(event.target.files).map((file, i) => ({
            id: i,
            name: file.name,
            size: (file.size / (1024 * 1024)).toFixed(1) + ' MB'
        }));
    },
    removeHomeworkFile(index) {
        const dt = new DataTransfer();
        const input = document.getElementById('homeworkFileInput');
        const files = input.files;
        for (let i = 0; i < files.length; i++) {
            if (i !== index) dt.items.add(files[i]);
        }
        input.files = dt.files;
        this.handleHomeworkChange({target: input});
    },

    handleVideoChange(event) {
        this.videoFiles = Array.from(event.target.files).map((file, i) => ({
            id: i,
            name: file.name,
            size: (file.size / (1024 * 1024)).toFixed(1) + ' MB'
        }));
    },
    removeVideoFile(index) {
        const dt = new DataTransfer();
        const input = document.getElementById('videoFileInput');
        const files = input.files;
        for (let i = 0; i < files.length; i++) {
            if (i !== index) dt.items.add(files[i]);
        }
        input.files = dt.files;
        this.handleVideoChange({target: input});
    },

    addGoal() {
        if (this.newGoalText.trim()) {
            this.goals.push({ text: this.newGoalText.trim(), percentage: 0, fromPrevious: false });
            this.newGoalText = '';
        }
    },
    removeGoal(idx) {
        this.goals.splice(idx, 1);
    },
    setPercentage(idx, pct) {
        this.goals[idx].percentage = pct;
    },
    loadChildGoals() {
        if (!this.currentChild) return;
        const prev = this.currentChild.previous_goals || [];
        if (prev.length > 0) {
            this.goals = prev.map(g => ({
                text: typeof g === 'string' ? g : (g.text || ''),
                percentage: typeof g === 'string' ? 0 : (g.percentage || 0),
                fromPrevious: true
            }));
        } else {
            this.goals = [];
        }
    },
    get currentChild() {
        return this.children.find(c => c.id == this.selectedChildId) || null;
    },
    init() {
        this.loadChildGoals();
        this.$watch('selectedChildId', () => this.loadChildGoals());
    }
}">

    <!-- الترويسة وأزرار التنقل -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-2xl flex items-center justify-center text-white text-xl shadow-lg" style="background-color: #0d9488;">
                <i class="fa-solid fa-notes-medical"></i>
            </div>
            <div>
                <h2 class="text-2xl font-black text-slate-800">بوابة الأخصائي: تسجيل بيانات وتقرير الجلسة</h2>
                <p class="text-xs text-slate-500 font-semibold mt-0.5">البحث عن الطفل، توثيق استجابته، تقييم الأهداف، كتابة التقرير، ورفع فيديو للأهل</p>
            </div>
        </div>

        <a href="{{ route('doctor.portal') }}" class="px-4 py-2.5 bg-white border border-slate-200 rounded-2xl text-xs font-bold text-slate-700 hover:bg-slate-50 transition shadow-xs flex items-center gap-2">
            <i class="fa-solid fa-clock-rotate-left text-slate-400"></i>
            <span>سجل جلسات الأطباء</span>
        </a>
    </div>

    <!-- رسائل الأخطاء إن وجدت -->
    @if (isset($errors) && $errors->any())
    <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-2xl text-xs font-bold space-y-1">
        <p class="flex items-center gap-1.5"><i class="fa-solid fa-triangle-exclamation"></i> يرجى مراجعة البيانات التالية:</p>
        <ul class="list-disc pr-5 font-medium">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- نموذج إضافة الجلسة التفصيلي -->
    <form action="{{ route('doctor.sessions.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8" @submit="isSubmitting = true">
        @csrf

        <!-- Overlay Loading Animation -->
        <div x-show="isSubmitting" class="fixed inset-0 z-50 flex items-center justify-center bg-white/80 backdrop-blur-sm" x-transition>
            <div class="flex flex-col items-center bg-white p-8 rounded-3xl shadow-2xl border border-slate-100 max-w-sm w-full text-center">
                <!-- Custom CSS Spinner -->
                <div class="relative w-20 h-20 mb-6">
                    <div class="absolute inset-0 border-4 border-slate-100 rounded-full"></div>
                    <div class="absolute inset-0 border-4 border-teal-500 rounded-full border-t-transparent animate-spin"></div>
                    <i class="fa-solid fa-cloud-arrow-up absolute inset-0 flex items-center justify-center text-teal-600 text-xl"></i>
                </div>
                <h3 class="text-xl font-black text-slate-800 mb-2">جاري رفع الملفات...</h3>
                <p class="text-sm font-bold text-slate-500 mb-4">يرجى الانتظار، قد يستغرق رفع الفيديوهات بعض الوقت حسب حجمها وسرعة الإنترنت.</p>
                
                <div class="w-full bg-slate-100 rounded-full h-2 mb-2 overflow-hidden">
                    <div class="bg-teal-500 h-2 rounded-full w-full animate-pulse"></div>
                </div>
                <p class="text-[10px] text-teal-600 font-bold">لا تقم بإغلاق هذه الصفحة</p>
            </div>
        </div>

        <!-- ==================== 1. البحث واختيار الطفل والبطاقة السريعة ==================== -->
        <div class="bg-white rounded-3xl p-6 md:p-8 border border-slate-100 shadow-sm space-y-6">
            
            <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                <i class="fa-solid fa-magnifying-glass-chart text-sm" style="color: #0d9488;"></i>
                <h3 class="font-black text-sm text-slate-800">1. البحث واختيار الطفل</h3>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                
                <!-- قائمة البحث واختيار الطفل -->
                <div class="space-y-3">
                    <label class="block font-bold text-slate-700 text-xs">
                        اختر الطفل من القائمة أو ابحث باسمه/كوده <span class="text-rose-500">*</span>
                    </label>

                    <select name="child_id" x-model="selectedChildId" required class="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white focus:border-slate-400 font-bold text-xs">
                        @foreach($children as $ch)
                        <option value="{{ $ch->id }}">{{ $ch->name }} ({{ $ch->code }})</option>
                        @endforeach
                    </select>

                    <p class="text-[11px] text-slate-400">يمكنك اختيار أي طفل مسجل بالخطة التأهيلية لتسجيل جلسته فوراً.</p>
                </div>

                <!-- بطاقة الطفل المختارة الحية (Live Child Summary Card) -->
                <div class="lg:col-span-2 p-5 rounded-3xl bg-slate-50 border border-slate-200/70 space-y-4" x-show="currentChild">
                    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4">
                        <img :src="currentChild ? currentChild.avatar : ''" alt="child" class="w-16 h-16 rounded-2xl object-cover ring-2 ring-white shadow-sm bg-white p-0.5">
                        
                        <div class="space-y-1 text-center sm:text-right flex-1">
                            <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                                <h4 class="font-black text-base text-slate-800" x-text="currentChild ? currentChild.name : ''"></h4>
                                <span class="font-mono text-[11px] font-bold px-2 py-0.5 bg-white rounded-lg border border-slate-200" style="color: #0d9488;" x-text="currentChild ? currentChild.code : ''"></span>
                            </div>

                            <p class="text-xs text-slate-500 font-medium">
                                العمر الزمني: <strong class="text-slate-700" x-text="currentChild ? currentChild.age : ''"></strong> 
                                <template x-if="currentChild && currentChild.mental_age">
                                    <span class="text-purple-700 font-bold mr-2">• العمر العقلي: <span x-text="currentChild.mental_age"></span></span>
                                </template>
                            </p>

                            <!-- سطور التشخيص المستقلة للطفل -->
                            <div class="flex flex-wrap items-center justify-center sm:justify-start gap-1 pt-1">
                                <template x-for="(dg, dindex) in (currentChild ? currentChild.diagnoses : [])" :key="dindex">
                                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-white text-slate-700 border border-slate-200 shadow-2xs" x-text="dg"></span>
                                </template>
                            </div>
                        </div>

                        <a :href="'/children/' + (currentChild ? currentChild.id : '')" target="_blank" class="px-3 py-1.5 bg-white hover:bg-slate-100 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 shadow-2xs transition flex items-center gap-1 shrink-0" title="فتح ملف الطفل الكامل">
                            <span>ملف الطفل</span>
                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                        </a>
                    </div>
                </div>

            </div>

        </div>

        <!-- ==================== 2. تفاصيل الجلسة والتقييم والاستجابة ==================== -->
        <div class="bg-white rounded-3xl p-6 md:p-8 border border-slate-100 shadow-sm space-y-6">
            
            <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                <i class="fa-solid fa-calendar-check text-sm" style="color: #0d9488;"></i>
                <h3 class="font-black text-sm text-slate-800">2. بيانات توقيت الجلسة واستجابة الطفل</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-5 text-xs font-medium">
                
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5">الأخصائي المعالج المنفذ <span class="text-rose-500">*</span></label>
                    <select name="specialist_name" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold">
                        @foreach($specialists as $sp)
                        <option value="{{ $sp }}">{{ $sp }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1.5">تاريخ الجلسة <span class="text-rose-500">*</span></label>
                    <input type="date" name="session_date" required value="{{ date('Y-m-d') }}" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1.5">وقت الجلسة</label>
                    <input type="text" name="session_time" value="04:00 م - 04:45 م" placeholder="مثال: 04:00 م - 04:45 م" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold font-mono">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1.5">غرفة التأهيل / القاعة <span class="text-rose-500">*</span></label>
                    <select name="room_name" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold">
                        @foreach($rooms as $rm)
                        <option value="{{ $rm }}">{{ $rm }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1.5">نوع الجلسة التأهيلية <span class="text-rose-500">*</span></label>
                    <select name="session_type" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold">
                        @foreach($sessionTypes as $st)
                        <option value="{{ $st }}">{{ $st }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- أزرار تفاعلية لاختيار استجابة ومزاج الطفل -->
                <div class="sm:col-span-4 space-y-2">
                    <label class="block font-bold text-slate-700 text-xs">
                        حالة واستجابة ومزاج الطفل أثناء الجلسة: <span class="text-rose-500">*</span>
                    </label>
                    <input type="hidden" name="child_mood" :value="mood">

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        
                        <button type="button" @click="mood = 'ممتاز ومتعاون ومتحمس'" :class="mood === 'ممتاز ومتعاون ومتحمس' ? 'bg-emerald-50 border-emerald-400 text-emerald-900 ring-2 ring-emerald-400/30' : 'bg-slate-50 border-slate-200 text-slate-600'" class="p-3.5 rounded-2xl border text-right transition group">
                            <span class="text-lg block mb-1"></span>
                            <p class="font-extrabold text-xs">ممتاز ومتعاون</p>
                            <p class="text-[10px] text-slate-400">استجابة عالية وتفاعل مبهج</p>
                        </button>

                        <button type="button" @click="mood = 'متوسط / تشتت انتباه متكرر'" :class="mood === 'متوسط / تشتت انتباه متكرر' ? 'bg-amber-50 border-amber-400 text-amber-900 ring-2 ring-amber-400/30' : 'bg-slate-50 border-slate-200 text-slate-600'" class="p-3.5 rounded-2xl border text-right transition group">
                            <span class="text-lg block mb-1"></span>
                            <p class="font-extrabold text-xs">متوسط / مشتت</p>
                            <p class="text-[10px] text-slate-400">احتاج تعزيز وتكرار التوجيه</p>
                        </button>

                        <button type="button" @click="mood = 'مقاوم / بكاء وعناد'" :class="mood === 'مقاوم / بكاء وعناد' ? 'bg-rose-50 border-rose-400 text-rose-900 ring-2 ring-rose-400/30' : 'bg-slate-50 border-slate-200 text-slate-600'" class="p-3.5 rounded-2xl border text-right transition group">
                            <span class="text-lg block mb-1"></span>
                            <p class="font-extrabold text-xs">مقاوم / عناد وبكاء</p>
                            <p class="text-[10px] text-slate-400">رفض بعض الأنشطة</p>
                        </button>

                        <button type="button" @click="mood = 'نشاط حركي زائد / تفريغ حسي'" :class="mood === 'نشاط حركي زائد / تفريغ حسي' ? 'bg-purple-50 border-purple-400 text-purple-900 ring-2 ring-purple-400/30' : 'bg-slate-50 border-slate-200 text-slate-600'" class="p-3.5 rounded-2xl border text-right transition group">
                            <span class="text-lg block mb-1"></span>
                            <p class="font-extrabold text-xs">نشاط حركي وتفريغ</p>
                            <p class="text-[10px] text-slate-400">يحتاج تكامل حسي وتهدئة</p>
                        </button>

                    </div>
                </div>

            </div>

        </div>

        <!-- ==================== 3. تقييم الأهداف والملاحظات الطبية والواجب المنزلي ==================== -->
        <div class="bg-white rounded-3xl p-6 md:p-8 border border-slate-100 shadow-sm space-y-6">
            
            <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                <i class="fa-solid fa-file-pen text-sm" style="color: #0d9488;"></i>
                <h3 class="font-black text-sm text-slate-800">3. الأهداف المنجزة وملاحظات الأخصائي والتمرين المنزلي</h3>
            </div>

            <!-- أهداف الجلسة التفاعلية مع نسب الإنجاز -->
            <div class="space-y-4">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <label class="block font-bold text-slate-700 text-xs">الأهداف التأهيلية ونسبة الإنجاز في كل هدف:</label>
                    <template x-if="goals.length > 0 && goals[0] && goals[0].fromPrevious">
                        <span class="px-2.5 py-1 bg-indigo-100 text-indigo-700 rounded-xl text-[10px] font-bold flex items-center gap-1">
                            <i class="fa-solid fa-rotate-left"></i>
                            محمّلة من الجلسة السابقة — عدّل النسب حسب تقدم اليوم
                        </span>
                    </template>
                </div>
                
                <div class="space-y-3">
                    <template x-for="(g, gIdx) in goals" :key="gIdx">
                        <div class="p-4 rounded-2xl border transition-all"
                             :class="g.percentage >= 100 ? 'bg-emerald-50/70 border-emerald-200' : 
                                     g.percentage >= 75 ? 'bg-sky-50/70 border-sky-200' : 
                                     g.percentage >= 50 ? 'bg-amber-50/70 border-amber-200' : 
                                     g.percentage >= 25 ? 'bg-orange-50/70 border-orange-200' : 
                                     'bg-slate-50/70 border-slate-200'">
                            
                            <!-- سطر الهدف -->
                            <div class="flex items-center gap-2 mb-3">
                                <span class="w-6 h-6 rounded-lg flex items-center justify-center text-[10px] font-black text-white shrink-0"
                                      :class="g.percentage >= 100 ? 'bg-emerald-500' : g.percentage >= 50 ? 'bg-sky-500' : 'bg-slate-400'"
                                      x-text="gIdx + 1"></span>
                                <span class="flex-1 font-bold text-xs text-slate-800" x-text="g.text"></span>
                                <template x-if="g.fromPrevious">
                                    <span class="px-1.5 py-0.5 bg-indigo-100 text-indigo-600 rounded-md text-[9px] font-bold shrink-0">جلسة سابقة</span>
                                </template>
                                <span class="text-xs font-black shrink-0 min-w-[36px] text-left"
                                      :class="g.percentage >= 100 ? 'text-emerald-600' : g.percentage >= 75 ? 'text-sky-600' : g.percentage >= 50 ? 'text-amber-600' : g.percentage >= 25 ? 'text-orange-600' : 'text-slate-400'"
                                      x-text="g.percentage + '%'"></span>
                                <input type="hidden" :name="'goals[' + gIdx + '][text]'" :value="g.text">
                                <input type="hidden" :name="'goals[' + gIdx + '][percentage]'" :value="g.percentage">
                                <button type="button" @click="removeGoal(gIdx)" class="text-slate-400 hover:text-rose-600 transition shrink-0">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                            
                            <!-- أزرار نسبة الإنجاز -->
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-[10px] font-bold text-slate-500 shrink-0">نسبة الإنجاز:</span>
                                <div class="flex gap-1.5 flex-wrap">
                                    <button type="button" @click="setPercentage(gIdx, 0)" 
                                            :class="g.percentage === 0 ? 'bg-slate-600 text-white ring-2 ring-slate-400/30 scale-105' : 'bg-slate-100 text-slate-500 hover:bg-slate-200'"
                                            class="px-2.5 py-1 rounded-lg text-[10px] font-bold transition-all">لم يبدأ</button>
                                    <button type="button" @click="setPercentage(gIdx, 25)"
                                            :class="g.percentage === 25 ? 'bg-orange-500 text-white ring-2 ring-orange-400/30 scale-105' : 'bg-slate-100 text-slate-500 hover:bg-orange-100'"
                                            class="px-2.5 py-1 rounded-lg text-[10px] font-bold transition-all">25%</button>
                                    <button type="button" @click="setPercentage(gIdx, 50)"
                                            :class="g.percentage === 50 ? 'bg-amber-500 text-white ring-2 ring-amber-400/30 scale-105' : 'bg-slate-100 text-slate-500 hover:bg-amber-100'"
                                            class="px-2.5 py-1 rounded-lg text-[10px] font-bold transition-all">50%</button>
                                    <button type="button" @click="setPercentage(gIdx, 75)"
                                            :class="g.percentage === 75 ? 'bg-sky-500 text-white ring-2 ring-sky-400/30 scale-105' : 'bg-slate-100 text-slate-500 hover:bg-sky-100'"
                                            class="px-2.5 py-1 rounded-lg text-[10px] font-bold transition-all">75%</button>
                                    <button type="button" @click="setPercentage(gIdx, 100)"
                                            :class="g.percentage === 100 ? 'bg-emerald-500 text-white ring-2 ring-emerald-400/30 scale-105' : 'bg-slate-100 text-slate-500 hover:bg-emerald-100'"
                                            class="px-2.5 py-1 rounded-lg text-[10px] font-bold transition-all">ممتاز 100%</button>
                                </div>
                            </div>
                            
                            <!-- شريط التقدم المرئي -->
                            <div class="mt-2.5 w-full bg-slate-200/60 rounded-full h-1.5 overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500"
                                     :class="g.percentage >= 100 ? 'bg-emerald-500' : g.percentage >= 75 ? 'bg-sky-500' : g.percentage >= 50 ? 'bg-amber-500' : g.percentage >= 25 ? 'bg-orange-500' : 'bg-slate-400'"
                                     :style="'width: ' + g.percentage + '%'"></div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- رسالة عندما لا توجد أهداف -->
                <template x-if="goals.length === 0">
                    <div class="p-5 rounded-2xl bg-slate-50 border border-dashed border-slate-300 text-center">
                        <i class="fa-solid fa-bullseye text-slate-300 text-2xl block mb-2"></i>
                        <p class="text-xs font-bold text-slate-400">لا توجد أهداف سابقة لهذا الطفل</p>
                        <p class="text-[10px] text-slate-400 mt-0.5">أضف أهداف الجلسة الجديدة من حقل الإدخال أدناه</p>
                    </div>
                </template>

                <!-- إضافة هدف جديد -->
                <div class="flex gap-2 pt-1">
                    <input type="text" x-model="newGoalText" @keydown.enter.prevent="addGoal()" placeholder="اكتب هدفاً جديداً واضغط إضافة (مثل: نطق صوت /ك/ بمفرده)..." class="flex-1 p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white text-xs font-semibold">
                    <button type="button" @click="addGoal()" class="px-4 py-2.5 rounded-2xl bg-purple-100 hover:bg-purple-200 text-purple-700 text-xs font-bold transition flex items-center gap-1.5">
                        <i class="fa-solid fa-plus text-xs"></i>
                        <span>إضافة هدف</span>
                    </button>
                </div>
            </div>

            <!-- تقرير الأخصائي المفصل -->
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <label class="block font-bold text-slate-700 text-xs">
                        ملاحظات وتقرير الأخصائي المفصل عن الجلسة <span class="text-rose-500">*</span>
                    </label>
                    <button type="button" @click="toggleRecording('clinicalNotes')" class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[10px] font-bold transition shadow-xs" :class="isRecording && recordingTarget === 'clinicalNotes' ? 'bg-rose-100 text-rose-600 animate-pulse border border-rose-200' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 border border-slate-200'">
                        <i class="fa-solid fa-microphone"></i>
                        <span x-text="isRecording && recordingTarget === 'clinicalNotes' ? 'جاري الاستماع... اضغط للإيقاف' : 'إملاء صوتي'"></span>
                    </button>
                </div>
                <div class="relative">
                    <textarea name="clinical_notes" x-model="clinicalNotes" required rows="4" placeholder="اكتب أو املأ ما تم إنجازه مع الطفل بالتفصيل، الاستجابات، الصعوبات، والملاحظات السلوكية أثناء التدريب..." class="w-full p-4 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-medium text-xs leading-relaxed" :class="isRecording && recordingTarget === 'clinicalNotes' ? 'border-rose-300 ring-2 ring-rose-100 bg-white' : ''"></textarea>
                    
                    <div x-show="interimText && recordingTarget === 'clinicalNotes'" class="absolute bottom-10 left-3 right-3 p-2 bg-slate-800/80 text-white rounded-xl text-xs backdrop-blur-sm shadow-sm" x-transition>
                        <span class="opacity-75">جاري الاستماع: </span>
                        <span x-text="interimText" class="font-bold"></span>
                    </div>

                    <!-- Recording Indicator -->
                    <div x-show="isRecording && recordingTarget === 'clinicalNotes'" class="absolute bottom-3 left-3 flex items-center gap-1.5 bg-rose-50 px-2 py-1 rounded-lg border border-rose-100">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                        </span>
                        <span class="text-[9px] font-bold text-rose-600">يتحدث الآن...</span>
                    </div>
                </div>
            </div>

            <!-- التمرين المنزلي للأهل -->
            <div class="space-y-4 p-4 rounded-3xl border border-amber-200 bg-amber-50/50">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="block font-bold text-amber-900 text-xs flex items-center gap-1.5">
                            <i class="fa-solid fa-house-user text-amber-600"></i>
                            <span>التمرين / الواجب المنزلي المطلوب من ولي الأمر (يظهر في تقرير الأهل):</span>
                        </label>
                        <button type="button" @click="toggleRecording('homeExercise')" class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[10px] font-bold transition shadow-xs" :class="isRecording && recordingTarget === 'homeExercise' ? 'bg-rose-100 text-rose-600 animate-pulse border border-rose-200' : 'bg-white text-amber-700 hover:bg-amber-100 border border-amber-200'">
                            <i class="fa-solid fa-microphone"></i>
                            <span x-text="isRecording && recordingTarget === 'homeExercise' ? 'جاري الاستماع... اضغط للإيقاف' : 'إملاء صوتي'"></span>
                        </button>
                    </div>
                    <div class="relative">
                        <textarea name="home_exercise" x-model="homeExercise" rows="2" placeholder="مثال: تكرار لعبة الكروت بالمرآة مع الطفل 10 دقائق يومياً قبل النوم، وتشجيعه عند نطق صوت الكاف..." class="w-full p-3.5 bg-white border border-amber-200/80 rounded-2xl outline-none focus:border-amber-400 font-medium text-xs text-amber-950 leading-relaxed" :class="isRecording && recordingTarget === 'homeExercise' ? 'border-rose-300 ring-2 ring-rose-100' : ''"></textarea>
                        
                        <div x-show="interimText && recordingTarget === 'homeExercise'" class="absolute bottom-10 left-3 right-3 p-2 bg-slate-800/80 text-white rounded-xl text-xs backdrop-blur-sm shadow-sm" x-transition>
                            <span class="opacity-75">جاري الاستماع: </span>
                            <span x-text="interimText" class="font-bold"></span>
                        </div>

                        <!-- Recording Indicator -->
                        <div x-show="isRecording && recordingTarget === 'homeExercise'" class="absolute bottom-3 left-3 flex items-center gap-1.5 bg-rose-50 px-2 py-1 rounded-lg border border-rose-100">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                            </span>
                            <span class="text-[9px] font-bold text-rose-600">يتحدث الآن...</span>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-2">
                    <label class="block font-bold text-amber-900 text-xs flex items-center gap-1.5">
                        <i class="fa-solid fa-paperclip text-amber-600"></i>
                        <span>مرفق الواجب (صورة توضيحية أو مقطع صوتي/فيديو يشرح التمرين للأهل):</span>
                    </label>
                    <div class="relative border-2 border-dashed border-amber-300 rounded-2xl p-4 text-center bg-white hover:bg-amber-50 transition cursor-pointer">
                        <input type="file" id="homeworkFileInput" name="homework_file[]" multiple accept="image/*,video/*,audio/*" @change="handleHomeworkChange" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full">
                        <div class="flex flex-col items-center gap-2 pointer-events-none">
                            <i class="fa-solid fa-cloud-arrow-up text-xl text-amber-500"></i>
                            <span class="text-xs font-bold text-amber-800">اضغط لرفع ملفات توضيحية للواجب المنزلي (اختياري)</span>
                        </div>
                    </div>
                    
                    <template x-if="homeworkFiles.length > 0">
                        <div class="mt-3 space-y-2">
                            <template x-for="(file, index) in homeworkFiles" :key="index">
                                <div class="p-2 bg-amber-50 border border-amber-200 text-amber-900 rounded-xl text-xs font-bold flex items-center justify-between gap-2 shadow-xs">
                                    <div class="flex items-center gap-2 truncate">
                                        <i class="fa-solid fa-paperclip text-amber-600"></i>
                                        <span x-text="file.name" class="truncate"></span>
                                        <span x-text="file.size" class="text-[10px] text-amber-600/70"></span>
                                    </div>
                                    <button type="button" @click="removeHomeworkFile(index)" class="text-rose-500 hover:text-rose-700 bg-white rounded-lg px-2 py-1 shadow-xs shrink-0">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>

        </div>

        <!-- ==================== 4. رفع مقطع فيديو توثيقي للطفل وإرسال التنبيه ==================== -->
        <div class="bg-white rounded-3xl p-6 md:p-8 border border-slate-100 shadow-sm space-y-6">
            
            <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                <i class="fa-solid fa-video text-sm" style="color: #0d9488;"></i>
                <h3 class="font-black text-sm text-slate-800">4. رفع مقطع فيديو توثيقي وإشعار ولي الأمر</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 items-center">
                
                <!-- مربع رفع الفيديو -->
                <div class="border-2 border-dashed border-purple-200 rounded-3xl p-6 text-center bg-purple-50/30 hover:bg-purple-50/70 transition relative">
                    <input type="file" id="videoFileInput" name="video[]" multiple accept="video/*,image/*" @change="handleVideoChange" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full">
                    <div class="pointer-events-none">
                        <i class="fa-solid fa-cloud-arrow-up text-3xl text-purple-500 mb-2"></i>
                        <p class="font-bold text-xs text-slate-700">اضغط لرفع مقاطع فيديو أو صور للطفل في الجلسة</p>
                        <p class="text-[10px] text-slate-400 mt-1">يدعم MP4, MOV (مقاطع توثيق الإنجاز 10 - 60 ثانية)</p>
                        <p class="text-[9px] text-rose-500 mt-2 font-bold bg-rose-50 inline-block px-2 py-1 rounded-md">💡 نصيحة: لتسريع الرفع، يفضل تصوير الفيديو بجودة متوسطة (720p) بدلاً من 4K</p>
                    </div>

                    <template x-if="videoFiles.length > 0">
                        <div class="mt-4 space-y-2 relative z-10">
                            <template x-for="(file, index) in videoFiles" :key="index">
                                <div class="p-2 bg-purple-100 text-purple-900 rounded-xl text-xs font-bold flex items-center justify-between gap-2 text-right shadow-xs">
                                    <div class="flex items-center gap-2 truncate">
                                        <i class="fa-solid fa-file-video text-purple-600"></i>
                                        <span x-text="file.name" class="truncate"></span>
                                        <span x-text="file.size" class="text-[10px] text-purple-600/70"></span>
                                    </div>
                                    <button type="button" @click="removeVideoFile(index)" class="text-rose-500 hover:text-rose-700 bg-white rounded-lg px-2 py-1 shadow-xs shrink-0 relative z-20">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block font-bold text-slate-700 text-xs mb-1.5">عنوان أو وصف الفيديو (يظهر للأهل):</label>
                        <input type="text" name="video_title" placeholder="مثال: استجابة ممتازة لنطق صوت /ك/ بمساعدة بصرية" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white text-xs font-semibold">
                    </div>

                    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs font-bold text-emerald-900 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <i class="fa-brands fa-whatsapp text-emerald-600 text-lg"></i>
                            <span>إرسال ملخص الجلسة والتمرين لولي الأمر عبر الواتساب</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="whatsapp_notify" value="1" checked class="sr-only peer">
                            <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-600"></div>
                        </label>
                    </div>
                </div>

            </div>

            <!-- أزرار الحفظ والنشر النهائي -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('doctor.portal') }}" class="px-6 py-3 rounded-2xl text-slate-500 font-bold hover:bg-slate-100 text-xs transition">
                    إلغاء
                </a>
                <button type="submit" class="px-9 py-4 rounded-2xl text-white font-black text-sm shadow-xl hover:opacity-95 active:scale-95 transition flex items-center gap-2.5" style="background: linear-gradient(135deg, #0d9488 0%, color-mix(in srgb, #0d9488 85%, #000) 100%);">
                    <i class="fa-solid fa-floppy-disk text-base"></i>
                    <span>حفظ ونشر التقرير لبروفايل الطفل والأهل</span>
                </button>
            </div>

        </div>

    </form>

</div>
@endsection

@push('scripts')
@if($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let errorHtml = '<ul class="text-xs text-rose-600 text-right space-y-1 list-disc list-inside mt-2">';
        @foreach($errors->all() as $error)
            errorHtml += '<li>{{ $error }}</li>';
        @endforeach
        errorHtml += '</ul>';

        Swal.fire({
            icon: 'error',
            title: 'يوجد خطأ في البيانات!',
            html: errorHtml,
            confirmButtonText: 'حسناً',
            confirmButtonColor: '#0d9488',
            customClass: {
                popup: 'rounded-3xl',
                title: 'text-lg font-black text-slate-800 font-cairo',
                htmlContainer: 'font-cairo'
            }
        });
    });
</script>
@endif
@endpush


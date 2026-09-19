<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>شاشة نداء المركز</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://code.responsivevoice.org/responsivevoice.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap');
        body { font-family: 'Cairo', sans-serif; background-color: #0f172a; overflow: hidden; }

        .notification-bg {
            background: linear-gradient(135deg, #1e40af, #3b82f6);
            animation: pulseBg 2s infinite alternate;
        }
        @keyframes pulseBg {
            from { background: linear-gradient(135deg, #1e40af, #1e3a8a); }
            to { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
        }
        
        .waiting-dots::after {
            content: '';
            animation: dots 2s infinite;
        }
        @keyframes dots {
            0% { content: ''; }
            33% { content: '.'; }
            66% { content: '..'; }
            100% { content: '...'; }
        }

        .fade-in-up {
            animation: fadeInUp 0.5s ease-out forwards;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="text-white h-screen w-screen relative flex flex-col" x-data="callingScreenApp()">

    <!-- شاشة البداية لتفعيل الصوت (ضرورية لسياسات المتصفح) -->
    <div x-show="!started" class="absolute inset-0 z-50 bg-slate-900 flex flex-col items-center justify-center">
        <h1 class="text-5xl font-black text-white mb-12 drop-shadow-md">شاشة النداء الآلي للمركز</h1>
        <button @click="startScreen()" class="px-12 py-6 bg-blue-600 hover:bg-blue-500 rounded-3xl text-3xl font-black text-white shadow-[0_0_40px_rgba(37,99,235,0.5)] transition hover:scale-105">
            <i class="fa-solid fa-volume-high ml-3"></i> تفعيل الشاشة والصوت
        </button>
    </div>

    <!-- المحتوى الرئيسي مقسم نصفين -->
    <div x-show="started && !showingNotification" x-transition.opacity.duration.1000ms class="absolute inset-0 flex bg-slate-900">
        
        <!-- النصف الأيمن: قائمة الانتظار -->
        <div class="w-1/2 h-full flex flex-col p-8 border-l border-slate-800 bg-slate-900 relative z-10">
            <div class="flex items-center justify-between mb-8 pb-6 border-b border-slate-800">
                <h2 class="text-4xl font-black text-white flex items-center gap-4">
                    <i class="fa-solid fa-list-ol text-blue-500"></i> قائمة الانتظار
                </h2>
                <div class="flex items-center gap-2 text-slate-400">
                    <i class="fa-solid fa-circle text-[10px] text-emerald-500 animate-pulse"></i>
                    <span class="text-sm font-bold">تحديث تلقائي</span>
                </div>
            </div>

            <div class="flex-1 overflow-hidden flex flex-col">
                <template x-if="waitlist.length === 0">
                    <div class="flex-1 flex flex-col items-center justify-center text-slate-400 opacity-80">
                        <i class="fa-solid fa-mug-hot text-8xl mb-6 text-slate-500"></i>
                        <h3 class="text-3xl font-bold">لا يوجد أحد في الانتظار حالياً</h3>
                        <p class="mt-4 text-slate-500">ستظهر أسماء الأطفال هنا بمجرد إضافتهم للقائمة</p>
                    </div>
                </template>

                <div class="space-y-4 overflow-y-auto pr-2" x-show="waitlist.length > 0">
                    <template x-for="(kid, index) in waitlist" :key="index">
                        <div class="bg-slate-800/80 p-5 rounded-2xl border border-slate-700 flex items-center gap-6 fade-in-up" :style="'animation-delay: ' + (index * 0.1) + 's'">
                            <div class="w-16 h-16 rounded-xl bg-blue-900/50 flex items-center justify-center text-blue-400 font-black text-2xl border border-blue-800 shrink-0">
                                <span x-text="'#' + (index + 1)"></span>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-3xl font-black text-white mb-2" x-text="kid.child_name"></h3>
                                <p class="text-slate-400 text-lg font-semibold flex items-center gap-2">
                                    <i class="fa-solid fa-user-doctor"></i> <span x-text="kid.specialist_name"></span>
                                </p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
            
            <!-- زر خفي للتجربة -->
            <button @click="triggerNotification({type: 'call', child_name: 'بطل تجريبي', specialist_name: 'الأخصائي أحمد'})" class="absolute bottom-5 right-5 opacity-10 hover:opacity-100 bg-white/10 text-white px-4 py-2 rounded-xl text-sm transition">
                <i class="fa-solid fa-flask"></i> تجربة النداء
            </button>
        </div>

        <!-- النصف الأيسر: فيديو للأطفال -->
        <div class="w-1/2 h-full bg-black relative z-0">
            <template x-if="started && youtubeId">
                <iframe class="absolute inset-0 w-full h-full pointer-events-none" 
                        :src="'https://www.youtube.com/embed/' + youtubeId + '?autoplay=1&mute=0&loop=1&playlist=' + youtubeId + '&controls=0&showinfo=0&rel=0'" 
                        frameborder="0" 
                        allow="autoplay; encrypted-media" 
                        allowfullscreen>
                </iframe>
            </template>
            <!-- overlay to prevent clicks on video -->
            <div class="absolute inset-0 bg-transparent z-10"></div>
            
            <div x-show="!youtubeId" class="absolute inset-0 flex flex-col items-center justify-center text-slate-600">
                <i class="fa-brands fa-youtube text-6xl mb-4 opacity-50"></i>
                <p>لم يتم تعيين فيديو (يرجى إضافته من الإعدادات)</p>
            </div>
        </div>

    </div>

    <!-- زر الإعدادات -->
    <button @click="showSettings = !showSettings" class="absolute top-6 left-6 text-slate-500 hover:text-white transition text-2xl z-50">
        <i class="fa-solid fa-gear"></i>
    </button>

    <!-- قائمة إعدادات الصوت -->
    <div x-show="showSettings" @click.away="showSettings = false" x-transition class="absolute top-16 left-6 bg-slate-800 border border-slate-700 rounded-2xl p-5 shadow-2xl z-50 w-80">
        <h3 class="text-white font-bold mb-3 border-b border-slate-700 pb-2">إعدادات الشاشة</h3>
        
        <div class="mb-4">
            <label class="block text-slate-300 text-sm mb-2">اختر المعلق الصوتي:</label>
            <select x-model="selectedVoiceIndex" @change="saveVoicePreference" class="w-full bg-slate-900 border border-slate-700 text-white rounded-xl p-2.5 text-sm outline-none focus:border-blue-500">
                <template x-for="(voice, index) in arabicVoices" :key="index">
                    <option :value="index" x-text="voice.name + (voice.lang ? ' (' + voice.lang + ')' : '')"></option>
                </template>
                <option x-show="arabicVoices.length === 0" value="">جاري تحميل الأصوات...</option>
            </select>
            <button @click="testVoice()" class="w-full mt-2 bg-slate-700 hover:bg-slate-600 text-white text-xs font-bold py-2 rounded-xl transition">
                <i class="fa-solid fa-play mr-2"></i> تجربة الصوت المختار
            </button>
        </div>

        <div class="mb-4">
            <label class="block text-slate-300 text-sm mb-2">معرف فيديو يوتيوب (ID):</label>
            <input type="text" x-model="youtubeId" @input="saveYoutubePreference" placeholder="مثال: t0Q2otsqC4I" class="w-full bg-slate-900 border border-slate-700 text-white rounded-xl p-2.5 text-sm outline-none focus:border-blue-500 font-mono text-left" dir="ltr">
            <p class="text-[10px] text-slate-500 mt-1">الرموز الموجودة بعد v= في رابط يوتيوب.</p>
        </div>
    </div>

    <!-- شاشة الإشعارات (النداء) -->
    <div x-show="started && showingNotification" x-transition.opacity.duration.500ms class="absolute inset-0 z-40 notification-bg flex flex-col items-center justify-center text-center p-12">
        <div class="bg-white/10 backdrop-blur-md rounded-[3rem] p-16 border border-white/20 shadow-2xl w-full max-w-6xl relative overflow-hidden">
            <!-- Decorative blur blobs -->
            <div class="absolute -top-32 -right-32 w-64 h-64 bg-yellow-400 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
            <div class="absolute -bottom-32 -left-32 w-64 h-64 bg-blue-400 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>

            <i class="fa-solid fa-bell text-8xl text-yellow-300 mb-10 animate-bounce relative z-10"></i>
            <h2 class="text-6xl font-black text-white mb-8 leading-relaxed relative z-10" x-html="notificationText1"></h2>
            
            <div class="w-48 h-2 bg-yellow-400 mx-auto rounded-full mb-10 relative z-10"></div>
            
            <h3 class="text-5xl font-bold text-yellow-200 leading-relaxed relative z-10" x-html="notificationText2"></h3>
        </div>
    </div>

    <script>
        function callingScreenApp() {
            return {
                started: false,
                showingNotification: false,
                showSettings: false,
                notificationText1: '',
                notificationText2: '',
                audioCtx: null,
                lastTimestamp: 0,
                arabicVoices: [],
                waitlist: [],
                selectedVoiceIndex: localStorage.getItem('preferred_voice_index') || 0,
                youtubeId: localStorage.getItem('preferred_youtube_id') || 't0Q2otsqC4I', // Tom & Jerry Default
                
                init() {
                    const loadVoices = () => {
                        let allVoices = window.speechSynthesis.getVoices();
                        this.arabicVoices = allVoices.filter(v => v.lang.startsWith('ar'));
                        
                        this.arabicVoices.unshift({
                            name: "صوت سحابي رجالي (Male - Online)",
                            lang: "Responsive",
                            isResponsiveMale: true
                        });

                        this.arabicVoices.unshift({
                            name: "صوت جوجل السحابي (احترافي نسائي)",
                            lang: "ar-Cloud",
                            isCloud: true
                        });
                    };
                    
                    loadVoices();
                    if (window.speechSynthesis.onvoiceschanged !== undefined) {
                        window.speechSynthesis.onvoiceschanged = loadVoices;
                    }
                },

                saveVoicePreference() {
                    localStorage.setItem('preferred_voice_index', this.selectedVoiceIndex);
                },

                saveYoutubePreference() {
                    localStorage.setItem('preferred_youtube_id', this.youtubeId);
                },

                testVoice() {
                    this.playDing();
                    setTimeout(() => {
                        this.speakArabic("أهلاً بك في شاشة النداء، هذا هو الصوت المختار.");
                    }, 1000);
                },

                startScreen() {
                    this.started = true;
                    this.startPolling();
                    this.fetchWaitlist();
                    setInterval(() => {
                        this.fetchWaitlist();
                    }, 10000); // تحديث القائمة كل 10 ثواني
                },

                fetchWaitlist() {
                    fetch('/api/center-screen/waitlist')
                        .then(res => res.json())
                        .then(data => {
                            this.waitlist = data;
                        })
                        .catch(err => console.error(err));
                },

                startPolling() {
                    setInterval(() => {
                        fetch('/api/center-screen/notifications')
                            .then(response => response.json())
                            .then(data => {
                                if (data && data.timestamp && data.timestamp > this.lastTimestamp) {
                                    if (this.lastTimestamp === 0) {
                                        this.lastTimestamp = data.timestamp;
                                    } else {
                                        this.lastTimestamp = data.timestamp;
                                        this.triggerNotification(data);
                                        // تحديث القائمة فوراً عند ظهور نداء جديد
                                        setTimeout(() => this.fetchWaitlist(), 1000);
                                    }
                                } else if (data && this.lastTimestamp === 0) {
                                    this.lastTimestamp = data.timestamp;
                                }
                            })
                            .catch(error => console.error('Error fetching notifications:', error));
                    }, 3000);
                },

                triggerNotification(data) {
                    if (data && data.type === 'call') {
                        this.notificationText1 = `حان دور البطل: <span class="text-yellow-300">${data.child_name}</span>`;
                        this.notificationText2 = `للدخول لجلسة الأخصائي: <span class="text-white">${data.specialist_name}</span>`;
                        let speechText = `حَانَ دَوْرُ البَطَل ${data.child_name}، لِلدُخُولِ لِجَلْسَةِ الأَخِصَّائِي ${data.specialist_name}.`;
                        
                        this.showingNotification = true;
                        this.playDing();

                        setTimeout(() => {
                            this.speakArabic(speechText);
                        }, 1000);

                        setTimeout(() => {
                            this.showingNotification = false;
                        }, 20000);
                    }
                },

                playDing() {
                    let audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3');
                    audio.play().catch(e => console.error('Audio play error:', e));
                },

                speakArabic(text) {
                    const selectedVoice = this.arabicVoices[this.selectedVoiceIndex];
                    
                    if (selectedVoice && selectedVoice.isResponsiveMale) {
                        if (typeof responsiveVoice !== 'undefined') {
                            responsiveVoice.speak(text, "Arabic Male", {pitch: 1, rate: 0.9});
                        }
                        return;
                    }

                    if (selectedVoice && selectedVoice.isCloud) {
                        const url = '/api/tts?text=' + encodeURIComponent(text);
                        const audio = new Audio(url);
                        audio.play().catch(e => console.error("Audio play error:", e));
                        return;
                    }

                    if ('speechSynthesis' in window) {
                        window.speechSynthesis.cancel();
                        const utterance = new SpeechSynthesisUtterance(text);
                        
                        if (selectedVoice) {
                            utterance.voice = selectedVoice;
                        } else {
                            utterance.lang = 'ar-SA';
                        }
                        
                        utterance.rate = 0.85; 
                        utterance.pitch = 1.0;
                        window.speechSynthesis.speak(utterance);
                    }
                }
            }
        }
    </script>
</body>
</html>
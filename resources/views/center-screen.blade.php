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
    </style>
</head>
<body class="text-white h-screen w-screen relative" x-data="callingScreenApp()">

    <!-- شاشة البداية لتفعيل الصوت (ضرورية لسياسات المتصفح) -->
    <div x-show="!started" class="absolute inset-0 z-50 bg-slate-900 flex flex-col items-center justify-center">
        <h1 class="text-5xl font-black text-white mb-12 drop-shadow-md">شاشة النداء الآلي للمركز</h1>
        <button @click="startScreen()" class="px-12 py-6 bg-blue-600 hover:bg-blue-500 rounded-3xl text-3xl font-black text-white shadow-[0_0_40px_rgba(37,99,235,0.5)] transition hover:scale-105">
            <i class="fa-solid fa-volume-high ml-3"></i> تفعيل الشاشة والصوت
        </button>
    </div>

    <!-- المحتوى الرئيسي (في انتظار الجلسات) -->
    <div x-show="started && !showingNotification" x-transition.opacity.duration.1000ms class="absolute inset-0 flex flex-col items-center justify-center bg-slate-900">
        <div class="text-center opacity-40">
            <i class="fa-solid fa-mug-hot text-8xl mb-8"></i>
            <h2 class="text-6xl font-bold tracking-wider">في انتظار الجلسات<span class="waiting-dots"></span></h2>
        </div>
        
        <!-- زر خفي للتجربة (يظهر بلون باهت جداً في الأسفل) -->
        <button @click="triggerNotification({finished_child: 'بطل تجريبي', next_child: 'البطل القادم', specialist: 'د. أحمد'})" class="absolute bottom-5 left-5 opacity-10 hover:opacity-100 bg-white/10 text-white px-4 py-2 rounded-xl text-sm transition">
            <i class="fa-solid fa-flask"></i> تجربة الشاشة والصوت
        </button>
    </div>

    <!-- زر الإعدادات -->
    <button @click="showSettings = !showSettings" class="absolute top-6 left-6 text-slate-500 hover:text-white transition text-2xl z-50">
        <i class="fa-solid fa-gear"></i>
    </button>

    <!-- قائمة إعدادات الصوت -->
    <div x-show="showSettings" @click.away="showSettings = false" x-transition class="absolute top-16 left-6 bg-slate-800 border border-slate-700 rounded-2xl p-5 shadow-2xl z-50 w-80">
        <h3 class="text-white font-bold mb-3 border-b border-slate-700 pb-2">إعدادات الصوت والنداء</h3>
        <label class="block text-slate-300 text-sm mb-2">اختر المعلق الصوتي:</label>
        <select x-model="selectedVoiceIndex" @change="saveVoicePreference" class="w-full bg-slate-900 border border-slate-700 text-white rounded-xl p-2.5 text-sm outline-none focus:border-blue-500">
            <template x-for="(voice, index) in arabicVoices" :key="index">
                <option :value="index" x-text="voice.name + (voice.lang ? ' (' + voice.lang + ')' : '')"></option>
            </template>
            <option x-show="arabicVoices.length === 0" value="">جاري تحميل الأصوات...</option>
        </select>
        <button @click="testVoice()" class="w-full mt-4 bg-blue-600 hover:bg-blue-500 text-white text-sm font-bold py-2 rounded-xl transition">
            <i class="fa-solid fa-play mr-2"></i> تجربة الصوت المختار
        </button>
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
                selectedVoiceIndex: localStorage.getItem('preferred_voice_index') || 0,
                
                init() {
                    // تحميل الأصوات المتاحة في المتصفح
                    const loadVoices = () => {
                        let allVoices = window.speechSynthesis.getVoices();
                        this.arabicVoices = allVoices.filter(v => v.lang.startsWith('ar'));
                        
                        // إضافة صوت جوجل السحابي الرائع كخيار أول
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

                testVoice() {
                    this.speakArabic("أهلاً بك في شاشة النداء، هذا هو الصوت المختار.");
                },

                startScreen() {
                    this.started = true;
                    this.audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                    const dummyUtterance = new SpeechSynthesisUtterance('');
                    window.speechSynthesis.speak(dummyUtterance);
                    this.startPolling();
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
                                    }
                                } else if (data && this.lastTimestamp === 0) {
                                    this.lastTimestamp = data.timestamp;
                                }
                            })
                            .catch(error => console.error('Error fetching notifications:', error));
                    }, 3000);
                },

                triggerNotification(data) {
                    this.notificationText1 = `انتهت جلسة البطل: <span class="text-blue-300">${data.finished_child}</span>`;
                    let speechText = `تَم الانتهاء من جَلْسَة البطل ${data.finished_child}. `;
                    
                    if (data.next_child) {
                        this.notificationText2 = `الدور الآن على: <span class="text-white">${data.next_child}</span><br>عند الأخصائي: <span class="text-white">${data.specialist}</span>`;
                        speechText += `وَالدَّورُ الآَن عَلَى البطل ${data.next_child}، عِنْدَ الأخصائي ${data.specialist}.`;
                    } else {
                        this.notificationText2 = `لا يوجد أطفال في الانتظار حالياً عند ${data.specialist}`;
                    }

                    this.showingNotification = true;
                    this.playDing();

                    setTimeout(() => {
                        this.speakArabic(speechText);
                    }, 800);

                    setTimeout(() => {
                        this.showingNotification = false;
                    }, 20000);
                },

                playDing() {
                    if (!this.audioCtx) return;
                    const oscillator = this.audioCtx.createOscillator();
                    const gainNode = this.audioCtx.createGain();
                    oscillator.type = 'sine';
                    oscillator.frequency.setValueAtTime(900, this.audioCtx.currentTime);
                    oscillator.frequency.exponentialRampToValueAtTime(300, this.audioCtx.currentTime + 1);
                    gainNode.gain.setValueAtTime(1, this.audioCtx.currentTime);
                    gainNode.gain.exponentialRampToValueAtTime(0.01, this.audioCtx.currentTime + 1);
                    oscillator.connect(gainNode);
                    gainNode.connect(this.audioCtx.destination);
                    oscillator.start();
                    oscillator.stop(this.audioCtx.currentTime + 1);
                },

                speakArabic(text) {
                    const selectedVoice = this.arabicVoices[this.selectedVoiceIndex];
                    
                    // 1. صوت رجالي سحابي (ResponsiveVoice)
                    if (selectedVoice && selectedVoice.isResponsiveMale) {
                        if (typeof responsiveVoice !== 'undefined') {
                            responsiveVoice.speak(text, "Arabic Male", {pitch: 1, rate: 0.9});
                        }
                        return;
                    }

                    // 2. استخدام واجهة جوجل السحابية عبر السيرفر الداخلي (صوت نسائي)
                    if (selectedVoice && selectedVoice.isCloud) {
                        const url = '/api/tts?text=' + encodeURIComponent(text);
                        const audio = new Audio(url);
                        audio.play().catch(e => console.error("Audio play error:", e));
                        return;
                    }

                    // 3. الاستخدام الافتراضي لأصوات المتصفح/النظام
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
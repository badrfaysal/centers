@extends('layouts.app')
@section('title', 'محادثة التمرين: ' . $homework->title)
@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl" x-data="audioRecorder()">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('doctor.homeworks.index') }}" class="text-gray-500 hover:text-emerald-600 transition">
            <i class="fa-solid fa-arrow-right text-xl"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-800">
            متابعة نطق: {{ $homework->child->name }}
        </h1>
    </div>

    <!-- تفاصيل التمرين -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 mb-6">
        <h2 class="text-lg font-bold text-gray-800 mb-2">{{ $homework->title }}</h2>
        <p class="text-gray-600 whitespace-pre-wrap">{{ $homework->description }}</p>
    </div>

    <!-- سجل المحادثة (الرسائل والتسجيلات) -->
    <div class="space-y-6 mb-6">
        @foreach($homework->messages as $msg)
            <div class="flex {{ $msg->sender_type === 'specialist' ? 'justify-start' : 'justify-end' }}">
                <div class="max-w-[80%] flex flex-col gap-1 {{ $msg->sender_type === 'specialist' ? 'items-start' : 'items-end' }}">
                    <span class="text-xs text-gray-500 mx-2">{{ $msg->sender_type === 'specialist' ? 'أنت' : 'ولي الأمر' }} - {{ $msg->created_at->format('h:i A') }}</span>
                    <div class="p-4 rounded-2xl {{ $msg->sender_type === 'specialist' ? 'bg-emerald-50 border border-emerald-100 text-emerald-900 rounded-tr-none' : 'bg-gray-100 border border-gray-200 text-gray-800 rounded-tl-none' }}">
                        @if($msg->message_type === 'audio')
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-headphones text-xl {{ $msg->sender_type === 'specialist' ? 'text-emerald-500' : 'text-gray-500' }}"></i>
                                <audio controls src="{{ asset('storage/' . $msg->content) }}" style="height: 40px; width: 250px;"></audio>
                            </div>
                        @else
                            <p class="whitespace-pre-wrap">{{ $msg->content }}</p>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- صندوق الرد -->
    <div class="bg-white p-4 rounded-3xl shadow-lg border border-gray-100">
        <form action="{{ route('doctor.homeworks.reply', $homework->id) }}" method="POST" enctype="multipart/form-data" id="replyForm">
            @csrf
            
            <div x-show="!isRecording && !hasRecording" class="flex items-end gap-3">
                <textarea name="content" rows="2" class="w-full bg-gray-50 border-transparent focus:bg-white focus:border-emerald-500 focus:ring-emerald-500 rounded-2xl resize-none" placeholder="اكتب ردك هنا..."></textarea>
                <div class="flex gap-2">
                    <button type="button" @click="startRecording()" class="w-12 h-12 flex-shrink-0 flex items-center justify-center rounded-full bg-gray-100 text-gray-600 hover:bg-emerald-100 hover:text-emerald-600 transition">
                        <i class="fa-solid fa-microphone text-xl"></i>
                    </button>
                    <button type="submit" class="w-12 h-12 flex-shrink-0 flex items-center justify-center rounded-full bg-emerald-600 text-white hover:bg-emerald-700 shadow-md shadow-emerald-500/30 transition transform hover:scale-105">
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </div>
            </div>

            <!-- واجهة التسجيل -->
            <div x-show="isRecording" class="flex items-center justify-between bg-red-50 p-4 rounded-2xl border border-red-100" style="display: none;">
                <div class="flex items-center gap-3 text-red-600 font-bold">
                    <span class="w-3 h-3 rounded-full bg-red-500 animate-ping"></span>
                    جارٍ التسجيل... <span x-text="recordingTime"></span>
                </div>
                <button type="button" @click="stopRecording()" class="bg-red-600 text-white px-4 py-2 rounded-xl hover:bg-red-700 font-bold shadow-sm">
                    إيقاف التسجيل
                </button>
            </div>

            <!-- واجهة بعد التسجيل (المعاينة) -->
            <div x-show="hasRecording && !isRecording" class="flex items-center justify-between bg-emerald-50 p-4 rounded-2xl border border-emerald-100" style="display: none;">
                <div class="flex items-center gap-4 w-full">
                    <button type="button" @click="discardRecording()" class="text-gray-400 hover:text-red-500 transition">
                        <i class="fa-solid fa-trash text-xl"></i>
                    </button>
                    <audio x-ref="audioPlayback" controls class="h-10 w-full"></audio>
                    <button type="button" @click="submitRecording()" class="bg-emerald-600 text-white px-6 py-2 rounded-xl hover:bg-emerald-700 font-bold shadow-md whitespace-nowrap flex items-center gap-2">
                        <i class="fa-solid fa-paper-plane"></i> إرسال التسجيل
                    </button>
                </div>
            </div>
            
            <input type="file" name="audio" x-ref="audioInput" class="hidden" accept="audio/*">
        </form>
    </div>
</div>

<script>
function audioRecorder() {
    return {
        isRecording: false,
        hasRecording: false,
        mediaRecorder: null,
        audioChunks: [],
        recordingTime: '00:00',
        timerInterval: null,
        startTime: null,

        async startRecording() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                this.mediaRecorder = new MediaRecorder(stream);
                this.audioChunks = [];

                this.mediaRecorder.ondataavailable = (event) => {
                    this.audioChunks.push(event.data);
                };

                this.mediaRecorder.onstop = () => {
                    const audioBlob = new Blob(this.audioChunks, { type: 'audio/webm' });
                    const audioUrl = URL.createObjectURL(audioBlob);
                    this.$refs.audioPlayback.src = audioUrl;
                    
                    // Create a file object and put it in the input
                    const file = new File([audioBlob], "recording.webm", { type: 'audio/webm' });
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    this.$refs.audioInput.files = dataTransfer.files;
                    
                    this.hasRecording = true;
                };

                this.mediaRecorder.start();
                this.isRecording = true;
                
                this.startTime = Date.now();
                this.timerInterval = setInterval(() => {
                    const diff = Math.floor((Date.now() - this.startTime) / 1000);
                    const minutes = String(Math.floor(diff / 60)).padStart(2, '0');
                    const seconds = String(diff % 60).padStart(2, '0');
                    this.recordingTime = `${minutes}:${seconds}`;
                }, 1000);

            } catch (err) {
                console.error("Error accessing microphone:", err);
                Swal.fire('خطأ', 'لم نتمكن من الوصول للميكروفون، تأكد من إعطاء الصلاحيات للمتصفح.', 'error');
            }
        },

        stopRecording() {
            if (this.mediaRecorder && this.mediaRecorder.state !== 'inactive') {
                this.mediaRecorder.stop();
                this.mediaRecorder.stream.getTracks().forEach(track => track.stop());
            }
            this.isRecording = false;
            clearInterval(this.timerInterval);
        },

        discardRecording() {
            this.hasRecording = false;
            this.audioChunks = [];
            this.$refs.audioInput.value = '';
            this.$refs.audioPlayback.src = '';
        },

        submitRecording() {
            if(this.$refs.audioInput.files.length > 0) {
                document.getElementById('replyForm').submit();
            }
        }
    }
}
</script>
@endsection
@extends('layouts.app')
@section('title', 'تمرين: ' . $homework->title)
@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl" x-data="parentAudioRecorder()">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('parent.homeworks.index', $child->code) }}" class="text-gray-500 hover:text-emerald-600 transition">
            <i class="fa-solid fa-arrow-right text-xl"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-800">
            تمرين منزلي
        </h1>
    </div>

    <!-- تفاصيل التمرين -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 mb-6">
        <h2 class="text-lg font-bold text-gray-800 mb-2">{{ $homework->title }}</h2>
        <p class="text-gray-600 whitespace-pre-wrap">{{ $homework->description }}</p>
        <div class="mt-4 pt-4 border-t border-gray-100 text-sm text-gray-500 flex items-center gap-2">
            <i class="fa-solid fa-user-doctor"></i> بواسطة الأخصائي: <span class="font-bold">{{ $homework->specialist->name ?? 'الأخصائي' }}</span>
        </div>
    </div>

    <!-- سجل المحادثة (الرسائل والتسجيلات) -->
    <div class="space-y-6 mb-6">
        @foreach($homework->messages as $msg)
            <div class="flex {{ $msg->sender_type === 'parent' ? 'justify-start' : 'justify-end' }}">
                <div class="max-w-[90%] md:max-w-[70%] flex flex-col gap-1 {{ $msg->sender_type === 'parent' ? 'items-start' : 'items-end' }}">
                    <span class="text-xs text-gray-500 mx-2">{{ $msg->sender_type === 'parent' ? 'أنت' : 'الأخصائي' }} - {{ $msg->created_at->format('h:i A') }}</span>
                    <div class="p-4 rounded-2xl shadow-sm {{ $msg->sender_type === 'parent' ? 'bg-emerald-50 border border-emerald-100 text-emerald-900 rounded-tr-none' : 'bg-gray-100 border border-gray-200 text-gray-800 rounded-tl-none' }}">
                        @if($msg->message_type === 'audio')
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-microphone-lines text-xl {{ $msg->sender_type === 'parent' ? 'text-emerald-500' : 'text-gray-500' }}"></i>
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

    <!-- صندوق الرد لولي الأمر -->
    <div class="bg-white p-4 rounded-3xl shadow-lg border border-gray-100">
        <form action="{{ route('parent.homeworks.reply', ['code' => $child->code, 'homework' => $homework->id]) }}" method="POST" enctype="multipart/form-data" id="parentReplyForm">
            @csrf
            
            <div x-show="!isRecording && !hasRecording" class="flex flex-col md:flex-row items-center gap-3">
                <textarea name="content" rows="2" class="w-full bg-gray-50 border-transparent focus:bg-white focus:border-emerald-500 focus:ring-emerald-500 rounded-2xl resize-none" placeholder="اكتب ملاحظة للأخصائي... (اختياري)"></textarea>
                
                <div class="flex w-full md:w-auto gap-2">
                    <button type="button" @click="startRecording()" class="flex-1 md:flex-none md:w-16 h-12 flex items-center justify-center gap-2 rounded-2xl bg-emerald-100 text-emerald-700 hover:bg-emerald-200 transition font-bold">
                        <i class="fa-solid fa-microphone text-xl"></i> <span class="md:hidden">تسجيل صوتي</span>
                    </button>
                    <button type="submit" class="flex-1 md:flex-none md:w-16 h-12 flex items-center justify-center gap-2 rounded-2xl bg-emerald-600 text-white hover:bg-emerald-700 shadow-md transition font-bold">
                        <i class="fa-solid fa-paper-plane"></i> <span class="md:hidden">إرسال نص</span>
                    </button>
                </div>
            </div>

            <!-- واجهة التسجيل -->
            <div x-show="isRecording" class="flex items-center justify-between bg-red-50 p-4 rounded-2xl border border-red-100" style="display: none;">
                <div class="flex items-center gap-3 text-red-600 font-bold">
                    <span class="w-3 h-3 rounded-full bg-red-500 animate-ping"></span>
                    التسجيل قيد التقدم... <span x-text="recordingTime" class="font-mono bg-red-100 px-2 py-1 rounded"></span>
                </div>
                <button type="button" @click="stopRecording()" class="bg-red-600 text-white px-4 py-2 rounded-xl hover:bg-red-700 font-bold shadow-sm flex items-center gap-2">
                    <i class="fa-solid fa-stop"></i> إيقاف
                </button>
            </div>

            <!-- واجهة بعد التسجيل (المعاينة) -->
            <div x-show="hasRecording && !isRecording" class="flex flex-col md:flex-row items-center justify-between bg-emerald-50 p-4 rounded-2xl border border-emerald-100 gap-4" style="display: none;">
                <div class="flex items-center gap-4 w-full">
                    <button type="button" @click="discardRecording()" class="w-10 h-10 flex items-center justify-center rounded-full bg-white text-red-500 shadow hover:bg-red-50 transition flex-shrink-0">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                    <audio x-ref="audioPlayback" controls class="h-10 w-full"></audio>
                </div>
                <button type="button" @click="submitRecording()" class="w-full md:w-auto bg-emerald-600 text-white px-6 py-2.5 rounded-xl hover:bg-emerald-700 font-bold shadow-md whitespace-nowrap flex items-center justify-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i> إرسال التسجيل
                </button>
            </div>
            
            <input type="file" name="audio" x-ref="audioInput" class="hidden" accept="audio/*">
        </form>
    </div>
</div>

<script>
function parentAudioRecorder() {
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
                // Ensure proper constraints for mobile browsers
                const stream = await navigator.mediaDevices.getUserMedia({ audio: { echoCancellation: true, noiseSuppression: true } });
                this.mediaRecorder = new MediaRecorder(stream);
                this.audioChunks = [];

                this.mediaRecorder.ondataavailable = (event) => {
                    this.audioChunks.push(event.data);
                };

                this.mediaRecorder.onstop = () => {
                    const audioBlob = new Blob(this.audioChunks, { type: 'audio/webm' });
                    const audioUrl = URL.createObjectURL(audioBlob);
                    this.$refs.audioPlayback.src = audioUrl;
                    
                    const file = new File([audioBlob], "parent_recording.webm", { type: 'audio/webm' });
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
                Swal.fire('عفواً!', 'لم نتمكن من الوصول للميكروفون. يرجى التأكد من السماح للمتصفح باستخدام الميكروفون.', 'error');
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
                document.getElementById('parentReplyForm').submit();
            }
        }
    }
}
</script>
@endsection
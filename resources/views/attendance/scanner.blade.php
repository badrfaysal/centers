@extends('layouts.app')

@section('title', 'نظام مسح الـ QR وتسجيل الحضور')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- الترويسة -->
    <div class="flex items-center gap-3 bg-white p-5 rounded-3xl border border-slate-100 shadow-sm">
        <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-white text-xl shadow-md" style="background-color: #0d9488;">
            <i class="fa-solid fa-qrcode"></i>
        </div>
        <div>
            <h2 class="text-2xl font-black text-slate-800">تسجيل الحضور بالـ QR Code</h2>
            <p class="text-xs text-slate-500 font-semibold mt-1">وجه كاميرا الجهاز نحو الـ QR Code الخاص بالطفل لتسجيل حضوره تلقائياً</p>
        </div>
    </div>

    <!-- رسائل النجاح والخطأ من النظام -->
    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'تم تسجيل الحضور!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#0d9488',
                confirmButtonText: 'حسناً',
                timer: 4000
            });
        });
    </script>
    @endif

    @if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'عفواً!',
                text: '{{ session('error') }}',
                confirmButtonColor: '#f43f5e',
                confirmButtonText: 'حسناً'
            });
        });
    </script>
    @endif

    <!-- كاميرا المسح -->
    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm text-center">
        <div id="reader" class="mx-auto overflow-hidden rounded-2xl border-4 border-dashed border-teal-200" style="width: 100%; max-width: 500px;"></div>
        <p class="text-xs text-slate-500 font-bold mt-4" id="scan-status">جاري تشغيل الكاميرا...</p>
    </div>

    <!-- إدخال يدوي للطوارئ -->
    <div class="bg-slate-50 rounded-3xl p-6 border border-slate-200 text-center">
        <p class="text-xs font-bold text-slate-600 mb-3">أو إدخال كود الطفل يدوياً (في حال تعذر قراءة الـ QR):</p>
        <div class="flex justify-center max-w-sm mx-auto">
            <input type="text" id="manual-code" placeholder="مثال: CH-1001" class="flex-1 p-3 text-center rounded-r-2xl border border-slate-300 focus:border-teal-500 outline-none text-sm font-bold uppercase">
            <button onclick="submitManualCode()" class="px-6 rounded-l-2xl text-white font-bold text-sm bg-teal-600 hover:bg-teal-700 transition">
                تسجيل
            </button>
        </div>
    </div>

</div>

<!-- مكتبة مسح QR -->
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let isProcessing = false;

        function onScanSuccess(decodedText, decodedResult) {
            if (isProcessing) return;
            
            // Expected text: URL ending in /scan/CH-1001 or just CH-1001
            let code = decodedText;
            if (code.includes('/scan/')) {
                code = code.split('/scan/')[1];
            }

            if (code && code.trim() !== '') {
                isProcessing = true;
                document.getElementById('scan-status').innerHTML = '<span class="text-teal-600">تم التقاط الكود! جاري التسجيل...</span>';
                
                // Play a beep sound
                let audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                let oscillator = audioCtx.createOscillator();
                let gainNode = audioCtx.createGain();
                oscillator.connect(gainNode);
                gainNode.connect(audioCtx.destination);
                oscillator.type = 'sine';
                oscillator.frequency.setValueAtTime(800, audioCtx.currentTime);
                gainNode.gain.setValueAtTime(0.1, audioCtx.currentTime);
                oscillator.start();
                oscillator.stop(audioCtx.currentTime + 0.1);

                // Redirect to scan route
                window.location.href = '/attendance/scan/' + encodeURIComponent(code.trim());
            }
        }

        function onScanFailure(error) {
            // handle scan failure, usually better to ignore and keep scanning
        }

        let html5QrcodeScanner = new Html5QrcodeScanner(
            "reader",
            { fps: 10, qrbox: {width: 250, height: 250} },
            /* verbose= */ false);
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);
        document.getElementById('scan-status').innerHTML = 'الكاميرا جاهزة للمسح.';
    });

    function submitManualCode() {
        let code = document.getElementById('manual-code').value;
        if (code && code.trim() !== '') {
            window.location.href = '/attendance/scan/' + encodeURIComponent(code.trim());
        } else {
            Swal.fire('تنبيه', 'يرجى إدخال كود الطفل أولاً', 'warning');
        }
    }
</script>
@endsection
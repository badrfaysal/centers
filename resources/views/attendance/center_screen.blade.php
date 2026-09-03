@extends('layouts.app')

@section('title', 'شاشة الاستقبال - تسجيل الحضور')

@section('content')
<div class="min-h-screen flex items-center justify-center -mt-10">
    <div class="bg-white rounded-3xl p-10 max-w-2xl w-full text-center border-4 border-teal-500 shadow-2xl relative overflow-hidden">
        
        <!-- طابع بصري -->
        <div class="absolute -top-24 -right-24 w-48 h-48 bg-teal-500 rounded-full opacity-10"></div>
        <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-amber-500 rounded-full opacity-10"></div>

        <div class="mb-8">
            <h1 class="text-4xl font-black text-slate-800 mb-2">أهلاً بك في المركز!</h1>
            <p class="text-lg font-bold text-slate-500">لتسجيل حضور طفلك لجلسة اليوم، يرجى مسح الكود أدناه باستخدام كاميرا هاتفك</p>
        </div>

        <div class="inline-block p-4 bg-white rounded-3xl shadow-lg border-2 border-slate-100 relative mb-8">
            @php
                // استخدام الـ IP الداخلي للشبكة بدلاً من localhost لضمان عمل الكود على الموبايل
                $lanIp = gethostbyname(gethostname());
                $port = request()->getPort();
                $checkInUrl = "http://{$lanIp}:{$port}/attendance/check-in";
            @endphp
            <!-- الرابط الذي سيقرأه الهاتف هو رابط تأكيد الحضور المباشر عبر الشبكة المحلية -->
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=350x350&data={{ urlencode($checkInUrl) }}&margin=0" alt="QR Code" class="w-64 h-64 mx-auto">
            
            <!-- ايقونة في المنتصف للزينة -->
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white w-12 h-12 rounded-xl flex items-center justify-center shadow-md">
                <i class="fa-solid fa-camera text-2xl text-teal-600"></i>
            </div>
        </div>

        <div class="space-y-2">
            <div class="flex items-center justify-center gap-3 text-teal-700 font-black text-xl">
                <i class="fa-solid fa-mobile-screen-button"></i>
                <span>افتح الكاميرا</span>
                <i class="fa-solid fa-arrow-left"></i>
                <span>امسح الكود</span>
                <i class="fa-solid fa-arrow-left"></i>
                <span>سجل الحضور فوراً!</span>
            </div>
        </div>

    </div>
</div>
@endsection
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>استعادة كلمة المرور</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        body { font-family: 'Cairo', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen p-4">

    <div class="bg-white rounded-[2rem] shadow-xl border border-slate-100 w-full max-w-md overflow-hidden flex flex-col">
        
        <!-- Header -->
        <div class="bg-rose-600 p-8 text-center text-white relative overflow-hidden">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-white opacity-10 rounded-full blur-2xl"></div>
            <div class="absolute -left-10 -bottom-10 w-40 h-40 bg-white opacity-10 rounded-full blur-2xl"></div>
            
            <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-4 backdrop-blur-sm border border-white/30 shadow-inner relative z-10">
                <i class="fa-solid fa-unlock-keyhole"></i>
            </div>
            <h1 class="text-2xl font-black relative z-10">نسيت كلمة المرور</h1>
            <p class="text-rose-100 text-sm mt-1 opacity-90 relative z-10">إعادة تعيين كلمة المرور برقم الجوال</p>
        </div>

        <!-- Form -->
        <div class="p-8">
            @if ($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-700 p-4 rounded-2xl text-sm font-bold mb-6 flex gap-3 items-start">
                    <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
                    <div>
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            <form action="{{ route('forgot.password.post') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">رقم الجوال المسجل بالمركز</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-phone"></i>
                        </div>
                        <input type="text" name="phone" value="{{ old('phone') }}" required class="w-full pr-11 pl-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 text-slate-800 font-bold transition-all outline-none" placeholder="05XXXXXXXX" dir="ltr">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">اسم المستخدم</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <input type="text" name="username" value="{{ old('username') }}" required class="w-full pr-11 pl-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 text-slate-800 font-bold transition-all outline-none" placeholder="اسم الدخول" dir="ltr">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">كلمة المرور الجديدة</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-lock"></i>
                        </div>
                        <input type="password" name="password" required class="w-full pr-11 pl-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 text-slate-800 font-bold transition-all outline-none" placeholder="••••••••" dir="ltr">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">تأكيد كلمة المرور</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-lock"></i>
                        </div>
                        <input type="password" name="password_confirmation" required class="w-full pr-11 pl-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 text-slate-800 font-bold transition-all outline-none" placeholder="••••••••" dir="ltr">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3.5 bg-rose-600 rounded-2xl text-white font-black text-sm shadow-lg shadow-rose-500/30 hover:bg-rose-700 transition-all flex items-center justify-center gap-2">
                        <span>إعادة تعيين</span>
                        <i class="fa-solid fa-rotate-right"></i>
                    </button>
                </div>
                
                <div class="text-center mt-4">
                    <a href="{{ route('login') }}" class="text-xs font-bold text-rose-600 hover:text-rose-800 transition">العودة لتسجيل الدخول</a>
                </div>
            </form>
        </div>
        
    </div>

</body>
</html>

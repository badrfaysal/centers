<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - نظام إدارة المركز</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Cairo', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen p-4">

    <div class="bg-white rounded-[2rem] shadow-xl border border-slate-100 w-full max-w-md overflow-hidden flex flex-col">
        
        <!-- Header -->
        <div class="bg-brand-primary p-8 text-center text-white relative overflow-hidden" style="background-color: #0d9488;">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-white opacity-10 rounded-full blur-2xl"></div>
            <div class="absolute -left-10 -bottom-10 w-40 h-40 bg-white opacity-10 rounded-full blur-2xl"></div>
            
            <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-4 backdrop-blur-sm border border-white/30 shadow-inner relative z-10">
                <i class="fa-solid fa-hospital-user"></i>
            </div>
            <h1 class="text-2xl font-black relative z-10">تسجيل الدخول</h1>
            <p class="text-teal-50 text-sm mt-1 opacity-90 relative z-10">منظومة الإدارة والمتابعة</p>
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

            <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">اسم المستخدم / الرقم القومي</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-id-card"></i>
                        </div>
                        <input type="text" name="username" value="{{ old('username') }}" required class="w-full pr-11 pl-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 text-slate-800 font-bold transition-all outline-none" placeholder="أدخل الرقم القومي الخاص بك" dir="ltr">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">كلمة المرور</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-lock"></i>
                        </div>
                        <input type="password" name="password" required class="w-full pr-11 pl-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 text-slate-800 font-bold transition-all outline-none" placeholder="••••••••" dir="ltr">
                    </div>
                    <p class="text-[11px] text-slate-500 mt-2 font-medium"><i class="fa-solid fa-circle-info mr-1 text-slate-400"></i> الكلمة الافتراضية هي نفس الرقم القومي</p>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3.5 rounded-2xl text-white font-black text-sm shadow-lg shadow-teal-500/30 hover:opacity-90 transition-all flex items-center justify-center gap-2" style="background-color: #0d9488;">
                        <span>تسجيل الدخول</span>
                        <i class="fa-solid fa-arrow-left-long"></i>
                    </button>
                </div>
            </form>
        </div>
        
    </div>

</body>
</html>



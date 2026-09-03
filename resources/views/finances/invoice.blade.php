<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة رقم {{ $invoice['number'] ?? '' }}</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f3f4f6;
        }
        @media print {
            .no-print { display: none !important; }
            body { 
                background-color: white;
                -webkit-print-color-adjust: exact; 
                print-color-adjust: exact; 
            }
            @page {
                margin: 0;
            }
        }
    </style>
</head>
<body class="text-slate-800">

    <!-- Top Action Bar -->
    <div class="no-print bg-white border-b shadow-sm mb-8 py-4 px-6 flex justify-between items-center max-w-4xl mx-auto rounded-b-3xl">
        <a href="{{ route('finances.index') }}" class="text-slate-500 hover:text-slate-700 font-semibold flex items-center gap-2">
            <i class="fas fa-arrow-right"></i>
            رجوع للمالية
        </a>
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-xl shadow font-semibold flex items-center gap-2 transition">
            <i class="fas fa-print"></i>
            طباعة الفاتورة
        </button>
    </div>

    <!-- Invoice Body -->
    <div class="max-w-3xl mx-auto bg-white p-10 rounded-3xl shadow-lg print:shadow-none print:rounded-none">
        
        <!-- Header -->
        <div class="flex justify-between items-center border-b pb-6 mb-8">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 text-2xl">
                    <i class="fas fa-hospital-user"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">{{ $settings['center_name'] ?? 'مركز رعاية الأطفال' }}</h1>
                    <p class="text-slate-500 text-sm mt-1">
                        {{ $settings['address'] ?? 'العنوان' }} | {{ $settings['phone'] ?? 'رقم الهاتف' }}
                    </p>
                    <p class="text-slate-500 text-sm">
                        {{ $settings['email'] ?? 'البريد الإلكتروني' }}
                    </p>
                </div>
            </div>
            <div class="text-left">
                <h2 class="text-3xl font-bold text-slate-300 uppercase tracking-wider mb-2">فاتورة مالية</h2>
                <div class="text-sm font-semibold text-slate-600 bg-slate-50 px-3 py-1 rounded-lg inline-block">
                    رقم: {{ $invoice['number'] ?? 'INV-000000' }}
                </div>
                <div class="text-sm text-slate-500 mt-2">
                    التاريخ: {{ $invoice['date'] ?? date('Y-m-d') }}
                </div>
            </div>
        </div>

        <!-- Client Info -->
        <div class="grid grid-cols-2 gap-6 mb-8 bg-slate-50 p-6 rounded-2xl border border-slate-100">
            <div>
                <p class="text-sm text-slate-500 mb-1">بيانات العميل</p>
                <div class="font-bold text-lg text-slate-800">{{ $invoice['child_name'] ?? 'اسم الطفل' }}</div>
                <div class="text-slate-600 text-sm mt-1">ولي الأمر: {{ $invoice['parent_name'] ?? 'اسم ولي الأمر' }}</div>
            </div>
            <div class="text-left border-r border-slate-200 pr-6">
                <p class="text-sm text-slate-500 mb-1">الأخصائي المعالج</p>
                <div class="font-bold text-slate-800">{{ $invoice['specialist_name'] ?? 'اسم الأخصائي' }}</div>
                
                <div class="mt-4 flex justify-end gap-2">
                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold border border-green-200">
                        {{ $invoice['status'] ?? 'مدفوع' }}
                    </span>
                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold border border-blue-200">
                        {{ $invoice['payment_method'] ?? 'نقدي' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="mb-8 border rounded-2xl overflow-hidden">
            <table class="w-full text-right">
                <thead class="bg-slate-50 border-b">
                    <tr>
                        <th class="py-3 px-4 font-semibold text-slate-600">البيان</th>
                        <th class="py-3 px-4 font-semibold text-slate-600 text-center">سعر الجلسة</th>
                        <th class="py-3 px-4 font-semibold text-slate-600 text-center">العدد</th>
                        <th class="py-3 px-4 font-semibold text-slate-600 text-left">الإجمالي</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr>
                        <td class="py-4 px-4">
                            <div class="font-bold text-slate-800">{{ $invoice['description'] ?? 'جلسات تأهيل' }}</div>
                        </td>
                        <td class="py-4 px-4 text-center text-slate-600">
                            {{ number_format($invoice['session_price'] ?? 0, 2) }} {{ $settings['currency'] ?? 'ر.س' }}
                        </td>
                        <td class="py-4 px-4 text-center text-slate-600">
                            {{ $invoice['sessions_count'] ?? 1 }}
                        </td>
                        <td class="py-4 px-4 text-left font-bold text-slate-800">
                            {{ number_format($invoice['total_amount'] ?? 0, 2) }} {{ $settings['currency'] ?? 'ر.س' }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Totals & Notes -->
        <div class="flex justify-between items-start mb-12">
            <!-- Notes -->
            <div class="w-1/2 pr-4">
                @if(!empty($invoice['notes']))
                <div class="bg-amber-50 border border-amber-100 p-4 rounded-2xl">
                    <h4 class="text-sm font-bold text-amber-800 mb-2">ملاحظات:</h4>
                    <p class="text-sm text-amber-700">{{ $invoice['notes'] }}</p>
                </div>
                @endif
            </div>

            <!-- Totals -->
            <div class="w-1/2 pl-4 print:w-[60%]">
                <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100">
                    <div class="flex justify-between items-center mb-3 text-slate-600">
                        <span>الإجمالي:</span>
                        <span>{{ number_format($invoice['total_amount'] ?? 0, 2) }} {{ $settings['currency'] ?? 'ر.س' }}</span>
                    </div>
                    
                    @if(isset($invoice['discount_amount']) && $invoice['discount_amount'] > 0)
                    <div class="flex justify-between items-center mb-3 text-red-500">
                        <span>الخصم ({{ $invoice['discount_percentage'] ?? 0 }}%):</span>
                        <span>-{{ number_format($invoice['discount_amount'] ?? 0, 2) }} {{ $settings['currency'] ?? 'ر.س' }}</span>
                    </div>
                    @endif

                    <div class="flex justify-between items-center my-4 py-4 border-y border-slate-200">
                        <span class="font-bold text-lg text-slate-800">الصافي المطلوب:</span>
                        <span class="font-bold text-xl text-blue-600">{{ number_format($invoice['net_amount'] ?? 0, 2) }} {{ $settings['currency'] ?? 'ر.س' }}</span>
                    </div>

                    <div class="flex justify-between items-center mb-2 text-green-600">
                        <span>المبلغ المدفوع:</span>
                        <span>{{ number_format($invoice['paid_amount'] ?? 0, 2) }} {{ $settings['currency'] ?? 'ر.س' }}</span>
                    </div>

                    @php
                        $remaining = $invoice['remaining_amount'] ?? 0;
                    @endphp
                    <div class="flex justify-between items-center {{ $remaining > 0 ? 'text-red-500 font-bold' : 'text-slate-500' }}">
                        <span>المتبقي:</span>
                        <span>{{ number_format($remaining, 2) }} {{ $settings['currency'] ?? 'ر.س' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="border-t pt-8 flex justify-between items-end mt-auto">
            <div class="text-slate-500 text-sm">
                <p>شكراً لثقتكم بـ <span class="font-bold text-slate-700">{{ $settings['center_name'] ?? 'مركز رعاية الأطفال' }}</span></p>
                <p class="mt-1">لأي استفسار يرجى التواصل معنا</p>
            </div>
            
            <div class="text-center w-48">
                <div class="border-b-2 border-slate-300 border-dashed pb-8 mb-2"></div>
                <p class="text-sm font-semibold text-slate-600">التوقيع / الختم</p>
            </div>
        </div>
        
    </div>

</body>
</html>

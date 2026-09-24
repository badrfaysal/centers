<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة - {{ $invoice->invoice_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Tajawal', sans-serif;
            background: #fff;
            color: #1e293b; /* slate-800 */
        }
        @media print {
            body {
                background: white;
            }
            .no-print {
                display: none !important;
            }
            @page {
                size: A4;
                margin: 15mm;
            }
        }
    </style>
</head>
<body class="p-8 max-w-4xl mx-auto" onload="window.print()">

    <!-- Print Controls -->
    <div class="mb-8 flex justify-between items-center no-print">
        <button onclick="window.print()" class="px-6 py-2.5 bg-teal-600 text-white rounded-xl font-bold hover:bg-teal-700 transition shadow-lg">طباعة الفاتورة</button>
        <button onclick="window.close()" class="px-6 py-2.5 bg-slate-200 text-slate-700 rounded-xl font-bold hover:bg-slate-300 transition">إغلاق</button>
    </div>

    <!-- Header -->
    <div class="flex items-start justify-between border-b-2 border-teal-600 pb-6 mb-8">
        <div>
            <h1 class="text-4xl font-black text-teal-800 mb-2">{{ $settings['center_name'] ?? 'المركز الحديث للتخاطب' }}</h1>
            <p class="text-slate-600 font-medium max-w-sm">{{ $settings['center_address'] ?? '' }}</p>
            <p class="text-slate-600 font-medium">هاتف: {{ $settings['center_phone'] ?? '' }}</p>
        </div>
        <div class="text-left">
            <h2 class="text-3xl font-black text-slate-800 mb-2 tracking-widest">فاتورة</h2>
            <p class="text-slate-500 font-bold">رقم الفاتورة: <span class="text-slate-800">{{ $invoice->invoice_number }}</span></p>
            <p class="text-slate-500 font-bold">التاريخ: <span class="text-slate-800">{{ $invoice->invoice_date }}</span></p>
            <p class="text-slate-500 font-bold">طريقة الدفع: 
                <span class="text-slate-800">
                    @if($invoice->payment_method === 'cash') نقدي 
                    @elseif($invoice->payment_method === 'visa') فيزا 
                    @else تحويل بنكي @endif
                </span>
            </p>
        </div>
    </div>

    <!-- Info -->
    <div class="grid grid-cols-2 gap-8 mb-8 bg-slate-50 p-6 rounded-2xl border border-slate-100">
        <div>
            <p class="text-sm text-slate-500 font-bold mb-1">فاتورة إلى (ولي الأمر):</p>
            <h3 class="text-xl font-black text-slate-800">{{ $invoice->parent_name }}</h3>
            <p class="text-slate-600 mt-2 font-medium">اسم الطفل: <span class="font-bold text-teal-700">{{ $invoice->child_name }}</span></p>
        </div>
        <div>
            <p class="text-sm text-slate-500 font-bold mb-1">الخدمة المُقدمة بواسطة:</p>
            <h3 class="text-xl font-black text-slate-800">الأخصائي/ {{ $invoice->specialist_name }}</h3>
        </div>
    </div>

    <!-- Items -->
    <table class="w-full text-right mb-8">
        <thead>
            <tr class="bg-teal-600 text-white">
                <th class="py-3 px-4 font-bold rounded-r-xl">البيان / الخدمة</th>
                <th class="py-3 px-4 font-bold text-center">الكمية (الجلسات)</th>
                <th class="py-3 px-4 font-bold text-center">سعر الوحدة</th>
                <th class="py-3 px-4 font-bold text-left rounded-l-xl">الإجمالي</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            <tr>
                <td class="py-4 px-4 font-bold text-slate-800">جلسات تخاطب / تأهيل</td>
                <td class="py-4 px-4 font-bold text-center text-slate-700">{{ $invoice->sessions_count }}</td>
                <td class="py-4 px-4 font-bold text-center text-slate-700">{{ number_format($invoice->session_price, 2) }} ج.م</td>
                <td class="py-4 px-4 font-black text-left text-slate-900">{{ number_format($invoice->total_amount, 2) }} ج.م</td>
            </tr>
        </tbody>
    </table>

    <!-- Totals -->
    <div class="flex justify-end">
        <div class="w-1/2 space-y-3 border border-slate-200 rounded-2xl p-6 bg-white shadow-sm">
            <div class="flex justify-between text-slate-600 font-bold">
                <span>الإجمالي الفرعي:</span>
                <span>{{ number_format($invoice->total_amount, 2) }} ج.م</span>
            </div>
            
            @if($invoice->discount_amount > 0)
            <div class="flex justify-between text-rose-600 font-bold">
                <span>الخصم:</span>
                <span>- {{ number_format($invoice->discount_amount, 2) }} ج.م</span>
            </div>
            @endif

            <div class="flex justify-between text-xl font-black text-slate-800 pt-3 border-t border-slate-100">
                <span>الإجمالي المستحق:</span>
                <span>{{ number_format($invoice->net_amount, 2) }} ج.م</span>
            </div>

            <div class="flex justify-between text-teal-600 font-bold pt-3 border-t border-slate-100">
                <span>المبلغ المدفوع:</span>
                <span>{{ number_format($invoice->paid_amount, 2) }} ج.م</span>
            </div>

            @if($invoice->remaining_amount > 0)
            <div class="flex justify-between text-rose-600 font-bold bg-rose-50 p-2 rounded-lg mt-2">
                <span>الباقي (دين):</span>
                <span>{{ number_format($invoice->remaining_amount, 2) }} ج.م</span>
            </div>
            @endif
        </div>
    </div>

    <!-- Footer -->
    <div class="mt-16 pt-8 border-t-2 border-slate-100 text-center text-slate-500 text-sm font-medium">
        <p>شكراً لثقتكم بنا، نتمنى لطفلكم دوام التقدم والنجاح.</p>
        <p class="mt-1 font-mono">Invoice generated by Center Management System</p>
    </div>

</body>
</html>

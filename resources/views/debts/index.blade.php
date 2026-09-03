@extends('layouts.app')

@section('title', 'ديون أولياء الأمور')

@section('content')
<div x-data="{ 
    showPayModal: false, 
    payInvoiceId: null, 
    payInvoiceNumber: '', 
    payMaxAmount: 0, 
    payAmount: 0,
    openModal(id, number, maxAmount) {
        this.payInvoiceId = id;
        this.payInvoiceNumber = number;
        this.payMaxAmount = maxAmount;
        this.payAmount = maxAmount;
        this.showPayModal = true;
    }
}" class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-6 rounded-3xl shadow-sm border border-slate-200">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-rose-50 rounded-2xl flex items-center justify-center text-rose-500 shadow-inner">
                <i class="fa-solid fa-hand-holding-dollar text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">ديون أولياء الأمور</h1>
                <p class="text-slate-500 text-sm mt-1">إدارة الديون المعلقة ومتابعة السداد</p>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-emerald-50 border-r-4 border-emerald-500 p-4 rounded-2xl flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-emerald-500 text-xl"></i>
            <p class="text-emerald-700 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- إجمالي الديون -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200 flex items-center gap-5 relative overflow-hidden">
            <div class="absolute -left-6 -bottom-6 w-24 h-24 bg-rose-50 rounded-full opacity-50"></div>
            <div class="w-16 h-16 bg-gradient-to-br from-rose-400 to-rose-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-rose-200 relative z-10">
                <i class="fa-solid fa-money-bill-trend-up text-2xl"></i>
            </div>
            <div class="relative z-10">
                <p class="text-sm font-medium text-slate-500 mb-1">إجمالي الديون المعلقة</p>
                <h3 class="text-3xl font-bold text-slate-800">
                    {{ number_format($totalDebts ?? 0, 2) }} <span class="text-lg text-slate-500">{{ $settings['currency'] ?? 'د.ع' }}</span>
                </h3>
            </div>
        </div>

        <!-- عدد المدينين -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200 flex items-center gap-5 relative overflow-hidden">
            <div class="absolute -left-6 -bottom-6 w-24 h-24 bg-amber-50 rounded-full opacity-50"></div>
            <div class="w-16 h-16 bg-gradient-to-br from-amber-400 to-amber-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-amber-200 relative z-10">
                <i class="fa-solid fa-users text-2xl"></i>
            </div>
            <div class="relative z-10">
                <p class="text-sm font-medium text-slate-500 mb-1">عدد أولياء الأمور المدينين</p>
                <h3 class="text-3xl font-bold text-slate-800">{{ $parentsCount ?? 0 }}</h3>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="bg-white p-4 rounded-3xl shadow-sm border border-slate-200 flex items-center gap-4">
        <div class="relative flex-1">
            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                <i class="fa-solid fa-search text-slate-400"></i>
            </div>
            <input type="text" placeholder="ابحث باسم ولي الأمر أو الطفل..." class="w-full pr-12 pl-4 py-3 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-brand-primary/20 text-slate-700 placeholder-slate-400 transition-all">
        </div>
        <button class="bg-brand-primary text-white px-6 py-3 rounded-2xl font-medium hover:bg-brand-primary/90 transition-colors shadow-lg shadow-brand-primary/20 flex items-center gap-2">
            <i class="fa-solid fa-filter"></i>
            تصفية
        </button>
    </div>

    <!-- Main Content -->
    <div class="space-y-6">
        @forelse($debtsByParent ?? [] as $parentName => $invoices)
            @php
                $parentTotalDebt = $invoices->sum('remaining_amount');
            @endphp
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-brand-primary/10 rounded-xl flex items-center justify-center text-brand-primary">
                            <i class="fa-solid fa-user-tie"></i>
                        </div>
                        <h2 class="text-xl font-bold text-slate-800">{{ $parentName }}</h2>
                    </div>
                    <div class="bg-rose-50 px-4 py-2 rounded-xl border border-rose-100">
                        <span class="text-sm text-rose-600 font-medium">إجمالي الدين:</span>
                        <span class="text-lg font-bold text-rose-700 mx-1">{{ number_format($parentTotalDebt, 2) }}</span>
                        <span class="text-sm text-rose-600">{{ $settings['currency'] ?? 'د.ع' }}</span>
                    </div>
                </div>

                <div class="p-6 overflow-x-auto">
                    <table class="w-full text-right">
                        <thead>
                            <tr class="text-slate-500 border-b border-slate-200 text-sm">
                                <th class="pb-3 px-4 font-semibold">رقم الفاتورة</th>
                                <th class="pb-3 px-4 font-semibold">اسم الطفل</th>
                                <th class="pb-3 px-4 font-semibold">الأخصائي</th>
                                <th class="pb-3 px-4 font-semibold">الصافي المطلوب</th>
                                <th class="pb-3 px-4 font-semibold">المدفوع</th>
                                <th class="pb-3 px-4 font-semibold">المتبقي</th>
                                <th class="pb-3 px-4 font-semibold">التاريخ</th>
                                <th class="pb-3 px-4 font-semibold w-32">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @foreach($invoices as $invoice)
                                <tr class="border-b border-slate-100 hover:bg-slate-50 transition-colors">
                                    <td class="py-4 px-4">
                                        <a href="{{ route('finances.show', $invoice->id) }}" class="text-brand-primary font-bold hover:underline">
                                            #{{ $invoice->invoice_number }}
                                        </a>
                                    </td>
                                    <td class="py-4 px-4 text-slate-700">{{ $invoice->child_name }}</td>
                                    <td class="py-4 px-4 text-slate-700">{{ $invoice->specialist_name }}</td>
                                    <td class="py-4 px-4 font-medium text-slate-700">{{ number_format($invoice->net_amount, 2) }}</td>
                                    <td class="py-4 px-4 text-emerald-600 font-medium">{{ number_format($invoice->paid_amount, 2) }}</td>
                                    <td class="py-4 px-4 font-bold text-rose-600">{{ number_format($invoice->remaining_amount, 2) }}</td>
                                    <td class="py-4 px-4 text-slate-500">{{ $invoice->invoice_date->format('Y-m-d') }}</td>
                                    <td class="py-4 px-4">
                                        <button 
                                            @click="openModal({{ $invoice->id }}, '{{ $invoice->invoice_number }}', {{ $invoice->remaining_amount }})"
                                            class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded-xl text-sm font-medium transition-colors w-full flex items-center justify-center gap-2 shadow-sm shadow-emerald-200">
                                            <i class="fa-solid fa-money-bill-wave"></i>
                                            تسديد
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <!-- Empty State -->
            <div class="bg-white rounded-3xl p-12 text-center shadow-sm border border-slate-200 flex flex-col items-center justify-center h-64">
                <div class="w-24 h-24 bg-emerald-50 rounded-full flex items-center justify-center mb-6">
                    <i class="fa-solid fa-party-horn text-5xl text-emerald-400"></i>
                </div>
                <h3 class="text-2xl font-bold text-slate-800 mb-2">لا توجد ديون معلقة</h3>
                <p class="text-slate-500">جميع أولياء الأمور قاموا بتسديد التزاماتهم المالية، عمل رائع!</p>
            </div>
        @endforelse
    </div>

    <!-- Payment Modal -->
    <div x-show="showPayModal" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0"
         style="display: none;">
         
        <div x-show="showPayModal" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm"
             @click="showPayModal = false"></div>

        <div x-show="showPayModal" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="bg-white rounded-3xl shadow-xl border border-slate-200 w-full max-w-md relative z-10 overflow-hidden">
            
            <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center text-emerald-600">
                        <i class="fa-solid fa-money-bill-wave"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800" x-text="'تسديد دين على فاتورة #' + payInvoiceNumber"></h3>
                </div>
                <button @click="showPayModal = false" class="text-slate-400 hover:text-slate-600 transition-colors w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-100">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form :action="'{{ url('debts') }}/' + payInvoiceId + '/pay'" method="POST" class="p-6">
                @csrf
                <div class="space-y-5">
                    <div class="bg-rose-50 p-4 rounded-2xl border border-rose-100 flex justify-between items-center">
                        <span class="text-rose-700 font-medium">المبلغ المتبقي:</span>
                        <div class="text-rose-700 font-bold text-xl">
                            <span x-text="payMaxAmount"></span>
                            <span class="text-sm">{{ $settings['currency'] ?? 'د.ع' }}</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">المبلغ المراد سداده</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                <span class="text-slate-400">{{ $settings['currency'] ?? 'د.ع' }}</span>
                            </div>
                            <input type="number" 
                                   name="amount" 
                                   x-model="payAmount" 
                                   step="0.01"
                                   min="0.01"
                                   :max="payMaxAmount"
                                   required
                                   class="w-full pr-14 pl-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary text-slate-800 font-medium transition-all">
                        </div>
                        <p class="text-xs text-slate-500 mt-2">
                            يمكنك دفع المبلغ جزئياً أو كلياً.
                        </p>
                    </div>
                </div>

                <div class="mt-8 flex items-center gap-3">
                    <button type="submit" class="flex-1 bg-emerald-500 hover:bg-emerald-600 text-white py-3 rounded-2xl font-bold transition-colors shadow-lg shadow-emerald-200 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-check"></i>
                        تأكيد الدفع
                    </button>
                    <button type="button" @click="showPayModal = false" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 py-3 rounded-2xl font-bold transition-colors">
                        إلغاء
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

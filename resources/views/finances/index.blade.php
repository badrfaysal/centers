@extends('layouts.app')
@section('title', 'الماليات والفواتير')

@section('content')
<div class="space-y-6" dir="rtl">
    <!-- Header -->
    <div class="flex justify-between items-center bg-white p-6 rounded-3xl border border-slate-100 shadow-sm relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-l from-brand-primary/5 to-transparent"></div>
        <div class="flex items-center gap-4 relative z-10">
            <div class="w-14 h-14 bg-gradient-to-br from-brand-primary to-brand-secondary rounded-2xl flex items-center justify-center text-white shadow-lg shadow-brand-primary/30">
                <i class="fa-solid fa-file-invoice-dollar text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">الماليات والفواتير</h1>
                <p class="text-sm text-slate-500 mt-1">إدارة فواتير وإيرادات المركز</p>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="flex items-center gap-3">
        <a href="{{ route('finances.index') }}" class="px-6 py-2.5 rounded-xl font-bold transition-all bg-brand-primary text-white shadow-md shadow-brand-primary/20 flex items-center gap-2">
            <i class="fa-solid fa-file-invoice"></i> الفواتير
        </a>
        <a href="{{ route('debts.index') }}" class="px-6 py-2.5 rounded-xl font-bold transition-all bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-brand-primary flex items-center gap-2">
            <i class="fa-solid fa-hand-holding-dollar"></i> الديون
        </a>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-3xl flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-xl"></i>
            <p class="font-medium">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- إجمالي الإيرادات -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex items-center gap-4 transition-all duration-300 hover:shadow-md hover:border-teal-200 group">
            <div class="w-12 h-12 rounded-2xl bg-teal-50 flex items-center justify-center text-teal-600 group-hover:scale-110 transition-transform duration-300">
                <i class="fa-solid fa-coins text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-slate-500 mb-1">إجمالي الإيرادات</p>
                <p class="text-xl font-bold text-slate-800">{{ number_format($totalRevenue ?? 0, 2) }} {{ $settings['currency'] ?? 'د.ك' }}</p>
            </div>
        </div>

        <!-- المبلغ المحصّل -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex items-center gap-4 transition-all duration-300 hover:shadow-md hover:border-emerald-200 group">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 group-hover:scale-110 transition-transform duration-300">
                <i class="fa-solid fa-hand-holding-dollar text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-slate-500 mb-1">المبلغ المحصّل</p>
                <p class="text-xl font-bold text-slate-800">{{ number_format($totalPaid ?? 0, 2) }} {{ $settings['currency'] ?? 'د.ك' }}</p>
            </div>
        </div>

        <!-- إجمالي الديون -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex items-center gap-4 transition-all duration-300 hover:shadow-md hover:border-rose-200 group">
            <div class="w-12 h-12 rounded-2xl bg-rose-50 flex items-center justify-center text-rose-600 group-hover:scale-110 transition-transform duration-300">
                <i class="fa-solid fa-triangle-exclamation text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-slate-500 mb-1">إجمالي الديون</p>
                <p class="text-xl font-bold text-slate-800">{{ number_format($totalDebts ?? 0, 2) }} {{ $settings['currency'] ?? 'د.ك' }}</p>
            </div>
        </div>

        <!-- عدد الفواتير -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex items-center gap-4 transition-all duration-300 hover:shadow-md hover:border-blue-200 group">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600 group-hover:scale-110 transition-transform duration-300">
                <i class="fa-solid fa-receipt text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-slate-500 mb-1">عدد الفواتير</p>
                <p class="text-xl font-bold text-slate-800">{{ number_format($invoicesCount ?? 0) }}</p>
            </div>
        </div>
    </div>

    <!-- Filters & Actions -->
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
        <form action="{{ route('finances.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-center justify-between">
            <div class="flex flex-col md:flex-row gap-4 w-full md:w-auto">
                <div class="relative min-w-[250px]">
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                        <i class="fa-solid fa-magnifying-glass text-slate-400"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" 
                        class="w-full pr-11 pl-4 py-3 rounded-2xl border border-slate-200 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 transition-all text-sm outline-none bg-slate-50 focus:bg-white" 
                        placeholder="ابحث برقم الفاتورة، اسم الطفل، ولي الأمر...">
                </div>
                
                <div class="min-w-[200px]">
                    <select name="status" class="w-full px-4 py-3 rounded-2xl border border-slate-200 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 transition-all text-sm outline-none bg-slate-50 focus:bg-white appearance-none cursor-pointer">
                        <option value="">جميع الحالات</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>مدفوعة بالكامل</option>
                        <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>مدفوعة جزئياً</option>
                        <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>غير مدفوعة</option>
                    </select>
                </div>

                <button type="submit" class="bg-slate-100 text-slate-700 hover:bg-slate-200 px-6 py-3 rounded-2xl font-medium transition-colors text-sm flex items-center justify-center gap-2">
                    <i class="fa-solid fa-filter"></i>
                    تصفية
                </button>
                @if(request()->anyFilled(['search', 'status']))
                    <a href="{{ route('finances.index') }}" class="bg-rose-50 text-rose-600 hover:bg-rose-100 px-6 py-3 rounded-2xl font-medium transition-colors text-sm flex items-center justify-center gap-2">
                        <i class="fa-solid fa-xmark"></i>
                        إلغاء
                    </a>
                @endif
            </div>

            <a href="{{ route('finances.create') }}" class="w-full md:w-auto bg-brand-primary hover:bg-brand-secondary text-white px-6 py-3 rounded-2xl font-medium transition-all duration-300 hover:shadow-lg hover:-translate-y-0.5 flex items-center justify-center gap-2">
                <i class="fa-solid fa-plus"></i>
                فاتورة جديدة
            </a>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="px-6 py-4 font-semibold text-slate-700 whitespace-nowrap">رقم الفاتورة</th>
                        <th class="px-6 py-4 font-semibold text-slate-700 whitespace-nowrap">اسم الطفل</th>
                        <th class="px-6 py-4 font-semibold text-slate-700 whitespace-nowrap">اسم الأخصائي</th>
                        <th class="px-6 py-4 font-semibold text-slate-700 whitespace-nowrap">ولي الأمر</th>
                        <th class="px-6 py-4 font-semibold text-slate-700 whitespace-nowrap">الإجمالي</th>
                        <th class="px-6 py-4 font-semibold text-slate-700 whitespace-nowrap">الخصم</th>
                        <th class="px-6 py-4 font-semibold text-slate-700 whitespace-nowrap">الصافي</th>
                        <th class="px-6 py-4 font-semibold text-slate-700 whitespace-nowrap">المدفوع</th>
                        <th class="px-6 py-4 font-semibold text-slate-700 whitespace-nowrap">الباقي</th>
                        <th class="px-6 py-4 font-semibold text-slate-700 whitespace-nowrap text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($invoices as $invoice)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-6 py-4">
                            <a href="{{ route('finances.show', $invoice->id) }}" class="font-black text-brand-primary hover:underline flex items-center gap-2">
                                <i class="fa-solid fa-file-invoice text-slate-400"></i>
                                #{{ $invoice->invoice_number ?? $invoice->id }}
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-brand-primary/10 text-brand-primary flex items-center justify-center font-bold text-xs">
                                    {{ mb_substr($invoice->child->name ?? 'ط', 0, 1) }}
                                </div>
                                <span class="font-medium text-slate-700">{{ $invoice->child->name ?? '---' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-slate-600">{{ $invoice->specialist->name ?? '---' }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $invoice->child->parent_name ?? '---' }}</td>
                        <td class="px-6 py-4 text-slate-600 font-medium">{{ number_format($invoice->total_amount, 2) }}</td>
                        <td class="px-6 py-4 text-slate-600 font-medium">{{ number_format($invoice->discount_amount, 2) }}</td>
                        <td class="px-6 py-4 font-bold text-slate-800">{{ number_format($invoice->net_amount, 2) }}</td>
                        <td class="px-6 py-4 text-emerald-600 font-bold">{{ number_format($invoice->paid_amount, 2) }}</td>
                        <td class="px-6 py-4 text-rose-600 font-bold">{{ number_format($invoice->remaining_amount, 2) }}</td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('finances.show', $invoice->id) }}" class="w-8 h-8 rounded-xl bg-slate-100 text-slate-600 hover:bg-brand-primary/10 hover:text-brand-primary flex items-center justify-center transition-colors tooltip" title="عرض التفاصيل والطباعة">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                @if($invoice->payment_status !== 'paid')
                                <a href="{{ route('debts.index', ['search' => $invoice->parent_name]) }}" class="w-8 h-8 rounded-xl bg-slate-100 text-slate-600 hover:bg-amber-100 hover:text-amber-600 flex items-center justify-center transition-colors tooltip" title="إضافة دفعة / تسديد">
                                    <i class="fa-solid fa-money-bill-wave"></i>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-slate-500">
                                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                    <i class="fa-solid fa-file-invoice-dollar text-4xl text-slate-300"></i>
                                </div>
                                <h3 class="text-lg font-bold text-slate-700 mb-1">لا توجد فواتير</h3>
                                <p class="text-sm">لم يتم العثور على أي فواتير مطابقة لبحثك أو لم يتم إضافة فواتير بعد.</p>
                                <a href="{{ route('finances.create') }}" class="mt-4 text-brand-primary font-medium hover:underline flex items-center gap-2">
                                    <i class="fa-solid fa-plus"></i>
                                    إنشاء أول فاتورة
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(isset($invoices) && $invoices->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
            {{ $invoices->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

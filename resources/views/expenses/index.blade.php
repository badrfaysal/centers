@extends('layouts.app')

@section('title', 'المصروفات')

@section('content')
<div x-data="{ showAddModal: false }" class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-6 rounded-3xl shadow-sm border border-slate-200">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-rose-50 rounded-2xl flex items-center justify-center text-rose-500 shadow-inner">
                <i class="fa-solid fa-money-bill-transfer text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">إدارة المصروفات</h1>
                <p class="text-slate-500 text-sm mt-1">تسجيل ومتابعة مصروفات المركز</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <button @click="showAddModal = true" class="bg-brand-primary text-white px-6 py-3 rounded-2xl font-bold hover:bg-brand-primary/90 transition-colors shadow-lg shadow-brand-primary/30 flex items-center gap-2">
                <i class="fa-solid fa-plus"></i>
                إضافة مصروف جديد
            </button>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-emerald-50 border-r-4 border-emerald-500 p-4 rounded-2xl flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-emerald-500 text-xl"></i>
            <p class="text-emerald-700 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Error Messages -->
    @if($errors->any())
        <div class="bg-rose-50 border-r-4 border-rose-500 p-4 rounded-2xl">
            <div class="flex items-center gap-3 mb-2">
                <i class="fa-solid fa-triangle-exclamation text-rose-500 text-xl"></i>
                <p class="text-rose-700 font-bold">يرجى تصحيح الأخطاء التالية:</p>
            </div>
            <ul class="list-disc list-inside text-rose-600 text-sm space-y-1 pr-4">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- مصروفات الشهر -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200 flex items-center gap-5 relative overflow-hidden">
            <div class="absolute -left-6 -bottom-6 w-24 h-24 bg-rose-50 rounded-full opacity-50"></div>
            <div class="w-16 h-16 bg-gradient-to-br from-rose-400 to-rose-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-rose-200 relative z-10">
                <i class="fa-solid fa-calendar-day text-2xl"></i>
            </div>
            <div class="relative z-10">
                <p class="text-sm font-medium text-slate-500 mb-1">مصروفات الشهر الحالي</p>
                <h3 class="text-3xl font-bold text-slate-800">
                    {{ number_format($thisMonthExpenses, 2) }} <span class="text-lg text-slate-500">{{ $settings['currency'] }}</span>
                </h3>
            </div>
        </div>

        <!-- إجمالي المصروفات -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200 flex items-center gap-5 relative overflow-hidden">
            <div class="absolute -left-6 -bottom-6 w-24 h-24 bg-slate-50 rounded-full opacity-50"></div>
            <div class="w-16 h-16 bg-gradient-to-br from-slate-600 to-slate-800 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-slate-200 relative z-10">
                <i class="fa-solid fa-vault text-2xl"></i>
            </div>
            <div class="relative z-10">
                <p class="text-sm font-medium text-slate-500 mb-1">إجمالي المصروفات</p>
                <h3 class="text-3xl font-bold text-slate-800">
                    {{ number_format($totalExpenses, 2) }} <span class="text-lg text-slate-500">{{ $settings['currency'] }}</span>
                </h3>
            </div>
        </div>
    </div>

    <!-- Chart Row -->
    @if($expensesChartData->count() > 0)
    <div class="bg-white p-5 rounded-3xl shadow-sm border border-slate-200">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center text-blue-500 text-sm">
                <i class="fa-solid fa-chart-pie"></i>
            </div>
            <h2 class="text-lg font-bold text-slate-800">توزيع المصروفات (أعلى الفئات)</h2>
        </div>
        <div class="relative h-48 w-full max-w-4xl mx-auto">
            <canvas id="expensesChart"></canvas>
        </div>
    </div>
    @endif

    <!-- Filter & Table -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        
        <!-- Filter Bar -->
        <div class="p-4 border-b border-slate-100 bg-slate-50/50">
            <form action="{{ route('expenses.index') }}" method="GET" class="flex items-center gap-4">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                        <i class="fa-solid fa-search text-slate-400"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث في بيان المصروف..." class="w-full pr-12 pl-4 py-3 bg-white border border-slate-200 rounded-2xl focus:ring-2 focus:ring-brand-primary/20 text-slate-700 placeholder-slate-400 transition-all shadow-sm">
                </div>
                <button type="submit" class="bg-slate-800 text-white px-6 py-3 rounded-2xl font-medium hover:bg-slate-700 transition-colors shadow-lg shadow-slate-200 flex items-center gap-2">
                    <i class="fa-solid fa-filter"></i>
                    تصفية
                </button>
                @if(request('search'))
                    <a href="{{ route('expenses.index') }}" class="bg-slate-200 text-slate-600 px-4 py-3 rounded-2xl font-medium hover:bg-slate-300 transition-colors">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="px-6 py-4 font-semibold text-slate-700 whitespace-nowrap">#</th>
                        <th class="px-6 py-4 font-semibold text-slate-700 whitespace-nowrap">التاريخ</th>
                        <th class="px-6 py-4 font-semibold text-slate-700 whitespace-nowrap">بيان المصروف</th>
                        <th class="px-6 py-4 font-semibold text-slate-700 whitespace-nowrap">المبلغ</th>
                        <th class="px-6 py-4 font-semibold text-slate-700">ملاحظات</th>
                        <th class="px-6 py-4 font-semibold text-slate-700 whitespace-nowrap text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($expenses as $index => $expense)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 text-slate-400 font-bold">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 text-slate-600 font-medium whitespace-nowrap">{{ $expense->expense_date->format('Y-m-d') }}</td>
                        <td class="px-6 py-4 font-bold text-slate-800">{{ $expense->statement }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-700 border border-rose-200 whitespace-nowrap">
                                {{ number_format($expense->amount, 2) }} {{ $settings['currency'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-slate-500 text-xs">{{ $expense->notes ?? '---' }}</td>
                        <td class="px-6 py-4 text-center">
                            <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" class="inline-block" onsubmit="return confirm('هل أنت متأكد من حذف هذا المصروف؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-8 h-8 rounded-xl bg-slate-100 text-slate-500 hover:bg-rose-100 hover:text-rose-600 flex items-center justify-center transition-colors tooltip" title="حذف المصروف">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="inline-flex flex-col items-center justify-center text-slate-400">
                                <i class="fa-solid fa-money-bill-transfer text-4xl mb-3 opacity-20"></i>
                                <p class="text-sm font-medium">لا توجد مصروفات مسجلة</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($expenses->hasPages())
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            {{ $expenses->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

    <!-- Add Expense Modal -->
    <div x-show="showAddModal" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0"
         style="display: none;">
         
        <div x-show="showAddModal" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm"
             @click="showAddModal = false"></div>

        <div x-show="showAddModal" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="bg-white rounded-3xl shadow-xl border border-slate-200 w-full max-w-lg relative z-10 overflow-hidden">
            
            <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-brand-primary/10 rounded-xl flex items-center justify-center text-brand-primary">
                        <i class="fa-solid fa-plus"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800">إضافة مصروف جديد</h3>
                </div>
                <button @click="showAddModal = false" class="text-slate-400 hover:text-slate-600 transition-colors w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-100">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form action="{{ route('expenses.store') }}" method="POST" class="p-6">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">بيان المصروف (التصنيف) <span class="text-rose-500">*</span></label>
                        <input type="text" name="statement" list="expense-categories" value="{{ old('statement') }}" required placeholder="اختر تصنيفاً أو اكتب تصنيفاً جديداً..." class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-brand-primary/20 text-slate-700 transition-all">
                        <datalist id="expense-categories">
                            @foreach(\App\Http\Controllers\SettingController::getDropdownList('expense_categories') as $cat)
                                <option value="{{ $cat }}"></option>
                            @endforeach
                        </datalist>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1.5">المبلغ ({{ $settings['currency'] }}) <span class="text-rose-500">*</span></label>
                            <input type="number" name="amount" value="{{ old('amount') }}" required min="0.01" step="0.01" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-brand-primary/20 text-slate-700 transition-all font-bold">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1.5">التاريخ <span class="text-rose-500">*</span></label>
                            <input type="date" name="expense_date" value="{{ old('expense_date', date('Y-m-d')) }}" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-brand-primary/20 text-slate-700 transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">ملاحظات إضافية (اختياري)</label>
                        <textarea name="notes" rows="3" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-brand-primary/20 text-slate-700 transition-all resize-none">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="mt-8 flex items-center justify-end gap-3">
                    <button type="button" @click="showAddModal = false" class="px-6 py-3 rounded-2xl font-bold text-slate-600 hover:bg-slate-100 transition-colors">
                        إلغاء
                    </button>
                    <button type="submit" class="bg-brand-primary hover:bg-brand-primary/90 text-white px-8 py-3 rounded-2xl font-bold transition-colors shadow-lg shadow-brand-primary/20 flex items-center gap-2">
                        <i class="fa-solid fa-check"></i>
                        حفظ المصروف
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('expensesChart');
        if (ctx) {
            const chartData = @json($expensesChartData);
            
            const labels = chartData.map(item => item.statement);
            const data = chartData.map(item => item.total);
            
            // Generate some nice brand colors
            const bgColors = [
                'rgba(14, 165, 233, 0.7)', // sky
                'rgba(16, 185, 129, 0.7)', // emerald
                'rgba(244, 63, 94, 0.7)',  // rose
                'rgba(245, 158, 11, 0.7)', // amber
                'rgba(139, 92, 246, 0.7)', // violet
                'rgba(236, 72, 153, 0.7)', // pink
                'rgba(99, 102, 241, 0.7)'  // indigo
            ];
            const borderColors = bgColors.map(color => color.replace('0.7', '1'));

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'المبلغ ({{ $settings["currency"] }})',
                        data: data,
                        backgroundColor: bgColors,
                        borderColor: borderColors,
                        borderWidth: 2,
                        borderRadius: 8,
                        barPercentage: 0.6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            titleFont: { family: "'Cairo', sans-serif" },
                            bodyFont: { family: "'Cairo', sans-serif" }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.05)' },
                            ticks: { font: { family: "'Cairo', sans-serif" } }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { family: "'Cairo', sans-serif", weight: 'bold' } }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush

@endsection

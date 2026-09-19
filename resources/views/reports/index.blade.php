@extends('layouts.app')

@section('title', 'التقارير والإحصائيات')

@section('content')
<div class="space-y-6">

    <!-- Header & Date Filter -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-500 shadow-inner">
                <i class="fa-solid fa-chart-line text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">التقارير والإحصائيات</h1>
                <p class="text-slate-500 text-sm mt-1">نظرة شاملة على أداء المركز المالي والإداري</p>
            </div>
        </div>
        
        <form action="{{ route('reports.index') }}" method="GET" class="flex flex-wrap sm:flex-nowrap items-center gap-3 w-full lg:w-auto">
            <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-2xl px-3 py-2 w-full sm:w-auto">
                <span class="text-sm font-bold text-slate-500">من:</span>
                <input type="date" name="start_date" value="{{ $startDate }}" class="bg-transparent border-none outline-none text-slate-700 text-sm font-bold focus:ring-0 p-0 w-full">
            </div>
            <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-2xl px-3 py-2 w-full sm:w-auto">
                <span class="text-sm font-bold text-slate-500">إلى:</span>
                <input type="date" name="end_date" value="{{ $endDate }}" class="bg-transparent border-none outline-none text-slate-700 text-sm font-bold focus:ring-0 p-0 w-full">
            </div>
            <button type="submit" class="bg-brand-primary text-white px-6 py-3 rounded-2xl font-bold hover:bg-brand-primary/90 transition-colors shadow-lg shadow-brand-primary/20 flex items-center gap-2 w-full sm:w-auto justify-center">
                <i class="fa-solid fa-filter"></i>
                تحديث
            </button>
        </form>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- الإيرادات -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-emerald-100 relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-emerald-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center text-emerald-600 mb-4">
                    <i class="fa-solid fa-arrow-trend-up text-xl"></i>
                </div>
                <p class="text-sm font-bold text-slate-500 mb-1">إجمالي الإيرادات</p>
                <h3 class="text-2xl font-black text-slate-800">
                    {{ number_format($incomes, 2) }} <span class="text-sm text-slate-400">{{ $settings['currency'] }}</span>
                </h3>
            </div>
        </div>

        <!-- المصروفات -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-rose-100 relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-rose-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <div class="w-12 h-12 bg-rose-100 rounded-xl flex items-center justify-center text-rose-600 mb-4">
                    <i class="fa-solid fa-arrow-trend-down text-xl"></i>
                </div>
                <p class="text-sm font-bold text-slate-500 mb-1">إجمالي المصروفات</p>
                <h3 class="text-2xl font-black text-slate-800">
                    {{ number_format($expenses, 2) }} <span class="text-sm text-slate-400">{{ $settings['currency'] }}</span>
                </h3>
            </div>
        </div>

        <!-- صافي الربح -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-blue-100 relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-blue-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600 mb-4">
                    <i class="fa-solid fa-wallet text-xl"></i>
                </div>
                <p class="text-sm font-bold text-slate-500 mb-1">صافي الربح</p>
                <h3 class="text-2xl font-black {{ $netProfit >= 0 ? 'text-blue-600' : 'text-rose-600' }}">
                    {{ number_format($netProfit, 2) }} <span class="text-sm text-slate-400">{{ $settings['currency'] }}</span>
                </h3>
            </div>
        </div>

        <!-- الديون -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-amber-100 relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-amber-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center text-amber-600 mb-4">
                    <i class="fa-solid fa-hand-holding-dollar text-xl"></i>
                </div>
                <p class="text-sm font-bold text-slate-500 mb-1">إجمالي الديون المعلقة</p>
                <h3 class="text-2xl font-black text-slate-800">
                    {{ number_format($debts, 2) }} <span class="text-sm text-slate-400">{{ $settings['currency'] }}</span>
                </h3>
            </div>
        </div>
    </div>

    <!-- Additional Stats & Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Chart -->
        <div class="lg:col-span-2 bg-white p-6 rounded-3xl shadow-sm border border-slate-200">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-500">
                    <i class="fa-solid fa-chart-area"></i>
                </div>
                <h2 class="text-lg font-bold text-slate-800">الإيرادات مقابل المصروفات</h2>
            </div>
            <div class="relative h-72 w-full">
                <canvas id="financialChart"></canvas>
            </div>
        </div>

        <!-- Quick Info -->
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center text-purple-500">
                        <i class="fa-solid fa-list-check"></i>
                    </div>
                    <h2 class="text-lg font-bold text-slate-800">إحصائيات النظام التفصيلية</h2>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 flex flex-col justify-center items-center text-center">
                        <span class="text-xs font-bold text-slate-500 mb-1">إجمالي الحالات (أطفال)</span>
                        <span class="text-2xl font-black text-slate-800">{{ $totalChildrenCount }}</span>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 flex flex-col justify-center items-center text-center">
                        <span class="text-xs font-bold text-slate-500 mb-1">عدد الأخصائيين</span>
                        <span class="text-2xl font-black text-slate-800">{{ $specialistsCount }}</span>
                    </div>
                </div>

                <div class="space-y-3 mt-4">
                    <div class="flex justify-between items-center p-3 bg-purple-50 rounded-xl border border-purple-100">
                        <span class="font-bold text-purple-700 text-sm">الجلسات المسجلة للفترة</span>
                        <span class="text-lg font-black text-purple-700">{{ $totalSessions }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-blue-50 rounded-xl border border-blue-100">
                        <span class="font-bold text-blue-700 text-sm">حالات جديدة للفترة</span>
                        <span class="text-lg font-black text-blue-700">{{ $newChildrenCount }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-emerald-50 rounded-xl border border-emerald-100">
                        <span class="font-bold text-emerald-700 text-sm">الفواتير المُصدرة للفترة</span>
                        <span class="text-lg font-black text-emerald-700">{{ $invoicesCount }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-amber-50 rounded-xl border border-amber-100">
                        <span class="font-bold text-amber-700 text-sm">الحجوزات للفترة</span>
                        <span class="text-lg font-black text-amber-700">{{ $newBookingsCount }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Report summary message -->
            <div class="bg-gradient-to-br from-brand-primary to-emerald-500 p-6 rounded-3xl shadow-lg text-white">
                <h3 class="font-bold text-lg mb-2"><i class="fa-solid fa-lightbulb ml-2 text-yellow-300"></i> ملخص الفترة</h3>
                <p class="text-sm text-emerald-50 leading-relaxed font-medium">
                    خلال هذه الفترة (من {{ $startDate }} إلى {{ $endDate }})، 
                    تم إضافة <strong class="text-white">{{ $newChildrenCount }}</strong> حالة جديدة، 
                    وتسجيل <strong class="text-white">{{ $totalSessions }}</strong> جلسة، 
                    وإصدار <strong class="text-white">{{ $invoicesCount }}</strong> فاتورة.
                    المركز حقق صافي ربح قدره <strong class="text-white bg-white/20 px-2 py-0.5 rounded-lg">{{ number_format($netProfit, 2) }}</strong>.
                </p>
            </div>
        </div>
    </div>

    <!-- Attendance Stats -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
        <!-- Highlights -->
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center text-orange-500">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <h2 class="text-lg font-bold text-slate-800">إحصائيات الحضور والغياب</h2>
                </div>
                
                <div class="space-y-4">
                    <!-- Total Apologies -->
                    <div class="flex flex-col p-4 bg-orange-50 rounded-2xl border border-orange-100">
                        <span class="text-sm font-bold text-orange-600 mb-1">إجمالي حالات الاعتذار للفترة</span>
                        <span class="text-2xl font-black text-orange-800">{{ $totalApologies }} حالة</span>
                    </div>

                    <!-- Most Apologized -->
                    <div class="flex flex-col p-4 bg-rose-50 rounded-2xl border border-rose-100">
                        <span class="text-sm font-bold text-rose-600 mb-1">أكثر طفل اعتذر عن جلساته للفترة</span>
                        <div class="flex justify-between items-end mt-1">
                            <span class="font-bold text-rose-800">{{ $mostApologizedChild ? $mostApologizedChild->name : 'لا يوجد' }}</span>
                            <span class="text-lg font-black text-rose-800">{{ $mostApologizedCount }} مرة</span>
                        </div>
                    </div>

                    <!-- Most Attended -->
                    <div class="flex flex-col p-4 bg-emerald-50 rounded-2xl border border-emerald-100">
                        <span class="text-sm font-bold text-emerald-600 mb-1">أكثر طفل حضر جلساته للفترة</span>
                        <div class="flex justify-between items-end mt-1">
                            <span class="font-bold text-emerald-800">{{ $mostAttendedChild ? $mostAttendedChild->name : 'لا يوجد' }}</span>
                            <span class="text-lg font-black text-emerald-800">{{ $mostAttendedCount }} مرة</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Absence Days Table (AlpineJS dynamic) -->
        <div class="lg:col-span-2 bg-white p-6 rounded-3xl shadow-sm border border-slate-200" 
             x-data="{
                allData: {{ json_encode($childrenAbsence) }},
                search: '',
                currentPage: 1,
                perPage: 10,
                get filteredData() {
                    if (this.search === '') return this.allData;
                    return this.allData.filter(item => item.child_name.toLowerCase().includes(this.search.toLowerCase()));
                },
                get totalPages() {
                    return Math.max(1, Math.ceil(this.filteredData.length / this.perPage));
                },
                get paginatedData() {
                    if (this.currentPage > this.totalPages) this.currentPage = this.totalPages;
                    let start = (this.currentPage - 1) * this.perPage;
                    return this.filteredData.slice(start, start + this.perPage);
                },
                nextPage() { if (this.currentPage < this.totalPages) this.currentPage++; },
                prevPage() { if (this.currentPage > 1) this.currentPage--; }
             }">
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-rose-50 rounded-xl flex items-center justify-center text-rose-500">
                        <i class="fa-solid fa-calendar-xmark"></i>
                    </div>
                    <h2 class="text-lg font-bold text-slate-800">أيام الغياب للأطفال (منذ آخر جلسة حضور)</h2>
                </div>
                <!-- Search Input -->
                <div class="relative w-full sm:w-64">
                    <input type="text" x-model="search" placeholder="بحث باسم الطفل..." class="w-full text-sm pl-4 pr-10 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white outline-none">
                    <i class="fa-solid fa-magnifying-glass absolute right-3.5 top-3.5 text-slate-400"></i>
                </div>
            </div>
            
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-sm text-right">
                    <thead class="text-xs text-slate-500 bg-slate-50 uppercase rounded-t-xl">
                        <tr>
                            <th scope="col" class="px-6 py-4 rounded-tr-xl font-bold">اسم الطفل</th>
                            <th scope="col" class="px-6 py-4 font-bold">تاريخ آخر جلسة حضرها</th>
                            <th scope="col" class="px-6 py-4 rounded-tl-xl font-bold">عدد أيام الغياب</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="absence in paginatedData" :key="absence.child_name">
                            <tr class="bg-white border-b border-slate-100 hover:bg-slate-50">
                                <td class="px-6 py-4 font-bold text-slate-800" x-text="absence.child_name"></td>
                                <td class="px-6 py-4 text-slate-500" x-text="absence.last_session_date"></td>
                                <td class="px-6 py-4 font-bold" :class="absence.days_absent === -1 || absence.days_absent > 14 ? 'text-rose-600' : 'text-amber-600'" x-text="absence.days_absent === -1 ? 'لم يحضر أبداً' : absence.days_absent + ' يوم'"></td>
                            </tr>
                        </template>
                        <tr x-show="paginatedData.length === 0">
                            <td colspan="3" class="px-6 py-8 text-center text-slate-500">
                                لا يوجد بيانات غياب مطابقة للبحث
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Controls -->
            <div class="mt-4 flex items-center justify-between" x-show="totalPages > 1">
                <span class="text-xs text-slate-500 font-bold">صفحة <span x-text="currentPage"></span> من <span x-text="totalPages"></span></span>
                <div class="flex items-center gap-2">
                    <button @click="prevPage()" :disabled="currentPage === 1" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all" :class="currentPage === 1 ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-slate-800 text-white hover:bg-slate-700'">السابق</button>
                    <button @click="nextPage()" :disabled="currentPage === totalPages" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all" :class="currentPage === totalPages ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-slate-800 text-white hover:bg-slate-700'">التالي</button>
                </div>
            </div>

        </div>
    </div>

    <!-- Expenses by Category -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200 mt-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-teal-50 rounded-xl flex items-center justify-center text-teal-500">
                <i class="fa-solid fa-list-check"></i>
            </div>
            <h2 class="text-lg font-bold text-slate-800">تفصيل المصروفات حسب التصنيف للفترة</h2>
        </div>
        
        @if($expensesByCategory->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($expensesByCategory as $expenseItem)
            <div class="flex justify-between items-center p-4 bg-slate-50 rounded-2xl border border-slate-100">
                <span class="font-bold text-slate-700 text-sm">{{ $expenseItem->category }}</span>
                <span class="text-lg font-black text-rose-600">{{ number_format($expenseItem->total, 2) }} {{ $settings['currency'] }}</span>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-8">
            <p class="text-slate-500 text-sm">لا توجد مصروفات مسجلة خلال هذه الفترة.</p>
        </div>
        @endif
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('financialChart');
        if (ctx) {
            const chartDates = @json($chartDates);
            const chartIncomes = @json($chartIncomes);
            const chartExpenses = @json($chartExpenses);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartDates,
                    datasets: [
                        {
                            label: 'الإيرادات',
                            data: chartIncomes,
                            borderColor: '#10b981', // emerald-500
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4
                        },
                        {
                            label: 'المصروفات',
                            data: chartExpenses,
                            borderColor: '#f43f5e', // rose-500
                            backgroundColor: 'rgba(244, 63, 94, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            labels: { font: { family: "'Cairo', sans-serif", weight: 'bold' } }
                        },
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
                            ticks: { font: { family: "'Cairo', sans-serif" } }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
@endsection

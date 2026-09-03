@extends('layouts.app')

@section('title', 'إدارة المرتبات والموظفين')

@section('content')
<div class="space-y-6" x-data="{
    calculateNet(id) {
        let base = parseFloat(document.getElementById('base_' + id).value) || 0;
        let bonus = parseFloat(document.getElementById('bonus_' + id).value) || 0;
        let advance = parseFloat(document.getElementById('advance_' + id).value) || 0;
        let net = base + bonus - advance;
        document.getElementById('net_' + id).innerText = net.toFixed(2);
        
        let el = document.getElementById('net_' + id);
        if(net < 0) {
            el.classList.add('text-rose-600');
            el.classList.remove('text-emerald-600');
        } else {
            el.classList.add('text-emerald-600');
            el.classList.remove('text-rose-600');
        }
        
        this.calculateTotal();
    },
    calculateTotal() {
        let total = 0;
        document.querySelectorAll('.net-amount').forEach(function(el) {
            total += parseFloat(el.innerText) || 0;
        });
        document.getElementById('grand_total').innerText = total.toFixed(2);
    }
}">

    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-6 rounded-3xl shadow-sm border border-slate-200">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-500 shadow-inner">
                <i class="fa-solid fa-money-check-dollar text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">صرف المرتبات</h1>
                <p class="text-slate-500 text-sm mt-1">كشف مرتبات شهر {{ $currentMonth }} لسنة {{ $currentYear }}</p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden" x-data="{ showAddModal: false }">
        
        <!-- Toolbar -->
        <div class="p-4 border-b border-slate-100 flex justify-end">
            <button @click="showAddModal = true" type="button" class="bg-brand-primary text-white px-4 py-2 rounded-xl font-bold text-sm shadow-sm hover:bg-teal-700 transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> إضافة موظف جديد
            </button>
        </div>

        <!-- Add Employee Modal -->
        <div x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm" style="display: none;">
            <div @click.away="showAddModal = false" class="bg-white rounded-3xl shadow-xl w-full max-w-md p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-slate-800">إضافة موظف للمرتبات</h3>
                    <button @click="showAddModal = false" type="button" class="text-slate-400 hover:text-rose-500"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>
                <form action="{{ route('employees.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block font-bold text-sm text-slate-700 mb-1">اسم الموظف <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:bg-white font-bold text-sm">
                    </div>
                    <div>
                        <label class="block font-bold text-sm text-slate-700 mb-1">المسمى الوظيفي <span class="text-rose-500">*</span></label>
                        <input type="text" name="job_title" required placeholder="مثال: عامل نظافة، موظف استقبال..." class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:bg-white font-bold text-sm">
                    </div>
                    <div>
                        <label class="block font-bold text-sm text-slate-700 mb-1">الراتب الشهري (ج.م) <span class="text-rose-500">*</span></label>
                        <input type="number" name="salary" min="0" step="0.01" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:bg-white font-bold text-sm">
                    </div>
                    <div>
                        <label class="block font-bold text-sm text-slate-700 mb-1">رقم الهاتف</label>
                        <input type="text" name="phone" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:bg-white text-sm">
                    </div>
                    <button type="submit" class="w-full bg-emerald-500 text-white font-bold py-3 rounded-xl hover:bg-emerald-600 transition">إضافة الموظف</button>
                </form>
            </div>
        </div>
        
        <form action="{{ route('payroll.payAll') }}" method="POST" id="payrollForm">
            @csrf
            
            <div class="overflow-x-auto">
                <table class="w-full text-right text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-6 py-4 font-semibold text-slate-700">الموظف / الأخصائي</th>
                            <th class="px-6 py-4 font-semibold text-slate-700">نوع الراتب / ملاحظات</th>
                            <th class="px-6 py-4 font-semibold text-slate-700">الراتب الأساسي/المستحق</th>
                            <th class="px-6 py-4 font-semibold text-slate-700">المكافأة (+)</th>
                            <th class="px-6 py-4 font-semibold text-slate-700">السلفة/الخصم (-)</th>
                            <th class="px-6 py-4 font-semibold text-slate-700 text-center">الصافي</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <!-- Specialists -->
                        @forelse($specialists as $specialist)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-brand-primary/10 text-brand-primary flex items-center justify-center font-bold text-lg">
                                        <i class="fa-solid fa-user-doctor"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800">{{ $specialist->name }}</p>
                                        <p class="text-xs text-slate-500">أخصائي - {{ $specialist->phone }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-xs rounded-full font-bold bg-slate-100 text-slate-600">
                                    {{ $specialist->salary_type === 'per_session' ? 'بالجلسة' : 'شهري/ثابت' }}
                                </span>
                                <p class="text-xs text-slate-500 mt-1">{{ $specialist->notes }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <input type="number" 
                                       name="payroll[{{ $specialist->id }}][base_salary]" 
                                       id="base_s_{{ $specialist->id }}"
                                       value="{{ $specialist->calculated_salary }}" 
                                       min="0" step="0.01"
                                       @input="calculateNet('s_' + {{ $specialist->id }})"
                                       class="w-28 px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-brand-primary/20 transition-all">
                            </td>
                            <td class="px-6 py-4">
                                <input type="number" 
                                       name="payroll[{{ $specialist->id }}][bonus]" 
                                       id="bonus_s_{{ $specialist->id }}"
                                       value="0" 
                                       min="0" step="0.01"
                                       @input="calculateNet('s_' + {{ $specialist->id }})"
                                       class="w-24 px-3 py-2 bg-emerald-50 border border-emerald-200 rounded-xl text-sm font-bold text-emerald-700 focus:ring-2 focus:ring-emerald-400/20 transition-all">
                            </td>
                            <td class="px-6 py-4">
                                <input type="number" 
                                       name="payroll[{{ $specialist->id }}][advance]" 
                                       id="advance_s_{{ $specialist->id }}"
                                       value="0" 
                                       min="0" step="0.01"
                                       @input="calculateNet('s_' + {{ $specialist->id }})"
                                       class="w-24 px-3 py-2 bg-rose-50 border border-rose-200 rounded-xl text-sm font-bold text-rose-700 focus:ring-2 focus:ring-rose-400/20 transition-all">
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-lg font-black text-emerald-600 net-amount" id="net_s_{{ $specialist->id }}">{{ number_format($specialist->calculated_salary, 2, '.', '') }}</span>
                                <span class="text-xs text-slate-400">{{ $settings['currency'] }}</span>
                            </td>
                        </tr>
                        @empty
                        @endforelse

                        <!-- Employees -->
                        @forelse($employees as $employee)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center font-bold text-lg">
                                        <i class="fa-solid fa-user-tie"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800">{{ $employee->name }}</p>
                                        <p class="text-xs text-slate-500">{{ $employee->job_title }} - {{ $employee->phone }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-xs rounded-full font-bold bg-slate-100 text-slate-600">
                                    شهري/ثابت
                                </span>
                                <div class="flex items-center gap-2 mt-1">
                                    <p class="text-xs text-slate-500">{{ $employee->notes }}</p>
                                    <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الموظف؟')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-rose-400 hover:text-rose-600 text-xs"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <input type="number" 
                                       name="employees_payroll[{{ $employee->id }}][base_salary]" 
                                       id="base_e_{{ $employee->id }}"
                                       value="{{ $employee->calculated_salary }}" 
                                       min="0" step="0.01"
                                       @input="calculateNet('e_' + {{ $employee->id }})"
                                       class="w-28 px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-brand-primary/20 transition-all">
                            </td>
                            <td class="px-6 py-4">
                                <input type="number" 
                                       name="employees_payroll[{{ $employee->id }}][bonus]" 
                                       id="bonus_e_{{ $employee->id }}"
                                       value="0" 
                                       min="0" step="0.01"
                                       @input="calculateNet('e_' + {{ $employee->id }})"
                                       class="w-24 px-3 py-2 bg-emerald-50 border border-emerald-200 rounded-xl text-sm font-bold text-emerald-700 focus:ring-2 focus:ring-emerald-400/20 transition-all">
                            </td>
                            <td class="px-6 py-4">
                                <input type="number" 
                                       name="employees_payroll[{{ $employee->id }}][advance]" 
                                       id="advance_e_{{ $employee->id }}"
                                       value="0" 
                                       min="0" step="0.01"
                                       @input="calculateNet('e_' + {{ $employee->id }})"
                                       class="w-24 px-3 py-2 bg-rose-50 border border-rose-200 rounded-xl text-sm font-bold text-rose-700 focus:ring-2 focus:ring-rose-400/20 transition-all">
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-lg font-black text-emerald-600 net-amount" id="net_e_{{ $employee->id }}">{{ number_format($employee->calculated_salary, 2, '.', '') }}</span>
                                <span class="text-xs text-slate-400">{{ $settings['currency'] }}</span>
                            </td>
                        </tr>
                        @empty
                        @endforelse
                        
                        @if($specialists->count() == 0 && $employees->count() == 0)
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="inline-flex flex-col items-center justify-center text-slate-400">
                                    <i class="fa-solid fa-users text-4xl mb-3 opacity-20"></i>
                                    <p class="text-sm font-medium">لا يوجد موظفين مسجلين حالياً</p>
                                </div>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Footer & Submit -->
            @if($specialists->count() > 0 || $employees->count() > 0)
            <div class="p-6 border-t border-slate-100 bg-slate-50 flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-3 bg-white px-5 py-3 rounded-2xl shadow-sm border border-slate-200">
                    <span class="font-bold text-slate-600">إجمالي المرتبات المطلوبة:</span>
                    <span class="text-2xl font-black text-emerald-600" id="grand_total">0.00</span>
                    <span class="text-sm font-bold text-slate-400">{{ $settings['currency'] }}</span>
                </div>
                
                <button type="submit" onclick="return confirm('هل أنت متأكد من صرف جميع المرتبات للموظفين أعلاه وإدراجها في المصروفات؟')" class="bg-emerald-500 hover:bg-emerald-600 text-white px-8 py-4 rounded-2xl font-bold transition-colors shadow-lg shadow-emerald-200 flex items-center gap-3 text-lg w-full md:w-auto justify-center">
                    <i class="fa-solid fa-check-double"></i>
                    صرف كل المرتبات
                </button>
            </div>
            @endif
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // حساب الإجمالي المبدئي عند تحميل الصفحة
        let total = 0;
        document.querySelectorAll('.net-amount').forEach(function(el) {
            total += parseFloat(el.innerText) || 0;
        });
        document.getElementById('grand_total').innerText = total.toFixed(2);
    });
</script>
@endpush
@endsection

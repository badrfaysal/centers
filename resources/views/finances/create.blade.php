@extends('layouts.app')
@section('title', 'إنشاء فاتورة جديدة')

@section('content')
<div class="space-y-6" dir="rtl" lang="ar">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-brand-primary/10 flex items-center justify-center text-brand-primary">
                <i class="fa-solid fa-file-circle-plus text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-slate-800 font-cairo">إنشاء فاتورة جديدة</h1>
        </div>
        <a href="{{ route('finances.index') }}" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl hover:bg-slate-200 transition-colors flex items-center gap-2 font-medium">
            <i class="fa-solid fa-arrow-right"></i>
            عودة للمالية
        </a>
    </div>

    @if ($errors->any())
        <div class="bg-rose-50 border-r-4 border-rose-500 rounded-2xl p-4 flex gap-3">
            <div class="text-rose-500 mt-0.5">
                <i class="fa-solid fa-circle-exclamation"></i>
            </div>
            <div>
                <h3 class="text-rose-800 font-bold mb-1">يرجى تصحيح الأخطاء التالية:</h3>
                <ul class="text-rose-700 text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>- {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form action="{{ route('finances.store') }}" method="POST"
          x-data="{
              childId: '{{ old('child_id', '') }}',
              specialistId: '{{ old('specialist_id', '') }}',
              sessionPrice: {{ old('session_price', 0) }},
              sessionsCount: {{ old('sessions_count', 1) }},
              discountType: '{{ old('discount_type', 'fixed') }}',
              discountValue: {{ old('discount_value', 0) }},
              paidAmount: {{ old('paid_amount', 0) }},
              currency: '{{ old('currency', 'ر.س') }}',
              get totalAmount() { return this.sessionPrice * this.sessionsCount },
              get discountAmount() {
                  if (this.discountType === 'percentage') return (this.totalAmount * Math.min(this.discountValue, 100)) / 100;
                  return Math.min(this.discountValue, this.totalAmount);
              },
              get netAmount() { return this.totalAmount - this.discountAmount },
              get remainingAmount() { return Math.max(this.netAmount - this.paidAmount, 0) },
              async fetchPrice() {
                  if (!this.specialistId) return;
                  try {
                      const res = await fetch('/api/specialist-price/' + this.specialistId);
                      const data = await res.json();
                      this.sessionPrice = data.session_rate || 0;
                  } catch (e) {
                      console.error('Error fetching price', e);
                  }
              }
          }"
          class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        @csrf

        <!-- Main Form -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 sm:p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Row 1 -->
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-slate-700">اسم الطفل <span class="text-rose-500">*</span></label>
                        <select x-model="childId" name="child_id" required
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-brand-primary focus:border-brand-primary transition-all">
                            <option value="">-- اختر الطفل --</option>
                            @foreach($children as $child)
                                <option value="{{ $child->id }}">{{ $child->name }} ({{ $child->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-slate-700">العملة</label>
                        <select x-model="currency" name="currency" required
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-brand-primary focus:border-brand-primary transition-all">
                            <option value="ر.س">ريال سعودي (ر.س)</option>
                            <option value="ج.م">جنيه مصري (ج.م)</option>
                            <option value="$">دولار أمريكي ($)</option>
                            <option value="د.إ">درهم إماراتي (د.إ)</option>
                            <option value="د.ك">دينار كويتي (د.ك)</option>
                        </select>
                    </div>

                    <!-- Row 2 -->
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-slate-700">الأخصائي</label>
                        <select x-model="specialistId" @change="fetchPrice()" name="specialist_id" required
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-brand-primary focus:border-brand-primary transition-all">
                            <option value="">-- اختر الأخصائي --</option>
                            @foreach($specialists as $specialist)
                                <option value="{{ $specialist->id }}">{{ $specialist->name }} - {{ $specialist->specialization }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-slate-700">سعر الجلسة</label>
                        <div class="relative">
                            <input type="number" x-model.number="sessionPrice" name="session_price" step="0.01" required
                                   class="w-full px-4 py-3 pl-12 rounded-xl border border-slate-200 focus:ring-2 focus:ring-brand-primary focus:border-brand-primary transition-all">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400 font-medium" x-text="currency"></div>
                        </div>
                    </div>

                    <!-- Row 3 -->
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-slate-700">عدد الجلسات</label>
                        <input type="number" x-model.number="sessionsCount" name="sessions_count" min="1" required
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-brand-primary focus:border-brand-primary transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-slate-700">طريقة الدفع</label>
                        <select name="payment_method" required
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-brand-primary focus:border-brand-primary transition-all">
                            <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>نقدي</option>
                            <option value="transfer" {{ old('payment_method') == 'transfer' ? 'selected' : '' }}>تحويل بنكي</option>
                            <option value="visa" {{ old('payment_method') == 'visa' ? 'selected' : '' }}>فيزا</option>
                        </select>
                    </div>

                    <!-- Row 4 -->
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-slate-700">نوع الخصم</label>
                        <select x-model="discountType" name="discount_type"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-brand-primary focus:border-brand-primary transition-all">
                            <option value="fixed">مبلغ ثابت</option>
                            <option value="percentage">نسبة مئوية %</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-slate-700">قيمة الخصم</label>
                        <div class="relative">
                            <input type="number" x-model.number="discountValue" name="discount_value" step="0.01" min="0"
                                   class="w-full px-4 py-3 pl-12 rounded-xl border border-slate-200 focus:ring-2 focus:ring-brand-primary focus:border-brand-primary transition-all">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400 font-medium" x-show="discountType === 'fixed'" x-text="currency"></div>
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400 font-medium" x-show="discountType === 'percentage'">%</div>
                        </div>
                    </div>

                    <!-- Row 5 -->
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-slate-700">المبلغ المدفوع</label>
                        <div class="relative">
                            <input type="number" x-model.number="paidAmount" name="paid_amount" step="0.01" min="0" required
                                   class="w-full px-4 py-3 pl-12 rounded-xl border border-slate-200 focus:ring-2 focus:ring-brand-primary focus:border-brand-primary transition-all">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400 font-medium" x-text="currency"></div>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-slate-700">تاريخ الفاتورة</label>
                        <input type="date" name="invoice_date" required value="{{ old('invoice_date', date('Y-m-d')) }}"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-brand-primary focus:border-brand-primary transition-all">
                    </div>
                    
                    <!-- Row 6 -->
                    <div class="space-y-2 md:col-span-2">
                        <label class="block text-sm font-bold text-slate-700">ملاحظات</label>
                        <textarea name="notes" rows="3"
                                  class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-brand-primary focus:border-brand-primary transition-all resize-none">{{ old('notes') }}</textarea>
                    </div>

                </div>
            </div>
        </div>

        <!-- Sidebar Summary -->
        <div class="space-y-6">
            <div class="bg-slate-800 rounded-3xl p-6 sm:p-8 text-white sticky top-6 shadow-lg">
                <h3 class="text-xl font-bold mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-calculator text-brand-primary"></i>
                    ملخص الفاتورة
                </h3>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center pb-4 border-b border-slate-700">
                        <span class="text-slate-300">الإجمالي</span>
                        <span class="font-bold text-lg" x-text="totalAmount.toFixed(2) + ' ' + currency"></span>
                    </div>
                    <div class="flex justify-between items-center pb-4 border-b border-slate-700">
                        <span class="text-slate-300">الخصم</span>
                        <span class="font-bold text-rose-400" x-text="'- ' + discountAmount.toFixed(2) + ' ' + currency"></span>
                    </div>
                    <div class="flex justify-between items-center pb-4 border-b border-slate-700">
                        <span class="text-slate-300">الصافي</span>
                        <span class="font-bold text-emerald-400 text-xl" x-text="netAmount.toFixed(2) + ' ' + currency"></span>
                    </div>
                    <div class="flex justify-between items-center pb-4 border-b border-slate-700">
                        <span class="text-slate-300">المدفوع</span>
                        <span class="font-bold text-lg" x-text="paidAmount.toFixed(2) + ' ' + currency"></span>
                    </div>
                    <div class="flex justify-between items-center pt-2">
                        <span class="text-slate-300">المتبقي (الدين)</span>
                        <span class="font-bold text-2xl" :class="remainingAmount > 0 ? 'text-rose-500' : 'text-slate-100'" x-text="remainingAmount.toFixed(2) + ' ' + currency"></span>
                    </div>
                </div>

                <button type="submit" class="w-full mt-8 bg-brand-primary text-white py-4 px-6 rounded-xl hover:bg-brand-primary/90 focus:ring-4 focus:ring-brand-primary/30 font-bold text-lg transition-all flex items-center justify-center gap-2 shadow-lg shadow-brand-primary/30">
                    <i class="fa-solid fa-save"></i>
                    حفظ الفاتورة
                </button>
            </div>
        </div>

    </form>
</div>
@endsection

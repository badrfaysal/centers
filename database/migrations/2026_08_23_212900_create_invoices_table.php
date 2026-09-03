<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique(); // رقم الفاتورة مثل INV-0001
            $table->foreignId('child_id')->constrained('children')->cascadeOnDelete();
            $table->foreignId('specialist_id')->constrained('specialists')->cascadeOnDelete();
            $table->string('child_name'); // اسم الطفل (cached)
            $table->string('specialist_name'); // اسم الأخصائي (cached)
            $table->string('parent_name'); // اسم ولي الأمر
            $table->decimal('session_price', 10, 2)->default(0); // سعر الجلسة الواحدة
            $table->integer('sessions_count')->default(1); // عدد الجلسات
            $table->decimal('total_amount', 10, 2)->default(0); // الإجمالي قبل الخصم
            $table->decimal('discount_amount', 10, 2)->default(0); // مبلغ الخصم
            $table->decimal('discount_percentage', 5, 2)->default(0); // نسبة الخصم
            $table->decimal('net_amount', 10, 2)->default(0); // الصافي بعد الخصم
            $table->decimal('paid_amount', 10, 2)->default(0); // المبلغ المدفوع
            $table->decimal('remaining_amount', 10, 2)->default(0); // الباقي (دين)
            $table->enum('payment_method', ['cash', 'transfer', 'visa'])->default('cash'); // طريقة الدفع
            $table->enum('payment_status', ['paid', 'partial', 'unpaid'])->default('unpaid'); // حالة الدفع
            $table->text('notes')->nullable(); // ملاحظات
            $table->date('invoice_date'); // تاريخ الفاتورة
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};

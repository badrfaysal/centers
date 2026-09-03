<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultation_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code')->unique(); // كود الحجز مثل BK-2001
            $table->string('parent_name'); // اسم ولي الأمر
            $table->string('phone'); // الهاتف والواتساب
            $table->string('child_name'); // اسم الطفل
            $table->string('child_age'); // عمر الطفل
            $table->string('service'); // الخدمة المطلوبة
            $table->text('notes')->nullable(); // ملاحظات ولي الأمر
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending'); // حالة الحجز
            $table->dateTime('scheduled_at')->nullable(); // الموعد المحدد للجلسة
            $table->string('specialist_name')->nullable(); // الأخصائي المحدد
            $table->string('room')->nullable(); // الغرفة المحددة
            $table->text('admin_notes')->nullable(); // ملاحظات الإدارة
            $table->string('confirmed_by')->nullable(); // اسم الموظف المؤكد
            $table->timestamp('confirmed_at')->nullable(); // وقت التأكيد
            $table->foreignId('child_id')->nullable()->constrained('children')->nullOnDelete(); // ربط بملف الطفل إن تم إنشاؤه
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultation_bookings');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('children', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // كود الطفل مثل CH-1001
            $table->string('name'); // اسم الطفل
            $table->date('birth_date'); // تاريخ الميلاد
            $table->enum('gender', ['male', 'female'])->default('male'); // النوع
            $table->string('parent_name'); // اسم ولي الأمر
            $table->string('parent_relation')->default('الأب'); // صلة القرابة
            $table->string('phone'); // رقم هاتف ولي الأمر (واتساب)
            $table->string('emergency_phone')->nullable(); // هاتف إضافي
            $table->string('address')->nullable(); // العنوان
            $table->string('initial_diagnosis'); // التشخيص الأولي
            $table->string('diagnosis_category')->default('speech'); // تصنيف التشخيص
            $table->string('main_specialist')->nullable(); // الأخصائي المتابع
            $table->text('medical_notes')->nullable(); // التاريخ المرضي وملاحظات
            $table->string('assistive_devices')->nullable(); // أجهزة مساعدة إن وجدت
            $table->string('package_type')->default('evaluation'); // نوع الباقة أو التقييم
            $table->string('preferred_days')->nullable(); // الأيام المفضلة للجلسات
            $table->string('avatar')->nullable(); // صورة الطفل أو الأفاتار
            $table->enum('status', ['active', 'on_hold', 'discharged'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('children');
    }
};
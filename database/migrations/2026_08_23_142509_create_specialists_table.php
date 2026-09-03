<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('specialists', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // كود الأخصائي مثل SP-101
            $table->string('name'); // الاسم الرباعي
            $table->string('specialization'); // التخصص: تخاطب، تكامل حسي، تعديل سلوك، إلخ
            $table->string('job_title')->default('أخصائي تأهيل'); // المسمى الوظيفي
            $table->string('phone'); // رقم الهاتف
            $table->string('email')->nullable(); // البريد الإلكتروني
            $table->string('license_number')->nullable(); // رقم ترخيص مزاولة المهنة
            $table->string('qualification')->nullable(); // المؤهل الدراسي
            $table->integer('experience_years')->default(1); // سنوات الخبرة
            $table->string('default_room')->nullable(); // القاعة / الغرفة الافتراضية
            $table->json('work_days')->nullable(); // أيام العمل المتاحة
            $table->string('salary_type')->default('per_session'); // monthly, percentage, per_session
            $table->decimal('session_rate', 10, 2)->default(0.00); // قيمة الجلسة أو النسبة
            $table->string('photo_path')->nullable(); // صورة الأخصائي
            $table->text('bio')->nullable(); // نبذة وسيرة ذاتية
            $table->enum('status', ['active', 'on_leave', 'inactive'])->default('active'); // الحالة
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('specialists');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('session_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained('children')->cascadeOnDelete();
            $table->foreignId('specialist_id')->nullable()->constrained('specialists')->nullOnDelete();
            $table->string('specialist_name'); // اسم الأخصائي
            $table->string('session_title')->default('جلسة تأهيل وتخاطب فردي'); // عنوان ونوع الجلسة
            $table->date('session_date'); // تاريخ الجلسة
            $table->time('start_time'); // وقت البدء مثل 10:00:00
            $table->time('end_time')->nullable(); // وقت الانتهاء مثل 10:45:00
            $table->string('room_name')->default('غرفة التخاطب 1'); // القاعة / الغرفة
            $table->string('day_of_week')->nullable(); // السبت، الأحد...
            $table->boolean('is_recurring')->default(false); // جلسة أسبوعية متكررة
            $table->enum('status', ['scheduled', 'completed', 'cancelled', 'rescheduled'])->default('scheduled'); // حالة الجدولة
            $table->enum('attendance_status', ['pending', 'attended', 'absent'])->default('pending'); // الحضور والغياب
            $table->text('notes')->nullable(); // ملاحظات للأهل أو الاستقبال
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_schedules');
    }
};
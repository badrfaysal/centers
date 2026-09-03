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
        Schema::create('therapy_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained('children')->onDelete('cascade');
            $table->string('specialist_name'); // اسم الأخصائي
            $table->date('session_date'); // تاريخ الجلسة
            $table->string('session_time')->nullable(); // وقت الجلسة
            $table->string('room_name')->default('غرفة التخاطب 1'); // الغرفة
            $table->string('session_type')->default('تخاطب ونطق'); // نوع الجلسة
            $table->string('child_mood')->default('ممتاز ومتعاون'); // استجابة ومزاج الطفل
            $table->json('goals_evaluated')->nullable(); // الأهداف التي تم تقييمها
            $table->text('clinical_notes'); // ملاحظات وتقرير الأخصائي
            $table->text('home_exercise')->nullable(); // تمرين منزلي مطلوب من ولي الأمر
            $table->string('video_path')->nullable(); // مسار الفيديو المرفوع
            $table->string('video_title')->nullable(); // عنوان الفيديو المرفوع
            $table->string('video_duration')->nullable(); // مدة الفيديو
            $table->boolean('whatsapp_notified')->default(false); // هل تم إشعار ولي الأمر
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('therapy_sessions');
    }
};
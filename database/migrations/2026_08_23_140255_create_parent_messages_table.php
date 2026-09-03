<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parent_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained('children')->onDelete('cascade');
            $table->enum('recipient_type', ['specialist', 'center'])->default('specialist'); // إلى الأخصائي أو المركز
            $table->string('parent_name'); // اسم ولي الأمر
            $table->string('subject')->nullable(); // موضوع الملاحظة
            $table->text('message'); // نص رسالة / ملاحظة ولي الأمر
            $table->text('doctor_reply')->nullable(); // رد الدكتور أو المركز
            $table->string('replied_by')->nullable(); // اسم المجيب (د. أحمد يسري)
            $table->timestamp('replied_at')->nullable(); // وقت الرد
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parent_messages');
    }
};
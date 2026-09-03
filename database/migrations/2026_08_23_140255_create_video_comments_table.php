<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('video_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('therapy_session_id')->constrained('therapy_sessions')->onDelete('cascade');
            $table->foreignId('child_id')->constrained('children')->onDelete('cascade');
            $table->enum('sender_type', ['parent', 'specialist', 'center'])->default('parent');
            $table->string('sender_name'); // اسم كاتب التعليق (أ. محمود السعدني / د. أحمد يسري)
            $table->text('comment'); // نص التعليق
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('video_comments');
    }
};
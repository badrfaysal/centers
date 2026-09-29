<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('homework_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('homework_id')->constrained('homeworks')->onDelete('cascade');
            $table->string('sender_type'); // 'specialist', 'parent'
            $table->string('message_type')->default('text'); // 'text', 'audio', 'file'
            $table->text('content'); // The message or file path
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('homework_messages');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('therapy_sessions', function (Blueprint $table) {
            $table->text('video_path')->nullable()->change();
            $table->text('homework_file_path')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('therapy_sessions', function (Blueprint $table) {
            $table->string('video_path', 255)->nullable()->change();
            $table->string('homework_file_path', 255)->nullable()->change();
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('specialist_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained('children')->onDelete('cascade');
            $table->string('specialist_name'); // اسم الأخصائي المقيم
            $table->unsignedTinyInteger('rating')->default(5); // 1 إلى 5 نجوم
            $table->text('feedback')->nullable(); // رأي وملاحظات ولي الأمر
            $table->string('parent_name'); // اسم ولي الأمر
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('specialist_ratings');
    }
};
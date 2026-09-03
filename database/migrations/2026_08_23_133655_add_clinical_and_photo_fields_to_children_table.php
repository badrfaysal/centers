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
        Schema::table('children', function (Blueprint $table) {
            $table->string('mental_age')->nullable()->after('birth_date'); // العمر العقلي / اللغوي
            $table->text('iq_tests_history')->nullable()->after('initial_diagnosis'); // مقاييس واختبارات الذكاء السابقة
            $table->string('neurologist_name')->nullable()->after('main_specialist'); // طبيب المخ والأعصاب إن وجد
            $table->text('current_medications')->nullable()->after('neurologist_name'); // الأدوية التي يتناولها بانتظام
            $table->string('photo_path')->nullable()->after('avatar'); // مسار الصورة المرفوعة للطفل
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('children', function (Blueprint $table) {
            $table->dropColumn([
                'mental_age',
                'iq_tests_history',
                'neurologist_name',
                'current_medications',
                'photo_path',
            ]);
        });
    }
};
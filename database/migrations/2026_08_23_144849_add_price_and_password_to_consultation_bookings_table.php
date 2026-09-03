<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consultation_bookings', function (Blueprint $table) {
            $table->string('parent_password')->nullable()->after('phone'); // كلمة مرور حساب ولي الأمر
            $table->decimal('session_price', 10, 2)->default(250.00)->after('room'); // سعر ورسوم الجلسة
            $table->string('payment_status')->default('unpaid')->after('session_price'); // unpaid, paid, deposit
        });
    }

    public function down(): void
    {
        Schema::table('consultation_bookings', function (Blueprint $table) {
            $table->dropColumn(['parent_password', 'session_price', 'payment_status']);
        });
    }
};
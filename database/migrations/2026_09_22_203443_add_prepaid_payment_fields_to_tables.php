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
        Schema::table('invoices', function (Blueprint $table) {
            $table->integer('consumed_sessions')->default(0)->after('sessions_count');
        });

        Schema::table('session_schedules', function (Blueprint $table) {
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->nullOnDelete()->after('specialist_id');
        });
    }

    public function down(): void
    {
        Schema::table('session_schedules', function (Blueprint $table) {
            $table->dropForeign(['invoice_id']);
            $table->dropColumn('invoice_id');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('consumed_sessions');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parent_messages', function (Blueprint $table) {
            $table->string('status')->default('new')->after('recipient_type'); // new, in_progress, resolved
            $table->boolean('is_urgent')->default(false)->after('status'); // هل هي عاجلة
            $table->text('admin_notes')->nullable()->after('doctor_reply'); // ملاحظات إدارية داخلية
        });
    }

    public function down(): void
    {
        Schema::table('parent_messages', function (Blueprint $table) {
            $table->dropColumn(['status', 'is_urgent', 'admin_notes']);
        });
    }
};
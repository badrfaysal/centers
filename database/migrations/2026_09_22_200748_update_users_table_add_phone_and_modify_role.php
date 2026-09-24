<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First add the phone column
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
        });

        // Modifying ENUM to STRING in MySQL can be tricky using Blueprint change() sometimes,
        // so we use a raw statement to be safe and convert it to VARCHAR.
        DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(50) NOT NULL DEFAULT 'parent'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('phone');
        });
        
        // Revert back to original ENUM
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'specialist', 'parent') NOT NULL DEFAULT 'parent'");
    }
};

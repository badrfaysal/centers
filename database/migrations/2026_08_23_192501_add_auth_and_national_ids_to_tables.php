<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->nullable()->after('name');
            $table->enum('role', ['admin', 'specialist', 'parent'])->default('parent')->after('email');
            $table->string('email')->nullable()->change(); // email becomes optional since we use username
        });

        Schema::table('specialists', function (Blueprint $table) {
            $table->string('national_id')->unique()->nullable()->after('id');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->after('national_id');
        });

        Schema::table('children', function (Blueprint $table) {
            $table->string('national_id')->nullable()->comment('Parent National ID')->after('id');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->after('national_id');
        });
    }

    public function down(): void
    {
        Schema::table('children', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'national_id']);
        });

        Schema::table('specialists', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'national_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'role']);
            $table->string('email')->nullable(false)->change();
        });
    }
};

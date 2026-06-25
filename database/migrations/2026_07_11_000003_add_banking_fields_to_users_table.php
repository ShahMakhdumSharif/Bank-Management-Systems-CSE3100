<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('phone', 30)->nullable();
            $table->string('role')->default('customer')->index();
            $table->string('status')->default('pending')->index();
            $table->string('employee_code')->nullable()->unique();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropUnique(['employee_code']);
            $table->dropColumn(['phone', 'role', 'status', 'employee_code']);
        });
    }
};

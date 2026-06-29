<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('city');
            $table->string('address');
            $table->string('phone', 30)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('branch_user', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('position')->nullable();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamps();

            $table->unique(['branch_id', 'user_id']);
        });

        Schema::create('accounts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->restrictOnDelete();
            $table->string('account_number')->unique();
            $table->string('type')->default('savings')->index();
            $table->string('status')->default('active')->index();
            $table->decimal('balance', 15, 2)->default(0);
            $table->text('freeze_reason')->nullable();
            $table->timestamp('frozen_at')->nullable();
            $table->timestamps();
        });

        Schema::create('transactions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('transaction_number')->unique();
            $table->string('type')->index();
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_after', 15, 2);
            $table->string('reference')->nullable()->index();
            $table->text('description')->nullable();
            $table->timestamp('occurred_at')->index();
            $table->timestamps();
        });

        Schema::create('employee_actions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('employee_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('subject_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action_type')->index();
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_actions');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('accounts');
        Schema::dropIfExists('branch_user');
        Schema::dropIfExists('branches');
    }
};

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
            $table->string('branch_code')->unique();
            $table->string('address');
            $table->string('city');
            $table->string('country_code', 2)->default('BD');
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('branch_employee', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamps();

            $table->unique(['branch_id', 'employee_id']);
        });

        Schema::create('accounts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->restrictOnDelete();
            $table->string('account_number')->unique();
            $table->string('account_type')->default('savings')->index();
            $table->decimal('balance', 15, 2)->default(0);
            $table->string('status')->default('active')->index();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('frozen_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('frozen_at')->nullable();
            $table->text('freeze_reason')->nullable();
            $table->timestamps();
        });

        Schema::create('transfer_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('sender_account_id')->constrained('accounts')->cascadeOnDelete();
            $table->foreignId('receiver_account_id')->constrained('accounts')->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->string('status')->default('pending')->index();
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });

        Schema::create('atm_card_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('pending')->index();
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });

        Schema::create('atm_cards', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('atm_card_request_id')->nullable()->constrained()->nullOnDelete();
            $table->string('card_number')->unique();
            $table->string('pin_hash');
            $table->string('status')->default('active')->index();
            $table->unsignedInteger('failed_attempts')->default(0);
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
        });

        Schema::create('transactions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('related_account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->foreignId('transfer_request_id')->nullable()->constrained()->nullOnDelete();
            $table->string('reference')->unique();
            $table->string('type')->index();
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_before', 15, 2);
            $table->decimal('balance_after', 15, 2);
            $table->string('status')->default('completed')->index();
            $table->string('source')->index();
            $table->text('description')->nullable();
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('employee_actions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('employee_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action_type')->index();
            $table->string('subject_type')->nullable()->index();
            $table->unsignedBigInteger('subject_id')->nullable()->index();
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_actions');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('atm_cards');
        Schema::dropIfExists('atm_card_requests');
        Schema::dropIfExists('transfer_requests');
        Schema::dropIfExists('accounts');
        Schema::dropIfExists('branch_employee');
        Schema::dropIfExists('branches');
    }
};

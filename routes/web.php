<?php

use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Customer\TransferRequestController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Employee\AccountManagementController;
use App\Http\Controllers\Employee\CustomerApprovalController;
use App\Http\Controllers\HomeController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/dashboard', DashboardController::class)
    ->middleware('auth')
    ->name('dashboard');

Route::get('/admin/dashboard', [DashboardController::class, 'admin'])
    ->middleware(['auth', 'role:'.User::ROLE_ADMIN])
    ->name('admin.dashboard');

Route::get('/employee/dashboard', [DashboardController::class, 'employee'])
    ->middleware(['auth', 'role:'.User::ROLE_EMPLOYEE])
    ->name('employee.dashboard');

Route::get('/customer/dashboard', [DashboardController::class, 'customer'])
    ->middleware(['auth', 'role:'.User::ROLE_CUSTOMER])
    ->name('customer.dashboard');

Route::middleware(['auth', 'role:'.User::ROLE_CUSTOMER, 'active.account'])
    ->prefix('customer')
    ->name('customer.')
    ->group(function (): void {
        Route::get('transfers', [TransferRequestController::class, 'index'])
            ->name('transfers.index');
        Route::get('transfers/create', [TransferRequestController::class, 'create'])
            ->name('transfers.create');
        Route::post('transfers/confirm', [TransferRequestController::class, 'confirm'])
            ->name('transfers.confirm');
        Route::post('transfers', [TransferRequestController::class, 'store'])
            ->name('transfers.store');
        Route::patch('transfers/{transfer}/cancel', [TransferRequestController::class, 'cancel'])
            ->name('transfers.cancel');
    });

Route::middleware(['auth', 'role:'.User::ROLE_ADMIN])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('branches/{branch}/delete', [BranchController::class, 'confirmDestroy'])
            ->name('branches.confirm-destroy');
        Route::resource('branches', BranchController::class);

        Route::get('employees/{employee}/delete', [EmployeeController::class, 'confirmDestroy'])
            ->name('employees.confirm-destroy');
        Route::resource('employees', EmployeeController::class);
    });

Route::middleware(['auth', 'role:'.User::ROLE_EMPLOYEE])
    ->prefix('employee')
    ->name('employee.')
    ->group(function (): void {
        Route::get('customers/pending', [CustomerApprovalController::class, 'index'])
            ->name('customers.pending');
        Route::get('customers/{customer}', [CustomerApprovalController::class, 'show'])
            ->name('customers.show');
        Route::post('customers/{customer}/approve', [CustomerApprovalController::class, 'approve'])
            ->name('customers.approve');
        Route::post('customers/{customer}/reject', [CustomerApprovalController::class, 'reject'])
            ->name('customers.reject');

        Route::get('accounts', [AccountManagementController::class, 'index'])
            ->name('accounts.index');
        Route::get('accounts/{account}', [AccountManagementController::class, 'show'])
            ->name('accounts.show');
        Route::post('accounts/{account}/freeze', [AccountManagementController::class, 'freeze'])
            ->name('accounts.freeze');
        Route::post('accounts/{account}/unfreeze', [AccountManagementController::class, 'unfreeze'])
            ->name('accounts.unfreeze');
    });

require __DIR__.'/auth.php';

<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/dashboard', DashboardController::class)
    ->middleware('auth')
    ->name('dashboard');

Route::middleware('auth')->group(function (): void {
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');
    Route::get('/employee/dashboard', [DashboardController::class, 'employee'])->name('employee.dashboard');
    Route::get('/customer/dashboard', [DashboardController::class, 'customer'])->name('customer.dashboard');
});

Route::middleware('auth')
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('branches/{branch}/delete', [\App\Http\Controllers\Admin\BranchController::class, 'confirmDestroy'])
            ->name('branches.confirm-destroy');
        Route::resource('branches', \App\Http\Controllers\Admin\BranchController::class);

        Route::get('employees/{employee}/delete', [\App\Http\Controllers\Admin\EmployeeController::class, 'confirmDestroy'])
            ->name('employees.confirm-destroy');
        Route::resource('employees', \App\Http\Controllers\Admin\EmployeeController::class);
    });

Route::middleware('auth')
    ->prefix('employee')
    ->name('employee.')
    ->group(function (): void {
        Route::get('customers/pending', [\App\Http\Controllers\Employee\CustomerApprovalController::class, 'index'])
            ->name('customers.pending');
        Route::get('customers/{customer}', [\App\Http\Controllers\Employee\CustomerApprovalController::class, 'show'])
            ->name('customers.show');
        Route::post('customers/{customer}/approve', [\App\Http\Controllers\Employee\CustomerApprovalController::class, 'approve'])
            ->name('customers.approve');
        Route::post('customers/{customer}/reject', [\App\Http\Controllers\Employee\CustomerApprovalController::class, 'reject'])
            ->name('customers.reject');
    });

require __DIR__.'/auth.php';

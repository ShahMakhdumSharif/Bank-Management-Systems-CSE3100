<?php

use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\ATM\ATMAuthenticationController;
use App\Http\Controllers\ATM\ATMTransactionController;
use App\Http\Controllers\CurrencyExchangeController;
use App\Http\Controllers\Customer\AccountTransactionController;
use App\Http\Controllers\Customer\ATMCardRequestController;
use App\Http\Controllers\Customer\TransferRequestController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Employee\AccountManagementController;
use App\Http\Controllers\Employee\ATMCardManagementController;
use App\Http\Controllers\Employee\ATMCardRequestQueueController;
use App\Http\Controllers\Employee\CustomerApprovalController;
use App\Http\Controllers\Employee\TransactionHistoryController;
use App\Http\Controllers\Employee\TransferApprovalController;
use App\Http\Controllers\HomeController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/atm', [ATMAuthenticationController::class, 'create'])
    ->name('atm.login');
Route::post('/atm/login', [ATMAuthenticationController::class, 'store'])
    ->name('atm.login.store');
Route::middleware('atm.authenticated')
    ->prefix('atm')
    ->name('atm.')
    ->group(function (): void {
        Route::get('session', [ATMAuthenticationController::class, 'session'])
            ->name('session');
        Route::post('deposit', [ATMTransactionController::class, 'deposit'])
            ->name('deposit');
        Route::post('withdraw', [ATMTransactionController::class, 'withdraw'])
            ->name('withdraw');
        Route::get('transactions/{transaction}/receipt', [ATMTransactionController::class, 'receipt'])
            ->name('receipt');
        Route::post('logout', [ATMAuthenticationController::class, 'destroy'])
            ->name('logout');
    });

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
        Route::get('account/transactions', [AccountTransactionController::class, 'create'])
            ->name('account.transactions');
        Route::get('account/transactions/{transaction}', [AccountTransactionController::class, 'show'])
            ->name('account.transactions.show');
        Route::post('account/deposit', [AccountTransactionController::class, 'deposit'])
            ->name('account.deposit');
        Route::post('account/withdraw', [AccountTransactionController::class, 'withdraw'])
            ->name('account.withdraw');

        Route::get('transfers', [TransferRequestController::class, 'index'])
            ->name('transfers.index');
        Route::get('transfers/create', [TransferRequestController::class, 'create'])
            ->middleware('transfer.minimum.balance')
            ->name('transfers.create');
        Route::post('transfers/confirm', [TransferRequestController::class, 'confirm'])
            ->middleware('transfer.minimum.balance')
            ->name('transfers.confirm');
        Route::post('transfers', [TransferRequestController::class, 'store'])
            ->middleware('transfer.minimum.balance')
            ->name('transfers.store');
        Route::patch('transfers/{transfer}/cancel', [TransferRequestController::class, 'cancel'])
            ->name('transfers.cancel');

        Route::get('card-requests', [ATMCardRequestController::class, 'index'])
            ->name('card-requests.index');
        Route::post('card-requests', [ATMCardRequestController::class, 'store'])
            ->name('card-requests.store');

        Route::get('currency-exchange', [CurrencyExchangeController::class, 'index'])
            ->name('currency-exchange.index');
        Route::get('currency-exchange/metadata', [CurrencyExchangeController::class, 'metadata'])
            ->name('currency-exchange.metadata');
        Route::get('currency-exchange/rates', [CurrencyExchangeController::class, 'rates'])
            ->name('currency-exchange.rates');
        Route::post('currency-exchange/convert', [CurrencyExchangeController::class, 'convert'])
            ->name('currency-exchange.convert');
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

        Route::get('transactions', [TransactionHistoryController::class, 'index'])
            ->name('transactions.index');
        Route::get('transactions/{transaction}', [TransactionHistoryController::class, 'show'])
            ->name('transactions.show');

        Route::get('transfers', [TransferApprovalController::class, 'index'])
            ->name('transfers.index');
        Route::get('transfers/{transfer}', [TransferApprovalController::class, 'show'])
            ->name('transfers.show');
        Route::post('transfers/{transfer}/approve', [TransferApprovalController::class, 'approve'])
            ->name('transfers.approve');
        Route::post('transfers/{transfer}/reject', [TransferApprovalController::class, 'reject'])
            ->name('transfers.reject');

        Route::get('card-requests', [ATMCardRequestQueueController::class, 'index'])
            ->name('card-requests.index');
        Route::get('card-requests/{cardRequest}', [ATMCardRequestQueueController::class, 'show'])
            ->name('card-requests.show');
        Route::post('card-requests/{cardRequest}/approve', [ATMCardRequestQueueController::class, 'approve'])
            ->name('card-requests.approve');
        Route::post('card-requests/{cardRequest}/reject', [ATMCardRequestQueueController::class, 'reject'])
            ->name('card-requests.reject');

        Route::get('atm-cards', [ATMCardManagementController::class, 'index'])
            ->name('atm-cards.index');
        Route::post('atm-cards/{card}/block', [ATMCardManagementController::class, 'block'])
            ->name('atm-cards.block');
        Route::post('atm-cards/{card}/unblock', [ATMCardManagementController::class, 'unblock'])
            ->name('atm-cards.unblock');
    });

require __DIR__.'/auth.php';

<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerTransactionRequest;
use App\Services\CustomerTransactionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AccountTransactionController extends Controller
{
    public function create(Request $request): View
    {
        $account = $request->attributes->get('activeAccount');
        $account->load(['transactions' => function ($query): void {
            $query->latest()->limit(8);
        }]);

        return view('customer.account.transactions', [
            'account' => $account,
        ]);
    }

    public function deposit(
        CustomerTransactionRequest $request,
        CustomerTransactionService $transactionService,
    ): RedirectResponse {
        $transactionService->deposit(
            $request->attributes->get('activeAccount'),
            $request->validated('amount'),
        );

        return redirect()
            ->route('customer.account.transactions')
            ->with('status', 'Deposit completed successfully.');
    }

    public function withdraw(
        CustomerTransactionRequest $request,
        CustomerTransactionService $transactionService,
    ): RedirectResponse {
        $transactionService->withdraw(
            $request->attributes->get('activeAccount'),
            $request->validated('amount'),
        );

        return redirect()
            ->route('customer.account.transactions')
            ->with('status', 'Withdrawal completed successfully.');
    }
}

<?php

namespace App\Http\Controllers\ATM;

use App\Http\Controllers\Controller;
use App\Http\Requests\ATMTransactionRequest;
use App\Models\Transaction;
use App\Services\ATMTransactionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ATMTransactionController extends Controller
{
    public function deposit(
        ATMTransactionRequest $request,
        ATMTransactionService $transactionService,
    ): RedirectResponse {
        $transaction = $transactionService->deposit(
            $request->attributes->get('atmCard'),
            $request->validated('amount'),
        );

        return redirect()
            ->route('atm.receipt', $transaction)
            ->with('status', 'ATM deposit completed successfully.');
    }

    public function withdraw(
        ATMTransactionRequest $request,
        ATMTransactionService $transactionService,
    ): RedirectResponse {
        $transaction = $transactionService->withdraw(
            $request->attributes->get('atmCard'),
            $request->validated('amount'),
        );

        return redirect()
            ->route('atm.receipt', $transaction)
            ->with('status', 'ATM withdrawal completed successfully.');
    }

    public function receipt(Request $request, Transaction $transaction): View
    {
        $card = $request->attributes->get('atmCard');

        abort_unless(
            $transaction->account_id === $card->account_id
            && $transaction->source === Transaction::SOURCE_ATM,
            404,
        );

        return view('atm.receipt', [
            'card' => $card,
            'transaction' => $transaction,
        ]);
    }
}

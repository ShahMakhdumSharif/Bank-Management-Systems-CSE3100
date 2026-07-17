<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerTransactionRequest;
use App\Models\Transaction;
use App\Services\CustomerTransactionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AccountTransactionController extends Controller
{
    public function create(Request $request): View
    {
        $account = $request->attributes->get('activeAccount');
        $filters = $this->filters($request);

        $transactions = $account->transactions()
            ->with(['relatedAccount.customer', 'handler'])
            ->when($filters['type'] !== 'all', fn ($query) => $query->where('type', $filters['type']))
            ->when($filters['status'] !== 'all', fn ($query) => $query->where('status', $filters['status']))
            ->when($filters['source'] !== 'all', fn ($query) => $query->where('source', $filters['source']))
            ->when($filters['from'] !== '', fn ($query) => $query->whereDate('created_at', '>=', $filters['from']))
            ->when($filters['to'] !== '', fn ($query) => $query->whereDate('created_at', '<=', $filters['to']))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('customer.account.transactions', [
            'account' => $account,
            'transactions' => $transactions,
            'filters' => $filters,
            'typeOptions' => Transaction::typeOptions(),
            'statusOptions' => Transaction::statusOptions(),
            'sourceOptions' => Transaction::sourceOptions(),
        ]);
    }

    public function show(Request $request, Transaction $transaction): View
    {
        $account = $request->attributes->get('activeAccount');

        if ((int) $transaction->account_id !== (int) $account->id) {
            throw new NotFoundHttpException();
        }

        $transaction->load(['relatedAccount.customer', 'handler', 'transferRequest']);

        return view('customer.account.transaction-show', [
            'account' => $account,
            'transaction' => $transaction,
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

    /**
     * @return array{type: string, status: string, source: string, from: string, to: string}
     */
    private function filters(Request $request): array
    {
        $type = (string) $request->query('type', 'all');
        $status = (string) $request->query('status', 'all');
        $source = (string) $request->query('source', 'all');

        return [
            'type' => array_key_exists($type, Transaction::typeOptions()) ? $type : 'all',
            'status' => array_key_exists($status, Transaction::statusOptions()) ? $status : 'all',
            'source' => array_key_exists($source, Transaction::sourceOptions()) ? $source : 'all',
            'from' => $this->dateFilter($request, 'from'),
            'to' => $this->dateFilter($request, 'to'),
        ];
    }

    private function dateFilter(Request $request, string $key): string
    {
        $value = (string) $request->query($key, '');

        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) ? $value : '';
    }
}

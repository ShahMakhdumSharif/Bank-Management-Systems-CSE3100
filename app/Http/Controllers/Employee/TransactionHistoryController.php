<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class TransactionHistoryController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $this->filters($request);

        $transactions = Transaction::query()
            ->with(['account.customer', 'account.branch', 'relatedAccount.customer', 'handler'])
            ->when($filters['search'] !== '', function ($query) use ($filters): void {
                $search = $filters['search'];

                $query->where(function ($query) use ($search): void {
                    $query->where('reference', 'like', "%{$search}%")
                        ->orWhereHas('account', function ($query) use ($search): void {
                            $query->where('account_number', 'like', "%{$search}%")
                                ->orWhereHas('customer', function ($query) use ($search): void {
                                    $query->where('name', 'like', "%{$search}%")
                                        ->orWhere('email', 'like', "%{$search}%")
                                        ->orWhere('phone', 'like', "%{$search}%");
                                });
                        });
                });
            })
            ->when($filters['type'] !== 'all', fn ($query) => $query->where('type', $filters['type']))
            ->when($filters['status'] !== 'all', fn ($query) => $query->where('status', $filters['status']))
            ->when($filters['source'] !== 'all', fn ($query) => $query->where('source', $filters['source']))
            ->when($filters['account_status'] !== 'all', function ($query) use ($filters): void {
                $query->whereHas('account', fn ($query) => $query->where('status', $filters['account_status']));
            })
            ->when($filters['from'] !== '', fn ($query) => $query->whereDate('created_at', '>=', $filters['from']))
            ->when($filters['to'] !== '', fn ($query) => $query->whereDate('created_at', '<=', $filters['to']))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('employee.transactions.index', [
            'transactions' => $transactions,
            'filters' => $filters,
            'typeOptions' => Transaction::typeOptions(),
            'statusOptions' => Transaction::statusOptions(),
            'sourceOptions' => Transaction::sourceOptions(),
            'accountStatusOptions' => [
                Account::STATUS_ACTIVE => 'Active accounts',
                Account::STATUS_FROZEN => 'Frozen accounts',
                Account::STATUS_CLOSED => 'Closed accounts',
            ],
        ]);
    }

    public function show(Transaction $transaction): View
    {
        $transaction->load(['account.customer', 'account.branch', 'relatedAccount.customer', 'handler', 'transferRequest']);

        return view('employee.transactions.show', [
            'transaction' => $transaction,
        ]);
    }

    /**
     * @return array{search: string, type: string, status: string, source: string, account_status: string, from: string, to: string}
     */
    private function filters(Request $request): array
    {
        $type = (string) $request->query('type', 'all');
        $status = (string) $request->query('status', 'all');
        $source = (string) $request->query('source', 'all');
        $accountStatus = (string) $request->query('account_status', 'all');

        return [
            'search' => trim((string) $request->query('search')),
            'type' => array_key_exists($type, Transaction::typeOptions()) ? $type : 'all',
            'status' => array_key_exists($status, Transaction::statusOptions()) ? $status : 'all',
            'source' => array_key_exists($source, Transaction::sourceOptions()) ? $source : 'all',
            'account_status' => in_array($accountStatus, [
                Account::STATUS_ACTIVE,
                Account::STATUS_FROZEN,
                Account::STATUS_CLOSED,
            ], true) ? $accountStatus : 'all',
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

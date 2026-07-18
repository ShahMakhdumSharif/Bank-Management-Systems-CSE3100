<?php

namespace App\Services;

use App\Models\Account;
use App\Models\EmployeeAction;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AdminAnalyticsService
{
    /**
     * @return array<string, mixed>
     */
    public function summary(): array
    {
        return [
            'customers' => $this->customerStatistics(),
            'accounts' => $this->accountStatistics(),
            'deposits' => $this->depositStatistics(),
            'employeePerformance' => $this->employeePerformance(),
        ];
    }

    /**
     * @return array<string, int>
     */
    private function customerStatistics(): array
    {
        return [
            'total' => User::query()
                ->where('role', User::ROLE_CUSTOMER)
                ->count(),
            'pending' => User::query()
                ->where('role', User::ROLE_CUSTOMER)
                ->where('status', User::STATUS_PENDING)
                ->count(),
            'approved' => User::query()
                ->where('role', User::ROLE_CUSTOMER)
                ->where('status', User::STATUS_APPROVED)
                ->count(),
            'rejected' => User::query()
                ->where('role', User::ROLE_CUSTOMER)
                ->where('status', User::STATUS_REJECTED)
                ->count(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function accountStatistics(): array
    {
        return [
            'total' => Account::query()->count(),
            'active' => Account::query()
                ->where('status', Account::STATUS_ACTIVE)
                ->count(),
            'frozen' => Account::query()
                ->where('status', Account::STATUS_FROZEN)
                ->count(),
            'totalBalance' => Account::query()->sum('balance'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function depositStatistics(): array
    {
        $depositTypes = [
            Transaction::TYPE_ATM_DEPOSIT,
            Transaction::TYPE_CUSTOMER_DEPOSIT,
        ];

        return [
            'completedCount' => Transaction::query()
                ->whereIn('type', $depositTypes)
                ->where('status', Transaction::STATUS_COMPLETED)
                ->count(),
            'completedTotal' => Transaction::query()
                ->whereIn('type', $depositTypes)
                ->where('status', Transaction::STATUS_COMPLETED)
                ->sum('amount'),
        ];
    }

    /**
     * Query Builder keeps the grouped performance report compact and efficient.
     *
     * @return Collection<int, object>
     */
    private function employeePerformance(): Collection
    {
        return DB::table('users')
            ->leftJoin('employee_actions', 'employee_actions.employee_id', '=', 'users.id')
            ->select([
                'users.id',
                'users.name',
                'users.email',
                DB::raw($this->sumActionsSql([
                    EmployeeAction::TYPE_CUSTOMER_APPROVED,
                    EmployeeAction::TYPE_CUSTOMER_REJECTED,
                ], 'customer_decisions')),
                DB::raw($this->sumActionsSql([
                    EmployeeAction::TYPE_TRANSFER_APPROVED,
                    EmployeeAction::TYPE_TRANSFER_REJECTED,
                ], 'transfer_decisions')),
                DB::raw($this->sumActionsSql([
                    EmployeeAction::TYPE_ATM_CARD_APPROVED,
                    EmployeeAction::TYPE_ATM_CARD_REJECTED,
                    EmployeeAction::TYPE_ATM_CARD_BLOCKED,
                    EmployeeAction::TYPE_ATM_CARD_UNBLOCKED,
                ], 'atm_card_actions')),
                DB::raw($this->sumActionsSql([
                    EmployeeAction::TYPE_ACCOUNT_FROZEN,
                    EmployeeAction::TYPE_ACCOUNT_UNFROZEN,
                ], 'account_status_actions')),
                DB::raw('COUNT(employee_actions.id) as total_actions'),
            ])
            ->where('users.role', User::ROLE_EMPLOYEE)
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderByDesc('total_actions')
            ->orderBy('users.name')
            ->limit(8)
            ->get();
    }

    /**
     * @param  array<int, string>  $actionTypes
     */
    private function sumActionsSql(array $actionTypes, string $alias): string
    {
        $quotedTypes = collect($actionTypes)
            ->map(fn (string $type): string => DB::getPdo()->quote($type))
            ->implode(', ');

        return "SUM(CASE WHEN employee_actions.action_type IN ({$quotedTypes}) THEN 1 ELSE 0 END) as {$alias}";
    }
}

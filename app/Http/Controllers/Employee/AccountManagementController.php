<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\FreezeAccountRequest;
use App\Models\Account;
use App\Models\EmployeeAction;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AccountManagementController extends Controller
{
    private const PAGE_SIZE_COOKIE = 'employee_account_page_size';

    /**
     * @var list<int>
     */
    private const PAGE_SIZE_OPTIONS = [10, 25, 50];

    public function index(Request $request): Response
    {
        $search = trim((string) $request->query('search'));
        $status = (string) $request->query('status', 'all');
        $pageSize = $this->pageSize($request);

        $accounts = Account::query()
            ->with(['customer', 'branch'])
            ->whereHas('customer', function ($query): void {
                $query->where('role', User::ROLE_CUSTOMER);
            })
            ->when($status !== 'all', function ($query) use ($status): void {
                $query->where('status', $status);
            })
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('account_number', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($query) use ($search): void {
                            $query->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate($pageSize)
            ->withQueryString();

        $response = response()->view('employee.accounts.index', [
            'accounts' => $accounts,
            'search' => $search,
            'status' => $status,
            'pageSize' => $pageSize,
            'pageSizeOptions' => self::PAGE_SIZE_OPTIONS,
        ]);

        if ($request->query->has('page_size')) {
            $response->withCookie(cookie()->forever(self::PAGE_SIZE_COOKIE, (string) $pageSize));
        }

        return $response;
    }

    public function show(Account $account): View
    {
        $account->load(['customer', 'branch', 'freezer', 'subjectActions.employee']);

        return view('employee.accounts.show', [
            'account' => $account,
        ]);
    }

    public function freeze(FreezeAccountRequest $request, Account $account): RedirectResponse
    {
        $employee = $request->user();
        $reason = $request->validated('freeze_reason');

        DB::transaction(function () use ($account, $employee, $reason, $request): void {
            $lockedAccount = Account::query()
                ->whereKey($account->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $lockedAccount->isActive()) {
                throw ValidationException::withMessages([
                    'account' => 'Only active accounts can be frozen.',
                ]);
            }

            $lockedAccount->update([
                'status' => Account::STATUS_FROZEN,
                'frozen_by' => $employee->id,
                'frozen_at' => now(),
                'freeze_reason' => $reason,
            ]);

            EmployeeAction::create([
                'employee_id' => $employee->id,
                'action_type' => EmployeeAction::TYPE_ACCOUNT_FROZEN,
                'subject_type' => Account::class,
                'subject_id' => $lockedAccount->id,
                'description' => $reason,
                'metadata' => [
                    'customer_id' => $lockedAccount->user_id,
                    'account_number' => $lockedAccount->account_number,
                    'previous_status' => Account::STATUS_ACTIVE,
                    'new_status' => Account::STATUS_FROZEN,
                ],
                'ip_address' => $request->ip(),
            ]);
        });

        return redirect()
            ->route('employee.accounts.show', $account)
            ->with('status', 'Account frozen successfully.');
    }

    public function unfreeze(Request $request, Account $account): RedirectResponse
    {
        $employee = $request->user();

        DB::transaction(function () use ($account, $employee, $request): void {
            $lockedAccount = Account::query()
                ->whereKey($account->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $lockedAccount->isFrozen()) {
                throw ValidationException::withMessages([
                    'account' => 'Only frozen accounts can be unfrozen.',
                ]);
            }

            $previousReason = $lockedAccount->freeze_reason;

            $lockedAccount->update([
                'status' => Account::STATUS_ACTIVE,
                'frozen_by' => null,
                'frozen_at' => null,
                'freeze_reason' => null,
            ]);

            EmployeeAction::create([
                'employee_id' => $employee->id,
                'action_type' => EmployeeAction::TYPE_ACCOUNT_UNFROZEN,
                'subject_type' => Account::class,
                'subject_id' => $lockedAccount->id,
                'description' => 'Account returned to active status.',
                'metadata' => [
                    'customer_id' => $lockedAccount->user_id,
                    'account_number' => $lockedAccount->account_number,
                    'previous_status' => Account::STATUS_FROZEN,
                    'new_status' => Account::STATUS_ACTIVE,
                    'previous_freeze_reason' => $previousReason,
                ],
                'ip_address' => $request->ip(),
            ]);
        });

        return redirect()
            ->route('employee.accounts.show', $account)
            ->with('status', 'Account unfrozen successfully.');
    }

    private function pageSize(Request $request): int
    {
        $requestedPageSize = (int) $request->query('page_size');

        if (in_array($requestedPageSize, self::PAGE_SIZE_OPTIONS, true)) {
            return $requestedPageSize;
        }

        $cookiePageSize = (int) $request->cookie(self::PAGE_SIZE_COOKIE, 10);

        if (in_array($cookiePageSize, self::PAGE_SIZE_OPTIONS, true)) {
            return $cookiePageSize;
        }

        return 10;
    }
}

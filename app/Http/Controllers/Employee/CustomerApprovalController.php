<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApproveCustomerRequest;
use App\Http\Requests\RejectCustomerRequest;
use App\Models\Account;
use App\Models\Branch;
use App\Models\EmployeeAction;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CustomerApprovalController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeEmployee($request);

        $search = trim((string) $request->query('search'));

        $customers = User::query()
            ->where('role', User::ROLE_CUSTOMER)
            ->where('status', User::STATUS_PENDING)
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('employee.customers.index', [
            'customers' => $customers,
            'search' => $search,
        ]);
    }

    public function show(Request $request, User $customer): View
    {
        $this->authorizeEmployee($request);
        $this->ensureCustomer($customer);

        $customer->load(['accounts.branch', 'subjectActions.employee']);

        return view('employee.customers.show', [
            'customer' => $customer,
            'branches' => $this->activeBranches(),
        ]);
    }

    public function approve(ApproveCustomerRequest $request, User $customer): RedirectResponse
    {
        $employee = $request->user();
        $data = $request->validated();
        $branch = Branch::query()
            ->whereKey($data['branch_id'])
            ->where('is_active', true)
            ->firstOrFail();

        DB::transaction(function () use ($customer, $employee, $data, $branch, $request): void {
            $lockedCustomer = User::query()
                ->whereKey($customer->id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->ensureCustomer($lockedCustomer);
            $this->ensurePending($lockedCustomer);

            $lockedCustomer->update([
                'status' => User::STATUS_APPROVED,
            ]);

            $account = Account::create([
                'user_id' => $lockedCustomer->id,
                'branch_id' => $branch->id,
                'account_number' => $this->generateAccountNumber(),
                'account_type' => $data['account_type'],
                'balance' => 0,
                'status' => Account::STATUS_ACTIVE,
                'approved_by' => $employee->id,
                'approved_at' => now(),
            ]);

            EmployeeAction::create([
                'employee_id' => $employee->id,
                'action_type' => EmployeeAction::TYPE_CUSTOMER_APPROVED,
                'subject_type' => User::class,
                'subject_id' => $lockedCustomer->id,
                'description' => 'Approved customer application and created account.',
                'metadata' => [
                    'branch_id' => $branch->id,
                    'account_id' => $account->id,
                    'account_number' => $account->account_number,
                    'account_type' => $account->account_type,
                ],
                'ip_address' => $request->ip(),
            ]);
        });

        return redirect()
            ->route('employee.customers.show', $customer)
            ->with('status', 'Customer approved and account created successfully.');
    }

    public function reject(RejectCustomerRequest $request, User $customer): RedirectResponse
    {
        $employee = $request->user();
        $data = $request->validated();
        $reason = $data['rejection_reason'];

        DB::transaction(function () use ($customer, $employee, $reason, $request): void {
            $lockedCustomer = User::query()
                ->whereKey($customer->id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->ensureCustomer($lockedCustomer);
            $this->ensurePending($lockedCustomer);

            $lockedCustomer->update([
                'status' => User::STATUS_REJECTED,
            ]);

            EmployeeAction::create([
                'employee_id' => $employee->id,
                'action_type' => EmployeeAction::TYPE_CUSTOMER_REJECTED,
                'subject_type' => User::class,
                'subject_id' => $lockedCustomer->id,
                'description' => $reason,
                'metadata' => [
                    'reason' => $reason,
                ],
                'ip_address' => $request->ip(),
            ]);
        });

        return redirect()
            ->route('employee.customers.show', $customer)
            ->with('status', 'Customer application rejected.');
    }

    private function authorizeEmployee(Request $request): void
    {
        abort_unless($request->user()?->isEmployee(), 403);
    }

    private function activeBranches()
    {
        return Branch::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'branch_code', 'city']);
    }

    private function ensureCustomer(User $customer): void
    {
        abort_unless($customer->isCustomer(), 404);
    }

    private function ensurePending(User $customer): void
    {
        if ($customer->status !== User::STATUS_PENDING) {
            throw ValidationException::withMessages([
                'customer' => 'Only pending customer applications can be reviewed.',
            ]);
        }
    }

    private function generateAccountNumber(): string
    {
        $nextId = ((int) Account::query()->lockForUpdate()->max('id')) + 1;

        do {
            $accountNumber = '10'.now()->format('ymd').str_pad((string) $nextId, 4, '0', STR_PAD_LEFT);
            $nextId++;
        } while (Account::query()->where('account_number', $accountNumber)->exists());

        return $accountNumber;
    }
}

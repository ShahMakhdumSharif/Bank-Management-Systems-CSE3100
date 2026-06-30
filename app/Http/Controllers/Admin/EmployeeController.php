<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeAdmin($request);

        $search = trim((string) $request->query('search'));

        $employees = User::query()
            ->where('role', User::ROLE_EMPLOYEE)
            ->withCount('branches')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('employee_code', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.employees.index', [
            'employees' => $employees,
            'search' => $search,
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorizeAdmin($request);

        return view('admin.employees.create', [
            'employee' => new User(['status' => User::STATUS_APPROVED]),
            'branches' => $this->branchOptions(),
            'assignedBranchIds' => [],
        ]);
    }

    public function store(StoreEmployeeRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['branch_ids', 'branch_position']);
        $data['role'] = User::ROLE_EMPLOYEE;

        $employee = User::create($data);
        $this->syncBranches($employee, $request->validated('branch_ids', []), $request->validated('branch_position'));

        return redirect()
            ->route('admin.employees.show', $employee)
            ->with('status', 'Employee created successfully.');
    }

    public function show(Request $request, User $employee): View
    {
        $this->authorizeAdmin($request);
        $this->ensureEmployee($employee);

        $employee->load(['branches' => fn ($query) => $query->orderBy('name')]);

        return view('admin.employees.show', [
            'employee' => $employee,
        ]);
    }

    public function edit(Request $request, User $employee): View
    {
        $this->authorizeAdmin($request);
        $this->ensureEmployee($employee);

        return view('admin.employees.edit', [
            'employee' => $employee->load('branches'),
            'branches' => $this->branchOptions(),
            'assignedBranchIds' => $employee->branches->pluck('id')->all(),
        ]);
    }

    public function update(UpdateEmployeeRequest $request, User $employee): RedirectResponse
    {
        $this->ensureEmployee($employee);

        $data = $request->safe()->except(['branch_ids', 'branch_position']);

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $employee->update($data);
        $this->syncBranches($employee, $request->validated('branch_ids', []), $request->validated('branch_position'));

        return redirect()
            ->route('admin.employees.show', $employee)
            ->with('status', 'Employee updated successfully.');
    }

    public function confirmDestroy(Request $request, User $employee): View
    {
        $this->authorizeAdmin($request);
        $this->ensureEmployee($employee);

        $employee->loadCount(['branches', 'employeeActions', 'performedTransactions']);

        return view('admin.employees.confirm-destroy', [
            'employee' => $employee,
        ]);
    }

    public function destroy(Request $request, User $employee): RedirectResponse
    {
        $this->authorizeAdmin($request);
        $this->ensureEmployee($employee);

        $employee->delete();

        return redirect()
            ->route('admin.employees.index')
            ->with('status', 'Employee deleted successfully.');
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()?->isMasterAdmin(), 403);
    }

    private function ensureEmployee(User $employee): void
    {
        abort_unless($employee->isEmployee(), 404);
    }

    private function branchOptions()
    {
        return Branch::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'city']);
    }

    private function syncBranches(User $employee, array $branchIds, ?string $position): void
    {
        $employee->branches()->sync(collect($branchIds)
            ->mapWithKeys(fn ($branchId) => [
                $branchId => [
                    'position' => $position,
                    'assigned_at' => now(),
                ],
            ])
            ->all());
    }
}

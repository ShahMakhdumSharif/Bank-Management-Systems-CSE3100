<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBranchRequest;
use App\Http\Requests\UpdateBranchRequest;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeAdmin($request);

        $search = trim((string) $request->query('search'));

        $branches = Branch::query()
            ->withCount(['accounts', 'employees'])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('branch_code', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.branches.index', [
            'branches' => $branches,
            'search' => $search,
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorizeAdmin($request);

        return view('admin.branches.create', [
            'branch' => new Branch(['is_active' => true]),
            'employees' => $this->employeeOptions(),
            'assignedEmployeeIds' => [],
        ]);
    }

    public function store(StoreBranchRequest $request): RedirectResponse
    {
        $branch = Branch::create($request->safe()->except('employee_ids'));

        $this->syncEmployees($branch, $request->validated('employee_ids', []));

        return redirect()
            ->route('admin.branches.show', $branch)
            ->with('status', 'Branch created successfully.');
    }

    public function show(Request $request, Branch $branch): View
    {
        $this->authorizeAdmin($request);

        $branch->load(['employees' => fn ($query) => $query->orderBy('name')])
            ->loadCount(['accounts', 'employees']);

        return view('admin.branches.show', [
            'branch' => $branch,
        ]);
    }

    public function edit(Request $request, Branch $branch): View
    {
        $this->authorizeAdmin($request);

        return view('admin.branches.edit', [
            'branch' => $branch->load('employees'),
            'employees' => $this->employeeOptions(),
            'assignedEmployeeIds' => $branch->employees->pluck('id')->all(),
        ]);
    }

    public function update(UpdateBranchRequest $request, Branch $branch): RedirectResponse
    {
        $branch->update($request->safe()->except('employee_ids'));

        $this->syncEmployees($branch, $request->validated('employee_ids', []));

        return redirect()
            ->route('admin.branches.show', $branch)
            ->with('status', 'Branch updated successfully.');
    }

    public function confirmDestroy(Request $request, Branch $branch): View
    {
        $this->authorizeAdmin($request);

        $branch->loadCount(['accounts', 'employees']);

        return view('admin.branches.confirm-destroy', [
            'branch' => $branch,
        ]);
    }

    public function destroy(Request $request, Branch $branch): RedirectResponse
    {
        $this->authorizeAdmin($request);

        if ($branch->accounts()->exists()) {
            return redirect()
                ->route('admin.branches.show', $branch)
                ->with('error', 'Branches with customer accounts cannot be deleted.');
        }

        $branch->delete();

        return redirect()
            ->route('admin.branches.index')
            ->with('status', 'Branch deleted successfully.');
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()?->isMasterAdmin(), 403);
    }

    private function employeeOptions()
    {
        return User::query()
            ->where('role', User::ROLE_EMPLOYEE)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'employee_code']);
    }

    private function syncEmployees(Branch $branch, array $employeeIds): void
    {
        $branch->employees()->sync(collect($employeeIds)
            ->mapWithKeys(fn ($employeeId) => [
                $employeeId => ['assigned_at' => now()],
            ])
            ->all());
    }
}

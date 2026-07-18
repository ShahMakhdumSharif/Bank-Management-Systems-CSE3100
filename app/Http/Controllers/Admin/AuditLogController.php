<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeAction;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $actionType = trim((string) $request->query('action_type'));
        $actionTypes = EmployeeAction::actionTypeOptions();

        $auditLogs = EmployeeAction::query()
            ->with('employee')
            ->when(array_key_exists($actionType, $actionTypes), function ($query) use ($actionType): void {
                $query->where('action_type', $actionType);
            })
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('action_type', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('employee', function ($query) use ($search): void {
                            $query->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%")
                                ->orWhere('employee_code', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.audit-logs.index', [
            'auditLogs' => $auditLogs,
            'actionTypes' => $actionTypes,
            'selectedActionType' => array_key_exists($actionType, $actionTypes) ? $actionType : '',
            'search' => $search,
        ]);
    }

    public function show(EmployeeAction $auditLog): View
    {
        $auditLog->load(['employee', 'subject']);

        return view('admin.audit-logs.show', [
            'auditLog' => $auditLog,
        ]);
    }
}

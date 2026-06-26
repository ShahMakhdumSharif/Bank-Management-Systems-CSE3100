<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class DashboardController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        return redirect()->route($request->user()->roleDashboardRoute());
    }

    public function admin(Request $request): View
    {
        abort_unless($request->user()->isMasterAdmin(), 403);

        return view('dashboards.admin', [
            'user' => $request->user(),
            'roles' => [
                User::ROLE_ADMIN => 'Master admins',
                User::ROLE_EMPLOYEE => 'Employees',
                User::ROLE_CUSTOMER => 'Customers',
            ],
        ]);
    }

    public function employee(Request $request): View
    {
        abort_unless($request->user()->isEmployee(), 403);

        return view('dashboards.employee', [
            'user' => $request->user(),
        ]);
    }

    public function customer(Request $request): View
    {
        abort_unless($request->user()->isCustomer(), 403);

        return view('dashboards.customer', [
            'user' => $request->user(),
        ]);
    }
}

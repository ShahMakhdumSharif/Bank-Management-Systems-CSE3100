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
        return view('dashboards.employee', [
            'user' => $request->user(),
        ]);
    }

    public function customer(Request $request): View
    {
        $user = $request->user()->load('account');
        $account = $user->account;
        $transferRequests = $account
            ? $account->outgoingTransferRequests()
                ->with(['receiverAccount.customer'])
                ->latest()
                ->limit(5)
                ->get()
            : collect();
        $cardRequests = $account
            ? $account->atmCardRequests()
                ->with('handler')
                ->latest()
                ->limit(5)
                ->get()
            : collect();

        return view('dashboards.customer', [
            'user' => $user,
            'account' => $account,
            'transferRequests' => $transferRequests,
            'cardRequests' => $cardRequests,
        ]);
    }
}

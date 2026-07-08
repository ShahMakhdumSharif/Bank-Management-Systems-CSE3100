<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\ATMCardRequestService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ATMCardRequestController extends Controller
{
    public function index(Request $request): View
    {
        $account = $request->attributes->get('activeAccount');

        $cardRequests = $account->atmCardRequests()
            ->with('handler')
            ->latest()
            ->paginate(10);

        return view('customer.atm-card-requests.index', [
            'account' => $account,
            'cardRequests' => $cardRequests,
        ]);
    }

    public function store(Request $request, ATMCardRequestService $cardRequestService): RedirectResponse
    {
        $cardRequestService->createPending($request->attributes->get('activeAccount'));

        return redirect()
            ->route('customer.card-requests.index')
            ->with('status', 'ATM-card request submitted for employee review.');
    }
}

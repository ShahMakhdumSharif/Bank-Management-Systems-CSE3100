<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\ATMCardRequest;
use Illuminate\Contracts\View\View;

class ATMCardRequestQueueController extends Controller
{
    public function index(): View
    {
        $cardRequests = ATMCardRequest::query()
            ->with(['account.customer', 'account.branch'])
            ->where('status', ATMCardRequest::STATUS_PENDING)
            ->oldest('requested_at')
            ->paginate(10);

        return view('employee.atm-card-requests.index', [
            'cardRequests' => $cardRequests,
        ]);
    }
}

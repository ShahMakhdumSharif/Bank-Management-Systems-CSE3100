<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\RejectATMCardRequest;
use App\Models\EmployeeAction;
use App\Models\ATMCardRequest;
use App\Services\ATMCardRequestService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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

    public function show(ATMCardRequest $cardRequest): View
    {
        $cardRequest->load(['account.customer', 'account.branch', 'handler', 'atmCard']);

        return view('employee.atm-card-requests.show', [
            'cardRequest' => $cardRequest,
            'issuedPin' => session('issued_pin'),
            'issuedCardId' => session('issued_card_id'),
        ]);
    }

    public function approve(
        Request $request,
        ATMCardRequest $cardRequest,
        ATMCardRequestService $cardRequestService,
    ): RedirectResponse {
        $employee = $request->user();
        $issued = $cardRequestService->approve($cardRequest, $employee->id);

        EmployeeAction::create([
            'employee_id' => $employee->id,
            'action_type' => EmployeeAction::TYPE_ATM_CARD_APPROVED,
            'subject_type' => ATMCardRequest::class,
            'subject_id' => $cardRequest->id,
            'description' => 'Approved ATM-card request and issued card.',
            'metadata' => [
                'atm_card_id' => $issued['card']->id,
                'account_id' => $issued['card']->account_id,
                'card_number' => $issued['card']->card_number,
            ],
            'ip_address' => $request->ip(),
        ]);

        return redirect()
            ->route('employee.card-requests.show', $cardRequest)
            ->with('status', 'ATM card approved. Show the PIN to the customer once.')
            ->with('issued_pin', $issued['pin'])
            ->with('issued_card_id', $issued['card']->id);
    }

    public function reject(
        RejectATMCardRequest $request,
        ATMCardRequest $cardRequest,
        ATMCardRequestService $cardRequestService,
    ): RedirectResponse {
        $employee = $request->user();
        $reason = $request->validated('rejection_reason');

        $cardRequestService->reject($cardRequest, $employee->id, $reason);

        EmployeeAction::create([
            'employee_id' => $employee->id,
            'action_type' => EmployeeAction::TYPE_ATM_CARD_REJECTED,
            'subject_type' => ATMCardRequest::class,
            'subject_id' => $cardRequest->id,
            'description' => $reason,
            'metadata' => [
                'account_id' => $cardRequest->account_id,
            ],
            'ip_address' => $request->ip(),
        ]);

        return redirect()
            ->route('employee.card-requests.show', $cardRequest)
            ->with('status', 'ATM-card request rejected.');
    }
}

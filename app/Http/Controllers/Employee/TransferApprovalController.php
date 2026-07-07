<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\RejectTransferRequest;
use App\Models\EmployeeAction;
use App\Models\TransferRequest;
use App\Services\TransferRequestService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TransferApprovalController extends Controller
{
    public function index(): View
    {
        $transferRequests = TransferRequest::query()
            ->with(['senderAccount.customer', 'receiverAccount.customer'])
            ->where('status', TransferRequest::STATUS_PENDING)
            ->oldest('requested_at')
            ->paginate(10);

        return view('employee.transfers.index', [
            'transferRequests' => $transferRequests,
        ]);
    }

    public function show(TransferRequest $transfer): View
    {
        $transfer->load(['senderAccount.customer', 'receiverAccount.customer', 'handler']);

        return view('employee.transfers.show', [
            'transfer' => $transfer,
        ]);
    }

    public function approve(
        Request $request,
        TransferRequest $transfer,
        TransferRequestService $transferService,
    ): RedirectResponse {
        $employee = $request->user();

        $transferService->approve($transfer, $employee->id);

        EmployeeAction::create([
            'employee_id' => $employee->id,
            'action_type' => EmployeeAction::TYPE_TRANSFER_APPROVED,
            'subject_type' => TransferRequest::class,
            'subject_id' => $transfer->id,
            'description' => 'Approved transfer request.',
            'metadata' => [
                'amount' => $transfer->amount,
                'sender_account_id' => $transfer->sender_account_id,
                'receiver_account_id' => $transfer->receiver_account_id,
            ],
            'ip_address' => $request->ip(),
        ]);

        return redirect()
            ->route('employee.transfers.show', $transfer)
            ->with('status', 'Transfer request approved successfully.');
    }

    public function reject(
        RejectTransferRequest $request,
        TransferRequest $transfer,
        TransferRequestService $transferService,
    ): RedirectResponse {
        $employee = $request->user();
        $reason = $request->validated('rejection_reason');

        $transferService->reject($transfer, $employee->id, $reason);

        EmployeeAction::create([
            'employee_id' => $employee->id,
            'action_type' => EmployeeAction::TYPE_TRANSFER_REJECTED,
            'subject_type' => TransferRequest::class,
            'subject_id' => $transfer->id,
            'description' => $reason,
            'metadata' => [
                'amount' => $transfer->amount,
                'sender_account_id' => $transfer->sender_account_id,
                'receiver_account_id' => $transfer->receiver_account_id,
            ],
            'ip_address' => $request->ip(),
        ]);

        return redirect()
            ->route('employee.transfers.show', $transfer)
            ->with('status', 'Transfer request rejected and sender refunded.');
    }
}

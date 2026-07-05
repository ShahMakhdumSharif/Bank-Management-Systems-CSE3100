<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransferRequest;
use App\Models\TransferRequest;
use App\Services\TransferRequestService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TransferRequestController extends Controller
{
    public function index(Request $request): View
    {
        $account = $request->attributes->get('activeAccount');

        $transferRequests = TransferRequest::query()
            ->with(['receiverAccount.customer', 'handler'])
            ->where('sender_account_id', $account->id)
            ->latest()
            ->paginate(10);

        return view('customer.transfers.index', [
            'account' => $account,
            'transferRequests' => $transferRequests,
        ]);
    }

    public function create(Request $request): View
    {
        return view('customer.transfers.create', [
            'account' => $request->attributes->get('activeAccount'),
        ]);
    }

    public function confirm(StoreTransferRequest $request, TransferRequestService $transferService): View
    {
        $account = $request->attributes->get('activeAccount');
        $validated = $request->validated();
        $preview = $transferService->validateRequest(
            $account,
            $validated['receiver_account_number'],
            $validated['amount'],
        );

        return view('customer.transfers.confirm', [
            'account' => $account,
            'receiverAccount' => $preview['receiver']->load('customer', 'branch'),
            'amount' => $preview['amount'],
        ]);
    }

    public function store(StoreTransferRequest $request, TransferRequestService $transferService): RedirectResponse
    {
        $account = $request->attributes->get('activeAccount');
        $validated = $request->validated();

        $transferService->createPending(
            $account,
            $validated['receiver_account_number'],
            $validated['amount'],
        );

        return redirect()
            ->route('customer.transfers.index')
            ->with('status', 'Transfer request submitted for employee approval.');
    }

    public function cancel(Request $request, TransferRequest $transfer, TransferRequestService $transferService): RedirectResponse
    {
        $transfer->load('senderAccount');
        $transferService->cancel($request->attributes->get('activeAccount'), $transfer);

        return redirect()
            ->route('customer.transfers.index')
            ->with('status', 'Pending transfer request cancelled.');
    }
}

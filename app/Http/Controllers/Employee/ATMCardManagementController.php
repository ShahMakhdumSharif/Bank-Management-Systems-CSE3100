<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\ATMCard;
use App\Models\EmployeeAction;
use App\Services\ATMCardRequestService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ATMCardManagementController extends Controller
{
    public function index(): View
    {
        $cards = ATMCard::query()
            ->with(['account.customer', 'issuer'])
            ->latest('issued_at')
            ->paginate(10);

        return view('employee.atm-cards.index', [
            'cards' => $cards,
        ]);
    }

    public function block(Request $request, ATMCard $card, ATMCardRequestService $cardRequestService): RedirectResponse
    {
        $cardRequestService->block($card);
        $this->recordAction($request, $card, EmployeeAction::TYPE_ATM_CARD_BLOCKED, 'Blocked ATM card.');

        return redirect()
            ->route('employee.atm-cards.index')
            ->with('status', 'ATM card blocked successfully.');
    }

    public function unblock(Request $request, ATMCard $card, ATMCardRequestService $cardRequestService): RedirectResponse
    {
        $cardRequestService->unblock($card);
        $this->recordAction($request, $card, EmployeeAction::TYPE_ATM_CARD_UNBLOCKED, 'Unblocked ATM card.');

        return redirect()
            ->route('employee.atm-cards.index')
            ->with('status', 'ATM card unblocked successfully.');
    }

    private function recordAction(Request $request, ATMCard $card, string $actionType, string $description): void
    {
        EmployeeAction::create([
            'employee_id' => $request->user()->id,
            'action_type' => $actionType,
            'subject_type' => ATMCard::class,
            'subject_id' => $card->id,
            'description' => $description,
            'metadata' => [
                'account_id' => $card->account_id,
                'card_number' => $card->card_number,
                'status' => $card->status,
            ],
            'ip_address' => $request->ip(),
        ]);
    }
}

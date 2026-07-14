<?php

namespace App\Http\Controllers\ATM;

use App\Http\Controllers\Controller;
use App\Http\Requests\ATMLoginRequest;
use App\Services\ATMAuthenticationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ATMAuthenticationController extends Controller
{
    public function create(): View
    {
        return view('atm.login');
    }

    public function store(ATMLoginRequest $request, ATMAuthenticationService $atmAuthentication): RedirectResponse
    {
        $card = $atmAuthentication->attempt(
            $request->validated('card_number'),
            $request->validated('pin'),
        );

        $request->session()->regenerate();
        $request->session()->put('atm.card_id', $card->id);
        $request->session()->put('atm.authenticated_at', now()->toDateTimeString());

        return redirect()->route('atm.session');
    }

    public function session(Request $request): View
    {
        $card = $request->attributes->get('atmCard');
        $card->loadMissing(['account.customer', 'account.branch']);

        return view('atm.session', [
            'account' => $card->account,
            'authenticatedAt' => $this->sessionTime($request, 'atm.authenticated_at'),
            'card' => $card,
            'lastActivityAt' => $this->sessionTime($request, 'atm.last_activity_at'),
        ]);
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->session()->forget('atm');
        $request->session()->regenerateToken();

        return redirect()
            ->route('atm.login')
            ->with('status', 'ATM session ended securely.');
    }

    private function sessionTime(Request $request, string $key): ?Carbon
    {
        $value = $request->session()->get($key);

        return $value ? Carbon::parse($value) : null;
    }
}

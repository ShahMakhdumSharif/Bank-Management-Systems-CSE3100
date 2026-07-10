<?php

namespace App\Http\Middleware;

use App\Models\ATMCard;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ATMAuthenticatedMiddleware
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $cardId = $request->session()->get('atm.card_id');

        $card = $cardId
            ? ATMCard::query()->with('account.customer')->find($cardId)
            : null;

        if (
            $card === null
            || $card->status !== ATMCard::STATUS_ACTIVE
            || ($card->expires_at !== null && $card->expires_at->isPast())
            || ! $card->account?->isActive()
        ) {
            $request->session()->forget('atm');

            return redirect()
                ->route('atm.login')
                ->with('error', 'Please sign in with an active ATM card.');
        }

        $request->attributes->set('atmCard', $card);

        return $next($request);
    }
}

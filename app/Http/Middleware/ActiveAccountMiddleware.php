<?php

namespace App\Http\Middleware;

use App\Models\Account;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ActiveAccountMiddleware
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        abort_unless($user?->isCustomer() && $user->status === User::STATUS_APPROVED, 403);

        $activeAccount = $user->accounts()
            ->where('status', Account::STATUS_ACTIVE)
            ->first();

        abort_unless($activeAccount !== null, 403);

        $request->attributes->set('activeAccount', $activeAccount);

        return $next($request);
    }
}

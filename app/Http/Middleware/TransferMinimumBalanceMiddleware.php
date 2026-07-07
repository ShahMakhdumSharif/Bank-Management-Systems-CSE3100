<?php

namespace App\Http\Middleware;

use App\Services\TransferRequestService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TransferMinimumBalanceMiddleware
{
    public function __construct(private readonly TransferRequestService $transferService)
    {
    }

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $account = $request->attributes->get('activeAccount');

        if ($request->has('amount')) {
            $this->transferService->ensureMinimumBalanceAfterPendingTransfers($account, $request->input('amount'));
        } else {
            $this->transferService->ensureCanCreateAnotherTransfer($account);
        }

        return $next($request);
    }
}

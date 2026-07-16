<?php

namespace App\Http\Controllers;

use App\Services\CurrencyExchangeService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Throwable;

class CurrencyExchangeController extends Controller
{
    public function index(CurrencyExchangeService $currencyExchange): View
    {
        return view('currency-exchange.index', [
            'currencies' => $currencyExchange->supportedCurrencies(),
        ]);
    }

    public function metadata(CurrencyExchangeService $currencyExchange): JsonResponse
    {
        return response()->json([
            'provider' => config('services.exchange_rate.provider'),
            'base_currency' => config('bank.currency'),
            'currencies' => $currencyExchange->supportedCurrencies(),
        ]);
    }

    public function rates(CurrencyExchangeService $currencyExchange): JsonResponse
    {
        try {
            return response()->json([
                'data' => [
                    'base_currency' => config('bank.currency'),
                    'rates' => $currencyExchange->ratesBasedOn(config('bank.currency')),
                ],
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'Exchange-rate table is temporarily unavailable. Please try again later.',
            ], 503);
        }
    }

    public function convert(Request $request, CurrencyExchangeService $currencyExchange): JsonResponse
    {
        $currencyCodes = array_keys($currencyExchange->supportedCurrencies());

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'decimal:0,2', 'min:0.01', 'max:999999999.99'],
            'from_currency' => ['required', 'string', Rule::in($currencyCodes)],
            'to_currency' => ['required', 'string', Rule::in($currencyCodes)],
        ]);

        try {
            return response()->json([
                'data' => $currencyExchange->convert(
                    $validated['from_currency'],
                    $validated['to_currency'],
                    $validated['amount'],
                ),
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'Exchange rates are temporarily unavailable. Please try again later.',
            ], 503);
        }
    }
}

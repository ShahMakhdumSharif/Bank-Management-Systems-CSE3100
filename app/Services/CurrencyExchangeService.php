<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class CurrencyExchangeService
{
    /**
     * @return array<string, string>
     */
    public function supportedCurrencies(): array
    {
        return config('services.exchange_rate.supported_currencies', []);
    }

    /**
     * @return array<string, mixed>
     */
    public function convert(string $fromCurrency, string $toCurrency, string|float|int $amount): array
    {
        $fromCurrency = strtoupper($fromCurrency);
        $toCurrency = strtoupper($toCurrency);
        $normalizedAmount = $this->normalizeAmount($amount);
        $quote = $this->quoteFor($fromCurrency, $toCurrency);
        $rate = $quote['rate'];

        if (! is_numeric($rate)) {
            throw new RuntimeException("Exchange rate for {$toCurrency} is unavailable.");
        }

        $convertedAmount = $this->normalizeAmount(((float) $normalizedAmount) * ((float) $rate));

        return [
            'from_currency' => $fromCurrency,
            'to_currency' => $toCurrency,
            'amount' => $normalizedAmount,
            'rate' => number_format((float) $rate, 6, '.', ''),
            'converted_amount' => $convertedAmount,
            'base_currency' => $quote['base_currency'],
            'time_last_update_utc' => $quote['updated_at'],
            'time_next_update_utc' => null,
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function ratesBasedOn(string $baseCurrency): array
    {
        $baseCurrency = strtoupper($baseCurrency);

        return collect($this->supportedCurrencies())
            ->map(function (string $name, string $currency) use ($baseCurrency): array {
                $quote = $this->quoteFor($baseCurrency, $currency);
                $rate = (float) $quote['rate'];

                return [
                    'currency' => $currency,
                    'name' => $name,
                    'rate' => number_format($rate, 6, '.', ''),
                    'bdt_equivalent' => $rate > 0 ? number_format(1 / $rate, 2, '.', '') : null,
                    'updated_at' => $quote['updated_at'],
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function quoteFor(string $fromCurrency, string $toCurrency): array
    {
        if ($fromCurrency === $toCurrency) {
            return [
                'base_currency' => $fromCurrency,
                'rate' => 1,
                'updated_at' => now()->toIso8601String(),
            ];
        }

        $cacheKey = 'currency-exchange:quote:'.$fromCurrency.':'.$toCurrency;
        $ttl = now()->addMinutes((int) config('services.exchange_rate.cache_minutes', 60));

        return Cache::remember($cacheKey, $ttl, function () use ($fromCurrency, $toCurrency): array {
            $apiKey = config('services.exchange_rate.key');

            if (blank($apiKey)) {
                throw new RuntimeException('Exchange-rate API key is missing.');
            }

            return $this->usesFastForex()
                ? $this->fetchFastForexQuote($apiKey, $fromCurrency, $toCurrency)
                : $this->fetchExchangeRateApiQuote($apiKey, $fromCurrency, $toCurrency);
        });
    }

    /**
     * @return array<string, mixed>
     */
    private function fetchFastForexQuote(string $apiKey, string $fromCurrency, string $toCurrency): array
    {
        $response = Http::timeout(10)
            ->acceptJson()
            ->withHeaders(['X-API-Key' => $apiKey])
            ->get($this->endpoint('fetch-one'), [
                'from' => $fromCurrency,
                'to' => $toCurrency,
            ]);

        if (! $response->successful()) {
            throw new RuntimeException('FastForex API request failed.');
        }

        $payload = $response->json();
        $rate = $payload['result'][$toCurrency] ?? null;

        if (! is_numeric($rate)) {
            throw new RuntimeException("FastForex response is missing the {$toCurrency} rate.");
        }

        return [
            'base_currency' => $payload['base'] ?? $fromCurrency,
            'rate' => $rate,
            'updated_at' => $payload['updated'] ?? null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function fetchExchangeRateApiQuote(string $apiKey, string $fromCurrency, string $toCurrency): array
    {
        $response = Http::timeout(10)
            ->acceptJson()
            ->get($this->endpoint("{$apiKey}/latest/{$fromCurrency}"));

        if (! $response->successful()) {
            throw new RuntimeException('ExchangeRate-API request failed.');
        }

        $payload = $response->json();

        if (($payload['result'] ?? null) !== 'success') {
            $errorType = $payload['error-type'] ?? 'unknown-error';

            throw new RuntimeException("ExchangeRate-API returned {$errorType}.");
        }

        $rate = $payload['conversion_rates'][$toCurrency] ?? null;

        if (! is_numeric($rate)) {
            throw new RuntimeException("ExchangeRate-API response is missing the {$toCurrency} rate.");
        }

        return [
            'base_currency' => $payload['base_code'] ?? $fromCurrency,
            'rate' => $rate,
            'updated_at' => $payload['time_last_update_utc'] ?? null,
        ];
    }

    private function endpoint(string $path): string
    {
        $baseUrl = rtrim((string) config('services.exchange_rate.base_url'), '/');
        $path = ltrim($path, '/');

        return "{$baseUrl}/{$path}";
    }

    private function usesFastForex(): bool
    {
        $provider = strtolower((string) config('services.exchange_rate.provider'));
        $baseUrl = strtolower((string) config('services.exchange_rate.base_url'));

        return str_contains($provider, 'fastforex') || str_contains($baseUrl, 'fastforex');
    }

    private function normalizeAmount(string|float|int $amount): string
    {
        return number_format((float) $amount, 2, '.', '');
    }
}

<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Services\CurrencyExchangeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CurrencyExchangeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'cache.default' => 'array',
            'services.exchange_rate.provider' => 'FastForex',
            'services.exchange_rate.key' => 'test-api-key',
            'services.exchange_rate.base_url' => 'https://api.fastforex.io',
        ]);
    }

    public function test_customer_can_view_currency_exchange_page(): void
    {
        $account = Account::factory()->create();

        $this
            ->actingAs($account->customer)
            ->get(route('customer.currency-exchange.index'))
            ->assertOk()
            ->assertSee('BDT exchange rates')
            ->assertSee('All rates based on BDT')
            ->assertSee('BDT - Bangladeshi Taka')
            ->assertSee('USD - United States Dollar');
    }

    public function test_currency_conversion_uses_external_api_through_backend(): void
    {
        $account = Account::factory()->create();

        Http::fake([
            'https://api.fastforex.io/fetch-one*' => Http::response([
                'base' => 'BDT',
                'result' => [
                    'USD' => 0.0082,
                ],
                'updated' => '2026-07-14T17:19:37Z',
            ]),
        ]);

        $this
            ->actingAs($account->customer)
            ->postJson(route('customer.currency-exchange.convert'), [
                'amount' => '1000',
                'from_currency' => 'BDT',
                'to_currency' => 'USD',
            ])
            ->assertOk()
            ->assertJsonPath('data.from_currency', 'BDT')
            ->assertJsonPath('data.to_currency', 'USD')
            ->assertJsonPath('data.amount', '1000.00')
            ->assertJsonPath('data.rate', '0.008200')
            ->assertJsonPath('data.converted_amount', '8.20');

        Http::assertSentCount(1);
        Http::assertSent(fn ($request): bool => $request->hasHeader('X-API-Key', 'test-api-key')
            && $request['from'] === 'BDT'
            && $request['to'] === 'USD');
    }

    public function test_customer_can_load_bdt_based_exchange_rate_table(): void
    {
        $account = Account::factory()->create();

        Http::fake(function ($request) {
            $toCurrency = $request['to'];

            return Http::response([
                'base' => 'BDT',
                'result' => [
                    $toCurrency => match ($toCurrency) {
                        'USD' => 0.0082,
                        'EUR' => 0.0072,
                        default => 1.25,
                    },
                ],
                'updated' => '2026-07-14T17:19:37Z',
            ]);
        });

        $this
            ->actingAs($account->customer)
            ->getJson(route('customer.currency-exchange.rates'))
            ->assertOk()
            ->assertJsonPath('data.base_currency', 'BDT')
            ->assertJsonPath('data.rates.0.currency', 'BDT')
            ->assertJsonPath('data.rates.0.rate', '1.000000')
            ->assertJsonPath('data.rates.1.currency', 'USD')
            ->assertJsonPath('data.rates.1.rate', '0.008200')
            ->assertJsonPath('data.rates.2.currency', 'EUR')
            ->assertJsonPath('data.rates.2.rate', '0.007200');
    }

    public function test_currency_conversion_caches_quote_by_currency_pair(): void
    {
        $service = app(CurrencyExchangeService::class);

        Http::fake([
            'https://api.fastforex.io/fetch-one*' => Http::response([
                'base' => 'USD',
                'result' => [
                    'BDT' => 121.5,
                ],
            ]),
        ]);

        $firstResult = $service->convert('USD', 'BDT', '10');
        $secondResult = $service->convert('USD', 'BDT', '20');

        $this->assertSame('1215.00', $firstResult['converted_amount']);
        $this->assertSame('2430.00', $secondResult['converted_amount']);
        Http::assertSentCount(1);
    }

    public function test_currency_conversion_validates_supported_currency_codes(): void
    {
        $account = Account::factory()->create();

        $this
            ->actingAs($account->customer)
            ->postJson(route('customer.currency-exchange.convert'), [
                'amount' => '100',
                'from_currency' => 'BDT',
                'to_currency' => 'XYZ',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('to_currency');
    }

    public function test_currency_conversion_returns_service_error_when_provider_fails(): void
    {
        $account = Account::factory()->create();

        Http::fake([
            'https://api.fastforex.io/fetch-one*' => Http::response([], 403),
        ]);

        $this
            ->actingAs($account->customer)
            ->postJson(route('customer.currency-exchange.convert'), [
                'amount' => '100',
                'from_currency' => 'BDT',
                'to_currency' => 'USD',
            ])
            ->assertStatus(503)
            ->assertJsonPath('message', 'Exchange rates are temporarily unavailable. Please try again later.');
    }
}

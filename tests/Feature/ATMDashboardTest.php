<?php

namespace Tests\Feature;

use App\Models\ATMCard;
use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ATMDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_atm_session_shows_masked_dashboard_details(): void
    {
        $account = Account::factory()->create([
            'account_number' => '102345678901',
            'balance' => 24500.75,
        ]);

        $card = ATMCard::factory()->create([
            'account_id' => $account->id,
            'card_number' => '5060123412349876',
        ]);

        $response = $this
            ->withSession([
                'atm.card_id' => $card->id,
                'atm.authenticated_at' => now()->subMinutes(5)->toDateTimeString(),
            ])
            ->get(route('atm.session'));

        $response
            ->assertOk()
            ->assertSee('ATM dashboard')
            ->assertSee('BDT 24,500.75')
            ->assertSee('**** **** **** 9876')
            ->assertSee('********8901')
            ->assertDontSee('5060123412349876')
            ->assertDontSee('102345678901');
    }
}

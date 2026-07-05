<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\TransferRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerTransferRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_create_pending_transfer_request(): void
    {
        $sender = User::factory()->approvedCustomer()->create();
        $senderAccount = Account::factory()->create([
            'user_id' => $sender->id,
            'balance' => 5000,
        ]);
        $receiverAccount = Account::factory()->create();

        $response = $this
            ->actingAs($sender)
            ->post(route('customer.transfers.store'), [
                'receiver_account_number' => $receiverAccount->account_number,
                'amount' => '1500.50',
            ]);

        $response->assertRedirect(route('customer.transfers.index'));

        $this->assertDatabaseHas('transfer_requests', [
            'sender_account_id' => $senderAccount->id,
            'receiver_account_id' => $receiverAccount->id,
            'amount' => 1500.50,
            'status' => TransferRequest::STATUS_PENDING,
        ]);
    }

    public function test_customer_cannot_request_self_transfer(): void
    {
        $customer = User::factory()->approvedCustomer()->create();
        $account = Account::factory()->create([
            'user_id' => $customer->id,
            'balance' => 5000,
        ]);

        $response = $this
            ->actingAs($customer)
            ->from(route('customer.transfers.create'))
            ->post(route('customer.transfers.store'), [
                'receiver_account_number' => $account->account_number,
                'amount' => '100',
            ]);

        $response
            ->assertRedirect(route('customer.transfers.create'))
            ->assertSessionHasErrors('receiver_account_number');

        $this->assertDatabaseCount('transfer_requests', 0);
    }

    public function test_customer_can_cancel_own_pending_transfer_request(): void
    {
        $sender = User::factory()->approvedCustomer()->create();
        $senderAccount = Account::factory()->create([
            'user_id' => $sender->id,
            'balance' => 5000,
        ]);
        $receiverAccount = Account::factory()->create();

        $transferRequest = TransferRequest::factory()->create([
            'sender_account_id' => $senderAccount->id,
            'receiver_account_id' => $receiverAccount->id,
            'amount' => 250,
            'status' => TransferRequest::STATUS_PENDING,
        ]);

        $response = $this
            ->actingAs($sender)
            ->patch(route('customer.transfers.cancel', $transferRequest));

        $response->assertRedirect(route('customer.transfers.index'));

        $this->assertDatabaseHas('transfer_requests', [
            'id' => $transferRequest->id,
            'status' => TransferRequest::STATUS_CANCELLED,
        ]);
    }
}

<?php

namespace Tests\Feature\Wallet;

use App\Domain\Money\Enums\TransactionStatus;
use App\Domain\Money\Enums\TransactionType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DepositMoneyTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_deposit_money(): void
    {
        $user = User::factory()->create();
        $wallet = $user->wallet()->create(['balance' => 0]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/deposits', [
            'amount' => 10000,
            'description' => 'Depósito de teste',
        ]);

        $response->assertCreated()
            ->assertJsonPath('message', 'Depósito realizado com sucesso.')
            ->assertJsonPath('wallet.balance', 10000)
            ->assertJsonPath('transaction.amount', 10000);

        $this->assertDatabaseHas('wallets', [
            'id' => $wallet->id,
            'balance' => 10000,
        ]);

        $this->assertDatabaseHas('transactions', [
            'type' => TransactionType::Deposit->value,
            'status' => TransactionStatus::Completed->value,
            'amount' => 10000,
            'wallet_to_id' => $wallet->id,
            'description' => 'Depósito de teste',
        ]);
    }
}

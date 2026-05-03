<?php

namespace Tests\Unit\Money;

use App\Domain\Money\Actions\TransferMoneyAction;
use App\Domain\Money\Exceptions\InsufficientBalanceException;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransferMoneyActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_prevents_transfer_when_sender_has_insufficient_balance(): void
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();

        $sender->wallet()->create(['balance' => 1000]);
        $recipient->wallet()->create(['balance' => 0]);

        $this->expectException(InsufficientBalanceException::class);

        app(TransferMoneyAction::class)->execute($sender, $recipient, 1500);
    }
}

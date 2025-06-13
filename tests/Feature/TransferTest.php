<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\BankAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransferTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_transfer()
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();

        BankAccount::create(['user_id' => $sender->id, 'balance' => 500]);
        BankAccount::create(['user_id' => $recipient->id, 'balance' => 100]);

        $this->actingAs($sender);

        $response = $this->post('/transferir', [
            'to_user' => $recipient->email,
            'amount' => 200,
            'description' => 'Teste de transferência',
        ]);

        $response->assertJson(['status' => 'success']);
        $this->assertDatabaseHas('transactions', [
            'user_id' => $sender->id,
            'receiver_id' => $recipient->id,
            'amount' => 200,
            'type' => 1,
        ]);
    }
}

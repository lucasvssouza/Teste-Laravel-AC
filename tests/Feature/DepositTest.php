<?php
namespace Tests\Feature;

use App\Models\BankAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepositTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_deposit()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        BankAccount::create(['user_id' => $user->id, 'balance' => 0]);

        $response = $this->post('/deposito', [
            'amount'      => 100,
            'description' => 'Depósito inicial',
        ]);

        $response->assertJson(['status' => 'success']);
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'amount'  => 100,
            'type'    => 2,
        ]);
    }
}

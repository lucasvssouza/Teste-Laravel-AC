<?php
namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FakeTransactionSeeder extends Seeder
{
    public function run(): void
    {
        // Garante que existam dois usuários
        $sender = User::firstOrCreate(
            ['email' => 'sender@example.com'],
            ['name' => 'Usuário Remetente', 'password' => bcrypt('senha123')]
        );

        $receiver = User::firstOrCreate(
            ['email' => 'receiver@example.com'],
            ['name' => 'Usuário Destinatário', 'password' => bcrypt('senha123')]
        );

        // Cria contas se não existirem
        $senderAccount = BankAccount::firstOrCreate(
            ['user_id' => $sender->id],
            ['balance' => 5000]
        );

        $receiverAccount = BankAccount::firstOrCreate(
            ['user_id' => $receiver->id],
            ['balance' => 1000]
        );

        for ($i = 0; $i < 20; $i++) {
       DB::transaction(function () use ($senderAccount, $receiverAccount, $sender, $receiver) {
    $amount = fake()->randomFloat(2, 1, 500);
    $description = fake()->sentence();

    $senderPrevious = $senderAccount->balance;
    $receiverPrevious = $receiverAccount->balance;

    // Atualiza saldos
    $senderAccount->decrement('balance', $amount);
    $receiverAccount->increment('balance', $amount);

    $senderNew = $senderPrevious - $amount;
    $receiverNew = $receiverPrevious + $amount;

    Transaction::create([
        'user_id'         => $sender->id,
        'receiver_id'     => $receiver->id,
        'amount'          => $amount,
        'description'     => $description,
        'type'            => 1, // Envio
        'previous_balance'=> $senderPrevious,
        'new_balance'     => $senderNew,
    ]);

});

        }

        $this->command->info('20 transações de teste criadas com sucesso.');
    }
}

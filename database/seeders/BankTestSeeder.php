<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\BankAccount;
use Illuminate\Support\Facades\Hash;

class BankTestSeeder extends Seeder
{
    public function run(): void
    {
        $user1 = User::create([
            'name' => 'Usuário Positivo',
            'email' => 'teste@teste.com',
            'password' => Hash::make('123456'),
        ]);

        BankAccount::create([
            'user_id' => $user1->id,
            'balance' => 1000,
        ]);

        $user2 = User::create([
            'name' => 'Usuário Negativo',
            'email' => 'teste2@teste.com',
            'password' => Hash::make('123456'),
        ]);

        BankAccount::create([
            'user_id' => $user2->id,
            'balance' => -1000,
        ]);

        
    }
}

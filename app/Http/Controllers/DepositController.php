<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DepositController extends Controller
{
    public function showDepositForm()
    {
        $user    = auth()->user();
        $balance = $user->bankAccount->balance ?? 0;

        return view('deposit.form', compact('balance'));
    }

    public function processDeposit(Request $request)
    {
        $request->validate([
            'amount'      => 'required|numeric|min:1',
            'description' => 'nullable|string|max:255',
        ]);

        $user        = auth()->user();
        $amount      = $request->amount;
        $description = $request->description ?? 'Depósito realizado';

        try {
            DB::beginTransaction();

            $account = BankAccount::where('user_id', $user->id)->lockForUpdate()->first();
            $previousBalance = $account->balance;
            $account->balance += $amount;
            $account->save();

            Transaction::create([
                'user_id'          => $user->id,
                'receiver_id'      => $user->id,
                'amount'           => $amount,
                'description'      => $description,
                'type'             => 2,
                'previous_balance' => $previousBalance,
                'new_balance'      => $account->balance,
            ]);

            DB::commit();

            Log::info("Depósito realizado com sucesso por {$user->email}", [
                'amount' => $amount,
                'balance' => $account->balance,
            ]);

            return response()->json(['status' => 'success', 'message' => 'Depósito realizado com sucesso!']);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("Erro ao processar depósito: " . $e->getMessage(), [
                'user_id' => $user->id,
                'amount' => $amount,
            ]);

            return response()->json(['status' => 'error', 'message' => 'Erro ao processar depósito.'], 500);
        }
    }
}

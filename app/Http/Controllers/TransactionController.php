<?php
namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Transaction;
use App\Models\User;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    public function showTransferForm()
    {
        $user    = auth()->user();
        $balance = $user->bankAccount->balance ?? 0;

        return view('transfer.form', compact('balance'));
    }

    public function processTransfer(Request $request)
    {
        $request->validate([
            'to_user'     => 'required',
            'amount'      => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
        ]);

        $user     = auth()->user();
        $account  = $user->bankAccount;
        $negative = $account->balance < 0 ? true : false;

        if ($request->to_user == $user->email) {
            return response()->json(['status' => 'error', 'message' => 'Você não pode transferir para você mesmo.']);
        }

        if (! $account || ($account->balance < $request->amount && ! $negative)) {
            return response()->json(['status' => 'error', 'message' => 'Saldo insuficiente.']);
        }

        $recipient = User::where(function ($q) use ($request) {
            $q->where('email', $request->to_user)
                ->orWhere('id', $request->to_user);
        })
            ->whereHas('bankAccount', function ($q) {
                $q->where('is_active', '1');
            })
            ->first();

        if (! $recipient || $recipient->id === $user->id) {
            return response()->json(['status' => 'error', 'message' => 'Destinatário inválido.']);
        }

        try {
            DB::beginTransaction();

            $prevSender = $account->balance;
            $account->balance -= $request->amount;
            $account->save();

            $recipientAccount = $recipient->bankAccount;
            $recipientAccount->balance += $request->amount;
            $recipientAccount->save();

            Transaction::create([
                'user_id'          => $user->id,
                'receiver_id'      => $recipient->id,
                'type'             => 1,
                'previous_balance' => $prevSender,
                'amount'           => $request->amount,
                'new_balance'      => $account->balance,
                'description'      => $request->description,
            ]);

            DB::commit();

            Log::info('Transferência realizada', [
                'from'   => $user->id,
                'to'     => $recipient->id,
                'amount' => $request->amount,
            ]);

            return response()->json(['status' => 'success', 'message' => 'Transferência realizada com sucesso.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao processar transferência', [
                'error'   => $e->getMessage(),
                'user_id' => $user->id,
            ]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function cancelTransaction($id)
    {
        $user = auth()->user();

        $transaction = Transaction::where('id', $id)
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('receiver_id', $user->id);
            })
            ->where('cancelled', false)
            ->with(['sender', 'receiver'])
            ->first();

        if (! $transaction) {
            return response()->json(['status' => 'error', 'message' => 'Transação não encontrada.'], 404);
        }

        try {
            DB::beginTransaction();

            $senderAccount = BankAccount::where('user_id', $transaction->user_id)->lockForUpdate()->first();

            if ($transaction->type == 1) {
                $receiverAccount = BankAccount::where('user_id', $transaction->receiver_id)->lockForUpdate()->first();

                $receiverAccount->balance -= $transaction->amount;
                $receiverAccount->save();

                $senderAccount->balance += $transaction->amount;
                $senderAccount->save();

            } elseif ($transaction->type == 2) {
                if ($senderAccount->balance < $transaction->amount) {
                    throw new \Exception('Saldo insuficiente para reverter o depósito.');
                }

                $senderAccount->balance -= $transaction->amount;
                $senderAccount->save();
            }

            $transaction->cancelled = true;
            $transaction->save();

            DB::commit();

            Log::info('Transação cancelada', [
                'transaction_id' => $transaction->id,
                'user_id'        => $user->id,
            ]);

            $updatedBalance = auth()->user()->bankAccount->fresh()->balance;

            return response()->json([
                'status'      => 'success',
                'message'     => 'Transação cancelada com sucesso.',
                'new_balance' => number_format($updatedBalance, 2, ',', '.'),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao cancelar transação', [
                'error'          => $e->getMessage(),
                'transaction_id' => $id,
                'user_id'        => $user->id,
            ]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}

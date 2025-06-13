<?php
namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    public function index()
    {
        $user    = auth()->user();
        $account = $user->bankAccount;
        $balance = $account->balance;

        return view('account.statement', [
            'balance' => $balance,
        ]);
    }

    public function fetchTransactions(Request $request)
    {
        $user = auth()->user();

        $transactions = Transaction::where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->orWhere('receiver_id', $user->id);
        })
            ->where('cancelled', false)
            ->with(['sender', 'receiver'])
            ->orderByDesc('created_at')
            ->paginate(10);

        $html = view('components.transaction-list', ['transactions' => $transactions])->render();

        return response()->json(['html' => $html]);
    }

    public function checkBalance()
    {
        $user    = auth()->user();
        $account = $user->bankAccount;
        $balance = $account->balance;

        return response()->json(['balance' =>
        number_format($balance, 2, ',', '.')]);
    }
}

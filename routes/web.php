<?php

use App\Http\Controllers\DepositController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\TransactionController;

// Redirecionamento dinâmico
Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('account.statement')
        : redirect()->route('login');
})->name('root');

// Autenticação
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

Route::get('/logout', [AuthController::class, 'logout'])->name('logout.get');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Grupo protegido por autenticação
Route::middleware(['auth'])->group(function () {

    // Conta bancária
    Route::get('/extrato', [BankAccountController::class, 'index'])->name('account.statement');
    Route::get('/saldo/atual', [BankAccountController::class, 'checkBalance'])->name('account.balance');
    Route::get('/transacoes', [BankAccountController::class, 'fetchTransactions'])->name('transactions.fetch');

    // Transferência
    Route::get('/transferencia', [TransactionController::class, 'showTransferForm'])->name('transfer.form');
    Route::post('/transferencia', [TransactionController::class, 'processTransfer'])->name('transfer.process');

    // Deposito
    Route::get('/deposito', [DepositController::class, 'showDepositForm'])->name('deposit.form');
    Route::post('/deposito', [DepositController::class, 'processDeposit'])->name('deposit.process');

    Route::post('/transacoes/{id}/cancelar', [TransactionController::class, 'cancelTransaction'])->name('transactions.cancel');
});


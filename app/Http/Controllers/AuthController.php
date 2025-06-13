<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            Log::info('Login realizado com sucesso.', ['email' => $request->email]);

            return response()->json([
                'status'      => 'success',
                'redirect_to' => route('account.statement'),
            ]);
        }

        Log::warning('Tentativa de login falhou.', ['email' => $request->email]);

        return response()->json([
            'status'  => 'error',
            'message' => 'Email ou senha inválidos!',
        ], 422);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:6'],
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);

            BankAccount::create([
                'user_id' => $user->id,
                'balance' => 0,
            ]);

            DB::commit();

            Log::info('Usuário cadastrado com sucesso.', ['user_id' => $user->id, 'email' => $user->email]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Cadastro realizado com sucesso.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao cadastrar usuário.', [
                'email' => $request->email,
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Erro ao cadastrar usuário. Tente novamente.',
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        Log::info('Logout efetuado.', ['user_id' => auth()->id()]);

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}

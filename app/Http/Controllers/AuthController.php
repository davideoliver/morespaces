<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $auth,
    ) {
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        if (! $this->auth->attempt($request->string('username')->toString(), $request->string('password')->toString())) {
            throw ValidationException::withMessages([
                'username' => 'Credenciais inválidas',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->route('home')->with('success', 'Login realizado com sucesso!');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $this->auth->register($request->validated());

        return redirect()->route('home')->with('success', 'Cadastro realizado com sucesso!');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Você saiu da sua conta.');
    }
}

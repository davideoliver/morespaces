<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Controla o fluxo de autenticação simplificado baseado em sessão.
 */
class AuthController extends Controller
{
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $request->session()->regenerate();
        $request->session()->put('username', $request->string('username')->toString());

        return back()->with('success', 'Login realizado com sucesso!');
    }

    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'rg' => ['required', 'string', 'max:30'],
            'username' => ['required', 'string', 'max:80'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        return back()->with('success', 'Cadastro realizado com sucesso!');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('username');
        $request->session()->regenerate();

        return redirect()->route('home')->with('success', 'Você saiu da sua conta.');
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Controla o formulário de contato demonstrativo.
 */
class ContactController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nome' => ['required', 'string', 'max:120'],
            'contatoEmail' => ['required', 'email', 'max:255'],
            'mensagem' => ['required', 'string', 'max:2000'],
        ]);

        return back()->with('success', 'Mensagem enviada com sucesso!');
    }
}

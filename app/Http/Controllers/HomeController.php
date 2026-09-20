<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('home', [
            'events' => [
                ['year' => '1900', 'description' => 'Fundação da MoreSpaces'],
                ['year' => 'Hoje', 'description' => 'Expansão para novos espaços e colaboradores'],
            ],
            'faqs' => [
                ['question' => 'O que a companhia faz?', 'answer' => 'Ela atua na criação e distribuição de espaços, aplicando tecnologias para atender às necessidades de clientes e parceiros.'],
                ['question' => 'Onde nós estamos?', 'answer' => 'Em todos os espaços onde ideias precisam de estrutura para crescer.'],
                ['question' => 'Como posso colaborar?', 'answer' => 'Entre em contato pelo formulário ou conheça mais sobre nossos projetos.'],
            ],
        ]);
    }

    public function contact(Request $request): RedirectResponse
    {
        $request->validate([
            'nome' => ['required', 'string', 'max:120'],
            'contatoEmail' => ['required', 'email', 'max:255'],
            'mensagem' => ['required', 'string', 'max:2000'],
        ]);

        return back()->with('success', 'Mensagem enviada com sucesso!');
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

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
}
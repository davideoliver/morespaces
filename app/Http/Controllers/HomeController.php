<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('home', [
            'isAuthenticated' => $this->isAuthenticated(),
            'isSpacesUser' => $this->isSpacesUser(),
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

    public function files(): View
    {
        return view('files', [
            'isAuthenticated' => $this->isAuthenticated(),
            'isSpacesUser' => $this->isSpacesUser(),
            'levels' => range(0, 4),
        ]);
    }

    public function file(int $level): Response
    {
        return response()->view('files.not-found', ['level' => $level], 404);
    }

    private function isSpacesUser(): bool
    {
        return str_contains(strtolower((string) session('username')), 'spaces');
    }

    private function isAuthenticated(): bool
    {
        return (string) session('username') !== '';
    }
}
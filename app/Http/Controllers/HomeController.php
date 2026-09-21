<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

/**
 * Controller principal da aplicação MoreSpaces.
 *
 * Concentra toda a lógica das páginas públicas (Home, FAQ, Contato),
 * do fluxo de autenticação simplificado (login/cadastro/logout baseado
 * em sessão, sem tabela de usuários) e da área de "Arquivos" (M.G.E),
 * que é liberada apenas para usuários cujo username contenha "spaces".
 */
class HomeController extends Controller
{
    /**
     * Página inicial (Home).
     *
     * Monta e envia para a view 'home' todos os dados dinâmicos da
     * landing page: estado de autenticação, se o usuário é "Spaces User"
     * (libera o link de Arquivos no menu), a linha do tempo de eventos
     * históricos da empresa e as perguntas frequentes (FAQ).
     */
    public function index(): View
    {
        return view('home', [
            'isAuthenticated' => $this->isAuthenticated(),
            'isSpacesUser' => $this->isSpacesUser(),
            // Linha do tempo exibida na seção "Sobre" (#about).
            'events' => [
                ['year' => '1900', 'description' => 'Fundação da MoreSpaces'],
                ['year' => 'Hoje', 'description' => 'Expansão para novos espaços e colaboradores'],
            ],
            // Perguntas do carrossel de FAQ (#faq), navegável via scripts.js.
            'faqs' => [
                ['question' => 'O que a companhia faz?', 'answer' => 'Ela atua na criação e distribuição de espaços, aplicando tecnologias para atender às necessidades de clientes e parceiros.'],
                ['question' => 'Onde nós estamos?', 'answer' => 'Em todos os espaços onde ideias precisam de estrutura para crescer.'],
                ['question' => 'Como posso colaborar?', 'answer' => 'Entre em contato pelo formulário ou conheça mais sobre nossos projetos.'],
            ],
        ]);
    }

    /**
     * Processa o envio do formulário de contato (#contact).
     *
     * Apenas valida os campos e retorna à página anterior com uma
     * mensagem de sucesso via flash session ('success'). Não há
     * persistência em banco de dados nem envio de e-mail real — é um
     * fluxo de demonstração.
     */
    public function contact(Request $request): RedirectResponse
    {
        $request->validate([
            'nome' => ['required', 'string', 'max:120'],
            'contatoEmail' => ['required', 'email', 'max:255'],
            'mensagem' => ['required', 'string', 'max:2000'],
        ]);

        return back()->with('success', 'Mensagem enviada com sucesso!');
    }

    /**
     * Autenticação simplificada baseada em sessão.
     *
     * Importante: NÃO há verificação de senha nem de existência do
     * usuário em banco de dados — qualquer combinação válida de
     * usuário/senha é aceita. O username é apenas guardado na sessão
     * para simular o estado "logado" (usado por isAuthenticated() e
     * isSpacesUser()). session()->regenerate() é chamado para evitar
     * fixação de sessão (session fixation) a cada novo login.
     */
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

    /**
     * Processa o formulário de cadastro (#register).
     *
     * Assim como o login, é apenas ilustrativo: os dados são validados
     * mas não são persistidos em nenhuma tabela — não existe model de
     * usuário no projeto. Serve para demonstrar o fluxo de UI/UX.
     */
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

    /**
     * Encerra a sessão do usuário.
     *
     * Remove apenas a chave 'username' da sessão e regenera o ID de
     * sessão por segurança, depois redireciona para a Home com uma
     * mensagem de despedida.
     */
    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('username');
        $request->session()->regenerate();

        return redirect()->route('home')->with('success', 'Você saiu da sua conta.');
    }

    /**
     * Lista os "níveis" de arquivos do M.G.E (área restrita).
     *
     * A view decide o que mostrar: se o usuário for "Spaces User",
     * exibe a grade de níveis (0 a 4); caso contrário, mostra um
     * estado vazio convidando o visitante a voltar depois.
     */
    public function files(): View
    {
        return view('files', [
            'isAuthenticated' => $this->isAuthenticated(),
            'isSpacesUser' => $this->isSpacesUser(),
            // range(0, 4) gera os 5 níveis exibidos nos cards da grade.
            'levels' => range(0, 4),
        ]);
    }

    /**
     * Exibe o conteúdo de um nível específico de arquivo.
     *
     * Atualmente nenhum nível possui conteúdo real (não há integração
     * com banco de dados ainda), então sempre é retornada a view
     * 'files.not-found' com status HTTP 404, informando o usuário de
     * que aquele nível ainda não está disponível.
     *
     * @param int $level Nível solicitado (validado como numérico pela rota).
     */
    public function file(int $level): Response
    {
        return response()->view('files.not-found', ['level' => $level], 404);
    }

    /**
     * Verifica se o usuário logado é um "Spaces User".
     *
     * Regra simplificada: o username salvo em sessão precisa conter a
     * substring "spaces" (case-insensitive). É essa flag que libera o
     * link "Arquivos" no menu e o acesso à área de níveis.
     */
    private function isSpacesUser(): bool
    {
        return str_contains(strtolower((string) session('username')), 'spaces');
    }

    /**
     * Verifica se existe uma sessão de usuário ativa.
     *
     * Não usa o sistema de Auth do Laravel (Auth::check()) porque não
     * há model/tabela de usuários — a "autenticação" é apenas a
     * presença da chave 'username' na sessão.
     */
    private function isAuthenticated(): bool
    {
        return (string) session('username') !== '';
    }
}

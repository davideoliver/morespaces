@extends('layouts.app')

@section('title', 'MoreSpaces | Arquivos')

@section('content')
<div class="page-shell files-page">
  <header class="site-header">
    <a class="brand" href="{{ route('home') }}#home" aria-label="MoreSpaces - Home">
      <img class="brand-mark" src="{{ asset('public/imgs/mswhite.png') }}" alt="MoreSpaces Logo" aria-hidden="true">
    </a>
    <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="main-navigation">
      <span class="menu-toggle__label">Menu</span>
      <span class="menu-toggle__icon" aria-hidden="true"><span></span><span></span><span></span></span>
    </button>
    <nav id="main-navigation" aria-label="Navegação principal">
      {{-- Nesta página os links da Home apontam de volta para lá com
           âncora (#), já que as seções não existem aqui. --}}
      <a href="{{ route('home') }}#home">Home</a>
      <a href="{{ route('home') }}#about-start">Sobre</a>
      <a href="{{ route('home') }}#faq">FAQ</a>
      <a href="{{ route('home') }}#contact">Contatos</a>
      @if (!$isAuthenticated)
      <a href="{{ route('home') }}#login">Login</a>
      <a href="{{ route('home') }}#register">Cadastro</a>
      @endif
      {{-- aria-current="page" aqui indica visualmente que estamos
           dentro da própria página de Arquivos. --}}
      @if ($isSpacesUser)<a href="{{ route('files') }}" aria-current="page">Arquivos</a>@endif
      @if ($isAuthenticated)
      <form class="logout-form" action="{{ route('logout') }}" method="POST">@csrf<button type="submit">Sair</button></form>
      @endif
    </nav>
  </header>

  {{-- Conteúdo condicional: só quem é "Spaces User" vê a grade de
       níveis; qualquer outro visitante autenticado ou não vê o
       estado vazio (.files-empty) logo no @else abaixo. --}}
  @if ($isSpacesUser)
  <main class="files-content">
    <section class="files-hero">
      <p class="eyebrow">M.G.E</p>
      <h1>Arquivos</h1>
      <p>Bem vindo agente,<br>
      Selecione um nível para consultar os arquivos do Maior Grupo de Exploradores (M.G.E) disponíveis para cada espaço.</p>.<br>
      <p class="note">Nota: Os arquivos podem ser acessados apenas por usuários autenticados.</p>
      <br>
    </section>
    <section class="files-levels" aria-labelledby="files-levels-title">
      <header>
        <p class="eyebrow">Catálogo</p>
        <h2 id="files-levels-title">Níveis disponíveis</h2>
      </header>
      <div class="files-level-grid">
        {{-- $levels vem de range(0, 4) no HomeController::files();
             cada card gera um link para /arquivos/{level}. --}}
        @foreach ($levels as $level)
        <a class="files-level-link" href="{{ route('files.level', ['level' => $level]) }}">
          <span class="files-level-link__number">{{ $level }}</span>
          <span><strong>Nível {{ $level }}</strong><small>Explorar arquivos</small></span>
        </a>
        @endforeach
      </div>
    </section>
  </main>
  @else
  <main class="files-empty">
    <div>
      <p class="eyebrow">MoreSpaces</p>
      <h1>Não há arquivos disponíveis.</h1>
      <p>Continue acompanhando a MoreSpaces para saber quando novos espaços forem liberados.</p>
      <a class="button-link" href="{{ route('home') }}#home">Voltar para Home</a>
    </div>
  </main>
  @endif
</div>
@endsection

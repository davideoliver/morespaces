@props([
    'isAuthenticated' => false,
    'isSpacesUser' => false,
    'filesPage' => false,
])

<header class="site-header">
  <a class="brand" href="{{ route('home') }}#home" aria-label="MoreSpaces - Home">
    <img class="brand-mark" src="{{ asset('public/imgs/mswhite.png') }}" alt="MoreSpaces Logo" aria-hidden="true">
  </a>
  <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="main-navigation">
    <span class="menu-toggle__label">Menu</span>
    <span class="menu-toggle__icon" aria-hidden="true"><span></span><span></span><span></span></span>
  </button>
  <nav id="main-navigation" aria-label="Navegação principal">
    @if ($filesPage)
    <a href="{{ route('home') }}#home">Home</a>
    <a href="{{ route('home') }}#about-start">Sobre</a>
    <a href="{{ route('home') }}#faq">FAQ</a>
    <a href="{{ route('home') }}#contact">Contatos</a>
    @else
    <a href="#home" aria-current="page">Home</a><a href="#about-start">Sobre</a><a href="#faq">FAQ</a>
    <a href="#contact">Contatos</a>
    @endif
    @if (!$isAuthenticated)
    <a href="{{ $filesPage ? route('home').'#login' : '#login' }}">Login</a>
    <a href="{{ $filesPage ? route('home').'#register' : '#register' }}">Cadastro</a>
    @endif
    @if ($isSpacesUser)<a href="{{ route('files') }}" @if ($filesPage) aria-current="page" @endif>Arquivos</a>@endif
    @if ($isAuthenticated)
    <form class="logout-form" action="{{ route('logout') }}" method="POST"><input type="hidden" name="_token" value="{{ csrf_token() }}"><button type="submit">Sair</button></form>
    @endif
  </nav>
</header>

{{-- Herda a estrutura HTML comum (head, scripts, css) do layout base. --}}
@extends('layouts.app')

@section('content')
<div class="page-shell">
  <header class="site-header">
    <a class="brand" href="{{ route('home') }}#home" aria-label="MoreSpaces - Home">
      <img class="brand-mark" src="{{ asset('public/imgs/mswhite.png') }}" alt="MoreSpaces Logo" aria-hidden="true">
    </a>
    {{-- Botão "hambúrguer", visível apenas em telas pequenas (ver
         media query no style.css); alterna a classe .is-open no <nav>
         via scripts.js. --}}
    <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="main-navigation">
      <span class="menu-toggle__label">Menu</span>
      <span class="menu-toggle__icon" aria-hidden="true"><span></span><span></span><span></span></span>
    </button>
    <nav id="main-navigation" aria-label="Navegação principal">
      {{-- Links âncora para as seções da própria Home; o destaque do
           item ativo (aria-current) é atualizado dinamicamente pelo
           scroll listener em scripts.js. --}}
      <a href="#home" aria-current="page">Home</a><a href="#about-start">Sobre</a><a href="#faq">FAQ</a>
      <a href="#contact">Contatos</a>
      {{-- Login/Cadastro só aparecem para visitantes não autenticados. --}}
      @if (!$isAuthenticated)
      <a href="#login">Login</a><a href="#register">Cadastro</a>
      @endif
      {{-- Link para a área restrita de Arquivos, liberado apenas
           quando $isSpacesUser é verdadeiro (vindo do HomeController). --}}
      @if ($isSpacesUser)<a href="{{ route('files') }}">Arquivos</a>@endif
      {{-- Usuário autenticado vê o botão de Sair em vez de Login/Cadastro. --}}
      @if ($isAuthenticated)
      <form class="logout-form" action="{{ route('logout') }}" method="POST"><input type="hidden" name="_token" value="{{ csrf_token() }}"><button type="submit">Sair</button></form>
      @endif
    </nav>
  </header>
  {{-- Faixa de mensagens de feedback (sucesso de formulário ou erros
       de validação). Some sozinha após alguns segundos via a
       animação CSS "collapseFlashMessage" definida em style.css. --}}
  @if (session('success') || $errors->any())
  <div class="flash-messages" aria-live="polite">
    @if (session('success'))
    <p class="form-message is-success" role="status">{{ session('success') }}</p>
    @endif
    @if ($errors->any())
    <div class="form-message is-error" role="alert">{{ $errors->first() }}</div>
    @endif
  </div>
  @endif

  <main class="page-content">
    <div class="page-article">
      <section id="home">
        <div class="hero-copy">
          <p class="eyebrow">MoreSpaces</p>
          <h1>Espaços modernos para ideias que querem crescer.</h1>
          <p>A MoreSpaces viabiliza a criação de espaços dos mais infinitos tipos para seus usuários e colaboradores.</p>
          <a class="button-link" href="#about">Conheça a MoreSpaces</a>
        </div>
      </section>
    </div>
    <aside id="about-start">
      <p class="about-tagline" style="text-align: center">Criamos espaços para que ninguém precise se espaçar.</p>
    </aside>

    <div class="page-article">
      <section id="about">
        <header>
          <p class="eyebrow">Nossa história</p>
          <h2>Sobre a MoreSpaces</h2>
        </header>
        <div>
          <h3>Valores</h3>
          <p>Confiança, fidelidade, inovação e principalmente excelência em todas as nossas atuações.</p>
        </div>
        <div>
          <h3>História</h3>
          <p>Fundada em 1900, a MoreSpaces nasceu como uma companhia familiar e cresceu criando espaços para pessoas, projetos e novas ideias.</p>
        </div>
        {{-- Tabela alimentada dinamicamente pelo array $events vindo do
             HomeController — basta adicionar um item ao array no
             controller para uma nova linha aparecer aqui automaticamente. --}}
        <table>
          <caption style="background: var(--surface); margin: 4px 3px">Eventos importantes para a história da MoreSpaces</caption>
          <thead>
            <tr>
              <th>Ano</th>
              <th>Evento</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($events as $event)<tr>
              <td>{{ $event['year'] }}</td>
              <td>{{ $event['description'] }}</td>
            </tr>@endforeach
          </tbody>
        </table>
      </section>
    </div>

    <div class="page-article">
      <section id="faq">
        <header>
          <p class="eyebrow">Dúvidas comuns</p>
          <h2>FAQ</h2>
        </header>
        {{-- Carrossel de perguntas frequentes: os slides (.faq-slide)
             são gerados a partir do array $faqs do controller, e a
             navegação entre eles (setas ← →) é controlada em
             scripts.js via initializeCarousel(). --}}
        <div class="faq-carousel" data-faq-carousel>
          <div class="faq-carousel__controls">
            <button class="faq-nav" type="button" data-prev aria-label="Pergunta anterior">←</button>
            <button class="faq-nav" type="button" data-next aria-label="Próxima pergunta">→</button>
          </div>
          <ol class="faq-carousel__track">
            @foreach ($faqs as $faq)<li class="faq-slide">
              <h3>{{ $faq['question'] }}</h3>
              <p>{{ $faq['answer'] }}</p>
            </li>@endforeach
          </ol>
        </div>
      </section>
    </div>

    <div class="page-article">
      <section id="contact" class="auth-section" style="padding-left: 0;">
        {{-- filemtime() é usado como "cache buster": ao anexar a data de
             modificação do arquivo na URL da imagem, o navegador é
             forçado a buscar a versão mais recente sempre que a imagem
             for alterada no servidor, evitando cache desatualizado. --}}
        <figure class="auth-figure auth-figure--contact">
          <img src="{{ asset('public/imgs/parking.webp') }}?v={{ filemtime(public_path('imgs/parking.webp')) }}" alt="Espaço MoreSpaces preparado para receber novas ideias">
          <figcaption>Um espaço aberto para sua próxima ideia.</figcaption>
        </figure>
        <div class="auth-content">
          <header>
            <p class="eyebrow">Fale conosco</p>
            <h2>Contatos</h2>
          </header>
          <h3>Mande sua mensagem</h3>
          {{-- O token CSRF é inserido manualmente (em vez da diretiva @csrf)
               para evitar que o validador W3C acuse o atributo
               autocomplete="off" indevidamente presente em campos hidden.
               csrf_token() gera o mesmo valor que a diretiva usaria.
               old('campo') repopula o campo com o valor digitado
               anteriormente caso a validação falhe (evita o usuário
               ter que redigitar tudo de novo). --}}
          <form action="{{ route('contact') }}" method="POST"><input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div><label for="nome">Nome:</label><input type="text" id="nome" name="nome" value="{{ old('nome') }}" required></div>
            <div><label for="contatoEmail">E-mail:</label><input type="email" id="contatoEmail" name="contatoEmail" value="{{ old('contatoEmail') }}" required></div>
            <div><label for="mensagem">Mensagem:</label><textarea id="mensagem" name="mensagem" required>{{ old('mensagem') }}</textarea></div>
            <button type="submit">Enviar</button>
          </form>
        </div>
      </section>
    </div>

    {{-- As seções de Login e Cadastro só são renderizadas no HTML
         quando o visitante NÃO está autenticado — uma vez logado,
         esse bloco inteiro desaparece da página. --}}
    @if (!$isAuthenticated)
    <div class="page-article" style="background-color: #1c2c2a;">
      <section id="login" class="auth-section" style="padding-right: 0;">
        <div class="auth-content">
          <p class="eyebrow">Acesso</p>
          <h2>Login</h2>
          <form action="{{ route('login') }}" method="POST"><input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div><label for="loginUsername">Usuário</label><input type="text" id="loginUsername" name="username" autocomplete="username" required></div>
            <div><label for="loginPassword">Senha</label><input type="password" id="loginPassword" name="password" autocomplete="current-password" required></div>
            <button type="submit">Entrar</button>
          </form>
          <p>Ainda não tem cadastro? <a href="#register">Criar uma conta</a></p>
        </div>
        <figure class="auth-figure auth-figure--login">
          <img src="{{ asset('public/imgs/pipes.webp') }}?v={{ filemtime(public_path('imgs/pipes.webp')) }}" alt="Marca MoreSpaces para identificação segura">
          <figcaption>Seu espaço, sempre ao seu alcance.</figcaption>
        </figure>
      </section>
    </div>

    <div class="page-article">
      <section id="register" class="auth-section" style="padding-left: 0;">
        <figure class="auth-figure auth-figure--register">
          <img src="{{ asset('public/imgs/eletricstation.webp') }}?v={{ filemtime(public_path('imgs/eletricstation.webp')) }}" alt="Ambiente MoreSpaces pronto para novos membros">
          <figcaption>Faça parte do próximo capítulo.</figcaption>
        </figure>
        <div class="auth-content">
          <p class="eyebrow">Comece agora</p>
          <h2>Cadastro</h2>
          <form action="{{ route('register') }}" method="POST"><input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div><label for="registerName">Nome</label><input type="text" id="registerName" name="name" autocomplete="name" required></div>
            <div><label for="registerRg">RG</label><input type="text" id="registerRg" name="rg" required></div>
            <div><label for="registerUsername">Usuário</label><input type="text" id="registerUsername" name="username" autocomplete="username" required></div>
            <div><label for="registerPassword">Senha</label><input type="password" id="registerPassword" name="password" autocomplete="new-password" minlength="8" required></div>
            <button type="submit">Cadastrar</button>
          </form>
        </div>
      </section>
    </div>
    @endif

    <footer class="site-footer">
      <nav class="social-links" aria-label="Redes sociais">
        <a href="https://github.com" target="_blank" rel="noreferrer" aria-label="GitHub">
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <path
              d="M12 .5a12 12 0 0 0-3.79 23.04c.6.11.82-.26.82-.58v-2.02c-3.34.73-4.04-1.43-4.04-1.43-.55-1.39-1.34-1.76-1.34-1.76-1.09-.74.08-.73.08-.73 1.2.09 1.83 1.24 1.83 1.24 1.07 1.83 2.8 1.3 3.48.99.11-.78.42-1.3.76-1.6-2.67-.3-5.47-1.34-5.47-5.96 0-1.32.47-2.4 1.24-3.25-.12-.3-.54-1.52.12-3.17 0 0 1.01-.32 3.3 1.23a11.4 11.4 0 0 1 6.02 0c2.29-1.55 3.3-1.23 3.3-1.23.66 1.65.24 2.87.12 3.17.77.85 1.24 1.93 1.24 3.25 0 4.63-2.81 5.66-5.49 5.95.43.37.81 1.1.81 2.22v3.29c0 .32.22.70.83.58A12 12 0 0 0 12 .5Z" />
          </svg>
        </a>
        <a href="https://www.linkedin.com" target="_blank" rel="noreferrer" aria-label="LinkedIn">
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <path
              d="M20.45 20.45h-3.56v-5.57c0-1.33-.03-3.04-1.85-3.04-1.85 0-2.14 1.45-2.14 2.94v5.67H9.34V8.99h3.41v1.56h.05c.47-.9 1.63-1.85 3.35-1.85 3.59 0 4.25 2.36 4.25 5.43v6.32ZM5.32 7.43a2.06 2.06 0 1 1 0-4.12 2.06 2.06 0 0 1 0 4.12ZM3.54 20.45H7.1V8.99H3.54v11.46Z" />
          </svg>
        </a>
        <a href="https://www.instagram.com" target="_blank" rel="noreferrer" aria-label="Instagram">
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <path
              d="M7 2h10a5 5 0 0 1 5 5v10a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5V7a5 5 0 0 1 5-5Zm0 2a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V7a3 3 0 0 0-3-3H7Zm5 3.5A4.5 4.5 0 1 1 7.5 12 4.5 4.5 0 0 1 12 7.5Zm0 2A2.5 2.5 0 1 0 14.5 12 2.5 2.5 0 0 0 12 9.5Zm5.25-3.25a1.25 1.25 0 1 1-1.25 1.25 1.25 1.25 0 0 1 1.25-1.25Z" />
          </svg>
        </a>
      </nav>

      <p>Davi De Oliveira Costa - Sistemas Computacionais &copy; 2026 MoreSpaces. Todos os direitos reservados.</p>
    </footer>
  </main>
</div>
@endsection

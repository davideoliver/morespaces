@extends('layouts.app')

@section('content')
<div class="page-shell">
  <header class="site-header">
    <a class="brand" href="{{ route('home') }}#home" aria-label="MoreSpaces - Home">
      <span class="brand-mark" aria-hidden="true"><span>M</span></span><span>MoreSpaces</span>
    </a>
    <nav aria-label="Navegação principal">
      <a href="#home" aria-current="page">Home</a><a href="#about-start">Sobre</a><a href="#faq">FAQ</a>
      <a href="#contact">Contatos</a><a href="#login">Login</a><a href="#register">Cadastro</a>
    </nav>
  </header>
  @if (session('success'))
  <p class="form-message is-success" role="status">{{ session('success') }}</p>
  @endif
  @if ($errors->any())
  <div class="form-message is-error" role="alert">{{ $errors->first() }}</div>
  @endif

  <main class="page-content">
    <article class="page-article">
      <section id="home">
        <div class="hero-copy">
          <p class="eyebrow">MoreSpaces</p>
          <h1>Espaços modernos para ideias que querem crescer.</h1>
          <p>A MoreSpaces viabiliza a criação de espaços dos mais infinitos tipos para seus usuários e colaboradores.</p>
          <a class="button-link" href="#about">Conheça a MoreSpaces</a>
        </div>
      </section>
    </article>
    <aside id="about-start">
      <center>Criamos espaços para que ninguém precise se espaçar.</center>
    </aside>

    <article class="page-article">
      <section id="about">
        <header>
          <p class="eyebrow">Nossa história</p>
          <h1>Sobre a MoreSpaces</h1>
        </header>
        <div>
          <h2>Valores</h2>
          <p>Confiança, fidelidade, inovação e principalmente excelência em todas as nossas atuações.</p>
        </div>
        <div>
          <h2>História</h2>
          <p>Fundada em 1900, a MoreSpaces nasceu como uma companhia familiar e cresceu criando espaços para pessoas, projetos e novas ideias.</p>
        </div>
        <table>
          <caption>Eventos importantes para a história da MoreSpaces</caption>
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
    </article>

    <article class="page-article">
      <section id="faq">
        <header>
          <p class="eyebrow">Dúvidas comuns</p>
          <h1>FAQ</h1>
        </header>
        <div class="faq-carousel" data-faq-carousel>
          <div class="faq-carousel__controls">
            <button class="faq-nav" type="button" data-prev aria-label="Pergunta anterior">←</button>
            <button class="faq-nav" type="button" data-next aria-label="Próxima pergunta">→</button>
          </div>
          <ol class="faq-carousel__track">
            @foreach ($faqs as $faq)<li class="faq-slide">
              <h2>{{ $faq['question'] }}</h2>
              <p>{{ $faq['answer'] }}</p>
            </li>@endforeach
          </ol>
        </div>
      </section>
    </article>

    <article class="page-article">
      <section id="contact" class="auth-section">
        <header>
          <p class="eyebrow">Fale conosco</p>
          <h1>Contatos</h1>
        </header>
        <h2>Mande sua mensagem</h2>
        <form action="{{ route('contact') }}" method="POST">@csrf
          <div><label for="nome">Nome:</label><input type="text" id="nome" name="nome" value="{{ old('nome') }}" required></div>
          <div><label for="contatoEmail">E-mail:</label><input type="email" id="contatoEmail" name="contatoEmail" value="{{ old('contatoEmail') }}" required></div>
          <div><label for="mensagem">Mensagem:</label><textarea id="mensagem" name="mensagem" required>{{ old('mensagem') }}</textarea></div>
          <button type="submit">Enviar</button>
        </form>
      </section>
    </article>

    <article class="page-article" style="background-color: #1c2c2a;">
      <section id="login" class="auth-section">
        <p class="eyebrow">Acesso</p>
        <h1>Login</h1>
        <form action="{{ route('login') }}" method="POST">@csrf
          <div><label for="loginUsername">Usuário</label><input type="text" id="loginUsername" name="username" autocomplete="username" required></div>
          <div><label for="loginPassword">Senha</label><input type="password" id="loginPassword" name="password" autocomplete="current-password" required></div>
          <button type="submit">Entrar</button>
        </form>
        <p>Ainda não tem cadastro? <a href="#register">Criar uma conta</a></p>
      </section>
    </article>

    <article class="page-article">
      <section id="register" class="auth-section">
        <p class="eyebrow">Comece agora</p>
        <h1>Cadastro</h1>
        <form action="{{ route('register') }}" method="POST">@csrf
          <div><label for="registerName">Nome</label><input type="text" id="registerName" name="name" autocomplete="name" required></div>
          <div><label for="registerRg">RG</label><input type="text" id="registerRg" name="rg" required></div>
          <div><label for="registerUsername">Usuário</label><input type="text" id="registerUsername" name="username" autocomplete="username" required></div>
          <div><label for="registerPassword">Senha</label><input type="password" id="registerPassword" name="password" autocomplete="new-password" minlength="8" required></div>
          <button type="submit">Cadastrar</button>
        </form>
      </section>
    </article>

    <footer class="site-footer">
      <div class="social-links" aria-label="Redes sociais">
        <a href="https://github.com" target="_blank" rel="noreferrer" aria-label="GitHub">GitHub</a>
        <a href="https://www.linkedin.com" target="_blank" rel="noreferrer" aria-label="LinkedIn">LinkedIn</a>
        <a href="https://www.instagram.com" target="_blank" rel="noreferrer" aria-label="Instagram">Instagram</a>
      </div>
      <p>Davi De Oliveira Costa - Sistemas Computacionais &copy; 2026 MoreSpaces. Todos os direitos reservados.</p>
    </footer>
  </main>
</div>
@endsection
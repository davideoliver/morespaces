{{-- Herda a estrutura HTML comum (head, scripts, css) do layout base. --}}
@extends('layouts.app')

@section('content')
<div class="page-shell">
  <x-site-header :is-authenticated="$isAuthenticated" :is-spaces-user="$isSpacesUser" />
  {{-- Faixa de mensagens de feedback (sucesso de formulário ou erros
       de validação). Some sozinha após alguns segundos via a
       animação CSS "collapseFlashMessage" definida em style.css. --}}
  <x-flash-messages />

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
        {{-- Tabela alimentada dinamicamente pelo array $events vindo da
             configuração da landing page. --}}
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
             são gerados a partir do array $faqs da configuração, e a
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
      <x-auth-section section-id="contact" section-style="padding-left: 0;" figure-class="auth-figure--contact" image="parking.webp" image-alt="Espaço MoreSpaces preparado para receber novas ideias" caption="Um espaço aberto para sua próxima ideia.">
        {{-- filemtime() é usado como "cache buster": ao anexar a data de
             modificação do arquivo na URL da imagem, o navegador é
             forçado a buscar a versão mais recente sempre que a imagem
             for alterada no servidor, evitando cache desatualizado. --}}
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
      </x-auth-section>
    </div>

    {{-- As seções de Login e Cadastro só são renderizadas no HTML
         quando o visitante NÃO está autenticado — uma vez logado,
         esse bloco inteiro desaparece da página. --}}
    @if (!$isAuthenticated)
    <div class="page-article" style="background-color: #1c2c2a;">
      <x-auth-section section-id="login" section-style="padding-right: 0;" figure-class="auth-figure--login" image="pipes.webp" image-alt="Marca MoreSpaces para identificação segura" caption="Seu espaço, sempre ao seu alcance." :image-first="false">
          <p class="eyebrow">Acesso</p>
          <h2>Login</h2>
          <form action="{{ route('login') }}" method="POST"><input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div><label for="loginUsername">Usuário</label><input type="text" id="loginUsername" name="username" autocomplete="username" required></div>
            <div><label for="loginPassword">Senha</label><input type="password" id="loginPassword" name="password" autocomplete="current-password" required></div>
            <button type="submit">Entrar</button>
          </form>
          <p>Ainda não tem cadastro? <a href="#register">Criar uma conta</a></p>
      </x-auth-section>
    </div>

    <div class="page-article">
      <x-auth-section section-id="register" section-style="padding-left: 0;" figure-class="auth-figure--register" image="eletricstation.webp" image-alt="Ambiente MoreSpaces pronto para novos membros" caption="Faça parte do próximo capítulo.">
          <p class="eyebrow">Comece agora</p>
          <h2>Cadastro</h2>
          <form action="{{ route('register') }}" method="POST"><input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div><label for="registerName">Nome</label><input type="text" id="registerName" name="name" autocomplete="name" required></div>
            <div><label for="registerRg">RG</label><input type="text" id="registerRg" name="rg" required></div>
            <div><label for="registerUsername">Usuário</label><input type="text" id="registerUsername" name="username" autocomplete="username" required></div>
            <div><label for="registerPassword">Senha</label><input type="password" id="registerPassword" name="password" autocomplete="new-password" minlength="8" required></div>
            <button type="submit">Cadastrar</button>
          </form>
      </x-auth-section>
    </div>
    @endif

    <x-site-footer />
  </main>
</div>
@endsection

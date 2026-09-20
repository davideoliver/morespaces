

<?php $__env->startSection('content'); ?>
<div class="page-shell">
  <header class="site-header">
    <a class="brand" href="<?php echo e(route('home')); ?>#home" aria-label="MoreSpaces - Home">
      <img class="brand-mark" src="<?php echo e(asset('public/imgs/mswhite.png')); ?>" alt="MoreSpaces Logo" aria-hidden="true">
    </a>
    <nav aria-label="Navegação principal">
      <a href="#home" aria-current="page">Home</a><a href="#about-start">Sobre</a><a href="#faq">FAQ</a>
      <a href="#contact">Contatos</a><a href="#login">Login</a><a href="#register">Cadastro</a>
    </nav>
  </header>
  <?php if(session('success')): ?>
  <p class="form-message is-success" role="status"><?php echo e(session('success')); ?></p>
  <?php endif; ?>
  <?php if($errors->any()): ?>
  <div class="form-message is-error" role="alert"><?php echo e($errors->first()); ?></div>
  <?php endif; ?>

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
            <?php $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><tr>
              <td><?php echo e($event['year']); ?></td>
              <td><?php echo e($event['description']); ?></td>
            </tr><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
            <?php $__currentLoopData = $faqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li class="faq-slide">
              <h2><?php echo e($faq['question']); ?></h2>
              <p><?php echo e($faq['answer']); ?></p>
            </li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </ol>
        </div>
      </section>
    </article>

    <article class="page-article">
      <section id="contact" class="auth-section" style="padding-left: 0;">
        <figure class="auth-figure auth-figure--contact">
          <img src="<?php echo e(asset('public/imgs/parking.jpg')); ?>?v=<?php echo e(filemtime(public_path('imgs/parking.jpg'))); ?>" alt="Espaço MoreSpaces preparado para receber novas ideias">
          <figcaption>Um espaço aberto para sua próxima ideia.</figcaption>
        </figure>
        <div class="auth-content">
          <header>
            <p class="eyebrow">Fale conosco</p>
            <h1>Contatos</h1>
          </header>
          <h2>Mande sua mensagem</h2>
          <form action="<?php echo e(route('contact')); ?>" method="POST"><?php echo csrf_field(); ?>
            <div><label for="nome">Nome:</label><input type="text" id="nome" name="nome" value="<?php echo e(old('nome')); ?>" required></div>
            <div><label for="contatoEmail">E-mail:</label><input type="email" id="contatoEmail" name="contatoEmail" value="<?php echo e(old('contatoEmail')); ?>" required></div>
            <div><label for="mensagem">Mensagem:</label><textarea id="mensagem" name="mensagem" required><?php echo e(old('mensagem')); ?></textarea></div>
            <button type="submit">Enviar</button>
          </form>
        </div>
      </section>
    </article>

    <article class="page-article" style="background-color: #1c2c2a;">
      <section id="login" class="auth-section" style="padding-right: 0;">
        <div class="auth-content">
          <p class="eyebrow">Acesso</p>
          <h1>Login</h1>
          <form action="<?php echo e(route('login')); ?>" method="POST"><?php echo csrf_field(); ?>
            <div><label for="loginUsername">Usuário</label><input type="text" id="loginUsername" name="username" autocomplete="username" required></div>
            <div><label for="loginPassword">Senha</label><input type="password" id="loginPassword" name="password" autocomplete="current-password" required></div>
            <button type="submit">Entrar</button>
          </form>
          <p>Ainda não tem cadastro? <a href="#register">Criar uma conta</a></p>
        </div>
        <figure class="auth-figure auth-figure--login">
          <img src="<?php echo e(asset('public/imgs/pipes.jpg')); ?>?v=<?php echo e(filemtime(public_path('imgs/pipes.jpg'))); ?>" alt="Marca MoreSpaces para identificação segura">
          <figcaption>Seu espaço, sempre ao seu alcance.</figcaption>
        </figure>
      </section>
    </article>

    <article class="page-article">
      <section id="register" class="auth-section" style="padding-left: 0;">
        <figure class="auth-figure auth-figure--register">
          <img src="<?php echo e(asset('public/imgs/eletricstation.jpg')); ?>?v=<?php echo e(filemtime(public_path('imgs/eletricstation.jpg'))); ?>" alt="Ambiente MoreSpaces pronto para novos membros">
          <figcaption>Faça parte do próximo capítulo.</figcaption>
        </figure>
        <div class="auth-content">
          <p class="eyebrow">Comece agora</p>
          <h1>Cadastro</h1>
          <form action="<?php echo e(route('register')); ?>" method="POST"><?php echo csrf_field(); ?>
            <div><label for="registerName">Nome</label><input type="text" id="registerName" name="name" autocomplete="name" required></div>
            <div><label for="registerRg">RG</label><input type="text" id="registerRg" name="rg" required></div>
            <div><label for="registerUsername">Usuário</label><input type="text" id="registerUsername" name="username" autocomplete="username" required></div>
            <div><label for="registerPassword">Senha</label><input type="password" id="registerPassword" name="password" autocomplete="new-password" minlength="8" required></div>
            <button type="submit">Cadastrar</button>
          </form>
        </div>
      </section>
    </article>

    <footer class="site-footer">
     <div class="social-links" aria-label="Redes sociais">
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
        </div>

      <p>Davi De Oliveira Costa - Sistemas Computacionais &copy; 2026 MoreSpaces. Todos os direitos reservados.</p>
    </footer>
  </main>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\morespaces\resources\views/home.blade.php ENDPATH**/ ?>
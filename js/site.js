/**
 * site.js — Script único da versão ESTÁTICA da MoreSpaces.
 *
 * Esta versão não depende de servidor: é composta apenas por HTML, CSS
 * e este arquivo, pensada para ser aberta diretamente pelo navegador
 * (ou hospedada em qualquer servidor de arquivos estáticos), sem PHP,
 * Composer ou banco de dados.
 *
 * Por não existir back-end, tudo o que na versão dinâmica (Laravel)
 * seria resolvido no servidor — sessão do usuário, validação dos
 * formulários, redirecionamentos com dados de sessão — precisa ser
 * simulado aqui no navegador. Por isso este arquivo concentra bem mais
 * lógica do que o scripts.js da versão Laravel: além da navegação e do
 * carrossel de FAQ (que são idênticos em ambas as versões), ele também
 * implementa uma "sessão" falsa via localStorage e o envio simulado
 * dos três formulários (login, cadastro e contato).
 */

/* ------------------------------------------------------------------ */
/* "Sessão" fake, guardada no localStorage (não existe mais servidor)  */
/* ------------------------------------------------------------------ */

// Chave usada no localStorage do navegador para guardar o "usuário
// logado". Como não há servidor, não existe sessão de verdade: o que
// existe é só este valor persistido localmente, no próprio navegador
// do visitante (por isso ele não é compartilhado entre dispositivos
// nem invalida automaticamente, ao contrário de uma sessão real).
const AUTH_KEY = 'msUsername';

// Lê o "usuário logado" atual do localStorage. Retorna string vazia
// quando não há ninguém autenticado, equivalente ao antigo
// session('username') do controller Laravel.
function getUsername() {
  return localStorage.getItem(AUTH_KEY) || '';
}

// Equivalente ao HomeController::isAuthenticated() da versão Laravel:
// considera autenticado qualquer usuário cujo nome não esteja vazio.
function isAuthenticated() {
  return getUsername() !== '';
}

// Equivalente ao HomeController::isSpacesUser(): a mesma regra
// simplificada — o username precisa conter a substring "spaces"
// (sem diferenciar maiúsculas/minúsculas) para liberar a área de
// Arquivos e o link correspondente no menu.
function isSpacesUser() {
  return getUsername().toLowerCase().includes('spaces');
}

// Grava o "login" no localStorage. Chamado após o envio bem-sucedido
// do formulário de login (não existe verificação real de senha).
function setUsername(username) {
  localStorage.setItem(AUTH_KEY, username);
}

// "Logout": apaga a chave do localStorage, voltando o visitante ao
// estado de não autenticado.
function clearUsername() {
  localStorage.removeItem(AUTH_KEY);
}

/* Mostra/esconde qualquer elemento marcado com data-auth="guest|user|spaces".
   Isso substitui os blocos @if($isAuthenticated)/@if($isSpacesUser) do
   Blade: como aqui não há renderização no servidor, cada elemento que
   depende do estado de login carrega um atributo data-auth no HTML, e
   esta função decide, em tempo de execução no navegador, se ele deve
   aparecer ou não — chamada sempre que o estado de autenticação muda
   (ao carregar a página, após login e após logout). */
function updateAuthUI() {
  document.querySelectorAll('[data-auth]').forEach((el) => {
    const rule = el.dataset.auth;
    let show = true;
    if (rule === 'guest') show = !isAuthenticated();
    else if (rule === 'user') show = isAuthenticated();
    else if (rule === 'spaces') show = isSpacesUser();
    el.style.display = show ? '' : 'none';
  });
}

/* ------------------------------------------------------------------ */
/* Mensagens de formulário (login, cadastro, contato)                  */
/* ------------------------------------------------------------------ */
const FORM_MESSAGE_TIMEOUT = 4000;
const formMessageTimers = new WeakMap();

/* Exibe a mensagem no elemento informado e agenda seu desaparecimento
   automático após FORM_MESSAGE_TIMEOUT milissegundos. Cancela qualquer
   temporizador anterior daquele mesmo elemento, evitando que uma nova
   mensagem seja apagada antes da hora por um timeout antigo ainda pendente. */
function showFormMessage(element, text, isSuccess) {
  if (!element) return;

  clearTimeout(formMessageTimers.get(element));

  element.textContent = text;
  element.classList.remove('is-success', 'is-error');
  element.classList.add(isSuccess ? 'is-success' : 'is-error');

  const timerId = setTimeout(() => {
    element.classList.remove('is-success', 'is-error');
    element.textContent = '';
  }, FORM_MESSAGE_TIMEOUT);

  formMessageTimers.set(element, timerId);
}

/* ------------------------------------------------------------------ */
/* Carrossel do FAQ                                                    */
/* ------------------------------------------------------------------ */

/**
 * Inicializa o carrossel de perguntas frequentes (FAQ).
 *
 * Idêntico ao equivalente da versão Laravel (scripts.js): procura o
 * container marcado com [data-faq-carousel] e, se ainda não tiver sido
 * inicializado (evita duplicar listeners caso a função seja chamada
 * mais de uma vez), liga os botões de "anterior" e "próximo" para
 * alternar a classe .is-active entre os slides (.faq-slide),
 * controlando qual pergunta fica visível.
 */
const initializeCarousel = () => {
  const carousel = document.querySelector('[data-faq-carousel]');
  if (!carousel || carousel.dataset.initialized === 'true') return;

  const slides = Array.from(carousel.querySelectorAll('.faq-slide'));
  const previousButton = carousel.querySelector('[data-prev]');
  const nextButton = carousel.querySelector('[data-next]');
  if (!slides.length || !previousButton || !nextButton) return;

  let current = 0;
  const render = () => slides.forEach((slide, index) => {
    slide.classList.toggle('is-active', index === current);
  });

  // O operador módulo (%) garante o comportamento "circular": ao
  // chegar no último slide e clicar em "próximo", volta para o
  // primeiro (e vice-versa para "anterior").
  previousButton.addEventListener('click', () => {
    current = (current - 1 + slides.length) % slides.length;
    render();
  });
  nextButton.addEventListener('click', () => {
    current = (current + 1) % slides.length;
    render();
  });
  carousel.dataset.initialized = 'true';
  render();
};

/* ------------------------------------------------------------------ */
/* Página                                                               */
/* ------------------------------------------------------------------ */

/**
 * Função principal, executada assim que o DOM estiver pronto.
 *
 * Diferente da versão Laravel, aqui a primeira coisa feita é
 * updateAuthUI(): como não existe Blade renderizando @if($isAuthenticated)
 * no servidor, a página HTML sempre chega "crua" (com todos os blocos
 * data-auth presentes) e é esta função quem decide, no próprio
 * navegador, o que mostrar ou esconder antes mesmo do usuário perceber.
 *
 * Depois disso, configura: menu mobile, navegação suave com destaque
 * do link ativo conforme o scroll, os três formulários de demonstração
 * (login, cadastro e contato — que aqui realmente fazem algo, ao
 * contrário da versão Laravel) e o carrossel de FAQ.
 */
const initializePage = () => {
  updateAuthUI();

  const header = document.querySelector('.site-header');
  const footer = document.querySelector('.site-footer');
  const menuToggle = document.querySelector('.menu-toggle');
  const navigation = document.querySelector('#main-navigation');
  const navigationLinks = Array.from(document.querySelectorAll('nav a[href^="#"]'));
  // Pré-calcula, para cada link de navegação, qual elemento da página
  // ele aponta — assim evitamos repetir querySelector a cada scroll.
  const navigationTargets = navigationLinks
    .map((link) => ({
      hash: link.getAttribute('href'),
      target: document.querySelector(link.getAttribute('href'))
    }))
    .filter(({ target }) => target);

  // Guarda a altura real do cabeçalho numa variável CSS (--header-height),
  // usada por outros elementos (ex.: .flash-messages) para se
  // posicionarem corretamente logo abaixo dele, inclusive quando o
  // header fica "fixed" no layout mobile.
  const updateHeaderHeight = () => {
    if (header) {
      document.documentElement.style.setProperty('--header-height', `${header.getBoundingClientRect().height}px`);
    }
  };

  // Marca visualmente (aria-current="page") qual link do menu
  // corresponde à seção atualmente em foco.
  const setActiveNavigation = (hash) => {
    navigationLinks.forEach((link) => {
      if (link.getAttribute('href') === hash) {
        link.setAttribute('aria-current', 'page');
      } else {
        link.removeAttribute('aria-current');
      }
    });
  };

  const closeNavigation = () => {
    if (!menuToggle || !navigation) return;
    menuToggle.setAttribute('aria-expanded', 'false');
    navigation.classList.remove('is-open');
  };

  // Alterna a abertura/fechamento do menu mobile (hambúrguer),
  // mantendo aria-expanded sincronizado para acessibilidade.
  menuToggle?.addEventListener('click', () => {
    if (!navigation) return;
    const isOpen = menuToggle.getAttribute('aria-expanded') === 'true';
    menuToggle.setAttribute('aria-expanded', String(!isOpen));
    navigation.classList.toggle('is-open', !isOpen);
  });

  // Fecha o menu mobile ao pressionar Esc, um padrão de acessibilidade.
  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') closeNavigation();
  });

  /**
   * Detecta, com base na posição de rolagem (scroll), qual seção
   * está "logo abaixo" do cabeçalho e atualiza o link ativo do menu
   * de acordo — é o que dá a sensação de "scrollspy" na Home.
   */
  const updateNavigationFromScroll = () => {
    if (!header || !navigationTargets.length) return;

    const headerBottom = header.getBoundingClientRect().bottom;
    const footerTop = footer?.getBoundingClientRect().top;

    // Quando o rodapé já apareceu na tela, nenhuma seção "âncora"
    // faz mais sentido como ativa — limpa o destaque do menu.
    if (footerTop !== undefined && footerTop <= headerBottom) {
      navigationLinks.forEach((link) => link.removeAttribute('aria-current'));
      return;
    }

    // reduce percorre todas as seções e mantém a última cujo topo já
    // passou da borda inferior do header — ou seja, a seção
    // "correntemente visível" no momento do scroll.
    const visibleTarget = navigationTargets.reduce((activeTarget, currentTarget) => {
      const targetTop = currentTarget.target.getBoundingClientRect().top;
      return targetTop <= headerBottom + 1 ? currentTarget : activeTarget;
    }, navigationTargets[0]);

    setActiveNavigation(visibleTarget.hash);
  };

  /* Rola suavemente até a seção correspondente ao hash informado,
     descontando a altura do cabeçalho fixo/sticky, e atualiza tanto o
     destaque do menu quanto a URL. É a mesma lógica usada pelos links
     de navegação e, agora, também pelo redirecionamento após o login.
     Foi extraída para uma função própria (em vez de ficar só dentro do
     listener de clique, como na versão Laravel) justamente para poder
     ser reaproveitada ali no handler de login mais abaixo. */
  const scrollToHash = (hash, { pushState = true } = {}) => {
    const target = document.querySelector(hash);
    if (!target || !header) return;

    setActiveNavigation(hash);
    const targetTop = target.getBoundingClientRect().top + window.scrollY;
    const headerOffset = header.getBoundingClientRect().height;

    window.scrollTo({
      top: Math.max(0, targetTop - headerOffset),
      behavior: 'smooth'
    });

    // pushState=false é usado no redirecionamento pós-login: queremos
    // rolar a tela até o topo, mas sem sujar o histórico do navegador
    // com uma entrada extra de "#home" que o usuário não pediu.
    if (pushState) history.pushState(null, '', hash);
  };

  // Substitui o comportamento padrão de "salto" do navegador ao
  // clicar em links âncora por um scroll suave que já leva em conta
  // a altura do header fixo, evitando que o conteúdo fique escondido
  // atrás dele.
  navigationLinks.forEach((link) => {
    link.addEventListener('click', (event) => {
      const targetHash = link.getAttribute('href');
      if (!document.querySelector(targetHash)) return;

      event.preventDefault();
      scrollToHash(targetHash);
      closeNavigation();
    });
  });

  // Reage à navegação por botões voltar/avançar do navegador.
  window.addEventListener('popstate', () => {
    setActiveNavigation(window.location.hash || '#home');
  });
  // { passive: true } informa ao navegador que este listener nunca
  // chama preventDefault(), permitindo otimizar a performance do scroll.
  window.addEventListener('scroll', updateNavigationFromScroll, { passive: true });
  window.addEventListener('resize', () => {
    updateHeaderHeight();
    updateNavigationFromScroll();
  });
  updateHeaderHeight();
  updateNavigationFromScroll();

  /* -------------------- Login --------------------
     Ao contrário da versão Laravel (onde o <form> realmente faz POST
     para o servidor), aqui o envio é inteiramente simulado no
     navegador: não existe verificação de senha nem de usuário
     cadastrado — qualquer usuário/senha preenchidos são aceitos,
     exatamente como o HomeController::login() original fazia. */
  const loginForm = document.getElementById('loginForm');
  loginForm?.addEventListener('submit', (event) => {
    event.preventDefault();
    const username = document.getElementById('loginUsername').value.trim();
    const password = document.getElementById('loginPassword').value;
    const message = document.getElementById('loginMessage');
    const ok = Boolean(username && password);

    showFormMessage(message, ok ? 'Login realizado com sucesso!' : 'Preencha usuário e senha.', ok);

    if (ok) {
      setUsername(username);
      updateAuthUI();
      loginForm.reset();

      // Aguarda o próximo frame para que o recálculo de layout provocado
      // por updateAuthUI() (seções de Login/Cadastro somem, "Arquivos"
      // aparece) já esteja aplicado antes de calcular o destino do scroll.
      requestAnimationFrame(() => {
        updateHeaderHeight();
        scrollToHash('#home', { pushState: false });
      });
    }
  });

  /* -------------------- Cadastro --------------------
     Mesma ideia do login: validação apenas de preenchimento e de
     tamanho mínimo da senha, sem persistência real em nenhum lugar
     (não existe banco de dados nesta versão estática). */
  const registerForm = document.getElementById('registerForm');
  registerForm?.addEventListener('submit', (event) => {
    event.preventDefault();
    const name = document.getElementById('registerName').value.trim();
    const rg = document.getElementById('registerRg').value.trim();
    const username = document.getElementById('registerUsername').value.trim();
    const password = document.getElementById('registerPassword').value;
    const message = document.getElementById('registerMessage');
    const isValid = Boolean(name && rg && username && password.length >= 8);

    showFormMessage(
      message,
      isValid
        ? 'Cadastro realizado com sucesso!'
        : 'Preencha todos os campos e use uma senha com no mínimo 8 caracteres.',
      isValid
    );

    // Igual ao controller original: cadastrar não loga automaticamente.
    if (isValid) registerForm.reset();
  });

  /* -------------------- Contato --------------------
     Também apenas validação de preenchimento dos campos; como não há
     servidor, a mensagem nunca é realmente enviada a lugar nenhum —
     o formulário só simula o sucesso para fins de demonstração da UI. */
  const contactForm = document.getElementById('contactForm');
  contactForm?.addEventListener('submit', (event) => {
    event.preventDefault();
    const nome = document.getElementById('nome').value.trim();
    const email = document.getElementById('contatoEmail').value.trim();
    const mensagem = document.getElementById('mensagem').value.trim();
    const message = document.getElementById('contactMessage');
    const ok = Boolean(nome && email && mensagem);

    showFormMessage(message, ok ? 'Mensagem enviada com sucesso!' : 'Preencha todos os campos.', ok);
    if (ok) contactForm.reset();
  });

  /* -------------------- Logout --------------------
     Equivalente ao HomeController::logout(): limpa a "sessão" (aqui,
     o localStorage), atualiza a interface e redireciona para a Home —
     só que, em vez de um redirect() do lado do servidor, é um simples
     window.location.href no navegador. */
  const logoutButton = document.getElementById('logoutButton');
  logoutButton?.addEventListener('click', () => {
    clearUsername();
    updateAuthUI();
    window.location.href = 'index.html#home';
  });

  initializeCarousel();
};

// Garante que initializePage só rode depois que o HTML estiver
// totalmente carregado — mas, se o script for injetado/executado
// depois desse momento (documento já "interactive"/"complete"),
// chama a função direto, sem esperar um evento que já passou.
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initializePage, { once: true });
} else {
  initializePage();
}

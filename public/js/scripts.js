/**
 * Inicializa o carrossel de perguntas frequentes (FAQ).
 *
 * Procura o container marcado com [data-faq-carousel] e, se ainda não
 * tiver sido inicializado (evita duplicar listeners caso a função
 * seja chamada mais de uma vez), liga os botões de "anterior" e
 * "próximo" para alternar a classe .is-active entre os slides
 * (.faq-slide), controlando qual pergunta fica visível.
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

/**
 * Função principal, executada assim que o DOM estiver pronto.
 * Configura: menu mobile, navegação suave com destaque do link
 * ativo conforme o scroll, os dois formulários de demonstração
 * (login/cadastro) e o carrossel de FAQ.
 */
const initializePage = () => {
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

  // Substitui o comportamento padrão de "salto" do navegador ao
  // clicar em links âncora por um scroll suave que já leva em conta
  // a altura do header fixo, evitando que o conteúdo fique escondido
  // atrás dele.
  navigationLinks.forEach((link) => {
    link.addEventListener('click', (event) => {
      const targetHash = link.getAttribute('href');
      const target = document.querySelector(targetHash);
      if (!target || !header) return;

      event.preventDefault();
      setActiveNavigation(targetHash);
      const targetTop = target.getBoundingClientRect().top + window.scrollY;
      const headerOffset = header.getBoundingClientRect().height;

      window.scrollTo({
        top: Math.max(0, targetTop - headerOffset),
        behavior: 'smooth'
      });
      // Atualiza a URL (ex.: .../#faq) sem recarregar a página,
      // permitindo compartilhar o link direto da seção.
      history.pushState(null, '', targetHash);
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

  const loginForm = document.getElementById('loginForm');
  const registerForm = document.getElementById('registerForm');

  // Validação client-side de demonstração para o formulário de login
  // (o formulário real do HTML usa action/method para POST no
  // servidor; este listener é apenas um feedback visual extra caso
  // exista um elemento #loginForm na página).
  loginForm?.addEventListener('submit', (event) => {
    event.preventDefault();
    const username = document.getElementById('loginUsername').value.trim();
    const password = document.getElementById('loginPassword').value;
    const message = document.getElementById('loginMessage');

    message.textContent = username && password ? 'Login realizado com sucesso!' : 'Preencha usuário e senha.';
    message.className = `form-message ${username && password ? 'is-success' : 'is-error'}`;
  });

  // Mesma ideia para o cadastro: validação simples de preenchimento
  // e tamanho mínimo de senha, apenas para feedback imediato ao usuário.
  registerForm?.addEventListener('submit', (event) => {
    event.preventDefault();
    const name = document.getElementById('registerName').value.trim();
    const rg = document.getElementById('registerRg').value.trim();
    const username = document.getElementById('registerUsername').value.trim();
    const password = document.getElementById('registerPassword').value;
    const message = document.getElementById('registerMessage');
    const isValid = name && rg && username && password.length >= 8;

    message.textContent = isValid ? 'Cadastro realizado com sucesso!' : 'Preencha todos os campos e use uma senha com no mínimo 8 caracteres.';
    message.className = `form-message ${isValid ? 'is-success' : 'is-error'}`;
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

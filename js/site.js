/* ------------------------------------------------------------------ */
/* "Sessão" fake, guardada no localStorage (não existe mais servidor)  */
/* ------------------------------------------------------------------ */
const AUTH_KEY = 'msUsername';

function getUsername() {
  return localStorage.getItem(AUTH_KEY) || '';
}

function isAuthenticated() {
  return getUsername() !== '';
}

function isSpacesUser() {
  return getUsername().toLowerCase().includes('spaces');
}

function setUsername(username) {
  localStorage.setItem(AUTH_KEY, username);
}

function clearUsername() {
  localStorage.removeItem(AUTH_KEY);
}

/* Mostra/esconde qualquer elemento marcado com data-auth="guest|user|spaces" */
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
/* Carrossel do FAQ                                                    */
/* ------------------------------------------------------------------ */
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
const initializePage = () => {
  updateAuthUI();

  const header = document.querySelector('.site-header');
  const footer = document.querySelector('.site-footer');
  const menuToggle = document.querySelector('.menu-toggle');
  const navigation = document.querySelector('#main-navigation');
  const navigationLinks = Array.from(document.querySelectorAll('nav a[href^="#"]'));
  const navigationTargets = navigationLinks
    .map((link) => ({
      hash: link.getAttribute('href'),
      target: document.querySelector(link.getAttribute('href'))
    }))
    .filter(({ target }) => target);

  const updateHeaderHeight = () => {
    if (header) {
      document.documentElement.style.setProperty('--header-height', `${header.getBoundingClientRect().height}px`);
    }
  };

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

  menuToggle?.addEventListener('click', () => {
    if (!navigation) return;
    const isOpen = menuToggle.getAttribute('aria-expanded') === 'true';
    menuToggle.setAttribute('aria-expanded', String(!isOpen));
    navigation.classList.toggle('is-open', !isOpen);
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') closeNavigation();
  });

  const updateNavigationFromScroll = () => {
    if (!header || !navigationTargets.length) return;

    const headerBottom = header.getBoundingClientRect().bottom;
    const footerTop = footer?.getBoundingClientRect().top;

    if (footerTop !== undefined && footerTop <= headerBottom) {
      navigationLinks.forEach((link) => link.removeAttribute('aria-current'));
      return;
    }

    const visibleTarget = navigationTargets.reduce((activeTarget, currentTarget) => {
      const targetTop = currentTarget.target.getBoundingClientRect().top;
      return targetTop <= headerBottom + 1 ? currentTarget : activeTarget;
    }, navigationTargets[0]);

    setActiveNavigation(visibleTarget.hash);
  };

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
      history.pushState(null, '', targetHash);
      closeNavigation();
    });
  });

  window.addEventListener('popstate', () => {
    setActiveNavigation(window.location.hash || '#home');
  });
  window.addEventListener('scroll', updateNavigationFromScroll, { passive: true });
  window.addEventListener('resize', () => {
    updateHeaderHeight();
    updateNavigationFromScroll();
  });
  updateHeaderHeight();
  updateNavigationFromScroll();

  /* -------------------- Login -------------------- */
  const loginForm = document.getElementById('loginForm');
  loginForm?.addEventListener('submit', (event) => {
    event.preventDefault();
    const username = document.getElementById('loginUsername').value.trim();
    const password = document.getElementById('loginPassword').value;
    const message = document.getElementById('loginMessage');
    const ok = Boolean(username && password);

    message.textContent = ok ? 'Login realizado com sucesso!' : 'Preencha usuário e senha.';
    message.className = `form-message ${ok ? 'is-success' : 'is-error'}`;

    if (ok) {
      setUsername(username);
      updateAuthUI();
      loginForm.reset();
    }
  });

  /* -------------------- Cadastro -------------------- */
  const registerForm = document.getElementById('registerForm');
  registerForm?.addEventListener('submit', (event) => {
    event.preventDefault();
    const name = document.getElementById('registerName').value.trim();
    const rg = document.getElementById('registerRg').value.trim();
    const username = document.getElementById('registerUsername').value.trim();
    const password = document.getElementById('registerPassword').value;
    const message = document.getElementById('registerMessage');
    const isValid = Boolean(name && rg && username && password.length >= 8);

    message.textContent = isValid
      ? 'Cadastro realizado com sucesso!'
      : 'Preencha todos os campos e use uma senha com no mínimo 8 caracteres.';
    message.className = `form-message ${isValid ? 'is-success' : 'is-error'}`;

    // Igual ao controller original: cadastrar não loga automaticamente.
    if (isValid) registerForm.reset();
  });

  /* -------------------- Contato -------------------- */
  const contactForm = document.getElementById('contactForm');
  contactForm?.addEventListener('submit', (event) => {
    event.preventDefault();
    const nome = document.getElementById('nome').value.trim();
    const email = document.getElementById('contatoEmail').value.trim();
    const mensagem = document.getElementById('mensagem').value.trim();
    const message = document.getElementById('contactMessage');
    const ok = Boolean(nome && email && mensagem);

    message.textContent = ok ? 'Mensagem enviada com sucesso!' : 'Preencha todos os campos.';
    message.className = `form-message ${ok ? 'is-success' : 'is-error'}`;
    if (ok) contactForm.reset();
  });

  /* -------------------- Logout -------------------- */
  const logoutButton = document.getElementById('logoutButton');
  logoutButton?.addEventListener('click', () => {
    clearUsername();
    updateAuthUI();
    window.location.href = 'index.html#home';
  });

  initializeCarousel();
};

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initializePage, { once: true });
} else {
  initializePage();
}

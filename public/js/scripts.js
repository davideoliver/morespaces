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

const initializePage = () => {
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

  const loginForm = document.getElementById('loginForm');
  const registerForm = document.getElementById('registerForm');

  loginForm?.addEventListener('submit', (event) => {
    event.preventDefault();
    const username = document.getElementById('loginUsername').value.trim();
    const password = document.getElementById('loginPassword').value;
    const message = document.getElementById('loginMessage');

    message.textContent = username && password ? 'Login realizado com sucesso!' : 'Preencha usuário e senha.';
    message.className = `form-message ${username && password ? 'is-success' : 'is-error'}`;
  });

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

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initializePage, { once: true });
} else {
  initializePage();
}

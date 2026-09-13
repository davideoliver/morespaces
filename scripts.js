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

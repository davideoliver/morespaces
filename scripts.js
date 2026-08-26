document.addEventListener('DOMContentLoaded', () => {
  const overlay = document.getElementById('loginOverlay');
  const openButtons = document.querySelectorAll('[data-login-open]');
  const closeButton = document.querySelector('[data-login-close]');
  const form = document.getElementById('cadastroForm');
  const passwordInput = document.getElementById('password');
  const passwordError = document.getElementById('password-error');
  const emailInput = document.getElementById('loginEmail');
  const emailError = document.getElementById('mensagem-erro');
  const urlInput = document.getElementById('loginWebsite');
  const urlError = document.getElementById('website-error');
  const radioGroup = document.getElementById('genero-group');
  const estadoInput = document.getElementById('loginEstado');
  const descricaoInput = document.getElementById('loginDescricao');
  const termosInput = document.getElementById('loginTermos');

  const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  const setFieldState = (field, errorElement, isValid) => {
    if (!field) return true;

    field.classList.remove('is-invalid', 'is-valid');
    if (errorElement) {
      errorElement.classList.remove('is-visible');
    }

    if (!field.value || (typeof field.value === 'string' && field.value.trim() === '')) {
      field.classList.add('is-invalid');
      if (errorElement) errorElement.classList.add('is-visible');
      return false;
    }

    if (isValid) {
      field.classList.add('is-valid');
      return true;
    }

    field.classList.add('is-invalid');
    if (errorElement) errorElement.classList.add('is-visible');
    return false;
  };

  const setRadioState = (isValid) => {
    radioGroup?.classList.remove('is-invalid', 'is-valid');
    if (isValid) {
      radioGroup?.classList.add('is-valid');
      return true;
    }
    radioGroup?.classList.add('is-invalid');
    return false;
  };

  const validatePassword = () => {
    if (!passwordInput) return true;
    const value = passwordInput.value;
    const isValid = value.length >= 8;

    passwordInput.classList.remove('is-invalid', 'is-valid');
    passwordError.classList.remove('is-visible');

    if (!value) {
      passwordInput.classList.add('is-invalid');
      passwordError.classList.add('is-visible');
      return false;
    }

    if (isValid) {
      passwordInput.classList.add('is-valid');
      return true;
    }

    passwordInput.classList.add('is-invalid');
    passwordError.classList.add('is-visible');
    return false;
  };

  const validateEmail = () => {
    if (!emailInput) return true;
    return setFieldState(emailInput, emailError, emailPattern.test(emailInput.value.trim()));
  };

  const validateUrl = () => {
    if (!urlInput) return true;
    const value = urlInput.value.trim();
    const isValid = /^https?:\/\//i.test(value);
    return setFieldState(urlInput, urlError, isValid);
  };

  const validateSelect = () => {
    if (!estadoInput) return true;
    const isValid = estadoInput.value !== '';
    estadoInput.classList.remove('is-invalid', 'is-valid');
    if (isValid) {
      estadoInput.classList.add('is-valid');
      return true;
    }
    estadoInput.classList.add('is-invalid');
    return false;
  };

  const validateTextarea = () => {
    if (!descricaoInput) return true;
    const isValid = descricaoInput.value.trim().length > 0;
    descricaoInput.classList.remove('is-invalid', 'is-valid');
    if (isValid) {
      descricaoInput.classList.add('is-valid');
      return true;
    }
    descricaoInput.classList.add('is-invalid');
    return false;
  };

  const validateCheckbox = () => {
    if (!termosInput) return true;
    const isValid = termosInput.checked;
    termosInput.classList.remove('is-invalid', 'is-valid');
    if (isValid) {
      termosInput.classList.add('is-valid');
      return true;
    }
    termosInput.classList.add('is-invalid');
    return false;
  };

  const validateRadio = () => {
    const selected = document.querySelector('input[name="genero"]:checked');
    return setRadioState(Boolean(selected));
  };

  const openLogin = () => {
    overlay?.classList.add('is-open');
    overlay?.setAttribute('aria-hidden', 'false');
  };

  const closeLogin = () => {
    overlay?.classList.remove('is-open');
    overlay?.setAttribute('aria-hidden', 'true');
  };

  openButtons.forEach((button) => button.addEventListener('click', openLogin));
  closeButton?.addEventListener('click', closeLogin);

  overlay?.addEventListener('click', (event) => {
    if (event.target === overlay) closeLogin();
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && overlay?.classList.contains('is-open')) {
      closeLogin();
    }
  });

  passwordInput?.addEventListener('input', validatePassword);
  emailInput?.addEventListener('input', validateEmail);
  emailInput?.addEventListener('blur', validateEmail);

  urlInput?.addEventListener('input', () => {
    let value = urlInput.value.replace(/\s/g, '');
    if (value && !/^https?:\/\//i.test(value)) {
      value = `https://${value}`;
    }
    urlInput.value = value;
    validateUrl();
  });

  descricaoInput?.addEventListener('input', validateTextarea);
  estadoInput?.addEventListener('change', validateSelect);
  termosInput?.addEventListener('change', validateCheckbox);
  document.querySelectorAll('input[name="genero"]').forEach((radio) => {
    radio.addEventListener('change', validateRadio);
  });

  form?.addEventListener('submit', (event) => {
    event.preventDefault();

    const isPasswordValid = validatePassword();
    const isEmailValid = validateEmail();
    const isUrlValid = validateUrl();
    const isRadioValid = validateRadio();
    const isSelectValid = validateSelect();
    const isDescriptionValid = validateTextarea();
    const isCheckboxValid = validateCheckbox();

    const isFormValid =
      isPasswordValid &&
      isEmailValid &&
      isUrlValid &&
      isRadioValid &&
      isSelectValid &&
      isDescriptionValid &&
      isCheckboxValid;

    if (isFormValid) {
      alert('Formulário enviado com sucesso!');
      closeLogin();
      form.reset();
      document.querySelectorAll('.is-valid').forEach((element) => element.classList.remove('is-valid'));
      document.querySelectorAll('.field-error').forEach((element) => element.classList.remove('is-visible'));
      return;
    }

    alert('Por favor, corrija os campos antes de enviar.');
  });
});

function updateTime() {
    const timeElement = document.getElementById('time');
    const now = new Date();
    const hours = now.getHours().toString().padStart(2, '0');
    const minutes = now.getMinutes().toString().padStart(2, '0');
    const seconds = now.getSeconds().toString().padStart(2, '0');
    const timeString = `${hours}:${minutes}:${seconds}`;
    timeElement.textContent = timeString;
}

setInterval(updateTime, 1000); // Atualiza a cada segundo
updateTime(); // Chama a função para exibir o tempo atual imediatamente

// 1. Função que usa Closure para manter o contador de consultas de forma privada
function criarRastreador() {
    let tentativas = 0; // Variável privada protegida pela closure

    return function(codigoPedido) {
        tentativas++;
        const elementoStatus = document.getElementById("statusFaq");
        
        if (!codigoPedido) {
            elementoStatus.textContent = "Por favor, digite um código de pedido.";
            return Promise.reject("Código vazio");
        }

        elementoStatus.textContent = `Consultando base de dados... (Consulta #${tentativas})`;

        // 2. Retornando uma Promise para simular a requisição assíncrona
        return new Promise((resolve, reject) => {
            setTimeout(() => {
                // Simulando uma verificação simples
                if (codigoPedido.toUpperCase() === "PEDRO123") {
                    resolve(`Sucesso! O pedido ${codigoPedido.toUpperCase()} foi despachado e está a caminho.`);
                } else {
                    reject(`Aviso: O código "${codigoPedido}" não foi encontrado em nosso sistema.`);
                }
            }, 1500); // Simula 1.5 segundos de espera
        });
    };
}

// Inicializa a closure fora do evento para preservar o estado de "tentativas"
const consultarStatus = criarRastreador();

// 3. Evento assíncrono acoplado ao botão do FAQ
document.addEventListener("DOMContentLoaded", () => {
    const botao = document.getElementById("btnRastrearFaq");
    const input = document.getElementById("inputPedido");
    const elementoStatus = document.getElementById("statusFaq");

    if (botao) {
        botao.addEventListener("click", async () => {
            const codigo = input.value.trim();
            
            try {
                // Aguarda a Promise ser resolvida
                const mensagemSucesso = await consultarStatus(codigo);
                elementoStatus.style.color = "green";
                elementoStatus.textContent = mensagemSucesso;
            } catch (erro) {
                // Captura caso a Promise seja rejeitada
                elementoStatus.style.color = "red";
                elementoStatus.textContent = erro;
            }
        });
    }
});
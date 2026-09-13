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

  const VideoBuffer = ({ videoId }) => {
    const [progress, setProgress] = React.useState(0);
    const [status, setStatus] = React.useState('Preparando vídeo...');
    const playerRef = React.useRef(null);

    React.useEffect(() => {
      let progressTimer;
      let isMounted = true;

      const updateProgress = () => {
        if (!playerRef.current || typeof playerRef.current.getVideoLoadedFraction !== 'function') return;

        const loadedFraction = playerRef.current.getVideoLoadedFraction();
        if (isMounted) {
          setProgress(Math.round(loadedFraction * 100));
        }
      };

      const createPlayer = () => {
        if (!isMounted || !window.YT || !window.YT.Player) return;

        playerRef.current = new window.YT.Player('presentation-video', {
          videoId,
          playerVars: { rel: 0, modestbranding: 1 },
          events: {
            onReady: () => {
              setStatus('Vídeo pronto');
              progressTimer = window.setInterval(updateProgress, 250);
              updateProgress();
            },
            onStateChange: (event) => {
              if (event.data === window.YT.PlayerState.BUFFERING) {
                setStatus('Carregando vídeo...');
              } else if (event.data === window.YT.PlayerState.PLAYING) {
                setStatus('Reproduzindo');
              } else if (event.data === window.YT.PlayerState.ENDED) {
                setStatus('Vídeo concluído');
              }
            },
            onError: () => setStatus('Não foi possível carregar o vídeo')
          }
        });
      };

      if (window.location.protocol === 'file:') {
        setStatus('Abra esta página pelo XAMPP para carregar o vídeo');
        return () => {
          isMounted = false;
        };
      }

      if (window.YT && window.YT.Player) {
        createPlayer();
      } else {
        const previousCallback = window.onYouTubeIframeAPIReady;
        window.onYouTubeIframeAPIReady = () => {
          previousCallback?.();
          createPlayer();
        };

        if (!document.querySelector('script[src="https://www.youtube.com/iframe_api"]')) {
          const apiScript = document.createElement('script');
          apiScript.src = 'https://www.youtube.com/iframe_api';
          document.head.appendChild(apiScript);
        }
      }

      return () => {
        isMounted = false;
        window.clearInterval(progressTimer);
        playerRef.current?.destroy();
      };
    }, [videoId]);

    return React.createElement(
      'div',
      { className: 'video-buffer' },
      React.createElement(
        'div',
        { id: 'presentation-video', className: 'video-buffer__player' },
        window.location.protocol === 'file:' && React.createElement(
          'div',
          { className: 'video-buffer__fallback' },
          React.createElement('strong', null, 'O vídeo precisa ser aberto por um servidor local.'),
          React.createElement('a', { href: `https://www.youtube.com/watch?v=${videoId}`, target: '_blank', rel: 'noreferrer' }, 'Abrir vídeo no YouTube')
        )
      ),
      React.createElement(
        'div',
        { className: 'video-buffer__status', 'aria-live': 'polite' },
        React.createElement('span', null, status),
        React.createElement('span', null, `${progress}%`)
      ),
      React.createElement(
        'div',
        { className: 'video-buffer__track', role: 'progressbar', 'aria-label': 'Carregamento do vídeo', 'aria-valuemin': '0', 'aria-valuemax': '100', 'aria-valuenow': progress },
        React.createElement('div', { className: 'video-buffer__progress', style: { width: `${progress}%` } })
      )
    );
  };

  document.addEventListener('DOMContentLoaded', () => {
    const videoRoot = document.getElementById('video-buffer-app');
    if (videoRoot && window.React && window.ReactDOM) {
      ReactDOM.createRoot(videoRoot).render(
        React.createElement(VideoBuffer, { videoId: videoRoot.dataset.videoId })
      );
    }
  });

  const interpolateColor = (start, end, amount) => {
    const startRgb = start.match(/[A-Fa-f\d]{2}/g).map((value) => parseInt(value, 16));
    const endRgb = end.match(/[A-Fa-f\d]{2}/g).map((value) => parseInt(value, 16));
    const color = startRgb.map((value, index) => Math.round(value + (endRgb[index] - value) * amount));
    return `#${color.map((value) => value.toString(16).padStart(2, '0')).join('')}`;
  };

  const GradualBackground = () => {
    React.useEffect(() => {
      const startColor = '#332e16';
      const targetColor = '#9b9476';
      let progress = 0;

      const updateBackground = () => {
        progress = Math.min(progress + 0.002, 1);
        document.body.style.setProperty('--bg', interpolateColor(startColor, targetColor, progress));
      };

      const intervalId = window.setInterval(updateBackground, 1000);
      updateBackground();

      return () => window.clearInterval(intervalId);
    }, []);

    return null;
  };

  document.addEventListener('DOMContentLoaded', () => {
    const backgroundRoot = document.getElementById('background-color-app');
    if (backgroundRoot && window.React && window.ReactDOM) {
      ReactDOM.createRoot(backgroundRoot).render(React.createElement(GradualBackground));
    }
  });

  const TypingText = ({ text }) => {
    const [visibleText, setVisibleText] = React.useState('');

    React.useEffect(() => {
      let characterIndex = 0;
      const intervalId = window.setInterval(() => {
        characterIndex += 1;
        setVisibleText(text.slice(0, characterIndex));

        if (characterIndex >= text.length) {
          window.clearInterval(intervalId);
        }
      }, 35);

      return () => window.clearInterval(intervalId);
    }, [text]);

    return React.createElement(
      'p',
      { className: 'typing-text', 'aria-label': text },
      visibleText,
      visibleText.length < text.length && React.createElement('span', { className: 'typing-caret', 'aria-hidden': 'true' })
    );
  };

  document.addEventListener('DOMContentLoaded', () => {
    const typingRoot = document.getElementById('typing-animation-app');
    if (typingRoot && window.React && window.ReactDOM) {
      ReactDOM.createRoot(typingRoot).render(
        React.createElement(TypingText, { text: typingRoot.dataset.text })
      );
    }
  });

  const FadeInImage = ({ source, alt }) => {
    const imageRef = React.useRef(null);

    React.useEffect(() => {
      let opacity = 0;
      const intervalId = window.setInterval(() => {
        opacity = Math.min(opacity + 0.05, 1);
        if (imageRef.current) {
          imageRef.current.style.opacity = opacity;
        }

        if (opacity >= 1) {
          window.clearInterval(intervalId);
        }
      }, 50);

      return () => window.clearInterval(intervalId);
    }, []);

    return React.createElement('img', {
      ref: imageRef,
      src: source,
      alt,
      className: 'fade-in-image'
    });
  };

  document.addEventListener('DOMContentLoaded', () => {
    const fadeInRoot = document.getElementById('fade-in-image-app');
    if (fadeInRoot && window.React && window.ReactDOM) {
      ReactDOM.createRoot(fadeInRoot).render(
        React.createElement(FadeInImage, {
          source: fadeInRoot.dataset.src,
          alt: fadeInRoot.dataset.alt
        })
      );
    }
  });

  const SlidingText = ({ text }) => {
    const [isRunning, setIsRunning] = React.useState(true);

    return React.createElement(
      React.Fragment,
      null,
      React.createElement(
        'div',
        { className: `sliding-text-viewport${isRunning ? ' is-running' : ''}` },
        React.createElement('span', { className: 'sliding-text-track', 'aria-label': text }, `${text}   ${text}`)
      ),
      React.createElement(
        'button',
        {
          type: 'button',
          className: 'sliding-text-toggle',
          onClick: () => setIsRunning((running) => !running),
          'aria-pressed': isRunning
        },
        isRunning ? 'Pausar texto' : 'Iniciar texto'
      )
    );
  };

  document.addEventListener('DOMContentLoaded', () => {
    const slidingTextRoot = document.getElementById('sliding-text-app');
    if (slidingTextRoot && window.React && window.ReactDOM) {
      ReactDOM.createRoot(slidingTextRoot).render(
        React.createElement(SlidingText, { text: slidingTextRoot.dataset.text })
      );
    }
  });
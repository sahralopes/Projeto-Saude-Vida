<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="shortcut icon" href="img/favicon-16x16.png" type="image/x-icon" />
  <link rel="stylesheet" href="css/style-login.css" />
  <title>Verificação de E-mail — Saúde & Vida</title>
</head>
<body class="login">

  <!-- BOTÃO VOLTAR (usa o mesmo CSS da tela de login) -->
  <button id="btn-voltar" class="btn-voltar-login">⟵ Voltar</button>

  <!-- BOTÃO E PAINEL DE ACESSIBILIDADE (MESMO PADRÃO DO ADM) -->
  <div id="icone-acessibilidade">
    <button id="btn-acessibilidade" type="button" aria-label="Acessibilidade">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-gear-fill" viewBox="0 0 16 16">
        <path d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.858z"/>
      </svg>
    </button>
  </div>

  <div id="acessibilidade-container" style="display:none;">
    <div class="acessibilidade-header">
      <h4>Acessibilidade</h4>
      <button id="btn-fechar-acessibilidade" type="button" aria-label="Fechar">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
          <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
        </svg>
      </button>
    </div>
    
    <div class="acessibilidade-opcoes">
      <div class="opcao-tema">
        <label for="theme-toggle">Tema:</label>
        <div class="theme-toggle-container">
          <button id="theme-toggle" type="button" aria-label="Alternar tema">
            <div class="toggle-track">
              <div class="toggle-thumb">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-sun" viewBox="0 0 16 16">
                  <path d="M8 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6zm0 1a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0zm0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13zm8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5zM3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8zm10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0zm-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0zm9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707zM4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708z"/>
                </svg>
              </div>
            </div>
            <span id="tema-texto">Claro</span>
          </button>
        </div>
      </div>
  
      <div class="opcao-fonte">
        <label for="fonte-slider">Tamanho da Fonte:</label>
        <input type="range" id="fonte-slider" min="12" max="22" value="16">
        <span id="fonte-tamanho">16px</span>
      </div>
    </div>
  </div>

  <div class="main-login">
    <div class="card-login card-split">
      
      <!-- Lado esquerdo -->
      <div class="card-side">
        <div class="logo">
          <img src="img/Logo-menu.png" alt="Logo Saúde e Vida" />
        </div>
        <p>Confirme seu e-mail para concluir o cadastro</p>
      </div>

      <!-- Lado direito -->
      <div class="card-form">
        <h1>VERIFICAÇÃO</h1>

        <div id="feedback-message" class="feedback-message"></div>

        <div class="textfield">
          <label for="email-verificacao">E-mail cadastrado:</label>
          <input type="email" id="email-verificacao" placeholder="seu@email.com"/>
          <button id="btnEnviarCodigo" class="btn-enviar btn-codigo">Enviar código</button>
        </div>

        <div class="textfield">
          <label for="codigo">Código recebido:</label>
          <input type="text" id="codigo" placeholder="Digite o código"/>
        </div>

        <div class="remember-forgot" id="aviso-2fa"></div>

        <button id="btnConfirmar" class="btn-enviar">Confirmar</button>

        <div class="register-link">
          <p>Já confirmou antes? <a id="irLogin" href="pag-login.php">Ir para o login</a></p>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/emailjs-com@3/dist/email.min.js"></script>
  <!-- Configurações do EmailJS -->
  <script>
    window.EMAILJS_PUBLIC_KEY = "8Uw6P1YAI82JnT7Kd";
    window.EMAILJS_SERVICE_ID = "service_dsy053g";
    window.EMAILJS_TEMPLATE_ID = "template_1pyc24p";
  </script>
  <script src="js/script-verificacao.js"></script>
</body>
</html>

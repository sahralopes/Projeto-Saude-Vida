<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="shortcut icon" href="img/favicon-16x16.png" type="image/x-icon" />

  <!-- Reusa o mesmo CSS da tela de login/verificação -->
  <link rel="stylesheet" href="css/style-login.css" />

  <title>Esqueci a Senha — Saúde & Vida</title>

  <style>
    /* Ajustes específicos desta página */
    .card-form { padding-top: 70px; }
    .card-form h1 { top: 24px; }
    .textfield.compacto input { max-width: 420px; }
    .btn-enviar { margin-top: 12px; }
    .register-link { margin-top: 6px; }
  </style>
</head>

<body class="login">

  <div class="main-login">
    <div class="card-login card-split">

      <!-- Lado esquerdo -->
      <div class="card-side">
        <div class="logo">
          <img src="img/Logo-menu.png" alt="Logo Saúde e Vida" />
        </div>
        <p>Vamos te ajudar a redefinir sua senha</p>
      </div>

      <!-- Lado direito -->
      <div class="card-form">
        <h1>ESQUECI A SENHA</h1>

        <div id="feedback-message" class="feedback-message"></div>

        <div class="textfield compacto">
          <label for="email-reset">E-mail cadastrado:</label>
          <input type="email" id="email-reset" placeholder="seu@email.com" />
        </div>

        <div class="textfield compacto">
          <label for="nova-senha">Nova senha (8 letras):</label>
          <input type="password" id="nova-senha" placeholder="Ex: abcdefgh" />
        </div>

        <div class="textfield compacto">
          <label for="confirma-senha">Confirmar nova senha:</label>
          <input type="password" id="confirma-senha" placeholder="Repita a senha" />
        </div>

        <button id="btnConfirmarReset" class="btn-enviar">Confirmar</button>

        <div class="register-link">
          <p>Lembrou a senha? <a href="pag-login.php">Voltar ao login</a></p>
        </div>
      </div>
    </div>
  </div>

  <!-- ========================= -->
  <!-- BOTÃO DE ACESSIBILIDADE   -->
  <!-- ========================= -->
  <div id="icone-acessibilidade">
    <button id="btn-acessibilidade" type="button" aria-label="Acessibilidade">
      <!-- Ícone de engrenagem -->
      <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="white" class="bi bi-gear-fill" viewBox="0 0 16 16">
        <path d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.23 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1c1.095.264 1.318 1.285.872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169c.82-.446 1.841-.023 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34c.264-.82 1.285-1.318 2.105-.872l.31.169c1.283.698 2.686-.705 1.987-1.987l-.169-.31a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.169-.311c.698-1.283-.705-2.686-1.987-1.987l-.31.17a1.464 1.464 0 0 1-2.105-.872l-.1-.34zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.858z"/>
      </svg>
    </button>
  </div>

  <!-- PAINEL DE ACESSIBILIDADE -->
  <div id="acessibilidade-container" style="display:none;">
    <div class="acessibilidade-header">
      <h4>Acessibilidade</h4>
      <button id="btn-fechar-acessibilidade" type="button" aria-label="Fechar">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="white" class="bi bi-x" viewBox="0 0 16 16">
          <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
        </svg>
      </button>
    </div>

    <div class="acessibilidade-opcoes">
      <div class="opcao-tema">
        <label for="theme-toggle">Tema:</label>
        <div class="theme-toggle-container">
          <button id="theme-toggle" type="button">
            <div class="toggle-track">
              <div class="toggle-thumb"></div>
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

  <script src="js/script-resetar-senha.js"></script>

</body>
</html>

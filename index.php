<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="shortcut icon" href="img/favicon-16x16.png" type="image/x-icon">
  <link rel="stylesheet" href="css/style-principal.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  <title>Saúde & Vida</title>
</head>
<body class="main" id="topo">

  <script>
    window.dadosUsuarioPHP = <?php 
      if(isset($_SESSION['usuario_id'])) {
          echo json_encode([
              'nomeCompleto' => $_SESSION['usuario_nome'],
              'login' => $_SESSION['usuario_login']
          ]); 
      } else {
          echo 'null';
      }
    ?>;
  </script>

  <header>
    <nav class="navbar navbar-expand-lg navbar-dark">
      <div class="container-fluid">
        <img src="img/Logo-menu (sem_fundo).png" alt="Logo Saúde e Vida">

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Alternar navegação">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto">
            <div class="dropdown">
                <a href="#" class="btn btn-secondary dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                    Especialidades
                </a>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <li><a class="dropdown-item" href="#">Clínica Geral</a></li>
                    <li><a class="dropdown-item" href="#">Pediatria</a></li>
                    <li><a class="dropdown-item" href="#">Ginecologia</a></li>
                    <li><a class="dropdown-item" href="#">Cardiologista</a></li>
                    <li><a class="dropdown-item" href="#">Psicologia</a></li>
                    <li><a class="dropdown-item" href="#">Neurologia</a></li>
                    <li><a class="dropdown-item" href="#">Oftalmologista</a></li>
                    <li><a class="dropdown-item" href="#">Obstetrícia</a></li>
                    <li><a class="dropdown-item" href="#">Ortopedista</a></li>
                </ul>
            </div>
            <li class="nav-item"><a class="nav-link" href="#sobre-nos">Sobre nós</a></li>
            <li class="nav-item"><a class="nav-link" href="#duvidas">Dúvidas</a></li>
            <li class="nav-item"><a class="nav-link" href="pag-consultas.php">Suas Consultas</a></li>

            <?php if (!isset($_SESSION['usuario_id'])): ?>
              <!-- Login só aparece se NÃO estiver logado -->
              <li class="nav-item"><a class="nav-link" href="pag-login.php">Login</a></li>
            <?php endif; ?>

            <?php if (isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'ADMIN'): ?>
              <li class="nav-item">
                <a class="nav-link" href="pag-admin.php" style="color: #ffd700; font-weight: 600;">
                   Painel Adm
                </a>
              </li>
            <?php endif; ?>

            <?php if (isset($_SESSION['usuario_id'])): ?>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="statusDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  Status
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="statusDropdown">
                  <li class="dropdown-item-text">
                    <strong><?php echo htmlspecialchars($_SESSION['usuario_nome']); ?></strong><br>
                    <small>Login: <?php echo htmlspecialchars($_SESSION['usuario_login']); ?></small>
                  </li>
                  <li><hr class="dropdown-divider"></li>
                  <li class="dropdown-item-text">
                    Tipo: <?php 
                      if (isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'ADMIN') {
                        echo 'Administrador';
                      } else {
                        echo 'Usuário';
                      }
                    ?>
                  </li>
                  <li><hr class="dropdown-divider"></li>
                </ul>
              </li>
            <?php endif; ?>
            
            <li class="nav-item" id="logout-item" style="display: none;">
              <a class="nav-link" href="#" id="btn-logout">Sair</a>
            </li>
          </ul>

        </div>
      </div>
    </nav>
  </header>

  <div id="icone-acessibilidade">
    <button id="btn-acessibilidade" type="button" aria-label="Acessibilidade">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-gear-fill" viewBox="0 0 16 16">
        <path d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872l-.1-.34zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.858z"/>
      </svg>
    </button>
  </div>

  <div id="acessibilidade-container">
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

  <div id="mensagem-boas-vindas" class="alert alert-success alert-dismissible fade show" role="alert" style="display: none; position: fixed; top: 80px; right: 20px; z-index: 1050%;">
    <div class="d-flex align-items-center">
      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-person-check me-2" viewBox="0 0 16 16">
        <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Zm1.679-4.493-1.335 2.226a.75.75 0 0 1-1.174.144l-.774-.773a.5.5 0 0 1 .708-.708l.547.548 1.17-1.951a.5.5 0 1 1 .858.515ZM11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM8 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"/>
        <path d="M8.256 14a4.474 4.474 0 0 1-.229-1.004H3c.001-.246.154-.986.832-1.664C4.484 10.68 5.711 10 8 10c.26 0 .507.009.74.025.226-.341.496-.65.804-.918C9.077 9.038 8.564 9 8 9c-5 0-6 3-6 4s1 1 1 1h5.256Z"/>
      </svg>
      <span id="texto-boas-vindas">Bem-vindo!</span>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>

  <div class="container">
    <div class="container-carrossel">
      <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
          <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active"></button>
          <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1"></button>
          <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2"></button>
          <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="3"></button>
        </div>

        <div class="carousel-inner">
          <div class="carousel-item active"><img src="img/pexels-equipe.jpg" class="d-block w-100" alt=""></div>
          <div class="carousel-item"><img src="img/pexels-raio-x.jpg" class="d-block w-100" alt=""></div>
          <div class="carousel-item"><img src="img/pexels-criança.jpg" class="d-block w-100" alt=""></div>
          <div class="carousel-item"><img src="img/pexels-exame.jpg" class="d-block w-100" alt=""></div>
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev" aria-label="Anterior">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next" aria-label="Próximo">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
        </button>
      </div>
    </div>

    <div class="text-container">
      <p>
        A Clínica Saúde e Vida é uma plataforma de atendimento online dedicada a oferecer cuidados médicos e psicológicos de qualidade, unindo tecnologia e humanização.
      </p>
      <div class="btn-container">
        <a href="#" id="btnAgendar" class="btn-agendar-consulta">Agende sua consulta</a>
      </div>
    </div>
  </div>

  <div class="container-carrossel-cards">
    <div id="carouselCards" class="carousel slide">
      <div class="carousel-inner">
        <div class="carousel-item active">
          <div class="row justify-content-center g-4">
            <div class="card text-center mb-3" style="width:170px;">
              <img src="img/cardiologista-Photoroom.png" class="card-img-top" alt="">
              <div class="card-body"><h5 class="card-title">Cardiologista</h5><a href="#" class="btn btn-primary">Saiba mais</a></div>
            </div>
            <div class="card text-center mb-3" style="width:170px;">
              <img src="img/psicologista-Photoroom.png" class="card-img-top" alt="">
              <div class="card-body"><h5 class="card-title">Psicologia</h5><a href="#" class="btn btn-primary">Saiba mais</a></div>
            </div>
            <div class="card text-center mb-3" style="width:170px;">
              <img src="img/neurologista-Photoroom.png" class="card-img-top" alt="">
              <div class="card-body"><h5 class="card-title">Neurologia</h5><a href="#" class="btn btn-primary">Saiba mais</a></div>
            </div>
          </div>
        </div>

        <div class="carousel-item">
          <div class="row justify-content-center g-4">
            <div class="card text-center mb-3" style="width:170px;">
              <img src="img/clinico-geral2-Photoroom.png" class="card-img-top" alt="">
              <div class="card-body"><h5 class="card-title">Clínico Geral</h5><a href="#" class="btn btn-primary">Saiba mais</a></div>
            </div>
            <div class="card text-center mb-3" style="width:170px;">
              <img src="img/ginecologista-Photoroom.png" class="card-img-top" alt="">
              <div class="card-body"><h5 class="card-title">Ginecologia</h5><a href="#" class="btn btn-primary">Saiba mais</a></div>
            </div>
            <div class="card text-center mb-3" style="width:170px;">
              <img src="img/pediatria-Photoroom.png" class="card-img-top" alt="">
              <div class="card-body"><h5 class="card-title">Pediatria</h5><a href="#" class="btn btn-primary">Saiba mais</a></div>
            </div>
          </div>
        </div>
      </div>

      <button class="carousel-control-prev" type="button" data-bs-target="#carouselCards" data-bs-slide="prev" aria-label="Anterior">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#carouselCards" data-bs-slide="next" aria-label="Próximo">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
      </button>
    </div>
  </div>

  <div id="sobre-nos" class="anchor-target" aria-hidden="true"></div>
  <div id="duvidas" class="anchor-target" aria-hidden="true"></div>

  <footer>
    <div class="footer-container">
      <div class="row-footer">
        <div class="footer-col">
          <h3>Clínica Médica <br>Saúde & Vida</h3>
          <p>Endereço: Rua Exemplo, 123 - Bairro, Cidade, Estado.</p>
          <p>Telefone: (00) 1234-5678 | WhatsApp: (00) 98765-4321</p>
          <p>E-mail: contato@saudevida.com</p>
        </div>
        <div class="footer-col">
          <h4>Clínica</h4>
          <ul>
            <li><a href="#">Quem Somos?</a></li>
            <li><a href="#">Especialidades</a></li>
            <li><a href="#">Fale Conosco</a></li>
          </ul>
        </div>
        <div class="footer-col">
          <h4>Siga-nos em nossas <br>redes sociais</h4>
          <div class="footer-social-media">
              <a href="https://wa.me/+5521983026169?text=MENSAGEM" target="_blank" class="footer-link" id="whatsapp">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-whatsapp" viewBox="0 0 16 16">
                <path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232"/>
                </svg>
              </a>
            <a href="#" class="footer-link" id="facebook" aria-label="Facebook">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-facebook" viewBox="0 0 16 16">
                <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951"/>
              </svg>
            </a>
            <a href="https://www.instagram.com" target="_blank" class="footer-link" id="instagram">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-instagram" viewBox="0 0 16 16">
              <path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.9 3.9 0 0 0-1.417.923A3.9 3.9 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.9 3.9 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.9 3.9 0 0 0-.923-1.417A3.9 3.9 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599s.453.546.598.92c.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.5 2.5 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.5 2.5 0 0 1-.92-.598 2.5 2.5 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233s.008-2.388.046-3.231c.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92s.546-.453.92-.598c.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92m-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217m0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334"/>
              </svg>
            </a>
          </div>
        </div>
        <div class="footer-col">
          <h4>Horário de Funcionamento</h4>
          <p>Segunda a Sexta: 8h às 18h | Sábado: 8h às 14h</p>
        </div>
      </div>
    </div>
  </footer>

  <div class="modal fade" id="modalLogout" tabindex="-1" aria-labelledby="modalLogoutLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalLogoutLabel">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-question-circle text-warning me-2" viewBox="0 0 16 16">
              <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
              <path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.187-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z"/>
            </svg>
            Confirmar Logout
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p class="mb-0">Tem certeza que deseja sair do sistema?</p>
          <small class="text-muted">Você precisará fazer login novamente para acessar suas consultas.</small>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-danger" id="confirmLogout">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right me-1" viewBox="0 0 16 16">
              <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0v2z"/>
              <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
            </svg>
            Sair do Sistema
          </button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

  <!-- Script pequeno só para o botão "Status" reaproveitar o mesmo logout -->
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var statusLogout = document.getElementById('status-logout-link');
      var btnLogout = document.getElementById('btn-logout');
      if (statusLogout && btnLogout) {
        statusLogout.addEventListener('click', function (e) {
          e.preventDefault();
          btnLogout.click(); // dispara o mesmo fluxo de logout que você já tem
        });
      }
    });
  </script>

  <script src="js/script-principal.js"></script>
</body>
</html>

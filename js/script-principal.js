document.addEventListener('DOMContentLoaded', function () {
  // ===== Verificar login e mostrar/esconder botão logout =====
  var logoutItem = document.getElementById('logout-item');
  var btnLogout = document.getElementById('btn-logout');
  
  function checkLoginStatus() {
    // CORREÇÃO: Tenta pegar os dados do PHP injetado primeiro
    var usuarioLogado = window.dadosUsuarioPHP;

    // Se não vier do PHP, tenta localStorage (fallback para casos específicos)
    if (!usuarioLogado) {
        try {
            usuarioLogado = JSON.parse(localStorage.getItem('usuarioLogado') || 'null');
        } catch (err) {
            usuarioLogado = null;
        }
    }
    
    if (usuarioLogado && logoutItem) {
      logoutItem.style.display = 'block';
      // Mostra mensagem de boas-vindas (se ainda não foi fechada recentemente?)
      // Por padrão mostra sempre que carrega se estiver logado.
      showWelcomeMessage(usuarioLogado.nomeCompleto);
    } else if (logoutItem) {
      logoutItem.style.display = 'none';
    }
  }

  // ===== Mostrar mensagem de boas-vindas =====
  function showWelcomeMessage(nomeCompleto) {
    var mensagemBoasVindas = document.getElementById('mensagem-boas-vindas');
    var textoBoasVindas = document.getElementById('texto-boas-vindas');
    
    if (mensagemBoasVindas && textoBoasVindas) {
      var primeiroNome = nomeCompleto ? nomeCompleto.split(' ')[0] : 'Usuário';
      textoBoasVindas.textContent = `Bem-vindo, ${primeiroNome}!`;
      mensagemBoasVindas.style.display = 'block';
      
      setTimeout(function() {
        if (mensagemBoasVindas.style.display !== 'none') {
          var bsAlert = new bootstrap.Alert(mensagemBoasVindas);
          bsAlert.close();
        }
      }, 5000);
    }
  }
  
  // Verificar status ao carregar a página
  checkLoginStatus();
  
  // Função de logout
  function fazerLogout() {
    try {
      // Limpa localStorage
      localStorage.removeItem('usuarioLogado');
      localStorage.removeItem('redirectAfterLogin');
      localStorage.removeItem('redirectAfterVerify');
      
      // Redireciona para o script PHP de logout para destruir a sessão
      window.location.href = 'pag-logout.php';
    } catch (e) {
      console.error('Erro ao fazer logout:', e);
    }
  }
  
  // Event listener para o botão de logout
  if (btnLogout) {
    btnLogout.addEventListener('click', function(e) {
      e.preventDefault();
      var modalLogout = new bootstrap.Modal(document.getElementById('modalLogout'));
      modalLogout.show();
    });
  }
  
  // Event listener para confirmar logout no modal
  var confirmLogoutBtn = document.getElementById('confirmLogout');
  if (confirmLogoutBtn) {
    confirmLogoutBtn.addEventListener('click', function() {
      var modalLogout = bootstrap.Modal.getInstance(document.getElementById('modalLogout'));
      modalLogout.hide();
      fazerLogout();
    });
  }

  // ===== Tema claro/escuro =====
  var themeToggleButton = document.getElementById('theme-toggle');
  var body = document.body;

  try {
    if (localStorage.getItem('theme') === 'dark') {
      body.classList.add('dark-mode');
    }
  } catch (e) {}

  if (themeToggleButton) {
    themeToggleButton.addEventListener('click', function () {
      body.classList.toggle('dark-mode');
      var isDark = body.classList.contains('dark-mode');
      try {
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
      } catch (e) {}
    });
  }

  // ===== Botão "Agendar" -> se não logado, manda pro login =====
  var btnAgendar = document.getElementById('btnAgendar');
  if (btnAgendar) {
    btnAgendar.addEventListener('click', function (e) {
      e.preventDefault();
      var destino = 'pag-agendamento.php';

      // Se já temos dados do PHP, vai direto
      if (window.dadosUsuarioPHP) {
          window.location.href = destino;
          return;
      }

      // Fallback localStorage
      var usuarioLogado = null;
      try {
        usuarioLogado = JSON.parse(localStorage.getItem('usuarioLogado') || 'null');
      } catch (err) { usuarioLogado = null; }

      if (!usuarioLogado) {
        // Salva intenção de redirecionamento
        try {
            localStorage.setItem('redirectAfterLogin', destino);
            localStorage.setItem('redirectAfterVerify', destino);
        } catch (e) {}
        window.location.href = `pag-login.php?returnUrl=${encodeURIComponent(destino)}`;
      } else {
        window.location.href = destino;
      }
    });
  }

  // ===== Acessibilidade (Fonte) =====
  var fonteSlider = document.getElementById('fonte-slider');
  var fonteTamanho = document.getElementById('fonte-tamanho');
  var acessibilidadeContainer = document.getElementById('acessibilidade-container');
  var btnAcessibilidade = document.getElementById('btn-acessibilidade');
  var btnFecharAcessibilidade = document.getElementById('btn-fechar-acessibilidade');
  var temaTexto = document.getElementById('tema-texto');

  var seletorTexto = 'p, a, li, h1, h2, h3, h4, h5, h6, button, label, input, textarea, small, span';
  function aplicarTamanhoFonte(px) {
    var nodes = document.querySelectorAll(seletorTexto);
    for (var i = 0; i < nodes.length; i++) {
      nodes[i].style.fontSize = px + 'px';
    }
  }

  function atualizarTextoTheme() {
    if (temaTexto) {
      temaTexto.textContent = body.classList.contains('dark-mode') ? 'Escuro' : 'Claro';
    }
  }

  if (fonteSlider && fonteTamanho) {
    var salvo = '16';
    try {
      salvo = localStorage.getItem('tamanhoFonte') || fonteSlider.value || '16';
    } catch (e) {}

    aplicarTamanhoFonte(salvo);
    fonteSlider.value = salvo;
    fonteTamanho.textContent = salvo + 'px';

    fonteSlider.addEventListener('input', function () {
      var novo = fonteSlider.value;
      aplicarTamanhoFonte(novo);
      fonteTamanho.textContent = novo + 'px';
      try {
        localStorage.setItem('tamanhoFonte', novo);
      } catch (e) {}
    });
  }

  if (btnAcessibilidade && acessibilidadeContainer) {
    btnAcessibilidade.addEventListener('click', function () {
      var visivel = acessibilidadeContainer.style.display === 'block';
      acessibilidadeContainer.style.display = visivel ? 'none' : 'block';
      atualizarTextoTheme();
    });
  }

  if (btnFecharAcessibilidade && acessibilidadeContainer) {
    btnFecharAcessibilidade.addEventListener('click', function () {
      acessibilidadeContainer.style.display = 'none';
    });
  }

  if (themeToggleButton) {
    themeToggleButton.addEventListener('click', function () {
      setTimeout(atualizarTextoTheme, 100);
    });
  }

  // ===== Navbar: rolagem suave =====
  function scrollToWithOffset(selector, offset) {
    var el = document.querySelector(selector);
    if (!el) return;
    var y = el.getBoundingClientRect().top + window.pageYOffset - (offset || 0);
    window.scrollTo({ top: y, behavior: 'smooth' });
  }

  var headerEl = document.querySelector('header');
  function getOffset() {
    return headerEl ? headerEl.offsetHeight + 8 : 0;
  }

  function closeNavbarIfOpen() {
    var nav = document.getElementById('navbarNav');
    if (nav && nav.classList.contains('show') && window.bootstrap) {
      var bsCollapse = bootstrap.Collapse.getOrCreateInstance(nav);
      bsCollapse.hide();
    }
  }

  var linkTopo = document.querySelector('a.nav-link[href="#topo"]');
  if (linkTopo) {
    linkTopo.addEventListener('click', function (e) {
      if (e && e.preventDefault) e.preventDefault();
      window.scrollTo({ top: 0, behavior: 'smooth' });
      closeNavbarIfOpen();
    });
  }

  var linkSobre = document.querySelector('a.nav-link[href="#sobre-nos"]');
  if (linkSobre) {
    linkSobre.addEventListener('click', function (e) {
      if (e && e.preventDefault) e.preventDefault();
      scrollToWithOffset('#sobre-nos', getOffset());
      closeNavbarIfOpen();
    });
  }

  var linkDuvidas = document.querySelector('a.nav-link[href="#duvidas"]');
  if (linkDuvidas) {
    linkDuvidas.addEventListener('click', function (e) {
      if (e && e.preventDefault) e.preventDefault();
      scrollToWithOffset('#duvidas', getOffset());
      closeNavbarIfOpen();
    });
  }
});
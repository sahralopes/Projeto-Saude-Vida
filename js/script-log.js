 // ===== Acessibilidade =====
  var fonteSlider = document.getElementById('fonte-slider');
  var fonteTamanho = document.getElementById('fonte-tamanho');
  var acessibilidadeContainer = document.getElementById('acessibilidade-container');
  var btnAcessibilidade = document.getElementById('btn-acessibilidade');
  var btnFecharAcessibilidade = document.getElementById('btn-fechar-acessibilidade');
  var temaTexto = document.getElementById('tema-texto');

  var seletorTexto = 'p, a, li, h2, h4, h5, h6, button, label, input, textarea, small';
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

  // Atualizar texto do tema quando mudar
  if (themeToggleButton) {
    themeToggleButton.addEventListener('click', function () {
      setTimeout(atualizarTextoTheme, 100);
    });
  }
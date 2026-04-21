// ========================= AGENDAMENTO =========================
document.addEventListener('DOMContentLoaded', function () {
  const formAgendamento  = document.getElementById('form-agendamento');
  const feedbackMessage  = document.getElementById('feedback-message');

  const inputNome        = document.getElementById('nome-completo');
  const inputResponsavel = document.getElementById('responsavel');
  const inputDataNasc    = document.getElementById('data-nascimento');
  const inputSexo        = document.getElementById('sexo');
  const inputTelefone    = document.getElementById('telefone');
  const inputEmail       = document.getElementById('email');
  const inputCPF         = document.getElementById('cpf');
  const inputEsp         = document.getElementById('especialidade');
  const inputMedico      = document.getElementById('medico');
  const inputHorario     = document.getElementById('horario');

  // ---------- Filtro Inteligente ----------
  if (inputEsp && inputMedico) {
    const todasOpcoesMedicos = Array.from(inputMedico.options);

    inputEsp.addEventListener('change', function () {
      const espSelecionada = this.value;
      inputMedico.value = "";

      todasOpcoesMedicos.forEach(opt => {
        if (opt.value === "") return;
        const espMedico = opt.getAttribute('data-esp');
        opt.style.display = (!espMedico || espMedico === espSelecionada) ? 'block' : 'none';
      });
    });
  }

  // ---------- Helpers ----------
  function onlyDigits(s) { return (s || '').replace(/\D/g, ''); }

  function validarEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test((email || '').trim());
  }

  function validarCPF(cpf) {
    cpf = onlyDigits(cpf);
    if (!/^\d{11}$/.test(cpf)) return false;
    if (/^(\d)\1{10}$/.test(cpf)) return false;

    let soma = 0;
    for (let i = 0; i < 9; i++) soma += parseInt(cpf[i]) * (10 - i);
    let dv1 = 11 - (soma % 11);
    dv1 = dv1 > 9 ? 0 : dv1;

    soma = 0;
    for (let i = 0; i < 10; i++) soma += parseInt(cpf[i]) * (11 - i);
    let dv2 = 11 - (soma % 11);
    dv2 = dv2 > 9 ? 0 : dv2;

    return dv1 === parseInt(cpf[9]) && dv2 === parseInt(cpf[10]);
  }

  function validarTelefone(tel) {
    tel = (tel || '').trim();
    return /^\(\d{2}\) 9\d{4}-\d{4}$/.test(tel);
  }

  function validarAnoData(str) {
    if (!str) return false;
    const [ano] = str.split('-').map(Number);
    return ano >= 1900 && ano <= 2100;
  }

  // ---------- Erros ----------
  function getFieldContainerByInput(el) { return el.closest('.field') || el.parentElement; }

  function ensureErrorSpan(c) {
    let span = c.querySelector('.error-msg');
    if (!span) {
      span = document.createElement('span');
      span.className = 'error-msg';
      c.appendChild(span);
    }
    return span;
  }

  function showFieldError(inputEl, msg) {
    const c = getFieldContainerByInput(inputEl);
    c.classList.add('is-invalid');
    c.classList.remove('is-valid');
    ensureErrorSpan(c).textContent = msg;
    inputEl.setCustomValidity(msg);
  }

  function clearFieldError(inputEl) {
    const c = getFieldContainerByInput(inputEl);
    c.classList.remove('is-invalid');
    c.classList.add('is-valid');
    inputEl.setCustomValidity('');
  }

  // ---------- Máscaras ----------
  inputCPF.addEventListener('input', function () {
    let d = onlyDigits(this.value).slice(0, 11);
    let f = '';
    if (d.length > 0)  f = d.slice(0, 3);
    if (d.length >= 4) f += '.' + d.slice(3, 6);
    if (d.length >= 7) f += '.' + d.slice(6, 9);
    if (d.length >= 10) f += '-' + d.slice(9, 11);
    this.value = f;
  });

  inputTelefone.addEventListener('input', function (e) {
    let v = e.target.value.replace(/\D/g, '');
    if (v.length > 11) v = v.slice(0, 11);
    if (v.length > 7) {
      v = v.replace(/^(\d\d)(\d{5})(\d{0,4}).*/, '($1) $2-$3');
    } else if (v.length > 2) {
      v = v.replace(/^(\d\d)(\d{0,5}).*/, '($1) $2');
    } else if (v.length > 0) {
      v = v.replace(/^(\d*)/, '($1');
    }
    e.target.value = v;
  });

  // ========= Revalidação e limpeza de erro do CPF =========
  if (inputCPF) {
    inputCPF.addEventListener('blur', function () {
      validarCampoCPF();
    });

    inputCPF.addEventListener('input', function () {
      if (validarCPF(this.value)) {
        clearFieldError(this);
      }
    });
  }

  // ========= Revalidação e limpeza de erro do NOME =========
  if (inputNome) {
    inputNome.addEventListener('blur', function () {
      validarCampoNome();
    });

    inputNome.addEventListener('input', function () {
      const nome = (this.value || '').trim();
      if (nome.length >= 8) {
        clearFieldError(this);
      }
    });
  }

  // ========= Revalidação e limpeza de erro do TELEFONE =========
  if (inputTelefone) {
    inputTelefone.addEventListener('blur', function () {
      validarCampoTelefone();
    });

    inputTelefone.addEventListener('input', function () {
      if (validarTelefone(this.value)) {
        clearFieldError(this);
      }
    });
  }

  // ---------- Validações ----------
  function validarCampoNome() {
    const nome = (inputNome.value || '').trim();
    if (nome.length < 8) {
      showFieldError(inputNome, 'Use de 8 a 60 letras.');
      return false;
    }
    clearFieldError(inputNome);
    return true;
  }

  function validarCampoDataNasc() {
    if (!validarAnoData(inputDataNasc.value)) {
      showFieldError(inputDataNasc, 'Data inválida.');
      return false;
    }
    clearFieldError(inputDataNasc);
    return true;
  }

  function validarCampoCPF() {
    if (!validarCPF(inputCPF.value)) {
      showFieldError(inputCPF, 'CPF inválido.');
      return false;
    }
    clearFieldError(inputCPF);
    return true;
  }

  function validarCampoTelefone() {
    if (!validarTelefone(inputTelefone.value)) {
      showFieldError(inputTelefone, 'Formato: (99) 99999-9999');
      return false;
    }
    clearFieldError(inputTelefone);
    return true;
  }

  function validarCampoEmail() {
    if (!validarEmail(inputEmail.value)) {
      showFieldError(inputEmail, 'E-mail inválido.');
      return false;
    }
    clearFieldError(inputEmail);
    return true;
  }

  function validarCampoSexo() {
    if (!inputSexo.value) {
      showFieldError(inputSexo, 'Selecione uma opção.');
      return false;
    }
    clearFieldError(inputSexo);
    return true;
  }

  function validarCampoEspecialidade() {
    if (!inputEsp.value) {
      showFieldError(inputEsp, 'Selecione uma especialidade.');
      return false;
    }
    clearFieldError(inputEsp);
    return true;
  }

  function validarCampoMedico() {
    if (!inputMedico.value) {
      showFieldError(inputMedico, 'Selecione um médico.');
      return false;
    }
    clearFieldError(inputMedico);
    return true;
  }

  function validarCampoHorario() {
    if (!inputHorario.value) {
      showFieldError(inputHorario, 'Escolha um horário.');
      return false;
    }
    clearFieldError(inputHorario);
    return true;
  }

  // ========================= FUNÇÃO CORRIGIDA =========================
  function validarAgendamento() {
    let ok = true;

    ok = validarCampoNome()          && ok;
    ok = validarCampoDataNasc()      && ok;
    ok = validarCampoCPF()           && ok;
    ok = validarCampoTelefone()      && ok;
    ok = validarCampoEmail()         && ok;
    ok = validarCampoSexo()          && ok;
    ok = validarCampoEspecialidade() && ok;
    ok = validarCampoMedico()        && ok;
    ok = validarCampoHorario()       && ok;

    return ok;
  }

  // ========================= SUBMIT =========================
  if (formAgendamento) {
    formAgendamento.addEventListener('submit', async function (e) {
      e.preventDefault();
      if (!validarAgendamento()) return;

      const dados = new FormData();
      dados.append("nome", inputNome.value);
      dados.append("responsavel", inputResponsavel.value);
      dados.append("data_nascimento", inputDataNasc.value);
      dados.append("sexo", inputSexo.value);
      dados.append("telefone", inputTelefone.value);
      dados.append("email", inputEmail.value);
      dados.append("cpf", inputCPF.value);
      dados.append("especialidade", inputEsp.value);
      dados.append("medico", inputMedico.value);
      dados.append("horario", inputHorario.value);

      try {
        const resp = await fetch("pag-agendamento.php", {
          method: "POST",
          body: dados
        });

        const json = await resp.json();

        if (json.status === "ok") {
          feedbackMessage.textContent = 'Consulta agendada com sucesso!';
          feedbackMessage.className   = 'feedback-message success';
          feedbackMessage.style.display = 'block';

          setTimeout(() => {
            window.location.href = "pag-consultas.php";
          }, 1500);

        } else {
          alert("Erro ao salvar: " + (json.detalhe || "Desconhecido"));
        }

      } catch (err) {
        alert("Erro na requisição: " + err);
      }
    });
  }

  // ========================= Botão "Limpar tudo" =========================
  const btnLimpar = document.getElementById('btn-limpar');

  if (btnLimpar && formAgendamento) {
    btnLimpar.addEventListener('click', () => {
      formAgendamento.reset();

      [
        inputNome,
        inputResponsavel,
        inputDataNasc,
        inputSexo,
        inputTelefone,
        inputEmail,
        inputCPF,
        inputEsp,
        inputMedico,
        inputHorario
      ].forEach((el) => {
        if (!el) return;
        const c = getFieldContainerByInput(el);
        c.classList.remove('is-invalid', 'is-valid');
        const span = c.querySelector('.error-msg');
        if (span) span.textContent = '';
        el.setCustomValidity('');
      });

      if (feedbackMessage) {
        feedbackMessage.textContent = 'Formulário limpo.';
        feedbackMessage.className = 'feedback-message success';
        feedbackMessage.style.display = 'block';
        setTimeout(() => feedbackMessage.style.display = 'none', 2000);
      }
    });
  }

  // ========================= ACESSIBILIDADE (original seu) =========================
  var fonteSlider = document.getElementById('fonte-slider');
  var fonteTamanho = document.getElementById('fonte-tamanho');
  var acessibilidadeContainer = document.getElementById('acessibilidade-container');
  var btnAcessibilidade = document.getElementById('btn-acessibilidade');
  var btnFecharAcessibilidade = document.getElementById('btn-fechar-acessibilidade');
  var themeToggleButton = document.getElementById('theme-toggle');
  var temaTexto = document.getElementById('tema-texto');
  var body = document.body;

  function aplicarTamanhoFonte(px) {
    var seletor = 'p, a, li, h2, h3, h4, h5, h6, button, label, input, textarea, small, span';
    document.querySelectorAll(seletor).forEach(el => el.style.fontSize = px + 'px');
  }

  function atualizarTextoTheme() {
    if (temaTexto) temaTexto.textContent = body.classList.contains('dark-mode') ? 'Escuro' : 'Claro';
  }

  try { if (localStorage.getItem('theme') === 'dark') body.classList.add('dark-mode'); } catch {}

  if (themeToggleButton) {
    themeToggleButton.addEventListener('click', function () {
      body.classList.toggle('dark-mode');
      localStorage.setItem('theme', body.classList.contains('dark-mode') ? 'dark' : 'light');
      atualizarTextoTheme();
    });
  }

  if (fonteSlider && fonteTamanho) {
    var salvo = localStorage.getItem('tamanhoFonte') || '16';
    aplicarTamanhoFonte(salvo);
    fonteSlider.value = salvo;
    fonteTamanho.textContent = salvo + 'px';

    fonteSlider.addEventListener('input', function () {
      aplicarTamanhoFonte(fonteSlider.value);
      fonteTamanho.textContent = fonteSlider.value + 'px';
      localStorage.setItem('tamanhoFonte', fonteSlider.value);
    });
  }

  if (btnAcessibilidade && acessibilidadeContainer) {
    btnAcessibilidade.addEventListener('click', function () {
      acessibilidadeContainer.style.display =
        acessibilidadeContainer.style.display === 'block' ? 'none' : 'block';
      atualizarTextoTheme();
    });
  }

  if (btnFecharAcessibilidade && acessibilidadeContainer) {
    btnFecharAcessibilidade.addEventListener('click', function () {
      acessibilidadeContainer.style.display = 'none';
    });
  }
});

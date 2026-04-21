// ========== Helpers ==========
async function sha256Hex(text) {
  const enc = new TextEncoder();
  const buf = await crypto.subtle.digest("SHA-256", enc.encode(text));
  return [...new Uint8Array(buf)].map(b => b.toString(16, "0")).join("");
}
function toast(el, msg, tipo = "error", ms = 2000) {
  if (!el) return;
  el.textContent = msg;
  el.className = `feedback-message ${tipo}`;
  el.style.display = "block";
  setTimeout(() => { el.style.display = "none"; }, ms);
}

// ========= Pós reset =========
(() => {
  const qp = new URLSearchParams(location.search);

  if (qp.get('reset') === 'ok') {
    const feedback = document.getElementById('feedback-message');
    if (feedback) {
      feedback.textContent = 'Senha alterada com sucesso! Faça login.';
      feedback.className = 'feedback-message success';
      feedback.style.display = 'block';
      setTimeout(() => feedback.style.display = 'none', 2000);
    }
  }

  const prefill = qp.get('login');
  if (prefill) {
    const inputUser = document.getElementById('usuario');
    if (inputUser) inputUser.value = prefill;
  }
})();

// ========= Elementos =========
const feedback = document.getElementById("feedback-message");
const btnEntrar = document.getElementById("enviar");
const inputUser = document.getElementById("usuario");
const inputPass = document.getElementById("senha");

// ========= Login =========
async function fazerLogin() {
  const loginDigitado = (inputUser?.value || "").trim();
  const senhaDigitada = (inputPass?.value || "").trim();

  if (!loginDigitado || !senhaDigitada) {
    toast(feedback, "Preencha usuário e senha.", "error");
    return;
  }

  const usuarios = JSON.parse(localStorage.getItem("usuarios") || "[]");

  if (!Array.isArray(usuarios) || usuarios.length === 0) {
    toast(feedback, "Nenhuma conta encontrada. Faça seu cadastro primeiro.", "error", 2500);
    setTimeout(() => {
      location.href = `pag-cadastro.php?novo=${encodeURIComponent(loginDigitado)}`;
    }, 900);
    return;
  }

  const user = usuarios.find(u => (u.login || "").toLowerCase() === loginDigitado.toLowerCase());

  if (!user) {
    toast(feedback, "Conta não encontrada. Crie uma nova conta.", "error", 2500);
    setTimeout(() => {
      location.href = `pag-cadastro.php?novo=${encodeURIComponent(loginDigitado)}`;
    }, 900);
    return;
  }

  let senhaOk = false;
  if (user.senhaHash) {
    const hashDigitada = await sha256Hex(senhaDigitada);
    senhaOk = hashDigitada === user.senhaHash;
  } else if (user.senha) {
    senhaOk = senhaDigitada === user.senha;
  }

  if (!senhaOk) {
    toast(feedback, "Usuário ou senha inválidos.", "error");
    return;
  }

  localStorage.setItem("usuarioLogado", JSON.stringify({
    nomeCompleto: user.nomeCompleto,
    login: user.login
  }));

  toast(feedback, "Login realizado com sucesso!", "success", 1200);

  setTimeout(() => {
    const redirect = localStorage.getItem('redirectAfterLogin');
    if (redirect) {
      localStorage.removeItem('redirectAfterLogin');
      location.href = redirect;
    } else {
      location.href = "index.php";
    }
  }, 900);
}

// ========= Eventos =========
btnEntrar?.addEventListener("click", async (e) => {
  e.preventDefault();
  await fazerLogin();
});

inputUser?.addEventListener("keypress", async (e) => {
  if (e.key === "Enter") {
    e.preventDefault();
    await fazerLogin();
  }
});
inputPass?.addEventListener("keypress", async (e) => {
  if (e.key === "Enter") {
    e.preventDefault();
    await fazerLogin();
  }
});

// =========================================================
// ==================  BOTÃO VOLTAR  ========================
// =========================================================
document.getElementById("btn-voltar")?.addEventListener("click", () => {
  location.href = "index.php";
});


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
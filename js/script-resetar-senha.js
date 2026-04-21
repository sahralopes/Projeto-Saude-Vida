// ===== Helpers =====
const norm  = s => (s || "").trim();
const lower = s => norm(s).toLowerCase();
const isEmail = e => /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(norm(e));
// No seu projeto, senha = exatamente 8 letras (mantive a mesma regra)
const validarSenha = s => /^[A-Za-z]{8}$/.test(norm(s));

async function sha256Hex(text) {
  const enc = new TextEncoder();
  const buf = await crypto.subtle.digest("SHA-256", enc.encode(text));
  return [...new Uint8Array(buf)].map(b => b.toString(16).padStart(2,"0")).join("");
}

function toast(el, msg, tipo="error", ms=1800){
  if(!el) return;
  el.textContent = msg;
  el.className = `feedback-message ${tipo}`;
  el.style.display = "block";
  setTimeout(()=> el.style.display = "none", ms);
}

// ===== Elementos =====
const feedback   = document.getElementById("feedback-message");
const inputEmail = document.getElementById("email-reset");
const inputNova  = document.getElementById("nova-senha");
const inputConf  = document.getElementById("confirma-senha");
const btnConfirm = document.getElementById("btnConfirmarReset");

// ===== Função de reset de senha =====
async function fazerResetSenha() {
  const email = norm(inputEmail.value);
  const nova  = norm(inputNova.value);
  const conf  = norm(inputConf.value);

  if (!isEmail(email)){
    toast(feedback, "Informe um e-mail válido.", "error"); 
    inputEmail.focus(); 
    return false;
  }
  if (!validarSenha(nova)){
    toast(feedback, "Senha deve ter exatamente 8 letras (A-Z).", "error"); 
    inputNova.focus(); 
    return false;
  }
  if (nova !== conf){
    toast(feedback, "As senhas não coincidem.", "error"); 
    inputConf.focus(); 
    return false;
  }

  // carrega base
  let usuarios = [];
  try { usuarios = JSON.parse(localStorage.getItem("usuarios") || "[]"); } catch { usuarios = []; }

  // encontra pelo e-mail (case-insensitive)
  const idx = usuarios.findIndex(u => lower(u.email || "") === lower(email));
  if (idx < 0){
    toast(feedback, "E-mail não encontrado. Faça um cadastro primeiro.", "error", 2200);
    return false;
  }

  // grava nova senha como hash
  try {
    usuarios[idx].senhaHash = await sha256Hex(nova);
  } catch {
    usuarios[idx].senhaHash = "fallback_" + btoa(nova);
  }
  // (opcional) remove senha em texto puro caso exista
  delete usuarios[idx].senha;

  // salva
  localStorage.setItem("usuarios", JSON.stringify(usuarios));

  toast(feedback, "Senha redefinida com sucesso!", "success", 1400);
  setTimeout(() => { location.href = "pag-login.php?reset=ok"; }, 900);
  return true;
}

// ===== Eventos =====
// Botão de confirmar
btnConfirm?.addEventListener("click", async (e) => {
  e.preventDefault();
  await fazerResetSenha();
});

// Enter nos campos
inputEmail?.addEventListener("keypress", async (e) => {
  if (e.key === "Enter") {
    e.preventDefault();
    inputNova.focus();
  }
});

inputNova?.addEventListener("keypress", async (e) => {
  if (e.key === "Enter") {
    e.preventDefault();
    inputConf.focus();
  }
});

inputConf?.addEventListener("keypress", async (e) => {
  if (e.key === "Enter") {
    e.preventDefault();
    await fazerResetSenha();
  }
});


// =========================================================
// ==================  TEMA CLARO/ESCURO  ===================
// =========================================================
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

// =========================================================
// ==================  ACESSIBILIDADE  ======================
// =========================================================
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

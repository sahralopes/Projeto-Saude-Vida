// ============ Helpers ============
const norm = s => (s || '').trim();
const genCode6 = () => String(Math.floor(100000 + Math.random() * 900000));
const maskEmail = (email='') => {
  const [u, d] = (email||'').split('@');
  if (!u || !d) return email;
  return `${u.slice(0,2)}${u.length>2 ? '*'.repeat(u.length-2) : ''}@${d}`;
};
function toast(el, msg, tipo='error', ms=1800){
  if(!el) return;
  el.textContent = msg;
  el.className = `feedback-message ${tipo}`;
  el.style.display = 'block';
  setTimeout(()=> el.style.display = 'none', ms);
}

// ============ EmailJS ============
// Configure aqui suas chaves se não estiverem na janela
const EMAILJS_PUBLIC_KEY  = "8Uw6P1YAI82JnT7Kd"; 
const EMAILJS_SERVICE_ID  = "service_dsy053g";
const EMAILJS_TEMPLATE_ID = "template_1pyc24p";

function emailJsReady(){
  return typeof window.emailjs !== 'undefined' && EMAILJS_PUBLIC_KEY;
}

// Inicializa EmailJS
document.addEventListener("DOMContentLoaded", () => {
    try { if (emailJsReady()) window.emailjs.init(EMAILJS_PUBLIC_KEY); } catch {}
});

async function sendCodeViaEmail(toEmail, code){
  if (!emailJsReady()) return { ok:false, reason:'not_configured' };
  try {
    const params = { to_email: toEmail, verification_code: code };
    await window.emailjs.send(EMAILJS_SERVICE_ID, EMAILJS_TEMPLATE_ID, params);
    return { ok:true };
  } catch (e) {
    return { ok:false, reason: e?.message || 'send_failed' };
  }
}

// ============ Elementos ============
const feedback        = document.getElementById('feedback-message');
const inputEmail      = document.getElementById('email-verificacao');
const inputCodigo     = document.getElementById('codigo');
const btnEnviarCodigo = document.getElementById('btnEnviarCodigo');
const btnConfirmar    = document.getElementById('btnConfirmar');
const aviso2FA        = document.getElementById('aviso-2fa');

// ============ Estado & Parâmetros URL ============
const params      = new URLSearchParams(location.search);
const loginParam  = norm(params.get('login'));
const emailParam  = norm(params.get('email')); // Pega o email da URL vindo do PHP

// Preenche o campo de email automaticamente se vier da URL
if(emailParam && inputEmail) {
    inputEmail.value = emailParam;
    inputEmail.readOnly = true; // Bloqueia edição para segurança básica
}

const CODE_TTL_MS    = 5 * 60 * 1000; // 5 minutos
const challKey       = login => `verify:${login}`;
const saveChall      = (login, obj) => localStorage.setItem(challKey(login), JSON.stringify(obj));
const loadChall      = login => { try { return JSON.parse(localStorage.getItem(challKey(login)) || 'null'); } catch { return null; } };
const clearChall     = login => localStorage.removeItem(challKey(login));

let countdownInterval = null;

// ============ Relógio ============
function startCountdownUI(login){
  if (countdownInterval) clearInterval(countdownInterval);
  const c = loadChall(login);
  if (!c || !c.expireAt){
    if (aviso2FA) aviso2FA.textContent = '';
    return;
  }
  function tick(){
    const left = c.expireAt - Date.now();
    if (left <= 0){
      aviso2FA.textContent = 'Código expirado. Peça um novo.';
      clearInterval(countdownInterval);
      countdownInterval = null;
      return;
    }
    const mm = String(Math.floor(left/60000)).padStart(2,'0');
    const ss = String(Math.floor((left%60000)/1000)).padStart(2,'0');
    aviso2FA.textContent = `Código enviado para ${maskEmail(c.email)} — expira em ${mm}:${ss}`;
  }
  tick();
  countdownInterval = setInterval(tick, 1000);
}

// ============ Enviar código ============
btnEnviarCodigo?.addEventListener('click', () => {
  const userLogin = loginParam || 'visitante';
  const email = norm(inputEmail.value);

  if (!email || !email.includes('@')){
      toast(feedback, 'E-mail inválido.', 'error');
      return;
  }

  const code = genCode6();
  const chal = {
    code,
    expireAt: Date.now() + CODE_TTL_MS,
    email
  };

  saveChall(userLogin, chal);
  startCountdownUI(userLogin);

  // Desabilita botão temporariamente
  btnEnviarCodigo.disabled = true;
  setTimeout(() => btnEnviarCodigo.disabled = false, 30000); // 30s cooldown

  (async () => {
    const result = await sendCodeViaEmail(email, code);
    if (result.ok){
      toast(feedback, 'Código enviado com sucesso!', 'success');
    } else {
      console.log("Erro EmailJS:", result.reason);
      // Fallback para demonstração se o EmailJS falhar ou estourar cota
      aviso2FA.textContent = `(DEMO) Seu código é: ${code}`; 
      toast(feedback, 'Código gerado (Modo Demo).', 'success');
    }
  })();
});

// ============ Confirmar ============
btnConfirmar?.addEventListener('click', () => {
  const userLogin = loginParam || 'visitante';
  const chal = loadChall(userLogin);

  if (!chal){
    toast(feedback, 'Solicite o código primeiro.', 'error');
    return;
  }

  if (Date.now() > chal.expireAt){
    clearChall(userLogin);
    toast(feedback, 'Código expirado.', 'error');
    return;
  }

  const codeDigitado = norm(inputCodigo.value);
  if (codeDigitado !== chal.code){
    toast(feedback, 'Código incorreto.', 'error');
    return;
  }

  // SUCESSO!
  clearChall(userLogin);
  if (countdownInterval) clearInterval(countdownInterval);
  
  toast(feedback, 'Verificado! Redirecionando...', 'success');

  // FLUXO FINAL: Vai para o Agendamento
  setTimeout(() => {
    window.location.href = 'pag-agendamento.php';
  }, 1000);
});

// Botão Voltar
document.getElementById("btn-voltar")?.addEventListener("click", () => {
  window.history.back();
});
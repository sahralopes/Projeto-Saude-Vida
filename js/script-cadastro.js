// ========================= Helpers =========================
const onlyDigits = (s) => (s || '').replace(/\D/g, '');
const norm       = (s) => (s || '').trim();
const lower      = (s) => norm(s).toLowerCase();

function validarNome(nome){ return /^[A-Za-zÀ-ÖØ-öø-ÿ\s]{8,60}$/.test(nome); }
function validarSexo(v){ return ['Feminino','Masculino','Outro'].includes(v); }
function validarEmail(e){ return /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(norm(e)); }
function validarCEP(cep){ return /^\d{5}-?\d{3}$/.test(cep); }
// >>> Separa fixo e celular
function validarTelefoneFixoBR(t){   // (+55)DD-XXXXXXXX  (8 dígitos)
  return /^\(\+55\)\d{2}-\d{8}$/.test(t);
}
function validarCelularBR(t){        // (+55)DD-XXXXXXXXX (9 dígitos)
  return /^\(\+55\)\d{2}-\d{9}$/.test(t);
}
function validarLogin(l){ return /^[A-Za-z]{6}$/.test(l); }
function validarSenha(s){ return /^[A-Za-z]{8}$/.test(s); }

function validarCPF(cpf) {
  cpf = onlyDigits(cpf);
  if (!/^\d{11}$/.test(cpf)) return false;
  if (/^(\d)\1{10}$/.test(cpf)) return false;
  let soma = 0;
  for (let i=0;i<9;i++) soma += parseInt(cpf[i]) * (10 - i);
  let dv1 = 11 - (soma % 11); dv1 = dv1 > 9 ? 0 : dv1;
  soma = 0;
  for (let i=0;i<10;i++) soma += parseInt(cpf[i]) * (11 - i);
  let dv2 = 11 - (soma % 11); dv2 = dv2 > 9 ? 0 : dv2;
  return dv1 === parseInt(cpf[9]) && dv2 === parseInt(cpf[10]);
}

// Banner superior
const feedbackMessage = document.getElementById('feedback-message');
function mostrarFeedback(msg, tipo='success', ms=2500){
  if(!feedbackMessage) return;
  feedbackMessage.textContent = msg;
  feedbackMessage.className = `feedback-message ${tipo}`;
  feedbackMessage.style.display = 'block';
  setTimeout(()=> feedbackMessage.style.display='none', ms);
}

// ========================= UI Inline (erros) =========================
function getFieldContainerByInput(el){ return el.closest('.field') || el.parentElement; }
function ensureErrorSpan(container){
  let span = container.querySelector('.error-msg');
  if(!span){ span = document.createElement('span'); span.className = 'error-msg'; container.appendChild(span); }
  return span;
}
function showFieldError(id, message){
  const el = document.getElementById(id);
  if(!el) return;
  const c = getFieldContainerByInput(el);
  c.classList.remove('is-valid');
  c.classList.add('is-invalid');
  ensureErrorSpan(c).textContent = message || 'Campo inválido';
  el.setCustomValidity(message || 'Campo inválido');
}
function clearFieldError(id){
  const el = document.getElementById(id);
  if(!el) return;
  const c = getFieldContainerByInput(el);
  c.classList.remove('is-invalid');
  c.classList.add('is-valid');
  const span = c.querySelector('.error-msg'); if(span) span.textContent = '';
  el.setCustomValidity('');
}

// ========================= Campos =========================
const formCadastro   = document.getElementById('form-cadastro');

const inputNome      = document.getElementById('nome-completo');
const inputMaterno   = document.getElementById('nome-materno');
const inputNasc      = document.getElementById('data_nascimento');
const inputSexo      = document.getElementById('sexo');
const inputCPF       = document.getElementById('cpf');
const inputEmail     = document.getElementById('email');
const inputCelular   = document.getElementById('celular');
const inputTelefone  = document.getElementById('telefone');
const inputCEP       = document.getElementById('cep');
const inputLogr      = document.getElementById('logradouro');
const inputNumero    = document.getElementById('numero_casa');
const inputCompl     = document.getElementById('complemento');
const inputBairro    = document.getElementById('bairro');
const inputCidade    = document.getElementById('cidade');
const inputEstado    = document.getElementById('estado');
const inputLogin     = document.getElementById('login');
const inputSenha     = document.getElementById('senha');
const inputConfirmar = document.getElementById('confirmar_senha');

const CAMPOS = [
  'nome-completo','nome-materno','data_nascimento','sexo',
  'cpf','celular','telefone','email',
  'cep','logradouro','numero_casa','complemento','bairro','cidade','estado',
  'login','senha','confirmar_senha'
];

// ========================= Validação em tempo real =========================
function estaValidoAgora(el){
  const raw = el.value || '';
  const value = norm(raw);
  let vazio = !value;

  if(el.id==='celular' || el.id==='telefone'){
    const semPrefixo = raw.replace(/^\(\+55\)/, '');
    const digits = semPrefixo.replace(/\D/g,'');
    vazio = digits.length === 0;
  }
  // Alguns campos não são obrigatórios no PHP, mas aqui mantemos validação de formato se preenchido
  if(vazio && ['nome-materno','celular','telefone','cep','logradouro','numero_casa','bairro','cidade','estado'].includes(el.id)) return true;
  if(vazio) return false;

  switch(el.id){
    case 'nome-completo':
    case 'nome-materno': return validarNome(value);
    case 'sexo':         return validarSexo(value);
    case 'cpf':          return validarCPF(value);
    case 'email':        return validarEmail(value);
    case 'celular':      return validarCelularBR(value);
    case 'telefone':     return validarTelefoneFixoBR(value);
    case 'cep':          return validarCEP(value);
    case 'numero_casa':  return /^\d+$/.test(value);
    case 'login':        return validarLogin(value);
    case 'senha':        return validarSenha(value);
    case 'confirmar_senha': return value === norm(inputSenha.value);
    default: return true;
  }
}

function validarCampoAoVivo(el){
  const raw = el.value || '';
  const value = norm(raw);
  let vazio = !value;

  if(el.id==='celular' || el.id==='telefone'){
    const semPrefixo = raw.replace(/^\(\+55\)/, '');
    const digits = semPrefixo.replace(/\D/g,'');
    vazio = digits.length === 0;
  }
  
  // Campos opcionais: se vazio, limpa erro e retorna true
  if(vazio && ['nome-materno','celular','telefone','cep','logradouro','numero_casa','bairro','cidade','estado','complemento'].includes(el.id)){
      clearFieldError(el.id);
      return true;
  }

  if(vazio){ showFieldError(el.id,'Preencha este campo'); return false; }

  let msg = '';
  switch(el.id){
    case 'nome-completo':
      if(!validarNome(value)) msg='Use de 8 a 60 letras.'; break;
    case 'nome-materno':
      if(!validarNome(value)) msg='Use de 8 a 60 letras.'; break;
    case 'sexo':
      if(!validarSexo(value)) msg='Selecione uma opção.'; break;
    case 'cpf':
      if(!validarCPF(value)) msg='CPF inválido.'; break;
    case 'email':
      if(!validarEmail(value)) msg='E-mail inválido.'; break;
    case 'celular':
      if(!validarCelularBR(value)) msg='Formato: (+55)DD-XXXXXXXXX'; break;
    case 'telefone':
      if(!validarTelefoneFixoBR(value)) msg='Formato: (+55)DD-XXXXXXXX'; break;
    case 'cep':
      if(!validarCEP(value)) msg='CEP inválido.'; break;
    case 'numero_casa':
      if(!/^\d+$/.test(value)) msg='Apenas números.'; break;
    case 'login':
      if(!validarLogin(value)) msg='Login: exatamente 6 letras.'; break;
    case 'senha':
      if(!validarSenha(value)) msg='Senha: exatamente 8 letras.';
      else { if(inputConfirmar?.value) validarCampoAoVivo(inputConfirmar); }
      break;
    case 'confirmar_senha':
      if(value !== norm(inputSenha.value)) msg='As senhas não coincidem.'; break;
  }
  if(msg){ showFieldError(el.id,msg); return false; }
  clearFieldError(el.id); return true;
}

CAMPOS.forEach(id=>{
  const el = document.getElementById(id); if(!el) return;
  el.addEventListener('blur',   ()=> validarCampoAoVivo(el));
  el.addEventListener('change', ()=> validarCampoAoVivo(el));
  el.addEventListener('input',  ()=> { if(estaValidoAgora(el)) clearFieldError(el.id); });
});

// ========================= Máscaras =========================
function aplicarMascaraCEP(el){
  let v = onlyDigits(el.value).slice(0,8);
  el.value = v.length>5 ? `${v.slice(0,5)}-${v.slice(5)}` : v;
}
function aplicarMascaraCPF(el){
  let v = onlyDigits(el.value).slice(0,11);
  if (v.length>9) el.value = `${v.slice(0,3)}.${v.slice(3,6)}.${v.slice(6,9)}-${v.slice(9)}`;
  else if (v.length>6) el.value = `${v.slice(0,3)}.${v.slice(3,6)}.${v.slice(6)}`;
  else if (v.length>3) el.value = `${v.slice(0,3)}.${v.slice(3)}`;
  else el.value = v;
}
function aplicarMascaraCelular(el){   
  let raw = (el.value || '').replace(/^\(\+55\)/,'');
  let d = raw.replace(/\D/g,'');
  if(d.startsWith('55') && d.length>9) d = d.slice(2);
  d = d.slice(0,11); 
  if(d.length===0) el.value='(+55)';
  else if(d.length<=2) el.value = `(+55)${d}`;
  else el.value = `(+55)${d.slice(0,2)}-${d.slice(2)}`;
}
function aplicarMascaraTelefoneFixo(el){ 
  let raw = (el.value || '').replace(/^\(\+55\)/,'');
  let d = raw.replace(/\D/g,'');
  if(d.startsWith('55') && d.length>8) d = d.slice(2);
  d = d.slice(0,10); 
  if(d.length===0) el.value='(+55)';
  else if(d.length<=2) el.value = `(+55)${d}`;
  else el.value = `(+55)${d.slice(0,2)}-${d.slice(2)}`;
}

inputCPF.addEventListener('input', (e)=>aplicarMascaraCPF(e.target));
inputCEP.addEventListener('input', (e)=>aplicarMascaraCEP(e.target));
if(!inputCelular.value)  inputCelular.value='(+55)';
if(!inputTelefone.value) inputTelefone.value='(+55)';
inputCelular.addEventListener('input', e=>aplicarMascaraCelular(e.target));
inputTelefone.addEventListener('input', e=>aplicarMascaraTelefoneFixo(e.target));

// ========================= ViaCEP =========================
function limparEndereco(){
  inputLogr.value=''; inputBairro.value=''; inputCidade.value=''; inputEstado.value='';
}
async function buscarCEP(cep){
  const limpo = onlyDigits(cep);
  try{
    const resp = await fetch(`https://viacep.com.br/ws/${limpo}/json/`);
    if(!resp.ok) throw new Error('HTTP');
    const data = await resp.json();
    if(data.erro) throw new Error('CEP não encontrado');

    inputLogr.value   = data.logradouro || '';
    inputBairro.value = data.bairro     || '';
    inputCidade.value = data.localidade || '';
    inputEstado.value = data.uf         || '';
    clearFieldError('cep');
  }catch(e){
    limparEndereco();
    showFieldError('cep','CEP não encontrado.');
  }
}
inputCEP.addEventListener('blur', async ()=>{
  const cep = norm(inputCEP.value);
  if(validarCEP(cep)){ await buscarCEP(cep); }
});

// ========================= Submit =========================
// CORREÇÃO CRÍTICA: Removemos o preventDefault() no caso de sucesso
// para permitir que o formulário HTML seja enviado ao PHP.
formCadastro.addEventListener('submit', (e)=>{
  
  // 1. Verifica todos os campos antes de tentar enviar
  let temErro = false;
  let primeiroCampoErro = null;

  CAMPOS.forEach(id => {
      const el = document.getElementById(id);
      if(el) {
          // Se for obrigatório e estiver vazio, ou se for inválido
          const isOptional = ['nome-materno','celular','telefone','cep','logradouro','numero_casa','complemento','bairro','cidade','estado'].includes(id);
          if(!isOptional || (el.value.trim() !== '')) {
             if(!validarCampoAoVivo(el)){
                 temErro = true;
                 if(!primeiroCampoErro) primeiroCampoErro = el;
             }
          }
      }
  });

  // 2. Se houver erro, BLOQUEIA o envio ao PHP
  if(temErro){
      e.preventDefault(); // Impede o submit
      if(primeiroCampoErro) primeiroCampoErro.focus();
      mostrarFeedback('Preencha corretamente os campos destacados.', 'error');
  } 
  
  // 3. Se NÃO houver erro, o código segue naturalmente e o formulário é enviado ao PHP.
});

// ========================= Botão "Limpar tudo" =========================
document.getElementById('btn-limpar')?.addEventListener('click', ()=>{
  formCadastro.reset();
  CAMPOS.forEach(id=>{
    const el = document.getElementById(id); if(!el) return;
    const c = getFieldContainerByInput(el);
    c.classList.remove('is-invalid','is-valid');
    const span = c.querySelector('.error-msg'); if(span) span.textContent = '';
    el.setCustomValidity('');
  });
  inputLogr.readOnly = inputBairro.readOnly = inputCidade.readOnly = inputEstado.readOnly = false;
  inputCelular.value = '(+55)'; 
  inputTelefone.value = '(+55)';
  mostrarFeedback('Formulário limpo.','success');
});


// ========================= ACESSIBILIDADE =========================
document.addEventListener('DOMContentLoaded', function () {
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
    document.querySelectorAll(seletor).forEach(function (el) {
      el.style.fontSize = px + 'px';
    });
  }

  function atualizarTextoTheme() {
    if (temaTexto) {
      temaTexto.textContent = body.classList.contains('dark-mode') ? 'Escuro' : 'Claro';
    }
  }

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
      atualizarTextoTheme();
    });
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
      var visible = acessibilidadeContainer.style.display === 'block';
      acessibilidadeContainer.style.display = visible ? 'none' : 'block';
      atualizarTextoTheme();
    });
  }

  if (btnFecharAcessibilidade) {
    btnFecharAcessibilidade.addEventListener('click', function () {
      acessibilidadeContainer.style.display = 'none';
    });
  }
});
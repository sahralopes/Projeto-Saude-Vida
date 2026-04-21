<?php
session_start();
require 'conexao.php';

/**
 * Grava log de autenticação na tabela logs_autenticacao
 * Campos da tabela: id, id_usuario, nome_usuario, cpf, metodo_2fa, data_hora, ip
 */
function gravaLogAutenticacao(PDO $conn, $idUsuario, $nomeUsuario, $cpf, $metodo2fa)
{
    try {
        // Descobrir IP real (se tiver proxy)
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $partes = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = trim($partes[0]);
        }

        $sqlLog = "INSERT INTO logs_autenticacao 
                   (id_usuario, nome_usuario, cpf, metodo_2fa, data_hora, ip)
                   VALUES (:id_usuario, :nome_usuario, :cpf, :metodo_2fa, NOW(), :ip)";

        $stmtLog = $conn->prepare($sqlLog);
        $stmtLog->bindValue(':id_usuario', $idUsuario, PDO::PARAM_INT);
        $stmtLog->bindValue(':nome_usuario', $nomeUsuario);
        $stmtLog->bindValue(':cpf', $cpf);
        $stmtLog->bindValue(':metodo_2fa', $metodo2fa);
        $stmtLog->bindValue(':ip', $ip);
        $stmtLog->execute();

    } catch (Exception $e) {
        // Só registra no log do PHP pra não quebrar o login em caso de erro no log
        error_log('Erro ao gravar log de autenticação: ' . $e->getMessage());
    }
}

$erro = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $login = trim($_POST['usuario'] ?? '');
    $senha = trim($_POST['senha'] ?? '');

    if (empty($login) || empty($senha)) {
        $erro = "Preencha login e senha.";
    } else {
        // Busca o usuário, incluindo o campo 'tipo', 'email' e 'cpf'
        $sql = "SELECT id, nome_completo, login, email, senha_hash, tipo, cpf
                FROM usuarios 
                WHERE login = :login 
                LIMIT 1";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':login', $login);
        $stmt->execute();

        if ($usuario = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Verifica se a senha bate com o hash no banco
            if (password_verify($senha, $usuario['senha_hash'])) {
                
                // --- LOGIN SUCESSO ---
                $_SESSION['usuario_id']    = $usuario['id'];
                $_SESSION['usuario_nome']  = $usuario['nome_completo'];
                $_SESSION['usuario_login'] = $usuario['login'];
                $_SESSION['usuario_email'] = $usuario['email'];
                $_SESSION['usuario_tipo']  = $usuario['tipo'];
                if (isset($usuario['cpf'])) {
                    $_SESSION['usuario_cpf'] = $usuario['cpf'];
                }

                // Grava log de autenticação (2FA via e-mail, por exemplo)
                gravaLogAutenticacao(
                    $conn,
                    $usuario['id'],
                    $usuario['nome_completo'],
                    $usuario['cpf'] ?? '',
                    'Código via e-mail' // descrição do 2FA
                );

                // === LÓGICA DE REDIRECIONAMENTO ===
                if ($usuario['tipo'] === 'ADMIN') {
                    // Se for ADMIN -> Vai para o Painel
                    header("Location: pag-admin.php");
                } else {
                    // Se for COMUM -> Segue o fluxo normal (Validação -> Agendamento)
                    // Passamos os dados para a verificação via URL
                    $emailEncoded = urlencode($usuario['email']);
                    $loginEncoded = urlencode($usuario['login']);
                    header("Location: pag-verificacao.php?login={$loginEncoded}&email={$emailEncoded}");
                }
                exit;

            } else {
                $erro = "Senha incorreta.";
            }
        } else {
            $erro = "Usuário não encontrado.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Saúde & Vida</title>
  <link rel="shortcut icon" href="img/favicon-16x16.png">
  <link rel="stylesheet" href="css/style-login.css">
</head>
<body class="login">
  <a href="index.php" class="btn-voltar-login">⟵ Voltar</a>

  <div class="main-login">
    <div class="card-login card-split">
      <div class="card-side">
        <div class="logo"><img src="img/Logo-menu.png" alt="Logo"></div>
        <p>Bem-vindo(a) de volta!</p>
      </div>
      <div class="card-form">
        <h1>LOGIN</h1>
        <?php if(!empty($erro)): ?>
            <div class="feedback-message error" style="display:block; position:relative; top:0; margin-bottom:15px;">
                <?= $erro ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="pag-login.php">
            <div class="textfield">
              <label for="usuario">Usuário:</label>
              <input type="text" id="usuario" name="usuario" placeholder="Seu login" required>
            </div>
            <div class="textfield">
              <label for="senha">Senha:</label>
              <input type="password" id="senha" name="senha" placeholder="Sua senha" required>
            </div>
            <div class="remember-forgot">
              <a href="pag-resetar-senha.php">Esqueci a senha</a>
            </div>
            <button type="submit" class="btn-enviar">Entrar</button>
        </form>

        <div class="register-link">
            <p>Não tem uma conta? <a href="pag-cadastro.php">Cadastre-se</a></p>
        </div>
      </div>
    </div>
  </div>

   <!-- ===== ACESSIBILIDADE ===== -->
  <div id="icone-acessibilidade">
    <button id="btn-acessibilidade" type="button" aria-label="Acessibilidade">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
           class="bi bi-gear-fill" viewBox="0 0 16 16">
        <path d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872l-.1-.34zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.858z"/>
      </svg>
    </button>
  </div>

  <div id="acessibilidade-container"        style="display:none;">
    <div class="acessibilidade-header">
      <h4>Acessibilidade</h4>
      <button id="btn-fechar-acessibilidade" type="button" aria-label="Fechar">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
             class="bi bi-x" viewBox="0 0 16 16">
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
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                     fill="currentColor" class="bi bi-sun" viewBox="0 0 16 16">
                  <path d="M8 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6zm0 1a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0zm0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13zm8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5zM3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8zm10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0zm-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0zm9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707zM4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708z"/>
                </svg>
              </div>
            </div>
          </button>
          <span id="tema-texto">Claro</span>
        </div>
      </div>

      <div class="opcao-fonte">
        <label for="fonte-slider">Tamanho da Fonte:</label>
        <input type="range" id="fonte-slider" min="12" max="22" value="16">
        <span id="fonte-tamanho">16px</span>
      </div>
    </div>
  </div>


  <script src="js/script-login.js"></script>
</body>
</html>

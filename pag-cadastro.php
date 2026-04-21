<?php
session_start();
require 'conexao.php';

$mensagem = "";
$tipo_msg = ""; // para class css (error ou success)

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nomeCompleto = $_POST['nome-completo'] ?? '';
    $email        = $_POST['email']         ?? '';
    $login        = $_POST['login']         ?? '';
    $cpf          = $_POST['cpf']           ?? '';
    $senha        = $_POST['senha']         ?? '';
    
    // Verifica se já existe
    $sql = "SELECT count(*) FROM usuarios WHERE login = :login OR cpf = :cpf";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':login', $login);
    $stmt->bindValue(':cpf', $cpf);
    $stmt->execute();
    
    if ($stmt->fetchColumn() > 0) {
        $mensagem = "Login ou CPF já cadastrado!";
        $tipo_msg = "error";
    } else {
        // Insere
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        $sqlInsert = "INSERT INTO usuarios (nome_completo, email, login, cpf, senha_hash, tipo) 
                      VALUES (:nome, :email, :login, :cpf, :senha, 'COMUM')";
        
        $stmtInsert = $conn->prepare($sqlInsert);
        $stmtInsert->bindValue(':nome', $nomeCompleto);
        $stmtInsert->bindValue(':email', $email);
        $stmtInsert->bindValue(':login', $login);
        $stmtInsert->bindValue(':cpf', $cpf);
        $stmtInsert->bindValue(':senha', $hash);
        
        if ($stmtInsert->execute()) {
            // Redireciona para login após sucesso
            header("Location: pag-login.php");
            exit;
        } else {
            $mensagem = "Erro ao cadastrar.";
            $tipo_msg = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Saúde & Vida</title>
    <link rel="stylesheet" href="css/style-cadastro.css">
</head>
<body>
    <?php if($mensagem): ?>
        <div class="feedback-message <?= $tipo_msg ?>" style="display:block; position:fixed; top:20px; right:20px;">
            <?= $mensagem ?>
        </div>
    <?php endif; ?>

    <div class="outer-container">
        <div class="main-cadastro">
            <h1>CADASTRO</h1>
            <div class="form-container">
                <form id="form-cadastro" method="POST" action="pag-cadastro.php">
                    <div class="textfield">
                        <div class="field full">
                            <label>Nome Completo:</label>
                            <input type="text" name="nome-completo" id="nome-completo" required>
                        </div>
                        <div class="field">
                             <label>Nome Materno:</label>
                             <input type="text" name="nome-materno" id="nome-materno">
                        </div>
                        <div class="field">
                            <label>Data Nascimento:</label>
                            <input type="date" name="data_nascimento" id="data_nascimento" required>
                        </div>
                        <div class="field">
                            <label>Sexo:</label>
                            <select name="sexo" id="sexo">
                                <option value="Feminino">Feminino</option>
                                <option value="Masculino">Masculino</option>
                            </select>
                        </div>
                        <div class="field">
                            <label>CPF:</label>
                            <input type="text" name="cpf" id="cpf" required>
                        </div>
                        <div class="field">
                            <label>Email:</label>
                            <input type="email" name="email" id="email" required>
                        </div>
                        <div class="field">
                            <label>Celular:</label>
                            <input type="text" name="celular" id="celular">
                        </div>
                         <div class="field">
                            <label>Telefone:</label>
                            <input type="text" name="telefone" id="telefone">
                        </div>
                        
                        <div class="field full"><label>CEP:</label><input type="text" name="cep" id="cep"></div>
                        <div class="field"><label>Logradouro:</label><input type="text" name="logradouro" id="logradouro"></div>
                        <div class="field"><label>Nº:</label><input type="text" name="numero_casa" id="numero_casa"></div>
                        <div class="field"><label>Bairro:</label><input type="text" name="bairro" id="bairro"></div>
                        <div class="field"><label>Cidade:</label><input type="text" name="cidade" id="cidade"></div>
                        <div class="field"><label>Estado:</label><input type="text" name="estado" id="estado"></div>

                        <div class="field full">
                            <label>Login (6 letras):</label>
                            <input type="text" name="login" id="login" required>
                        </div>
                        <div class="field">
                            <label>Senha (8 chars):</label>
                            <input type="password" name="senha" id="senha" required>
                        </div>
                        <div class="field">
                            <label>Confirmar:</label>
                            <input type="password" name="confirmar_senha" id="confirmar_senha" required>
                        </div>
                    </div>
                    <div class="botoes">
                        <button type="submit" class="btn-cadastro">Cadastrar</button>
                        <button type="button" id="btn-limpar" class="btn-cadastro">Limpar Tudo</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="footer-actions">
            <a href="pag-login.php" class="btn-voltar">Voltar</a>
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

  <div id="acessibilidade-container" style="display:none;">
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


    <script src="js/script-cadastro.js"></script>
</body>
</html>
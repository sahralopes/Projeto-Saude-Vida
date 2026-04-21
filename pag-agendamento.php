<?php
session_start();
include "conexao.php";

// --- LÓGICA DE SALVAR AGENDAMENTO (POST) ---
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    header('Content-Type: application/json');

    if (!isset($_SESSION['usuario_id'])) {
        echo json_encode(["status" => "erro", "detalhe" => "Usuário não logado."]);
        exit;
    }

    $id_usuario     = $_SESSION['usuario_id'];
    $nome           = $_POST["nome-completo"] ?? $_POST["nome"] ?? '';
    $data_nasc      = $_POST["data_nascimento"] ?? '';
    $sexo           = $_POST["sexo"] ?? '';
    $telefone       = $_POST["telefone"] ?? '';
    $email          = $_POST["email"] ?? '';
    $cpf            = $_POST["cpf"] ?? '';
    $especialidade  = $_POST["especialidade"] ?? '';
    $horario        = $_POST["horario"] ?? '';
    $id_medico      = $_POST["medico"] ?? null; // Novo campo

    try {
        // Atualizado para incluir id_medico
        $sql = "INSERT INTO consultas 
        (id_usuario, nome_paciente, data_nascimento, sexo, telefone, email, cpf, especialidade, horario, id_medico)
        VALUES (:id, :nome, :nasc, :sexo, :tel, :email, :cpf, :esp, :hora, :medico)";

        $stmt = $conn->prepare($sql);
        
        $stmt->bindValue(':id', $id_usuario);
        $stmt->bindValue(':nome', $nome);
        $stmt->bindValue(':nasc', $data_nasc);
        $stmt->bindValue(':sexo', $sexo);
        $stmt->bindValue(':tel', $telefone);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':cpf', $cpf);
        $stmt->bindValue(':esp', $especialidade);
        $stmt->bindValue(':hora', $horario);
        $stmt->bindValue(':medico', $id_medico);

        if ($stmt->execute()) {
            echo json_encode(["status" => "ok"]);
        } else {
            echo json_encode(["status" => "erro", "detalhe" => "Falha ao inserir."]);
        }
    } catch (PDOException $e) {
        echo json_encode(["status" => "erro", "detalhe" => $e->getMessage()]);
    }
    exit;
}

// --- BUSCAR LISTA DE MÉDICOS PARA O FORMULÁRIO ---
$medicos = [];
try {
    $stmt = $conn->query("SELECT id, nome, especialidade FROM medicos ORDER BY nome ASC");
    $medicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Se der erro, segue com lista vazia
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Agendamento - Saúde & Vida</title>
  <link rel="shortcut icon" href="img/favicon-16x16.png">
  <link rel="stylesheet" href="css/style-agendamento.css">
  <script>
     <?php if(!isset($_SESSION['usuario_id'])): ?>
        window.location.href = 'pag-login.php';
     <?php endif; ?>
  </script>
</head>
<body>
  <div id="feedback-message" class="feedback-message"></div>

  <div class="outer-container">
    <div class="main-cadastro">
      <h1>AGENDAMENTO</h1>
      <div class="form-container">
        <form id="form-agendamento">
          <div class="textfield">
            
            <div class="field full">
              <label for="nome-completo">Nome Completo:</label>
              <input type="text" id="nome-completo" name="nome-completo" required>
            </div>
            <div class="field full">
                <label for="responsavel">Nome do Responsável (se menor):</label>
                <input type="text" id="responsavel" name="responsavel">
            </div>
            <div class="field">
              <label for="data-nascimento">Data de Nascimento:</label>
              <input type="date" id="data-nascimento" name="data-nascimento" required>
            </div>
            <div class="field">
              <label for="sexo">Sexo:</label>
              <select id="sexo" name="sexo" required>
                <option value="" disabled selected>-- Selecione --</option>
                <option value="Masculino">Masculino</option>
                <option value="Feminino">Feminino</option>
                <option value="Outro">Outro</option>
              </select>
            </div>
            <div class="field">
              <label for="telefone">Celular:</label>
              <input type="tel" id="telefone" name="telefone" required>
            </div>
            <div class="field">
              <label for="email">E-mail:</label>
              <input type="email" id="email" name="email" required>
            </div>
            <div class="field full">
              <label for="cpf">CPF:</label>
              <input type="text" id="cpf" name="cpf" required>
            </div>

            <div class="field">
              <label for="especialidade">Especialidade:</label>
              <select id="especialidade" name="especialidade" required>
                <option value="" disabled selected>-- Selecione --</option>
                <option value="Clínico Geral">Clínico Geral</option>
                <option value="Cardiologista">Cardiologista</option>
                <option value="Pediatria">Pediatria</option>
                <option value="Ginecologia">Ginecologia</option>
                <option value="Neurologia">Neurologia</option>
                <option value="Psicologia">Psicologia</option>
                <option value="Oftalmologista">Oftalmologista</option>
                <option value="Ortopedista">Ortopedista</option>
              </select>
            </div>

            <div class="field">
                <label for="medico">Médico de Preferência:</label>
                <select id="medico" name="medico" required>
                    <option value="" disabled selected>-- Escolha o médico --</option>
                    <?php foreach($medicos as $m): ?>
                        <option value="<?= $m['id'] ?>" data-esp="<?= $m['especialidade'] ?>">
                            <?= htmlspecialchars($m['nome']) ?> (<?= htmlspecialchars($m['especialidade']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="field full">
              <label for="horario">Data e Horário:</label>
              <input type="datetime-local" id="horario" name="horario" required>
            </div>
          </div>

          <div class="botoes">
            <button type="submit" class="btn-cadastro">Agendar</button>
            <button type="button" id="btn-limpar" class="btn-cadastro">Limpar Tudo</button>
          </div>
        </form>
      </div>
    </div>
    <div class="footer-actions">
        <button onclick="history.back()" class="btn-voltar">Voltar</button>
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


  <script src="js/script-agendamento.js"></script>
</body>
</html>
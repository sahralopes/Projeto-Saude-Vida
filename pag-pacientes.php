<?php
session_start();
require 'conexao.php';

// Segurança: Verifica se é ADMIN
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'ADMIN') {
    header("Location: pag-login.php");
    exit;
}

$mensagem = "";
$busca    = trim($_GET['q'] ?? ''); // termo de pesquisa

// --- LÓGICA DE CADASTRO (INSERT) ---
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['acao']) && $_POST['acao'] === 'cadastrar') {
    $nome  = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $cpf   = trim($_POST['cpf']);
    $login = trim($_POST['login']);
    $senha = trim($_POST['senha']); // Senha em texto puro

    if (!empty($nome) && !empty($login) && !empty($senha) && !empty($cpf)) {
        try {
            // Verifica duplicidade
            $check = $conn->prepare("SELECT id FROM usuarios WHERE login = :login OR cpf = :cpf");
            $check->bindValue(':login', $login);
            $check->bindValue(':cpf', $cpf);
            $check->execute();

            if ($check->rowCount() > 0) {
                $mensagem = "Erro: Login ou CPF já cadastrados!";
            } else {
                // Criptografa a senha
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

                $sqlInsert = "INSERT INTO usuarios (nome_completo, email, login, cpf, senha_hash, tipo) 
                              VALUES (:nome, :email, :login, :cpf, :senha, 'COMUM')";
                
                $stmt = $conn->prepare($sqlInsert);
                $stmt->bindValue(':nome',  $nome);
                $stmt->bindValue(':email', $email);
                $stmt->bindValue(':login', $login);
                $stmt->bindValue(':cpf',   $cpf);
                $stmt->bindValue(':senha', $senhaHash);
                
                if ($stmt->execute()) {
                    header("Location: pag-pacientes.php?sucesso=1");
                    exit;
                } else {
                    $mensagem = "Erro ao cadastrar paciente.";
                }
            }
        } catch (PDOException $e) {
            $mensagem = "Erro no banco: " . $e->getMessage();
        }
    } else {
        $mensagem = "Preencha todos os campos obrigatórios.";
    }
}

// --- LÓGICA DE EXCLUSÃO (DELETE) ---
if (isset($_GET['excluir'])) {
    $id_excluir = intval($_GET['excluir']);
    
    // Evita que o admin se exclua por acidente
    if ($id_excluir != $_SESSION['usuario_id']) {
        try {
            // Primeiro deleta as consultas desse paciente para não dar erro de chave estrangeira
            $conn->prepare("DELETE FROM consultas WHERE id_usuario = ?")->execute([$id_excluir]);

            // Depois deleta o usuário
            $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = :id");
            $stmt->bindValue(':id', $id_excluir);
            
            if ($stmt->execute()) {
                header("Location: pag-pacientes.php?deletado=1");
                exit;
            }
        } catch (PDOException $e) {
            $mensagem = "Erro ao excluir: " . $e->getMessage();
        }
    } else {
        $mensagem = "Você não pode excluir seu próprio usuário aqui.";
    }
}

// --- LISTAGEM (SELECT) COM FILTRO DE BUSCA ---
$sql = "SELECT * FROM usuarios WHERE tipo = 'COMUM'";

if ($busca !== '') {
    $sql .= " AND (
                nome_completo LIKE :busca
                OR cpf   LIKE :busca
                OR login LIKE :busca
                OR email LIKE :busca
              )";
}

$sql .= " ORDER BY nome_completo ASC";

$stmt = $conn->prepare($sql);

if ($busca !== '') {
    $stmt->bindValue(':busca', "%{$busca}%");
}

$stmt->execute();
$pacientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="img/favicon-16x16.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <link rel="stylesheet" href="css/style-admin.css">
    <title>Pacientes - Painel Admin</title>
    <style>
        .modal-header { background-color: #2B5F8C; color: white; }
        .modal-title { font-weight: bold; }
        .btn-close { filter: invert(1); }
        .acoes-btn { display: flex; gap: 5px; }
    </style>
</head>
<body>
    <input type="checkbox" id="nav-toggle">
    
    <div class="sidebar">
        <div class="sidebar-brand">
            <img src="img/Logo-menu (sem_fundo).png" alt="Logo Saúde e Vida">
            <p>Painel Admin</p>
        </div>
        <div class="sidebar-menu">
            <ul>
                <li>
                    <a href="pag-admin.php"><span class="las la-home"></span><span>Início</span></a>
                </li>

                <li>
                    <a href="pag-pacientes.php" class="active"><span class="las la-users"></span><span>Pacientes</span></a>
                </li>
                
                <li>
                    <a href="pag-medicos.php"><span class="las la-stethoscope"></span><span>Médicos</span></a>
                </li>
                
                <li>
                    <a href="pag-agenda-consultas.php"><span class="las la-clipboard-list"></span><span>Agendamento</span></a>
                </li>

                <li>
                    <a href="pag-logs-autenticacao.php">
                    <span class="las la-shield-alt"></span><span>LOG</span>
                    </a>
                </li>
                
                <li>
                    <a href="index.php"><span class="las la-hospital"></span><span>Página Principal</span></a>
                </li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        <header>
            <h3>
                <label for="nav-toggle"><span class="las la-bars"></span></label> 
                Pacientes
            </h3>

            <!-- Busca local só de pacientes -->
            <form class="search-wrapper" method="get" action="pag-pacientes.php">
                <span class="las la-search"></span>
                <input
                    type="search"
                    name="q"
                    placeholder="Pesquisar pacientes"
                    value="<?php echo htmlspecialchars($busca); ?>"
                />
            </form>
        </header>

        <main>
            <div class="container-fluid mt-3">
                
                <?php if(isset($_GET['sucesso'])): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        Paciente cadastrado com sucesso!
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if(isset($_GET['deletado'])): ?>
                    <div class="alert alert-warning alert-dismissible fade show">
                        Paciente e seus dados removidos com sucesso!
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if($mensagem): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?= $mensagem ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">Lista de pacientes</h4>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCadastrarPaciente">
                        <span class="las la-user-plus"></span> Novo Paciente
                    </button>
                </div>

                <div class="table-responsive bg-white p-3 rounded">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>CPF</th>
                                <th>Login</th>
                                <th>E-mail</th>
                                <th>Data Cadastro</th>
                                <th style="width: 100px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count($pacientes) > 0): ?>
                                <?php foreach($pacientes as $p): ?>
                                <tr>
                                    <td><?= htmlspecialchars($p['nome_completo']) ?></td>
                                    <td><?= htmlspecialchars($p['cpf']) ?></td>
                                    <td><?= htmlspecialchars($p['login']) ?></td>
                                    <td><?= htmlspecialchars($p['email']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($p['data_cadastro'])) ?></td>
                                    <td>
                                        <div class="acoes-btn">
                                            <a href="pag-pacientes.php?excluir=<?= $p['id'] ?>" 
                                               class="btn btn-sm btn-outline-danger"
                                               onclick="return confirm('Tem certeza? Isso apagará também as consultas deste paciente.');">
                                               <span class="las la-trash"></span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="text-center">Nenhum paciente encontrado.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <div class="modal fade" id="modalCadastrarPaciente" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Cadastrar Novo Paciente</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form method="POST" action="pag-pacientes.php">
                <input type="hidden" name="acao" value="cadastrar">

                <div class="mb-3">
                    <label class="form-label">Nome Completo:</label>
                    <input type="text" class="form-control" name="nome" required>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">CPF:</label>
                        <input type="text" class="form-control" name="cpf" placeholder="000.000.000-00" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">E-mail:</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Login (Usuário):</label>
                        <input type="text" class="form-control" name="login" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Senha Inicial:</label>
                        <input type="password" class="form-control" name="senha" required>
                    </div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-success">Salvar Paciente</button>
                </div>
            </form>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script-admin.js"></script>
</body>
</html>

<?php
session_start();
require 'conexao.php';

// Segurança: Verifica se é ADMIN
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'ADMIN') {
    header("Location: pag-login.php");
    exit;
}

$mensagem = "";
$busca = trim($_GET['q'] ?? ''); // termo de pesquisa

// --- LÓGICA DE CADASTRO (INSERT) ---
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['acao']) && $_POST['acao'] === 'cadastrar') {
    $nome          = trim($_POST['nome']);
    $crm           = trim($_POST['crm']);
    $especialidade = trim($_POST['especialidade']);
    $telefone      = trim($_POST['telefone']);
    $email         = trim($_POST['email']);

    if (!empty($nome) && !empty($crm)) {
        try {
            $sqlInsert = "INSERT INTO medicos (nome, crm, especialidade, telefone, email) 
                          VALUES (:nome, :crm, :esp, :tel, :email)";
            $stmt = $conn->prepare($sqlInsert);
            $stmt->bindValue(':nome', $nome);
            $stmt->bindValue(':crm', $crm);
            $stmt->bindValue(':esp', $especialidade);
            $stmt->bindValue(':tel', $telefone);
            $stmt->bindValue(':email', $email);
            
            if ($stmt->execute()) {
                header("Location: pag-medicos.php?sucesso=1");
                exit;
            } else {
                $mensagem = "Erro ao cadastrar médico.";
            }
        } catch (PDOException $e) {
            $mensagem = "Erro no banco: " . $e->getMessage();
        }
    } else {
        $mensagem = "Preencha os campos obrigatórios.";
    }
}

// --- LÓGICA DE EXCLUSÃO (DELETE) ---
if (isset($_GET['excluir'])) {
    $id_excluir = intval($_GET['excluir']);
    try {
        $sqlDelete = "DELETE FROM medicos WHERE id = :id";
        $stmt = $conn->prepare($sqlDelete);
        $stmt->bindValue(':id', $id_excluir);
        
        if ($stmt->execute()) {
            header("Location: pag-medicos.php?deletado=1");
            exit;
        }
    } catch (PDOException $e) {
        $mensagem = "Erro ao excluir: " . $e->getMessage();
    }
}

// --- LISTAGEM (SELECT) COM BUSCA ---
$sql = "SELECT * FROM medicos WHERE 1=1";

if ($busca !== '') {
    $sql .= " AND (
        nome LIKE :busca
        OR crm LIKE :busca
        OR especialidade LIKE :busca
        OR telefone LIKE :busca
        OR email LIKE :busca
    )";
}

$sql .= " ORDER BY nome ASC";

$stmt = $conn->prepare($sql);

if ($busca !== '') {
    $stmt->bindValue(':busca', "%{$busca}%");
}

$stmt->execute();
$medicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <title>Médicos - Painel Admin</title>
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
                <li><a href="pag-admin.php"><span class="las la-home"></span><span>Início</span></a></li>
                <li><a href="pag-pacientes.php"><span class="las la-users"></span><span>Pacientes</span></a></li>
                <li><a href="pag-medicos.php" class="active"><span class="las la-stethoscope"></span><span>Médicos</span></a></li>
                <li><a href="pag-agenda-consultas.php"><span class="las la-clipboard-list"></span><span>Agendamento</span></a></li>
                <li><a href="pag-logs-autenticacao.php"><span class="las la-shield-alt"></span><span>LOG</span></a></li>
                <li><a href="index.php"><span class="las la-hospital"></span><span>Página Principal</span></a></li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        <header>
            <h3>
                <label for="nav-toggle"><span class="las la-bars"></span></label> 
                Médicos
            </h3>

            <!-- BUSCA FUNCIONAL -->
            <form class="search-wrapper" method="get" action="pag-medicos.php">
                <span class="las la-search"></span>
                <input 
                    type="search" 
                    name="q" 
                    placeholder="Pesquisar médicos"
                    value="<?php echo htmlspecialchars($busca); ?>"
                />
            </form>
        </header>

        <main>
            <div class="container-fluid mt-3">
                
                <?php if(isset($_GET['sucesso'])): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        Médico cadastrado com sucesso!
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if(isset($_GET['deletado'])): ?>
                    <div class="alert alert-warning alert-dismissible fade show">
                        Médico removido com sucesso!
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
                    <h4 class="mb-0">Lista de médicos</h4>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCadastrarMedico">
                        <span class="las la-plus"></span> Novo
                    </button>
                </div>

                <div class="table-responsive bg-white p-3 rounded">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>CRM</th>
                                <th>Especialidade</th>
                                <th>Telefone</th>
                                <th>E-mail</th>
                                <th style="width: 100px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count($medicos) > 0): ?>
                                <?php foreach($medicos as $m): ?>
                                <tr>
                                    <td><?= htmlspecialchars($m['nome']) ?></td>
                                    <td><?= htmlspecialchars($m['crm']) ?></td>
                                    <td><?= htmlspecialchars($m['especialidade']) ?></td>
                                    <td><?= htmlspecialchars($m['telefone']) ?></td>
                                    <td><?= htmlspecialchars($m['email']) ?></td>
                                    <td>
                                        <div class="acoes-btn">
                                            <a href="pag-medicos.php?excluir=<?= $m['id'] ?>" 
                                               class="btn btn-sm btn-outline-danger"
                                               onclick="return confirm('Tem certeza que deseja excluir este médico?');">
                                               <span class="las la-trash"></span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="text-center">Nenhum médico encontrado.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </main>
    </div>

    <!-- MODAL DE CADASTRO -->
    <div class="modal fade" id="modalCadastrarMedico" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cadastrar Novo Médico</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <form method="POST" action="pag-medicos.php">
                        <input type="hidden" name="acao" value="cadastrar">

                        <div class="mb-3">
                            <label class="form-label">Nome Completo:</label>
                            <input type="text" class="form-control" name="nome" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">CRM:</label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="crm"
                                    name="crm" 
                                    placeholder="123456-RJ" 
                                    maxlength="9"
                                    required
                                >
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Especialidade:</label>
                                <select class="form-select" name="especialidade" required>
                                    <option value="" disabled selected>Selecione</option>
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
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Telefone:</label>
                            <input type="text" class="form-control" name="telefone">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">E-mail:</label>
                            <input type="email" class="form-control" name="email">
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">Salvar Médico</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script-admin.js"></script>

</body>
</html>

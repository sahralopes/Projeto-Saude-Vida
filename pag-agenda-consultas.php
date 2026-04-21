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

// --- LÓGICA DE EXCLUSÃO (DELETE) ---
if (isset($_GET['excluir'])) {
    $id_excluir = intval($_GET['excluir']);
    try {
        $stmt = $conn->prepare("DELETE FROM consultas WHERE id = :id");
        $stmt->bindValue(':id', $id_excluir);
        if ($stmt->execute()) {
            header("Location: pag-agenda-consultas.php?deletado=1");
            exit;
        }
    } catch (PDOException $e) {
        $mensagem = "Erro ao excluir: " . $e->getMessage();
    }
}

// --- LÓGICA DE ATUALIZAR STATUS ---
if (isset($_POST['acao']) && $_POST['acao'] === 'atualizar_status') {
    $id_consulta = intval($_POST['id_consulta']);
    $novo_status = $_POST['novo_status'];
    
    try {
        $stmt = $conn->prepare("UPDATE consultas SET status = :status WHERE id = :id");
        $stmt->bindValue(':status', $novo_status);
        $stmt->bindValue(':id', $id_consulta);
        $stmt->execute();
        header("Location: pag-agenda-consultas.php?atualizado=1");
        exit;
    } catch (PDOException $e) {
        $mensagem = "Erro ao atualizar: " . $e->getMessage();
    }
}

// --- LISTAGEM (SELECT) COM BUSCA ---
$sql = "SELECT c.*, u.nome_completo AS nome_usuario 
        FROM consultas c 
        LEFT JOIN usuarios u ON c.id_usuario = u.id
        WHERE 1=1";

if ($busca !== '') {
    $sql .= " AND (
        u.nome_completo LIKE :busca
        OR c.nome_paciente LIKE :busca
        OR c.especialidade LIKE :busca
        OR c.status LIKE :busca
        OR c.horario LIKE :busca
    )";
}

$sql .= " ORDER BY c.horario DESC";

$stmt = $conn->prepare($sql);

if ($busca !== '') {
    $stmt->bindValue(':busca', "%{$busca}%");
}

$stmt->execute();
$consultas = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <title>Agendamentos - Painel Admin</title>
    <style>
        .modal-header { background-color: #2B5F8C; color: white; }
        .btn-close { filter: invert(1); }
        .acoes-btn { display: flex; gap: 5px; }
        
        /* Cores para os status */
        .status-pendente { background-color: #ffc107; color: #000; }
        .status-confirmada { background-color: #198754; color: #fff; }
        .status-concluida { background-color: #0d6efd; color: #fff; }
        .status-cancelada { background-color: #dc3545; color: #fff; }
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
                    <a href="pag-pacientes.php"><span class="las la-users"></span><span>Pacientes</span></a>
                </li>
                
                <li>
                    <a href="pag-medicos.php"><span class="las la-stethoscope"></span><span>Médicos</span></a>
                </li>
                
                <li>
                    <a href="pag-agenda-consultas.php" class="active"><span class="las la-clipboard-list"></span><span>Agendamento</span></a>
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
                Agendamentos
            </h3>

            <!-- BUSCA FUNCIONAL -->
            <form class="search-wrapper" method="get" action="pag-agenda-consultas.php">
                <span class="las la-search"></span>
                <input 
                    type="search" 
                    name="q" 
                    placeholder="Pesquisar consultas"
                    value="<?php echo htmlspecialchars($busca); ?>"
                />
            </form>
        </header>

        <main>
            <div class="container-fluid mt-3">
                
                <?php if(isset($_GET['deletado'])): ?>
                    <div class="alert alert-warning alert-dismissible fade show">
                        Agendamento excluído com sucesso!
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                <?php if(isset($_GET['atualizado'])): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        Status atualizado com sucesso!
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
                    <h4 class="mb-0">Agenda de consultas</h4>
                </div>

                <div class="table-responsive bg-white p-3 rounded">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Hora</th>
                                <th>Paciente</th>
                                <th>Especialidade</th>
                                <th>Status</th>
                                <th style="width: 140px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count($consultas) > 0): ?>
                                <?php foreach($consultas as $c): 
                                    $data = date('d/m/Y', strtotime($c['horario']));
                                    $hora = date('H:i', strtotime($c['horario']));
                                    
                                    $nomePaciente = !empty($c['nome_paciente']) ? $c['nome_paciente'] : $c['nome_usuario'];
                                    
                                    $statusClass = 'bg-secondary';
                                    $status = strtolower($c['status'] ?? 'pendente');
                                    if (strpos($status, 'confirm') !== false)      $statusClass = 'status-confirmada';
                                    else if (strpos($status, 'pendente') !== false) $statusClass = 'status-pendente';
                                    else if (strpos($status, 'conclu') !== false)   $statusClass = 'status-concluida';
                                    else if (strpos($status, 'cancel') !== false)   $statusClass = 'status-cancelada';
                                ?>
                                <tr>
                                    <td><?= $data ?></td>
                                    <td><?= $hora ?></td>
                                    <td><?= htmlspecialchars($nomePaciente) ?></td>
                                    <td><?= htmlspecialchars($c['especialidade']) ?></td>
                                    <td>
                                        <span class="badge <?= $statusClass ?>"><?= htmlspecialchars($c['status'] ?? 'Pendente') ?></span>
                                    </td>
                                    <td>
                                        <div class="acoes-btn">
                                            <button class="btn btn-sm btn-outline-primary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalEditarStatus"
                                                    onclick="preencherModal(<?= $c['id'] ?>, '<?= $c['status'] ?? 'Pendente' ?>')">
                                                <span class="las la-edit"></span>
                                            </button>

                                            <a href="pag-agenda-consultas.php?excluir=<?= $c['id'] ?>" 
                                               class="btn btn-sm btn-outline-danger"
                                               onclick="return confirm('Tem certeza que deseja cancelar e excluir este agendamento?');">
                                               <span class="las la-trash"></span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="text-center">Nenhuma consulta agendada.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <div class="modal fade" id="modalEditarStatus" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Atualizar Status</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form method="POST" action="pag-agenda-consultas.php">
                <input type="hidden" name="acao" value="atualizar_status">
                <input type="hidden" name="id_consulta" id="id_consulta_modal">

                <div class="mb-3">
                    <label class="form-label">Novo Status:</label>
                    <select class="form-select" name="novo_status" id="status_modal">
                        <option value="Pendente">Pendente</option>
                        <option value="Confirmada">Confirmada</option>
                        <option value="Concluída">Concluída</option>
                        <option value="Cancelada">Cancelada</option>
                    </select>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Salvar Alteração</button>
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
    
    <script>
        function preencherModal(id, statusAtual) {
            document.getElementById('id_consulta_modal').value = id;
            document.getElementById('status_modal').value = statusAtual;
        }
    </script>
</body>
</html>

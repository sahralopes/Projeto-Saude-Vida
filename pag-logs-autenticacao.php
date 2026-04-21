<?php
session_start();
require 'conexao.php';

// Só ADMIN pode ver essa tela
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'ADMIN') {
    header('Location: pag-login.php');
    exit;
}

// ----- FILTROS -----
$busca = trim($_GET['busca'] ?? '');
$tipo  = $_GET['tipo'] ?? 'todos'; // 'nome', 'cpf' ou 'todos'

// Monta SQL base
$sql = "SELECT id, id_usuario, nome_usuario, cpf, metodo_2fa, data_hora, ip
        FROM logs_autenticacao
        WHERE 1=1";

$params = [];

if ($busca !== '') {
    if ($tipo === 'nome') {
        $sql .= " AND nome_usuario LIKE :busca";
        $params[':busca'] = '%' . $busca . '%';
    } elseif ($tipo === 'cpf') {
        $sql .= " AND cpf LIKE :busca";
        $params[':busca'] = '%' . $busca . '%';
    } else { 
        // todos: procura em nome e cpf
        $sql .= " AND (nome_usuario LIKE :busca OR cpf LIKE :busca)";
        $params[':busca'] = '%' . $busca . '%';
    }
}

$sql .= " ORDER BY data_hora DESC";

$stmt = $conn->prepare($sql);

foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v);
}

$stmt->execute();
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs de Autenticação - Painel Admin</title>
    <link rel="shortcut icon" href="img/favicon-16x16.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <link rel="stylesheet" href="css/style-admin.css">
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
            <li><a href="pag-medicos.php"><span class="las la-stethoscope"></span><span>Médicos</span></a></li>
            <li><a href="pag-agenda-consultas.php"><span class="las la-clipboard-list"></span><span>Agendamento</span></a></li>
            <li><a href="pag-logs-autenticacao.php" class="active"><span class="las la-shield-alt"></span><span>Logs Autenticação</span></a></li>
            <li><a href="index.php"><span class="las la-hospital"></span><span>Página Principal</span></a></li>
        </ul>
    </div>
</div>

<div class="main-content">
    <header>
        <h3>
            <label for="nav-toggle"><span class="las la-bars"></span></label>
            Logs de Autenticação
        </h3>
    </header>

    <main class="container-fluid mt-3">
        <!-- Filtro -->
        <div class="card mb-3">
            <div class="card-body">
                <form class="row g-2" method="GET" action="pag-logs-autenticacao.php">
                    <div class="col-md-4">
                        <label for="busca" class="form-label">Busca</label>
                        <input type="text" id="busca" name="busca" class="form-control"
                               placeholder="Nome ou CPF" value="<?= htmlspecialchars($busca) ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="tipo" class="form-label">Buscar por</label>
                        <select id="tipo" name="tipo" class="form-select">
                            <option value="todos"   <?= $tipo === 'todos' ? 'selected' : '' ?>>Nome ou CPF</option>
                            <option value="nome"    <?= $tipo === 'nome'  ? 'selected' : '' ?>>Somente Nome</option>
                            <option value="cpf"     <?= $tipo === 'cpf'   ? 'selected' : '' ?>>Somente CPF</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <span class="las la-search"></span> Buscar
                        </button>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <a href="pag-logs-autenticacao.php" class="btn btn-outline-secondary w-100">
                            Limpar
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabela de Logs -->
        <div class="table-responsive bg-white p-3 rounded">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Data / Hora</th>
                        <th>Nome Usuário</th>
                        <th>CPF</th>
                        <th>2º Fator (2FA)</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($logs) > 0): ?>
                        <?php foreach ($logs as $log): 
                            $dataHora = date('d/m/Y H:i:s', strtotime($log['data_hora']));
                        ?>
                        <tr>
                            <td><?= $dataHora ?></td>
                            <td><?= htmlspecialchars($log['nome_usuario']) ?></td>
                            <td><?= htmlspecialchars($log['cpf']) ?></td>
                            <td>
                                <span class="badge bg-info text-dark">
                                    <?= htmlspecialchars($log['metodo_2fa']) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($log['ip']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">
                                Nenhum log encontrado.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
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

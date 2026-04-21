<?php
session_start();
require 'conexao.php';

// Bloqueia acesso se não estiver logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: pag-login.php");
    exit;
}

$id_usuario = $_SESSION['usuario_id'];

// Se o usuário clicou em "cancelar"
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    header("Content-Type: application/json");

    if (!isset($_POST['id_consulta'])) {
        echo json_encode(["status" => "erro", "msg" => "ID não enviado"]);
        exit;
    }

    $idConsulta = $_POST['id_consulta'];

    try {
        $sql = "DELETE FROM consultas WHERE id = :id AND id_usuario = :user";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':id', $idConsulta);
        $stmt->bindValue(':user', $id_usuario);
        $stmt->execute();

        echo json_encode(["status" => "ok"]);
    } catch (Exception $e) {
        echo json_encode(["status" => "erro", "msg" => $e->getMessage()]);
    }
    exit;
}

// Buscar consultas ativas
$sql = "SELECT id, especialidade, nome_paciente, horario 
        FROM consultas 
        WHERE id_usuario = :id 
        ORDER BY horario ASC";

$stmt = $conn->prepare($sql);
$stmt->bindValue(':id', $id_usuario);
$stmt->execute();
$consultas = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suas Consultas</title>
    <link rel="shortcut icon" href="img/favicon-16x16.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style-consultas.css">
</head>

<body class="bg-light">
    <div class="container mt-4">

        <h2 class="mb-4">Minhas Consultas</h2>

        <?php if (count($consultas) === 0): ?>
            <div class="alert alert-info">Você não possui consultas agendadas.</div>
        <?php else: ?>

            <div class="table-responsive bg-white p-3 rounded shadow-sm">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Data/Hora</th>
                            <th>Especialidade</th>
                            <th>Paciente</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="lista-consultas">
                        <?php foreach ($consultas as $c): ?>
                            <tr data-id="<?= $c['id'] ?>">
                                <td><?= date('d/m/Y H:i', strtotime($c['horario'])) ?></td>
                                <td><?= htmlspecialchars($c['especialidade']) ?></td>
                                <td><?= htmlspecialchars($c['nome_paciente']) ?></td>
                                <td>
                                    <button class="btn-cancelar" onclick="cancelarConsulta(<?= $c['id'] ?>)">
                                        Cancelar
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php endif; ?>

        <a href="index.php" class="btn btn-secondary mt-3">Voltar</a>
    </div>

    <script>
        function cancelarConsulta(id) {
            if (!confirm("Tem certeza que deseja cancelar essa consulta?")) return;

            const formData = new FormData();
            formData.append("id_consulta", id);

            fetch("pag-consultas.php", {
                method: "POST",
                body: formData
            })
            .then(r => r.json())
            .then(res => {
                if (res.status === "ok") {
                    document.querySelector(`tr[data-id='${id}']`).remove();

                    if (document.querySelectorAll("#lista-consultas tr").length === 0) {
                        location.reload();
                    }

                } else {
                    alert("Erro ao cancelar: " + res.msg);
                }
            })
            .catch(err => alert("Erro: " + err));
        }
    </script>
</body>
</html>

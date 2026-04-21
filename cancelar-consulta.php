<?php
session_start();
require 'conexao.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(["status" => "erro", "msg" => "Não logado"]);
    exit;
}

if (!isset($_POST['id'])) {
    echo json_encode(["status" => "erro", "msg" => "ID da consulta não enviado"]);
    exit;
}

$idConsulta = intval($_POST['id']);
$idUsuario  = $_SESSION['usuario_id'];

try {
    $sql = "DELETE FROM consultas 
            WHERE id = :id AND id_usuario = :user";

    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':id', $idConsulta);
    $stmt->bindValue(':user', $idUsuario);

    if ($stmt->execute()) {
        echo json_encode(["status" => "ok"]);
    } else {
        echo json_encode(["status" => "erro", "msg" => "Falha ao excluir"]);
    }

} catch (Exception $e) {
    echo json_encode(["status" => "erro", "msg" => $e->getMessage()]);
}

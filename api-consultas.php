<?php
session_start();
require 'conexao.php';
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode([]); // Retorna array vazio se não logado
    exit;
}

$id_usuario = $_SESSION['usuario_id'];

try {
    // Busca consultas apenas deste usuário
    $sql = "SELECT especialidade, nome_paciente, horario 
            FROM consultas 
            WHERE id_usuario = :id 
            ORDER BY horario ASC";
            
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':id', $id_usuario);
    $stmt->execute();
    
    $consultas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($consultas);

} catch (Exception $e) {
    echo json_encode(["erro" => $e->getMessage()]);
}
?>
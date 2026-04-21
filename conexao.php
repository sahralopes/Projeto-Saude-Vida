<?php
$host   = 'localhost';
$banco  = 'saudevida';  // O nome do banco que vamos criar
$usuario = 'root';      // Usuário padrão do XAMPP
$senha   = '';          // Senha padrão é vazia

try {
    // Cria a conexão usando PDO
    $conn = new PDO("mysql:host=$host;dbname=$banco;charset=utf8", $usuario, $senha);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco: " . $e->getMessage());
}
?>
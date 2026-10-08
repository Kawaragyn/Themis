<?php
session_start();
require_once __DIR__ . "/../auth/conexao.php";

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: criar-post.html");
    exit;
}

$id_usuario = (int) $_SESSION['id_usuario'];
$titulo     = trim($_POST['titulo']   ?? '');
$conteudo   = trim($_POST['conteudo'] ?? '');

if ($titulo === '' || $conteudo === '') {
    echo "<script>alert('Preencha todos os campos.'); window.history.back();</script>";
    exit;
}

$sql = "INSERT INTO TOPICOS_FORUM (id_usuario, titulo, conteudo)
        VALUES (?, ?, ?)";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("iss", $id_usuario, $titulo, $conteudo);

if ($stmt->execute()) {
    header("Location: forum.php");
    exit;
} else {
    echo "Erro ao criar tópico: " . htmlspecialchars($stmt->error);
}

$stmt->close();
$conexao->close();
?>
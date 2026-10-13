<?php
session_start();
require_once __DIR__ . "/../auth/conexao.php";

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../auth/login.php");
    exit;
}

$id_usuario = (int) $_SESSION['id_usuario'];
$id_topico  = (int) ($_GET['id'] ?? 0);
$origem     = $_GET['origem'] ?? 'forum';

if ($id_topico <= 0) {
    header("Location: forum.php");
    exit;
}

// Segurança: só apaga se o post for do usuário logado
$sql = "DELETE FROM TOPICOS_FORUM 
        WHERE id_topico = ? AND id_usuario = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("ii", $id_topico, $id_usuario);
$stmt->execute();

$stmt->close();
$conexao->close();

// Volta pra página certa
if ($origem === 'perfil') {
    header("Location: ../perfil/meus-posts.php");
} else {
    header("Location: forum.php");
}
exit;
?>
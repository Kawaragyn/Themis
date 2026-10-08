<?php
session_start();
require_once __DIR__ . "/../auth/conexao.php";

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: mostrarperfil.php");
    exit;
}

$id_usuario = (int) $_SESSION['id_usuario'];

if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
    echo "<script>alert('Erro no upload da imagem.'); window.history.back();</script>";
    exit;
}

$arquivo = $_FILES['foto'];

if ($arquivo['size'] > 2 * 1024 * 1024) {
    echo "<script>alert('A imagem deve ter no máximo 2MB.'); window.history.back();</script>";
    exit;
}

$tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
if (!in_array($arquivo['type'], $tiposPermitidos)) {
    echo "<script>alert('Formato inválido. Use JPG, PNG, GIF ou WEBP.'); window.history.back();</script>";
    exit;
}

$extensao = pathinfo($arquivo['name'], PATHINFO_EXTENSION);
$nomeArquivo = "user_{$id_usuario}_" . time() . "." . $extensao;
$caminhoFisico = __DIR__ . "/../../image/perfil/" . $nomeArquivo;

if (!is_dir(__DIR__ . "/../../image/perfil/")) {
    mkdir(__DIR__ . "/../../image/perfil/", 0755, true);
}

if (!move_uploaded_file($arquivo['tmp_name'], $caminhoFisico)) {
    echo "<script>alert('Erro ao salvar a imagem.'); window.history.back();</script>";
    exit;
}

$caminhoBanco = "../../image/perfil/" . $nomeArquivo;

$sql = "UPDATE USUARIOS SET foto_perfil = ? WHERE id_usuario = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("si", $caminhoBanco, $id_usuario);

if ($stmt->execute()) {
    header("Location: mostrarperfil.php");
    exit;
} else {
    echo "Erro ao salvar no banco: " . htmlspecialchars($stmt->error);
}

$stmt->close();
$conexao->close();
?>
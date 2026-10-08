<?php
session_start();
require_once __DIR__ . "/../auth/conexao.php";

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../auth/login.php");
    exit;
}

$id_usuario = (int) $_SESSION['id_usuario'];

$sql = "SELECT nome, foto_perfil FROM USUARIOS WHERE id_usuario = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$u = $stmt->get_result()->fetch_assoc();
$stmt->close();

$foto = !empty($u['foto_perfil']) ? $u['foto_perfil'] : "../../image/foto-perfil.png";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Tópico | THEMIS</title>
    <link rel="stylesheet" href="../../styles/header.css">
    <link rel="stylesheet" href="../../styles/global.css">
    <link rel="stylesheet" href="../../styles/option-card.css">
    <link rel="stylesheet" href="../../styles/reset.css">
    <link rel="stylesheet" href="../../styles/button.css">
    <link rel="stylesheet" href="../../styles/navbar.css">
    <link rel="stylesheet" href="../../styles/forum.css">
</head>
<body>
    <header>
        <div class="botoes-forum">
            <div class="btn-voltar">
                <a href="forum.php">←</a>
            </div>
            <button type="submit" form="formPost" class="btn-postar">PUBLICAR</button>
        </div>
    </header>

    <main>
        <div class="forum-postar">
            <div class="bitelo">
                <img src="<?= htmlspecialchars($foto) ?>" alt="Foto de perfil">
            </div>

            <form action="salvar-post.php" method="POST" class="card-postar" id="formPost">
                <input 
                    type="text" 
                    id="titulo" 
                    name="titulo" 
                    maxlength="200"
                    placeholder="Título do tópico"
                    required
                    class="input-titulo">

                <textarea 
                    id="conteudo" 
                    name="conteudo" 
                    rows="6" 
                    maxlength="5000"
                    placeholder="Estou pensando em..."
                    required
                    class="input-conteudo"></textarea>
            </form>
        </div>
    </main>
</body>
</html>
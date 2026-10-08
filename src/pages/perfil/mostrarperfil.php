<?php
session_start();
require_once __DIR__ . "/../auth/conexao.php";

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../auth/login.php");
    exit;
}

$id_usuario = (int) $_SESSION['id_usuario'];

$sql = "SELECT usuario, nome, email, pontos_participacao, data_cadastro, foto_perfil
        FROM USUARIOS 
        WHERE id_usuario = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$u = $stmt->get_result()->fetch_assoc();
$stmt->close();
$conexao->close();

if (!$u) {
    session_destroy();
    header("Location: ../auth/login.php");
    exit;
}

$foto = !empty($u['foto_perfil']) ? $u['foto_perfil'] : "../../image/BItelo.jpg";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil | THEMIS</title>
    <link rel="stylesheet" href="../../styles/global.css">
    <link rel="stylesheet" href="../../styles/reset.css">
    <link rel="stylesheet" href="../../styles/option-card.css">
    <link rel="stylesheet" href="../../styles/navbar.css">
</head>
<body>
    <div class="conta">
        <section>
            <img src="<?= htmlspecialchars($foto) ?>" alt="Foto de Perfil" id="foto-preview">
            <h2><?= htmlspecialchars($u['nome']) ?></h2>
            <h3>@<?= htmlspecialchars($u['usuario']) ?></h3>
            <p>🏆 <?= (int)$u['pontos_participacao'] ?> pontos</p>

            <a href="alterar-dados.php" class="btn-editar">Editar perfil</a>
        </section>
    </div>

    <footer>
        <nav>
            <ul>
                <li><a href="../forum/forum.php" title="Fórum"><i data-lucide="message-square"></i></a></li>
                <li><a href="../noticias/noticias.php" title="Notícias"><i data-lucide="globe"></i></a></li>
                <li><a href="../formacao/trilhas.php" title="Início"><i data-lucide="home"></i></a></li>
                <li><a href="../acervo/index.php" title="Acervo"><i data-lucide="book-open"></i></a></li>
                <li><a href="mostrarperfil.php" title="Perfil"><i data-lucide="user"></i></a></li>
            </ul>
        </nav>
    </footer>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>lucide.createIcons();</script>
</body>
</html>
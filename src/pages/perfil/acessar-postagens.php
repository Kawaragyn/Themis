<?php
session_start();
require_once __DIR__ . "/../auth/conexao.php";

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../auth/login.php");
    exit;
}

$id_usuario = (int) $_SESSION['id_usuario'];

$sql = "SELECT id_usuario, nome, usuario, foto_perfil 
        FROM USUARIOS WHERE id_usuario = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$u = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$u) {
    session_destroy();
    header("Location: ../auth/login.php");
    exit;
}

$foto = !empty($u['foto_perfil']) ? $u['foto_perfil'] : "../../image/foto-perfil.png";

$sql = "SELECT id_topico, titulo, conteudo, data_criacao
        FROM TOPICOS_FORUM
        WHERE id_usuario = ?
        ORDER BY data_criacao DESC";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$posts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Posts | THEMIS</title>
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

            <a href="mostrarperfil.php" class="btn-editar">← Voltar ao perfil</a>
        </section>
    </div>

    <main class="lista-posts">
        <h1>Meus posts</h1>

        <?php if (empty($posts)): ?>
            <p>Você ainda não publicou nada. 
                <a href="../forum/criar-post.php">Criar primeiro post</a>
            </p>
        <?php else: ?>
            <?php foreach ($posts as $p): ?>
                <article class="topic-card">
                    <div class="topic-header">
                        <div class="user-info">
                            <img src="<?= htmlspecialchars($foto) ?>" 
                                 alt="Foto" class="avatar">
                            <h3 class="name"><?= htmlspecialchars($u['nome']) ?></h3>
                            <span class="user">@<?= htmlspecialchars($u['usuario']) ?></span>
                        </div>

                        <a href="../forum/apagar-post.php?id=<?= (int)$p['id_topico'] ?>&origem=perfil" 
                           class="btn-apagar"
                           onclick="return confirm('Apagar este post?');">
                            ⋮ 
                        </a>
                    </div>

                    <?php if (!empty($p['titulo'])): ?>
                        <h4 class="topic-titulo"><?= htmlspecialchars($p['titulo']) ?></h4>
                    <?php endif; ?>

                    <p class="topic-preview">
                        <?= nl2br(htmlspecialchars($p['conteudo'])) ?>
                    </p>

                    <div class="topic-interacoes">
                        <span class="date">
                            <?= date('d/m/Y H:i', strtotime($p['data_criacao'])) ?>
                        </span>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>

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
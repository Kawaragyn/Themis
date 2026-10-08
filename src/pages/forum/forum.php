<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . "/../auth/conexao.php";

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../auth/login.php");
    exit;
}

$id_usuario = (int) $_SESSION['id_usuario'];

$sql = "SELECT nome, pontos_participacao FROM USUARIOS WHERE id_usuario = ?";
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

$sql = "SELECT t.id_topico, t.titulo, t.conteudo, t.data_criacao,
               u.nome, u.usuario
        FROM TOPICOS_FORUM t
        INNER JOIN USUARIOS u ON u.id_usuario = t.id_usuario
        WHERE t.status_moderacao = 'aprovado'
        ORDER BY t.data_criacao DESC";
$topicos = $conexao->query($sql)->fetch_all(MYSQLI_ASSOC);

$conexao->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fórum | THEMIS</title>
    <link rel="stylesheet" href="../../styles/header.css">
    <link rel="stylesheet" href="../../styles/global.css">
    <link rel="stylesheet" href="../../styles/option-card.css">
    <link rel="stylesheet" href="../../styles/reset.css">
    <link rel="stylesheet" href="../../styles/button.css">
    <link rel="stylesheet" href="../../styles/navbar.css">
</head>
<body>
    <header>
        <div id="header-forum">
            <div>
                <a href="#" title="Notificações" class="notificacoes"><i data-lucide="bell"></i></a>
            </div>
            <div>
                <a href="#" title="Pesquisar" class="lupa"><i data-lucide="search"></i></a>
            </div>
        </div>
    </header>

    <main>
        <div id="lista-topicos">
            <?php if (empty($topicos)): ?>
                <p style="padding: 20px;">Nenhum tópico ainda. Seja o primeiro a postar!</p>
            <?php else: ?>
                <?php foreach ($topicos as $t): ?>
                    <article class="topic-card" data-id="<?= (int)$t['id_topico'] ?>">
                        <div class="topic-header">
                            <div class="user-info">
                                <img src="../../image/pessoa1.jpeg" alt="Foto de <?= htmlspecialchars($t['nome']) ?>" class="avatar">
                                <h3 class="name"><?= htmlspecialchars($t['nome']) ?></h3>
                                <span class="user">@<?= htmlspecialchars($t['usuario']) ?></span>
                            </div>
                            <a href="#" class="btn-seguir">Seguir</a>
                        </div>

                        <?php if (!empty($t['titulo'])): ?>
                            <h4 class="topic-titulo"><?= htmlspecialchars($t['titulo']) ?></h4>
                        <?php endif; ?>

                        <p class="topic-preview"><?= nl2br(htmlspecialchars($t['conteudo'])) ?></p>

                        <div class="topic-interacoes">
                            <div class="curtidas">
                                <button class="btn-curtir">
                                    <i data-lucide="heart"></i>
                                </button>
                                <span class="num-curtidas">0</span>
                            </div>
                            <div class="comentarios">
                                <button class="btn-comentar">
                                    <i data-lucide="message-circle"></i>
                                </button>
                                <span class="num-comentario">0</span>
                            </div>
                            <span class="date">
                                <?= date('d/m/Y', strtotime($t['data_criacao'])) ?>
                            </span>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div id="btn-noticias">
            <a href="criar-post.html" class="btn-add">+</a>
        </div>
    </main>

    <footer>
        <nav>
            <ul>
                <li><a href="forum.php" title="Fórum"><i data-lucide="message-square"></i></a></li>
                <li><a href="../noticias/noticias.php" title="Notícias"><i data-lucide="globe"></i></a></li>
                <li><a href="../formacao/trilhas.php" title="Início"><i data-lucide="home"></i></a></li>
                <li><a href="../acervo/index.php" title="Acervo"><i data-lucide="book-open"></i></a></li>
                <li><a href="../perfil/mostrarperfil.php" title="Perfil"><i data-lucide="user"></i></a></li>
            </ul>
        </nav>
    </footer>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>lucide.createIcons();</script>

    <script>
        // Mantém só a interação de curtir (visual, ainda não salva no banco)
        document.querySelectorAll('.btn-curtir').forEach(botao => {
            botao.addEventListener('click', () => {
                const container = botao.parentElement;
                const contador = container.querySelector('.num-curtidas');
                let valor = parseInt(contador.textContent, 10);

                if (botao.classList.contains('curtido')) {
                    botao.classList.remove('curtido');
                    contador.textContent = valor - 1;
                } else {
                    botao.classList.add('curtido');
                    contador.textContent = valor + 1;
                }
            });
        });
    </script>
</body>
</html>
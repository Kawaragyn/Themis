<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
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

if (!$stmt) {
    die("Erro ao preparar query de usuário: " . $conexao->error);
}

$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();
$u = $resultado->fetch_assoc();
$stmt->close();

if (!$u) {
    session_destroy();
    header("Location: ../auth/login.php");
    exit;
}

$sql = "SELECT id_trilha, titulo, descricao, ordem 
        FROM TRILHAS 
        ORDER BY ordem, id_trilha";

$resultado = $conexao->query($sql);

if (!$resultado) {
    die("Erro ao buscar trilhas: " . $conexao->error);
}

$trilhas = $resultado->fetch_all(MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trilhas | THEMIS</title>
    <link rel="stylesheet" href="../../styles/header.css">
    <link rel="stylesheet" href="../../styles/global.css">
    <link rel="stylesheet" href="../../styles/reset.css">
    <link rel="stylesheet" href="../../styles/navbar.css">
</head>
<body>
    <?php require_once __DIR__ . "/../shared/header.php"; ?>

    <main>

        <section class="lista-trilhas">
            <h2>Olá, <?= htmlspecialchars($u['nome']) ?>!</h2>

            <?php if (empty($trilhas)): ?>
                <p>Nenhuma trilha cadastrada ainda.</p>
            <?php else: ?>
                <?php foreach ($trilhas as $t): ?>
                    <a href="modulos.php?id_trilha=<?= (int)$t['id_trilha'] ?>" class="card-trilha">
                        <h3><?= htmlspecialchars($t['titulo']) ?></h3>
                        <p><?= htmlspecialchars($t['descricao'] ?? '') ?></p>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <nav>
            <ul>
                <li>
                    <a href="../forum/forum.php" title="Fórum">
                        <i data-lucide="message-square"></i>
                    </a>
                </li>
                <li>
                    <a href="../noticias/noticias.php" title="Notícias">
                        <i data-lucide="globe"></i>
                    </a>
                </li>
                <li>
                    <a href="trilhas.php" title="Início">
                        <i data-lucide="home"></i>
                    </a>
                </li>
                <li>
                    <a href="../acervo/index.php" title="Acervo">
                        <i data-lucide="book-open"></i>
                    </a>
                </li>
                <li>
                    <a href="../perfil/mostrarperfil.php" title="Perfil">
                        <i data-lucide="user"></i>
                    </a>
                </li>
            </ul>
        </nav>
    </footer>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>lucide.createIcons();</script>
</body>
</html>
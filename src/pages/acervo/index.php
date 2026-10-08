<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acervo | THEMIS</title>
    <link rel="stylesheet" href="../../styles/global.css">
    <link rel="stylesheet" href="../../styles/reset.css">
    <link rel="stylesheet" href="../../styles/header.css">
    <link rel="stylesheet" href="../../styles/button.css">
    <link rel="stylesheet" href="../../styles/navbar.css">
</head>
<body>
    <?php require_once __DIR__ . "/../shared/header.php"; ?>
    <main>
        <section class="botoes">
            <a href="#" class="btn-conteudo">Conteúdos</a>
            <a href="#" class="btn-exercicios">Exercícios</a>
            <a href="#" class="btn-leis">Leis</a>
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
                    <a href="../formacao/trilhas.php" title="Início">
                        <i data-lucide="home"></i>
                    </a>
                </li>
                <li>
                    <a href="index.php" title="Acervo">
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

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
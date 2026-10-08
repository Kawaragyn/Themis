<?php
// Abre o armário de sessões do PHP para descobrir qual administrador/usuário está logado
session_start();

// Trava de segurança: se não estiver logado, chuta de volta para a página de login
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.html");
    exit();
}

// Traz a conexão com o banco de dados
require_once __DIR__ . "/../auth/conexao.php";

// Só executa se o formulário de notícias foi enviado via método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Coleta os dados textuais limpando espaços em branco nas pontas
    $titulo     = trim($_POST['titulo']);
    $resumo     = trim($_POST['resumo']);
    $fonte      = trim($_POST['fonte']);
    $url_origem = trim($_POST['url_origem']);
    $abrangencia = trim($_POST['abrangencia']); // Ex: 'Nacional' ou 'Internacional'
    
    // Resgata o ID do usuário logado na sessão (quem está publicando a notícia)
    $id_usuario = $_SESSION['id_usuario'];

    // Validação de campos obrigatórios (conforme as regras NOT NULL do seu banco de dados)
    if (empty($titulo) || empty($resumo) || empty($fonte) || empty($abrangencia)) {
        echo "<script>
                alert('Por favor, preencha todos os campos obrigatórios!');
                window.history.back();
              </script>";
        exit();
    }

    // Prepara a estrutura SQL com "?" para receber os dados com total segurança contra SQL Injection
    $sql = "INSERT INTO NOTICIAS_ATUALIDADES (titulo, resumo, fonte, id_usuario, url_origem, abrangencia) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conexao->prepare($sql);
    
    if ($stmt) {
        // "ssisss" define os tipos de dados na ordem exata dos "?":
        // s = string, s = string, s = string, i = inteiro (id_usuario), s = string, s = string
        $stmt->bind_param("ssisss", $titulo, $resumo, $fonte, $id_usuario, $url_origem, $abrangencia);
        
        // Executa o comando e insere a notícia no banco de dados
        if ($stmt->execute()) {
            echo "<script>
                    alert('Notícia publicada com sucesso!');
                    window.location.href = 'noticias.php';
                  </script>";
        } else {
            // Exibe mensagem caso ocorra algum erro técnico inesperado no MySQL
            echo "Erro ao publicar notícia: " . $stmt->error;
        }
        
        // Fecha a preparação do comando para liberar a memória do servidor
        $stmt->close();
    }
}

// Fecha oficialmente a ponte de conexão com o MySQL
$conexao->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notícias | THEMIS</title>
    <link rel="stylesheet" href="../../styles/header.css">
    <link rel="stylesheet" href="../../styles/global.css">
    <link rel="stylesheet" href="../../styles/reset.css">
    <link rel="stylesheet" href="../../styles/button.css">
    <link rel="stylesheet" href="../../styles/navbar.css">
</head>
<body>
    <h1>uuuuuuuuuuuuuuuu</h1>
    <header id="header-principal">
        <div class="bitelo">
            <img src="../../image/BItelo.jpg" alt="foto-de-perfil">
        </div>
        <div class="duo">
            <img src="../../image/duo.svg" alt="logo">
        </div>
        <div class="qtd-trofeus">
            <span>🏆5</span>
        </div>
        
    </header>
    <main>

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
                    <a href="noticias.php" title="Notícias">
                        <i data-lucide="globe"></i>
                    </a>
                </li>
                <li>
                    <a href="../formacao/trilhas.php" title="Início">
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

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
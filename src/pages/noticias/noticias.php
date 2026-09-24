<?php
// Abre o armário de sessões do PHP para descobrir qual administrador/usuário está logado
session_start();

// Trava de segurança: se não estiver logado, chuta de volta para a página de login
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.html");
    exit();
}

// Traz a conexão com o banco de dados
require_once "conexao.php";

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

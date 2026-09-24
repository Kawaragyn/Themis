<?php
// Abre o armário de sessões do PHP para descobrir qual usuário está logado no site
session_start();

// Trava o acesso: se não existir um ID de usuário na sessão, chuta o visitante para a página de login
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.html");
    exit();
}

// Traz a conexão com o banco de dados
require_once "conexao.php";

// Só executa o bloco abaixo se o formulário do fórum foi enviado via método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Captura o texto do tópico e limpa espaços vazios nas pontas
    $conteudo = trim($_POST['conteudo']);
    
    // Resgata o ID do usuário que está guardado de forma segura na sessão do servidor
    $id_usuario = $_SESSION['id_usuario'];

    // Validação simples: impede o envio se o campo de texto estiver totalmente em branco
    if (empty($conteudo)) {
        echo "<script>
                alert('Por favor, digite algum conteúdo antes de postar!');
                window.history.back();
              </script>";
        exit();
    }

    // Prepara a estrutura SQL com "?" para os dados dinâmicos, evitando ataques de injeção
    // Nota: O campo 'status_moderacao' não entra aqui porque o banco já põe 'pendente' por padrão
    $sql = "INSERT INTO TOPICOS_FORUM (id_usuario, contenido) VALUES (?, ?)";
    $stmt = $conexao->prepare($sql);
    
    if ($stmt) {
        // "is" significa: 1º parâmetro é Inteiro (id_usuario), 2º parâmetro é String (conteudo)
        $stmt->bind_param("is", $id_usuario, $conteudo);
        
        // Executa o comando de inserção no banco de dados
        if ($stmt->execute()) {
            // Se der certo, avisa o usuário e atualiza a página do fórum
            echo "<script>
                    alert('Tópico enviado com sucesso! Aguardando moderação.');
                    window.location.href = 'forum.php';
                  </script>";
        } else {
            // Exibe mensagem caso aconteça alguma falha técnica desconhecida no banco
            echo "Erro ao postar tópico: " . $stmt->error;
        }
        
        // Fecha a preparação do comando para liberar memória
        $stmt->close();
    }
}

// Fecha a conexão com o banco de dados
$conexao->close();
?>
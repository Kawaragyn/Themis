<?php
// Inclui o arquivo de conexão criado no passo anterior
require_once "conexao.php";

// Verifica se o usuário chegou aqui enviando o formulário
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Coleta os dados e remove espaços extras no início e fim
    $usuario = trim($_POST['usuario']);
    $nome    = trim($_POST['nome']);
    $email   = trim($_POST['email']);
    $senha   = $_POST['senha'];

    // 1. CRIPTOGRAFIA DA SENHA (Segurança obrigatória)
    // O PASSWORD_DEFAULT gera um hash seguro (uma string gigante aleatória)
    $senha_criptografada = password_hash($senha, PASSWORD_DEFAULT);

    // 2. PREPARAÇÃO DO COMANDO SQL (Evita invasões por SQL Injection)
    // Usamos "?" no lugar dos valores por segurança
    $sql = "INSERT INTO USUARIOS (usuario, nome, email, senha) VALUES (?, ?, ?, ?)";
    $stmt = $conexao->prepare($sql);
    
    if ($stmt) {
        // "ssss" significa que estamos passando 4 variáveis do tipo String (texto)
        $stmt->bind_param("ssss", $usuario, $nome, $email, $senha_criptografada);
        
        // 3. EXECUÇÃO DO CADASTRO
        if ($stmt->execute()) {
            // Se der certo, exibe um alerta visual e redireciona para a página de login
            echo "<script>
                    alert('Cadastro realizado com sucesso! Bem-vindo ao Themis.');
                    window.location.href = 'login.html';
                  </script>";
        } else {
            // Tratamento de erro caso o Usuário ou E-mail já existam (Erro 1062 do MySQL)
            if ($conexao->errno == 1062) {
                echo "<script>
                        alert('Erro: Este nome de usuário ou e-mail já está em uso!');
                        window.history.back();
                      </script>";
            } else {
                echo "Erro ao cadastrar: " . $stmt->error;
            }
        }
        
        // Fecha a declaração por segurança
        $stmt->close();
    }
}

// Fecha a conexão com o banco de dados
$conexao->close();
?>
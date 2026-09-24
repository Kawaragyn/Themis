<?php
$host = "localhost";
$usuario_banco = "root"; // Padrão de servidores locais
$senha_banco = "";     // Padrão de servidores locais (vazio)
$nome_banco = "themis_db";

// Cria a conexão
$conexao = new mysqli($host, $usuario_banco, $senha_banco, $nome_banco);

// Se houver erro, para o sistema e mostra a falha
if ($conexao->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conexao->connect_error);
}

// Configura para aceitar acentos (ç, ã, é) corretamente do banco
$conexao->set_charset("utf8mb4");
?>
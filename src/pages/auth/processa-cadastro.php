<?php
session_start();
require_once __DIR__ . "/conexao.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: cadastro.php");
    exit;
}

$usuario = trim($_POST['usuario'] ?? '');
$nome    = trim($_POST['nome']    ?? '');
$email   = trim($_POST['email']   ?? '');
$senha   = $_POST['senha']        ?? '';

// Validações básicas
if ($usuario === '' || $nome === '' || $email === '' || $senha === '') {
    echo "<script>alert('Preencha todos os campos.'); window.history.back();</script>";
    exit;
}

if (strlen($senha) < 6) {
    echo "<script>alert('A senha deve ter pelo menos 6 caracteres.'); window.history.back();</script>";
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('E-mail inválido.'); window.history.back();</script>";
    exit;
}

// Verifica duplicidade antes (mensagem mais amigável)
$sql = "SELECT id_usuario FROM USUARIOS WHERE usuario = ? OR email = ? LIMIT 1";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("ss", $usuario, $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo "<script>alert('Usuário ou e-mail já cadastrado.'); window.history.back();</script>";
    $stmt->close();
    $conexao->close();
    exit;
}
$stmt->close();

// Hash da senha
$senha_criptografada = password_hash($senha, PASSWORD_DEFAULT);

// Insere no banco
$sql = "INSERT INTO USUARIOS (usuario, nome, email, senha) VALUES (?, ?, ?, ?)";
$stmt = $conexao->prepare($sql);

if ($stmt) {
    $stmt->bind_param("ssss", $usuario, $nome, $email, $senha_criptografada);

    if ($stmt->execute()) {
        // ✅ Cria a sessão automaticamente (login imediato)
        $_SESSION['id_usuario'] = $stmt->insert_id;
        $_SESSION['usuario']    = $usuario;
        $_SESSION['nome']       = $nome;

        // ✅ Redireciona direto pras trilhas
        echo "<script>
                alert('Cadastro realizado com sucesso! Bem-vindo ao Themis.');
                window.location.href = '../formacao/trilhas.php';
              </script>";
    } else {
        if ($stmt->errno == 1062) {
            echo "<script>alert('Usuário ou e-mail já está em uso.'); window.history.back();</script>";
        } else {
            echo "Erro ao cadastrar: " . htmlspecialchars($stmt->error);
        }
    }

    $stmt->close();
}

$conexao->close();
?>
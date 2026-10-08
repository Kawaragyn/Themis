<?php
session_start();
require_once __DIR__ . "/conexao.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $identificador = trim($_POST['usuario'] ?? '');
    $senha         = $_POST['senha']      ?? '';

    if ($identificador === '' || $senha === '') {
        $erro = "Preencha todos os campos.";
    } else {
        $sql = "SELECT id_usuario, usuario, nome, senha 
                FROM USUARIOS 
                WHERE usuario = ? OR email = ? 
                LIMIT 1";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ss", $identificador, $identificador);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 0) {
            $erro = "Usuário/E-mail ou senha incorretos.";
        } else {
            $user = $resultado->fetch_assoc();

            if (!password_verify($senha, $user['senha'])) {
                $erro = "Usuário/E-mail ou senha incorretos.";
            } else {
                $_SESSION['id_usuario'] = (int) $user['id_usuario'];
                $_SESSION['usuario']    = $user['usuario'];
                $_SESSION['nome']       = $user['nome'];

                $stmt->close();
                $conexao->close();
                header("Location: ../formacao/trilhas.php");
                exit;
            }
        }
        $stmt->close();
    }
}
$conexao->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar | THEMIS</title>
    <link rel="stylesheet" href="../../styles/global.css">
    <link rel="stylesheet" href="../../styles/reset.css">
    <link rel="stylesheet" href="../../styles/auth.css">
</head>
<body>
    <header>
        <div class="opcoes">
            <div class="btn-voltar">
                <a href="../../../index.html">&larr; Voltar</a>
            </div>
            <div id="logar-cadastrar">
                <a href="cadastro.php">Criar conta</a>
            </div>
        </div>
    </header>

    <main>
        <div class="header-sign-up-in">
            <h1>Entrar</h1>
            <p>Bem-vindo de volta! Insira seus dados para acessar a plataforma.</p>
        </div>
        
        <form action="login.php" method="POST" class="forms">
            <div id="formulario">
                <label for="usuario">Usuário ou E-mail</label>
                <input type="text" id="usuario" name="usuario" required>
            </div>

            <div id="formulario">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" required>
            </div>

            <?php if (!empty($erro)): ?>
                <p style="color:red; font-weight:bold;"><?= htmlspecialchars($erro) ?></p>
            <?php endif; ?>

            <div id="formulario">
                <a href="#">Esqueceu a senha?</a>
            </div>

            <div class="altura-entrar">
                <button type="submit" class="salvar">ENTRAR</button>
            </div>
        </form>
    </main>
</body>
</html>
<?php
session_start();
require_once __DIR__ . "/../auth/conexao.php";

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../auth/login.php");
    exit;
}

$id_usuario = (int) $_SESSION['id_usuario'];
$erro = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome    = trim($_POST['nome']    ?? '');
    $usuario = trim($_POST['usuario'] ?? '');
    $senha   = $_POST['senha']        ?? '';
    $confirmar = $_POST['confirmar']  ?? '';

    if ($nome === '' || $usuario === '') {
        $erro = "Preencha nome e usuário.";
    } elseif ($senha !== '' && strlen($senha) < 6) {
        $erro = "A senha deve ter pelo menos 6 caracteres.";
    } elseif ($senha !== '' && $senha !== $confirmar) {
        $erro = "As senhas não conferem.";
    } else {
        $sql_check = "SELECT id_usuario FROM USUARIOS WHERE usuario = ? AND id_usuario != ? LIMIT 1";
        $stmt = $conexao->prepare($sql_check);
        $stmt->bind_param("si", $usuario, $id_usuario);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $erro = "Esse nome de usuário já está em uso.";
            $stmt->close();
        } else {
            $stmt->close();

            if ($senha !== '') {
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                $sql = "UPDATE USUARIOS SET nome = ?, usuario = ?, senha = ? WHERE id_usuario = ?";
                $stmt = $conexao->prepare($sql);
                $stmt->bind_param("sssi", $nome, $usuario, $senha_hash, $id_usuario);
            } else {
                $sql = "UPDATE USUARIOS SET nome = ?, usuario = ? WHERE id_usuario = ?";
                $stmt = $conexao->prepare($sql);
                $stmt->bind_param("ssi", $nome, $usuario, $id_usuario);
            }

            if ($stmt->execute()) {
                $_SESSION['nome']    = $nome;
                $_SESSION['usuario'] = $usuario;

                if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                    $arquivo = $_FILES['foto'];

                    if ($arquivo['size'] <= 2 * 1024 * 1024) {
                        $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

                        if (in_array($arquivo['type'], $tiposPermitidos)) {
                            $extensao = pathinfo($arquivo['name'], PATHINFO_EXTENSION);
                            $nomeArquivo = "user_{$id_usuario}_" . time() . "." . $extensao;
                            $pastaDestino = __DIR__ . "/../../image/perfil/";

                            if (!is_dir($pastaDestino)) {
                                mkdir($pastaDestino, 0755, true);
                            }

                            $caminhoFisico = $pastaDestino . $nomeArquivo;

                            if (move_uploaded_file($arquivo['tmp_name'], $caminhoFisico)) {
                                $caminhoBanco = "../../image/perfil/" . $nomeArquivo;
                                $sql_foto = "UPDATE USUARIOS SET foto_perfil = ? WHERE id_usuario = ?";
                                $stmt_foto = $conexao->prepare($sql_foto);
                                $stmt_foto->bind_param("si", $caminhoBanco, $id_usuario);
                                $stmt_foto->execute();
                                $stmt_foto->close();
                            }
                        }
                    }
                }

                $stmt->close();
                $conexao->close();
                header("Location: mostrarperfil.php");
                exit;
            } else {
                $erro = "Erro ao atualizar: " . $stmt->error;
                $stmt->close();
            }
        }
    }
}

$sql = "SELECT usuario, nome, email, foto_perfil FROM USUARIOS WHERE id_usuario = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$u = $stmt->get_result()->fetch_assoc();
$stmt->close();
$conexao->close();

$foto = !empty($u['foto_perfil']) ? $u['foto_perfil'] : "../../image/BItelo.jpg";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil | THEMIS</title>
    <link rel="stylesheet" href="../../styles/global.css">
    <link rel="stylesheet" href="../../styles/reset.css">
    <link rel="stylesheet" href="../../styles/option-card.css">
    <link rel="stylesheet" href="../../styles/navbar.css">
</head>
<body>
    <div class="conta">
        <section>
            <h1>Editar perfil</h1>

            <?php if (!empty($erro)): ?>
                <p style="color:red; font-weight:bold;">⚠️ <?= htmlspecialchars($erro) ?></p>
            <?php endif; ?>

            <form action="alterar-dados.php" method="POST" enctype="multipart/form-data" class="form-editar">
                
                <div class="foto-upload">
                    <img src="<?= htmlspecialchars($foto) ?>" alt="Prévia" id="foto-preview">
                    <label for="foto" class="btn-foto">Trocar foto</label>
                    <input type="file" id="foto" name="foto" accept="image/*">
                </div>

                <label for="nome">Nome</label>
                <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($u['nome']) ?>" required maxlength="100">

                <label for="usuario">Usuário</label>
                <input type="text" id="usuario" name="usuario" value="<?= htmlspecialchars($u['usuario']) ?>" required maxlength="25">

                <label for="email">E-mail (não editável)</label>
                <input type="email" id="email" value="<?= htmlspecialchars($u['email']) ?>" disabled>

                <label for="senha">Nova senha (deixe em branco pra não alterar)</label>
                <input type="password" id="senha" name="senha" minlength="6">

                <label for="confirmar">Confirmar nova senha</label>
                <input type="password" id="confirmar" name="confirmar" minlength="6">

                <div class="form-botoes">
                    <a href="mostrarperfil.php" class="btn-cancelar">Cancelar</a>
                    <button type="submit" class="btn-salvar">Salvar alterações</button>
                </div>
            </form>
        </section>
    </div>

    <script>
        const inputFoto = document.getElementById('foto');
        const preview = document.getElementById('foto-preview');

        inputFoto.addEventListener('change', () => {
            const arquivo = inputFoto.files[0];
            if (arquivo) {
                preview.src = URL.createObjectURL(arquivo);
            }
        });
    </script>
</body>
</html>
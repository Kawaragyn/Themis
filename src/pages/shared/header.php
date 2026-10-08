<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$nome_header   = "Usuário";
$foto_header   = "../../image/foto-perfil.png";
$pontos_header = 0;

if (isset($_SESSION['id_usuario'])) {
    $caminho_conexao = __DIR__ . "/../auth/conexao.php";

    if (file_exists($caminho_conexao)) {
        require_once $caminho_conexao;

        $sql_h = "SELECT nome, pontos_participacao, foto_perfil 
                  FROM USUARIOS WHERE id_usuario = ?";
        $stmt_h = $conexao->prepare($sql_h);
        $stmt_h->bind_param("i", $_SESSION['id_usuario']);
        $stmt_h->execute();
        $u_h = $stmt_h->get_result()->fetch_assoc();
        $stmt_h->close();

        if ($u_h) {
            $nome_header   = $u_h['nome'];
            $pontos_header = (int) $u_h['pontos_participacao'];
            $foto_header   = !empty($u_h['foto_perfil']) 
                ? $u_h['foto_perfil'] 
                : "../../image/foto-perfil.png";
        }
    }
}
?>
<header id="header-principal">
    <div class="bitelo">
        <img src="<?= htmlspecialchars($foto_header) ?>" alt="foto-de-perfil">
    </div>
    <div class="duo">
        <img src="../../image/duo.svg" alt="logo">
    </div>
    <div class="qtd-trofeus">
        <span>🏆 <?= $pontos_header ?></span>
    </div>
</header>
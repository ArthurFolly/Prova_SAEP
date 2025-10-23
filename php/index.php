<?php require 'config.php';
if (isset($_SESSION['usuario_id'])) header('Location: principal.php');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>SAEP - Login</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/script.js"></script>
</head>
<body>
<div class="login-box">
    <h2>Sistema de Almoxarifado</h2>
    <h3>Login</h3>
    <form method="POST" onsubmit="return validarLogin()">
        <input type="text" name="nome_usuario" id="nome_usuario" placeholder="Usuário" required>
        <div class="input-group">
            <input type="password" name="senha" id="senha" placeholder="Senha" required>
            <span class="olho" onclick="toggleSenha('senha', this)">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                </svg>
            </span>
        </div>
        <button type="submit">Entrar</button>
    </form>
    <p class="link">Não tem conta? <a href="cadastro_usuario.php">Cadastre-se</a></p>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $stmt = $conn->prepare("SELECT id, senha FROM usuarios WHERE nome_usuario = ?");
        $stmt->bind_param('s', $_POST['nome_usuario']);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($u = $res->fetch_assoc()) {
            if (password_verify($_POST['senha'], $u['senha'])) {
                $_SESSION['usuario_id'] = $u['id'];
                $_SESSION['nome_usuario'] = $_POST['nome_usuario'];
                header('Location: principal.php');
                exit;
            } else echo '<p class="erro">Senha incorreta!</p>';
        } else echo '<p class="erro">Usuário não encontrado!</p>';
    }
    ?>
</div>
</body>
</html>
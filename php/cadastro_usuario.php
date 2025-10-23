<?php require 'config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Usuário</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/script.js"></script>
</head>
<body>
<div class="login-box">
    <h2>Cadastrar Novo Usuário</h2>
    <form method="POST" onsubmit="return validarCadastroUsuario()">
        <input type="text" name="nome_usuario" id="nome_usuario" placeholder="Nome de Usuário" required>
        <div class="input-group">
            <input type="password" name="senha" id="senha" placeholder="Senha (mín. 6)" required>
            <span class="olho" onclick="toggleSenha('senha', this)">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                </svg>
            </span>
        </div>
        <div class="input-group">
            <input type="password" name="confirmar_senha" id="confirmar_senha" placeholder="Confirmar Senha" required>
            <span class="olho" onclick="toggleSenha('confirmar_senha', this)">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                </svg>
            </span>
        </div>
        <button type="submit">Cadastrar</button>
    </form>
    <p class="link">Já tem conta? <a href="index.php">Faça login</a></p>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nome = trim($_POST['nome_usuario']);
        $senha = $_POST['senha'];
        $conf = $_POST['confirmar_senha'];
        if (strlen($senha) < 6) echo '<p class="erro">Senha deve ter 6+ caracteres!</p>';
        elseif ($senha !== $conf) echo '<p class="erro">Senhas não coincidem!</p>';
        else {
            $stmt = $conn->prepare("SELECT id FROM usuarios WHERE nome_usuario = ?");
            $stmt->bind_param("s", $nome);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) echo '<p class="erro">Usuário já existe!</p>';
            else {
                $hash = password_hash($senha, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO usuarios (nome_usuario, senha) VALUES (?, ?)");
                $stmt->bind_param("ss", $nome, $hash);
                if ($stmt->execute()) echo '<p class="sucesso">Cadastrado! <a href="index.php">Login</a></p>';
                else echo '<p class="erro">Erro!</p>';
            }
        }
    }
    ?>
</div>
</body>
</html>
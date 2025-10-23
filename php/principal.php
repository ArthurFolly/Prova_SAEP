<?php require 'config.php';
if (!isset($_SESSION['usuario_id'])) header('Location: index.php');
if (isset($_GET['logout'])) { session_destroy(); header('Location: index.php'); exit; }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>SAEP - Principal</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/script.js"></script>
</head>
<body>
<div class="container">
    <header>
        <h1>Bem-vindo, <?php echo htmlspecialchars($_SESSION['nome_usuario']); ?>!</h1>
        <a href="?logout=1" class="sair">Sair</a>
    </header>
    <div id="relogio"></div>
    <div class="menu">
        <a href="cadastro.php" class="btn">Cadastro de Produtos</a>
        <a href="gestao.php" class="btn">Gestão de Estoque</a>
        <?php if ($_SESSION['is_admin'] == 1): ?>
            <a href="relatorio.php" class="btn">Relatórios</a>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
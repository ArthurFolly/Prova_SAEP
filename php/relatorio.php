<?php require 'config.php';
if (!isset($_SESSION['usuario_id'])) header('Location: index.php');
if ($_SESSION['is_admin'] != 1) header('Location: principal.php');

$stmt = $conn->prepare("SELECT m.id, p.nome as produto, u.nome_usuario as usuario, m.tipo, m.quantidade, m.data 
FROM movimentacoes m 
JOIN produtos p ON m.produto_id = p.id 
JOIN usuarios u ON m.usuario_id = u.id 
ORDER BY m.data DESC");
$stmt->execute();
$resultado = $stmt->get_result();
$movimentacoes = $resultado->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Movimentações</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/script.js"></script>
</head>
<body>
<div class="container">
    <header>
        <h1>Relatório de Entradas e Saídas</h1>
        <a href="principal.php" class="btn-voltar">Voltar</a>
    </header>

    <table>
        <tr>
            <th>ID</th><th>Produto</th><th>Usuário</th><th>Tipo</th><th>Quantidade</th><th>Data</th>
        </tr>
        <?php foreach ($movimentacoes as $m): ?>
        <tr>
            <td><?php echo $m['id']; ?></td>
            <td><?php echo htmlspecialchars($m['produto']); ?></td>
            <td><?php echo htmlspecialchars($m['usuario']); ?></td>
            <td><?php echo $m['tipo']; ?></td>
            <td><?php echo $m['quantidade']; ?></td>
            <td><?php echo date('d/m/Y H:i:s', strtotime($m['data'])); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <div id="relogio"></div>
</div>
</body>
</html>
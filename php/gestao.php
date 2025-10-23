<?php require 'config.php';
if (!isset($_SESSION['usuario_id'])) header('Location: index.php');

$produtos = $conn->query("SELECT * FROM produtos")->fetch_all(MYSQLI_ASSOC);

// Bubble Sort
for ($i = 0; $i < count($produtos)-1; $i++) {
    for ($j = 0; $j < count($produtos)-$i-1; $j++) {
        if ($produtos[$j]['nome'] > $produtos[$j+1]['nome']) {
            $temp = $produtos[$j];
            $produtos[$j] = $produtos[$j+1];
            $produtos[$j+1] = $temp;
        }
    }
}

if (isset($_POST['movimentar'])) {
    $p = $conn->query("SELECT estoque_atual, estoque_minimo FROM produtos WHERE id = {$_POST['produto_id']}")->fetch_assoc();
    $novo = $p['estoque_atual'] + ($_POST['quantidade'] * ($_POST['tipo'] == 'entrada' ? 1 : -1));
    if ($_POST['tipo'] == 'saida' && $novo < $p['estoque_minimo']) {
        echo "<script>alert('ALERTA: Estoque abaixo do mínimo!');</script>";
    }
    $conn->query("UPDATE produtos SET estoque_atual = $novo WHERE id = {$_POST['produto_id']}");
    $stmt = $conn->prepare("INSERT INTO movimentacoes (produto_id, usuario_id, tipo, quantidade, data) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisis", $_POST['produto_id'], $_SESSION['usuario_id'], $_POST['tipo'], $_POST['quantidade'], $_POST['data']);
    $stmt->execute();
    header('Location: gestao.php');
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gestão de Estoque</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/script.js"></script>
</head>
<body>
<div class="container">
    <header>
        <h1>Gestão de Estoque</h1>
        <a href="principal.php" class="btn-voltar">Voltar</a>
    </header>

    <table>
        <tr><th>ID</th><th>Nome</th><th>Estoque Atual</th><th>Mínimo</th></tr>
        <?php foreach ($produtos as $p): ?>
        <tr <?php if ($p['estoque_atual'] < $p['estoque_minimo']) echo 'class="alerta"'; ?>>
            <td><?php echo $p['id']; ?></td>
            <td><?php echo htmlspecialchars($p['nome']); ?></td>
            <td><?php echo $p['estoque_atual']; ?></td>
            <td><?php echo $p['estoque_minimo']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h2>Movimentação</h2>
    <form method="POST" onsubmit="return validarMovimentacao()">
        <select name="produto_id" required>
            <option value="">Selecione</option>
            <?php foreach ($produtos as $p): ?>
            <option value="<?php echo $p['id']; ?>"><?php echo $p['nome']; ?></option>
            <?php endforeach; ?>
        </select>
        <div class="radio-group">
            <label><input type="radio" name="tipo" value="entrada" required> Entrada</label>
            <label><input type="radio" name="tipo" value="saida"> Saída</label>
        </div>
        <input type="number" name="quantidade" placeholder="Quantidade" required>
        <input type="datetime-local" name="data" required>
        <button type="submit" name="movimentar">Registrar</button>
    </form>

    <div id="relogio"></div>
</div>
</body>
</html>
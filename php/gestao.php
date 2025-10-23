<?php
require 'config.php';
if (!isset($_SESSION['usuario_id'])) header('Location: index.php');

// === BUSCA PRODUTOS COM ALERTA VISUAL ===
$produtos = $conn->query("SELECT * FROM produtos ORDER BY estoque_atual < estoque_minimo DESC, nome")->fetch_all(MYSQLI_ASSOC);

// === ALERTA AUTOMÁTICO NA PÁGINA (SÓ UMA VEZ) ===
$alerta_estoque = false;
foreach ($produtos as $p) {
    if ($p['estoque_atual'] < $p['estoque_minimo']) {
        $alerta_estoque = true;
        break;
    }
}
if ($alerta_estoque) {
    echo "<script>alert('ALERTA: Há produtos com estoque abaixo do mínimo!');</script>";
}

// === MOVIMENTAÇÃO SEGURA COM PREPARED STATEMENTS ===
if (isset($_POST['movimentar'])) {
    $produto_id = (int)$_POST['produto_id'];
    $tipo = $_POST['tipo']; // 'entrada' ou 'saida'
    $quantidade = (int)$_POST['quantidade'];
    $data = $_POST['data'];

    // Validação básica
    if ($quantidade <= 0 || !in_array($tipo, ['entrada', 'saida']) || empty($data)) {
        $erro = "Dados inválidos!";
    } else {
        // Busca estoque atual
        $stmt = $conn->prepare("SELECT estoque_atual, estoque_minimo FROM produtos WHERE id = ?");
        $stmt->bind_param("i", $produto_id);
        $stmt->execute();
        $p = $stmt->get_result()->fetch_assoc();

        if (!$p) {
            $erro = "Produto não encontrado!";
        } else {
            $fator = ($tipo === 'entrada') ? 1 : -1;
            $novo_estoque = $p['estoque_atual'] + ($quantidade * $fator);

            // Alerta se saída levar abaixo do mínimo
            if ($tipo === 'saida' && $novo_estoque < $p['estoque_minimo']) {
                $alerta_saida = true;
            }

            // Atualiza estoque
            $stmt = $conn->prepare("UPDATE produtos SET estoque_atual = ? WHERE id = ?");
            $stmt->bind_param("ii", $novo_estoque, $produto_id);
            $stmt->execute();

            // Registra movimentação
            $stmt = $conn->prepare("INSERT INTO movimentacoes (produto_id, usuario_id, tipo, quantidade, data) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iisis", $produto_id, $_SESSION['usuario_id'], $tipo, $quantidade, $data);
            $stmt->execute();

            header('Location: gestao.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>SAEP - Gestão de Estoque</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/script.js"></script>
</head>
<body>
<div class="container">
    <header>
        <h1>Gestão de Estoque</h1>
        <a href="principal.php" class="btn-voltar">Voltar</a>
    </header>

    <!-- ALERTA VISUAL NA TABELA -->
    <?php if ($alerta_estoque): ?>
        <div class="erro" style="margin:20px 0; text-align:center; font-weight:bold;">
            ALERTA: Produtos com estoque abaixo do mínimo!
        </div>
    <?php endif; ?>

    <!-- ALERTA DE SAÍDA CRÍTICA -->
    <?php if (isset($alerta_saida)): ?>
        <div class="erro" style="margin:20px 0; text-align:center;">
            ATENÇÃO: Saída registrada, mas o estoque ficou abaixo do mínimo!
        </div>
    <?php endif; ?>

    <!-- TABELA DE PRODUTOS -->
    <table>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Estoque Atual</th>
            <th>Mínimo</th>
            <th>Status</th>
        </tr>
        <?php foreach ($produtos as $p): ?>
        <tr <?php if ($p['estoque_atual'] < $p['estoque_minimo']) echo 'class="alerta"'; ?>>
            <td><?= $p['id'] ?></td>
            <td><?= htmlspecialchars($p['nome']) ?></td>
            <td><?= $p['estoque_atual'] ?></td>
            <td><?= $p['estoque_minimo'] ?></td>
            <td>
                <?php if ($p['estoque_atual'] < $p['estoque_minimo']): ?>
                    <span style="color:red; font-weight:bold;">BAIXO</span>
                <?php else: ?>
                    <span style="color:green;">OK</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <!-- FORMULÁRIO DE MOVIMENTAÇÃO -->
    <h2>Registrar Movimentação</h2>
    <?php if (isset($erro)): ?>
        <p class="erro"><?= $erro ?></p>
    <?php endif; ?>

    <form method="POST" onsubmit="return validarMovimentacao()">
        <select name="produto_id" required>
            <option value="">Selecione um produto</option>
            <?php foreach ($produtos as $p): ?>
                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nome']) ?></option>
            <?php endforeach; ?>
        </select>

        <div class="radio-group">
            <label><input type="radio" name="tipo" value="entrada" required> Entrada</label>
            <label><input type="radio" name="tipo" value="saida"> Saída</label>
        </div>

        <input type="number" name="quantidade" placeholder="Quantidade" min="1" required>
        <input type="datetime-local" name="data" required>

        <button type="submit" name="movimentar">Registrar Movimentação</button>
    </form>

    <div id="relogio"></div>
</div>
</body>
</html>
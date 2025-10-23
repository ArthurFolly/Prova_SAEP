<?php
require 'config.php';
if (!isset($_SESSION['usuario_id'])) header('Location: index.php');

// RESETAR FORMULÁRIO APÓS SALVAR
$editando = false;
if (isset($_POST['editar']) || isset($_POST['adicionar'])) {
    $editando = false; // Após salvar, volta ao modo adicionar
} elseif (isset($_GET['editar'])) {
    $editando = true;
    $id_edit = (int)$_GET['editar'];
    $stmt = $conn->prepare("SELECT * FROM produtos WHERE id = ?");
    $stmt->bind_param("i", $id_edit);
    $stmt->execute();
    $produto_edit = $stmt->get_result()->fetch_assoc();
}

$mensagem = $_SESSION['mensagem'] ?? '';
unset($_SESSION['mensagem']);

$busca = $_POST['busca'] ?? '';
$like = "%$busca%";
$stmt = $conn->prepare("SELECT * FROM produtos WHERE nome LIKE ?");
$stmt->bind_param("s", $like);
$stmt->execute();
$produtos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// ADICIONAR
if (isset($_POST['adicionar'])) {
    $stmt = $conn->prepare("INSERT INTO produtos (nome, descricao, material, tamanho, peso, estoque_minimo, estoque_atual) VALUES (?, ?, ?, ?, ?, ?, 0)");
    $stmt->bind_param("ssssdi", $_POST['nome'], $_POST['descricao'], $_POST['material'], $_POST['tamanho'], $_POST['peso'], $_POST['estoque_minimo']);
    $stmt->execute();
    $_SESSION['mensagem'] = ['tipo' => 'sucesso', 'texto' => 'Produto adicionado!'];
    header('Location: cadastro.php');
    exit;
}

// EDITAR
if (isset($_POST['editar'])) {
    $stmt = $conn->prepare("UPDATE produtos SET nome=?, descricao=?, material=?, tamanho=?, peso=?, estoque_minimo=? WHERE id=?");
    $stmt->bind_param("ssssdii", $_POST['nome'], $_POST['descricao'], $_POST['material'], $_POST['tamanho'], $_POST['peso'], $_POST['estoque_minimo'], $_POST['id']);
    $stmt->execute();
    $_SESSION['mensagem'] = ['tipo' => 'sucesso', 'texto' => 'Produto atualizado com sucesso!'];
    header('Location: cadastro.php');
    exit;
}

// EXCLUIR
if (isset($_POST['excluir'])) {
    $stmt = $conn->prepare("DELETE FROM produtos WHERE id = ?");
    $stmt->bind_param("i", $_POST['id']);
    $stmt->execute();
    $_SESSION['mensagem'] = ['tipo' => 'sucesso', 'texto' => 'Produto excluído!'];
    header('Location: cadastro.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Produtos</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/script.js"></script>
</head>
<body>
<div class="container">
    <header>
        <h1>Cadastro de Produtos</h1>
        <a href="principal.php" class="btn-voltar">Voltar</a>
    </header>

    <!-- MENSAGEM -->
    <?php if ($mensagem): ?>
        <div class="<?php echo $mensagem['tipo']; ?>">
            <?php echo htmlspecialchars($mensagem['texto']); ?>
        </div>
    <?php endif; ?>

    <!-- BUSCA -->
    <form method="POST" class="busca">
        <input type="text" name="busca" placeholder="Buscar produto..." value="<?php echo htmlspecialchars($busca); ?>">
        <button type="submit">Buscar</button>
    </form>

    <!-- TABELA -->
    <table>
        <tr>
            <th>ID</th><th>Nome</th><th>Descrição</th><th>Material</th><th>Tamanho</th><th>Peso</th>
            <th>Est. Mín.</th><th>Est. Atual</th><th>Ações</th>
        </tr>
        <?php foreach ($produtos as $p): ?>
        <tr>
            <td><?php echo $p['id']; ?></td>
            <td><?php echo htmlspecialchars($p['nome']); ?></td>
            <td><?php echo htmlspecialchars($p['descricao']); ?></td>
            <td><?php echo htmlspecialchars($p['material']); ?></td>
            <td><?php echo htmlspecialchars($p['tamanho']); ?></td>
            <td><?php echo number_format($p['peso'], 2); ?></td>
            <td><?php echo $p['estoque_minimo']; ?></td>
            <td><strong><?php echo $p['estoque_atual']; ?></strong></td>
            <td>
                <a href="cadastro.php?editar=<?php echo $p['id']; ?>" class="btn" style="padding: 6px 12px; font-size: 14px;">Editar</a>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                    <button type="submit" name="excluir" class="btn" style="background: #dc3545; padding: 6px 12px; font-size: 14px;" onclick="return confirm('Excluir?')">Excluir</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <!-- FORMULÁRIO -->
    <h2 id="formTitulo"><?php echo $editando ? 'Editar Produto' : 'Adicionar Produto'; ?></h2>
    <form method="POST" id="formProduto" onsubmit="return validarProduto()">
        <input type="hidden" name="id" id="id" value="<?php echo $editando ? $produto_edit['id'] : ''; ?>">
        <input type="text" name="nome" id="nome" placeholder="Nome" value="<?php echo $editando ? htmlspecialchars($produto_edit['nome']) : ''; ?>" required>
        <input type="text" name="descricao" id="descricao" placeholder="Descrição" value="<?php echo $editando ? htmlspecialchars($produto_edit['descricao']) : ''; ?>">
        <input type="text" name="material" id="material" placeholder="Material" value="<?php echo $editando ? htmlspecialchars($produto_edit['material']) : ''; ?>">
        <input type="text" name="tamanho" id="tamanho" placeholder="Tamanho" value="<?php echo $editando ? htmlspecialchars($produto_edit['tamanho']) : ''; ?>">
        <input type="number" step="0.01" name="peso" id="peso" placeholder="Peso" value="<?php echo $editando ? $produto_edit['peso'] : ''; ?>">
        <input type="number" name="estoque_minimo" id="estoque_minimo" placeholder="Estoque Mínimo" value="<?php echo $editando ? $produto_edit['estoque_minimo'] : ''; ?>" required>
        <input type="number" value="<?php echo $editando ? $produto_edit['estoque_atual'] : '0'; ?>" readonly title="Só muda com entrada/saída">
        <button type="submit" name="<?php echo $editando ? 'editar' : 'adicionar'; ?>" id="btnEnviar">
            <?php echo $editando ? 'Salvar Alterações' : 'Adicionar Produto'; ?>
        </button>
    </form>

    <div id="relogio"></div>
</div>
</body>
</html>
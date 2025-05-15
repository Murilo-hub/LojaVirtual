<?php

require_once('../models/produtos.php');
require_once('../config/config.php');
require_once('../models/carrinho.php');

// Verifica se o cliente está logado
if (!isset($_SESSION['cliente_id'])) {
    header("Location: login.php");
    exit;
}

$clienteId = $_SESSION['cliente_id'];

// Adiciona o produto ao carrinho
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['produto_id'])) {
    $produtoId = (int) $_POST['produto_id'];
    $quantidade = isset($_POST['quantidade']) ? (int) $_POST['quantidade'] : 1;

    adicionarAoCarrinho($clienteId, $produtoId, $quantidade);

    // Redireciona de volta para evitar reenvio de formulário
    header("Location: carrinho.php");
    exit;
}

// Cria um objeto de produtos e busca todos os produtos
$produtosModel = new Produtos();
$produtos = $produtosModel->buscarTodosProdutos();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include_once('../includes/header.php'); ?>
    <main>
        <h2>Produtos Disponíveis</h2>
        <div class="produtos-lista">
            <?php if ($produtos): ?>
                <?php foreach ($produtos as $produto): ?>
                    <div class="produto-item">
                        <img src="assets/img/<?php echo $produto['id']; ?>.jpg" alt="<?php echo $produto['nome']; ?>">
                        <h3><?php echo $produto['nome']; ?></h3>
                        <p><?php echo $produto['descricao']; ?></p>
                        <p class="preco">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
                        <form method="post">
                            <input type="hidden" name="produto_id" value="<?= $produto['id'] ?>">
                            <label for="quantidade">Quantidade:</label>
                            <input type="number" name="quantidade" value="1" min="1" required>
                            <button type="submit">Adicionar ao Carrinho</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Não há produtos disponíveis no momento.</p>
            <?php endif; ?>
        </div>
    </main>
    <?php include_once('../includes/footer.php'); ?>
</body>
</html>

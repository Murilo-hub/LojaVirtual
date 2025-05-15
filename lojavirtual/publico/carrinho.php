<?php
session_start();
require_once '../config/config.php';
require_once '../models/carrinho.php';
require_once '../includes/auth.php'; // Verifica se o usuario está logado

verificarLoginCliente();

$clienteId = $_SESSION['cliente_id'];

// Remoção de item (via GET por simplicidade)
if (isset($_GET['remover'])) {
    $produtoId = (int)$_GET['remover'];
    removerDoCarrinho($clienteId, $produtoId);
    header("Location: carrinho.php");
    exit;
}

$itens = listarCarrinho($clienteId);
$total = 0;

foreach ($itens as $item) {
    $total += $item['subtotal'];
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Meu Carrinho</title>
    <link rel="stylesheet" href="assets/css/estilo.css"> <!-- se tiver -->
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container">
        <h2>Meu Carrinho</h2>

        <?php if (empty($itens)): ?>
            <p>Seu carrinho está vazio.</p>
        <?php else: ?>
            <table border="1" cellpadding="10">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Preço</th>
                        <th>Quantidade</th>
                        <th>Subtotal</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($itens as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['nome']) ?></td>
                            <td>R$ <?= number_format($item['preco'], 2, ',', '.') ?></td>
                            <td><?= $item['quantidade'] ?></td>
                            <td>R$ <?= number_format($item['subtotal'], 2, ',', '.') ?></td>
                            <td><a href="carrinho.php?remover=<?= $item['id'] ?>">Remover</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <p><strong>Total:</strong> R$ <?= number_format($total, 2, ',', '.') ?></p>
            <a href="checkout.php"><button>Finalizar Compra</button></a>
        <?php endif; ?>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
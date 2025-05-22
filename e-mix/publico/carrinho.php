<?php

require '../includes/conn.php';
include '../publico/models/carrinho.php';

$carrinho = $_SESSION['carrinho'] ?? [];

$total = 0.0;

if (!empty($carrinho)) {
    // Obter produtos do carrinho do banco
    $ids = implode(',', array_keys($carrinho));
    $stmt = $pdo->query("SELECT * FROM produtos WHERE id IN ($ids)");
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $produtos = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho</title>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <h2>Seu Carrinho</h2>

    <?php if (empty($produtos)): ?>
        <p>Seu carrinho está vazio.</p>
    <?php else: ?>
        <form method="POST" action="carrinho_atualizar.php">
            <table border="1">
                <tr>
                    <th>Produto</th>
                    <th>Preço</th>
                    <th>Quantidade</th>
                    <th>Total</th>
                    <th>Ação</th>
                </tr>
                <?php foreach ($produtos as $produto): 
                    $id = $produto['id'];
                    $qtd = $carrinho[$id];
                    $subtotal = $qtd * $produto['preco'];
                    $total += $subtotal;
                ?>
                <tr>
                    <td><?= htmlspecialchars($produto['nome']) ?></td>
                    <td>R$ <?= number_format($produto['preco'], 2, ',', '.') ?></td>
                    <td>
                        <input type="number" name="quantidade[<?= $id ?>]" value="<?= $qtd ?>" min="1">
                    </td>
                    <td>R$ <?= number_format($subtotal, 2, ',', '.') ?></td>
                    <td><a href="carrinho_remover.php?id=<?= $id ?>">Remover</a></td>
                </tr>
                <?php endforeach; ?>
            </table>

            <p><strong>Total: R$ <?= number_format($total, 2, ',', '.') ?></strong></p>
            <button type="submit">Atualizar Carrinho</button>
            <a href="checkout.php">Finalizar Pedido</a>
        </form>
    <?php endif; ?>

    <?php include '../includes/footer.php'; ?>

</body>
</html>
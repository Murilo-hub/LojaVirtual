<?php
session_start();

require '../includes/conn.php';
include '../publico/models/carrinho.php';

$itensCarrinho = [];
$total = 0.0;

if(!isset($_SESSION['cliente_id'])) {
    
} else{
    $clienteid = $_SESSION['cliente_id'];
    $itensCarrinho = Carrinho::getItensDoCliente($clienteid);
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

    <?php if (empty($itensCarrinho)): ?>
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
                <?php foreach ($itensCarrinho as $item): 
                    $subtotal = $item['quantidade_no_carrinho'] * $item['preco'];
                    $total += $subtotal;
                ?>
                <tr>
                    <td><?= htmlspecialchars($item['nome']) ?></td>
                    <td>R$ <?= number_format($item['preco'], 2, ',', '.') ?></td>
                    <td>
                        <input type="number" name="quantidade[<?= $item['id'] ?>]" value="<?= htmlspecialchars($item['quantidade_no_carrinho']) ?>" min="1">
                    </td>
                    <td>R$ <?= number_format($subtotal, 2, ',', '.') ?></td>
                    <td><a href="carrinho_remover.php?id=<?= $item['id'] ?>">Remover</a></td>
                </tr>
                <?php endforeach; ?>
            </table>

            <p><strong>Total: R$ <?= number_format($total, 2, ',', '.') ?></strong></p>
            <button type="submit">Atualizar Carrinho</button>
            <a href="finalizar.php">Finalizar Pedido</a>
        </form>
    <?php endif; ?>

    <?php include '../includes/footer.php'; ?>

</body>
</html>
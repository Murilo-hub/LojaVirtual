<?php
session_start();
require '../includes/conn.php';
require '../includes/auth_cliente.php';
require '../publico/models/carrinho.php';

$cliente_id = $_SESSION['cliente_id'];
$carrinho = [];

if (empty($cliente_id)){
    echo "Você precisa estar logado para finalizar o pedido.";
    exit;
}

$carrinho = Carrinho::getItensDoCliente($cliente_id);

if(empty($carrinho)) {
    echo "<p>Seu carrinho está vazio. Adicione produtos antes de finalizar o pedido.</p>";
    echo "<a href='index.php'>Voltar para a loja</a>";
    exit;
}

$total = 0;
foreach ($carrinho as $item) {
    $total += $item['preco'] * $item['quantidade_no_carrinho'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try{
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO pedidos (cliente_id, total) VALUES (:cliente_id, :total)");
        $stmt_pedido->execute([$cliente_id, $total]);
        $pedido_id = $pdo->lastInsertId();

        $stmt = $pdo->prepare("INSERT INTO pedidos_produtos (pedido_id, produto_id, quantidade) VALUES (:pedido_id, :produto_id, :quantidade)");
        foreach ($carrinho as $item) {
            $produto_id_atual = $item['id'];
            $quantidade_atual = $item['quantidade_no_carrinho'];
            $preco_unitario_atual = $item['preco'];

            $stmt_item->execute([$pedido_id, $produto_id_atual, $quantidade_atual, $preco_unitario_atual]);

            $stmt_estoque = $pdo->prepare("UPDATE produtos SET quantidade_estoque = quantidade_estoque - ? WHERE id = ? AND quantidade_estoque >= ?");
            $stmt_estoque->execute([$quantidade_atual, $produto_id_atual, $quantidade_atual]);
            if ($stmt_estoque->rowCount() == 0) {
                throw new Exception("Estoque insuficiente para o produto: " . htmlspecialchars($item['nome']));
            }
        }

            Carrinho::limparDoCarrinho($cliente_id);
            $pdo->commit();

            header("Location: pedido_sucesso.php?pedido_id=" . $pedido_id); // Passar pedido_id pode ser útil
            exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "<p>Erro ao finalizar pedido: " . htmlspecialchars($e->getMessage()) . "</p>";
        echo '<p><a href="carrinho.php">Voltar ao carrinho</a></p>';
    }

}

?>

<!DOCTYPE html>
<html lang="pt-br"> <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar Pedido</title> </head>
<body>
    <?php include '../includes/header.php'; // Adicionar header se tiver ?>
    <h2>Finalizar Pedido</h2>

    <h3>Resumo dos Itens:</h3>
    <table border="1" style="width:50%; margin-bottom:20px;">
        <thead>
            <tr>
                <th>Produto</th>
                <th>Quantidade</th>
                <th>Preço Unit.</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($carrinho as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['nome']) ?></td>
                <td><?= htmlspecialchars($item['quantidade_no_carrinho']) ?></td>
                <td>R$ <?= number_format($item['preco'], 2, ',', '.') ?></td>
                <td>R$ <?= number_format($item['preco'] * $item['quantidade_no_carrinho'], 2, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <p>Total do pedido: <strong>R$ <?= number_format($total, 2, ',', '.') ?></strong></p>

    <form method="POST">
        <button type="submit">Confirmar Pedido e Pagar</button>
    </form>
    <p><a href="carrinho.php">Voltar ao Carrinho</a></p>
    <?php include '../includes/footer.php'; // Adicionar footer se tiver ?>
</body>
</html>
<?php
session_start();
require '../includes/conn.php';
require '../includes/auth_cliente.php';
require '../publico/models/carrinho.php';

$cliente_id = $_SESSION['cliente_id'];

if (empty($cliente_id)){
    echo "Você precisa estar logado para finalizar o pedido.";
    exit;
}

$carrinhoItens = Carrinho::getItensDoCliente($cliente_id); // Renomeado para clareza

if(empty($carrinhoItens)) {
    echo "<p>Seu carrinho está vazio. Adicione produtos antes de finalizar o pedido.</p>";
    echo "<a href='index.php'>Voltar para a loja</a>";
    exit;
}

// Calcula o total
$total = 0;
foreach ($carrinhoItens as $item) {
    $total += $item['preco'] * $item['quantidade_no_carrinho'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try{
        $pdo->beginTransaction();

            $cupomIdAplicado = null; 

            $stmt_novo_pedido = $pdo->prepare(
            "INSERT INTO pedidos (cliente_id, cupom_id) 
             VALUES (:cliente_id, :cupom_id)"
        );
        
        $stmt_novo_pedido->execute([
            ':cliente_id' => $cliente_id,
            ':cupom_id'   => $cupomIdAplicado
        ]);
        
        $pedido_id = $pdo->lastInsertId();

        $stmt_inserir_item = $pdo->prepare("
            INSERT INTO item_pedido (pedido_id, produto_id, quantidade, preco_unitario) 
            VALUES (:pedido_id, :produto_id, :quantidade, :preco_unitario)
        ");

        $stmt_atualizar_estoque = $pdo->prepare(
            "UPDATE produtos SET quantidade_estoque = quantidade_estoque - :quantidade 
            WHERE id = :produto_id AND quantidade_estoque >= :quantidade_requerida"
        );

        foreach ($carrinhoItens as $item) { // $carrinhoItens é a variável com os itens do Carrinho::getItensDoCliente()
            $stmt_inserir_item->execute([
                ':pedido_id' => $pedido_id,
                ':produto_id' => $item['id'],
                ':quantidade' => $item['quantidade_no_carrinho'],
                ':preco_unitario' => $item['preco'] 
            ]);

            $stmt_atualizar_estoque->execute([
                ':quantidade' => $item['quantidade_no_carrinho'],
                ':produto_id' => $item['id'],
                ':quantidade_requerida' => $item['quantidade_no_carrinho']
            ]);

            if ($stmt_atualizar_estoque->rowCount() == 0) {
                throw new Exception("Estoque insuficiente ou produto não encontrado para: " . htmlspecialchars($item['nome']));
            }
        }

        // 3. Limpar o carrinho do cliente
        Carrinho::limparCarrinhoDoCliente($cliente_id);

        $pdo->commit();

        header("Location: pedido_sucesso.php?pedido_id=" . $pedido_id);
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        echo "<p>Erro ao finalizar pedido: " . htmlspecialchars($e->getMessage()) . "</p>";
        echo '<p><a href="carrinho.php">Voltar ao carrinho</a></p>';
    }
}

// ... (resto do seu HTML) ...
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar Pedido</title>
</head>
<body>
    <?php include '../includes/header.php'; ?>
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
            <?php foreach ($carrinhoItens as $item):?>
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

    <form method="POST" action="pedido_sucesso.php">
        <button type="submit">Confirmar Pedido e Pagar</button>
    </form>
    <p><a href="carrinho.php">Voltar ao Carrinho</a></p>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
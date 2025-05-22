<?php
session_start();
require '../includes/conn.php';
require '../includes/auth_cliente.php'; // cliente precisa estar logado

$cliente_id = $_SESSION['cliente_id'] ?? null;
$carrinho = $_SESSION['carrinho'] ?? [];

if (empty($carrinho)) {
    echo "<p>Seu carrinho está vazio.</p>";
    exit;
}

// Buscar produtos no carrinho
$ids = implode(',', array_keys($carrinho));
$stmt = $pdo->query("SELECT * FROM produtos WHERE id IN ($ids)");
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
foreach ($produtos as $produto) {
    $total += $produto['preco'] * $carrinho[$produto['id']];
}

// Se formulário for enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        // 1. Criar pedido
        $stmt = $pdo->prepare("INSERT INTO pedidos (cliente_id, status) VALUES (?, 'Aguardando Pagamento')");
        $stmt->execute([$cliente_id]);
        $pedido_id = $conn->lastInsertId();

        // 2. Inserir itens do pedido
        $stmt_item = $pdo->prepare("INSERT INTO item_pedido (pedido_id, produto_id, quantidade, preco_unitario) VALUES (?, ?, ?, ?)");

        foreach ($produtos as $produto) {
            $produto_id = $produto['id'];
            $quantidade = $carrinho[$produto_id];
            $preco_unitario = $produto['preco'];

            $stmt_item->execute([$pedido_id, $produto_id, $quantidade, $preco_unitario]);

            // 3. Atualizar estoque
            $stmt_estoque = $pdo->prepare("UPDATE produtos SET quantidade_estoque = quantidade_estoque - ? WHERE id = ?");
            $stmt_estoque->execute([$quantidade, $produto_id]);
        }

        $pdo->commit();

        unset($_SESSION['carrinho']); // limpa carrinho
        header("Location: pedido_sucesso.php");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "<p>Erro ao finalizar pedido: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h2>Finalizar Pedido</h2>
    <p>Total do pedido: <strong>R$ <?= number_format($total, 2, ',', '.') ?></strong></p>

    <form method="POST">
        <button type="submit">Confirmar Pedido</button>
    </form>
</body>
</html>
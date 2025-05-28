<?php
require '../includes/conn.php';        // Conexão com o banco
require '../includes/auth.php';        // Verifica se o admin está logado
require '../includes/functions.php';     // Funções auxiliares

// Atualizar status se for POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pedido_id'], $_POST['novo_status'])) {
    $pedido_id = (int) $_POST['pedido_id'];
    $novo_status = $_POST['novo_status'];

    $stmt = $conn->prepare("UPDATE pedidos SET status = ? WHERE id = ?");
    $stmt->execute([$novo_status, $pedido_id]);
    $mensagem = "Status do pedido #$pedido_id atualizado com sucesso.";
}

// Buscar todos os pedidos
$sql = "SELECT p.*, c.nome AS nome_cliente
        FROM pedidos p
        JOIN clientes c ON p.cliente_id = c.id
        ORDER BY p.data_pedido DESC";
$stmt = $pdo->query($sql);
$pedidos = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos - ADM</title>
</head>
<body>
    <h2>Gerenciar Pedidos</h2>

    <?php if (isset($mensagem)): ?>
        <p style="color: green;"><?= $mensagem ?></p>
    <?php endif; ?>

    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Data</th>
                <th>Status</th>
                <th>Atualizar</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pedidos as $pedido): ?>
                <tr>
                    <td><?= $pedido['id'] ?></td>
                    <td><?= htmlspecialchars($pedido['nome_cliente']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($pedido['data_pedido'])) ?></td>
                    <td>
                        <form method="POST" style="display: flex; gap: 8px;">
                            <input type="hidden" name="pedido_id" value="<?= $pedido['id'] ?>">
                            <select name="novo_status">
                                <?php
                                $status_disponiveis = [
                                    'Aguardando Pagamento',
                                    'Pagamento Confirmado',
                                    'Em Processamento',
                                    'Enviado',
                                    'Cancelado',
                                    'Pagamento Falhou'
                                ];
                                foreach ($status_disponiveis as $status):
                                ?>
                                    <option value="<?= $status ?>" <?= ($status === $pedido['status']) ? 'selected' : '' ?>>
                                        <?= $status ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit">Atualizar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="index.php">Voltar</a>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
    
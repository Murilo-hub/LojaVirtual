<?php
session_start();
require '../includes/conn.php'; 
include '../includes/header.php';

$pedido_id_confirmado = isset($_GET['pedido_id']) ? filter_input(INPUT_GET, 'pedido_id', FILTER_VALIDATE_INT) : null;

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido Confirmado!</title> </head>
<body>
    <h2>Pedido realizado com sucesso!</h2>

    <?php if ($pedido_id_confirmado): ?>
        <p>O número do seu pedido é: <strong><?= htmlspecialchars($pedido_id_confirmado) ?></strong>. Guarde este número para referência.</p>
    <?php endif; ?>
    
    <p>Obrigado por comprar conosco. Você receberá atualizações sobre o status do seu pedido por e-mail.</p>
    <p><a href="index.php">Voltar à loja</a></p>
    <p><a href="meus_pedidos.php">Ver Meus Pedidos</a></p> <?php include '../includes/footer.php'; ?>
</body>
</html>
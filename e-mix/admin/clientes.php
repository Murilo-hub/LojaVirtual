<?php
session_start();
require '../includes/conn.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$clientes = $pdo->query("SELECT id, nome, email, telefone FROM clientes")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes - ADM</title>
</head>
<body>
    <h2>Clientes Cadastrados</h2>
    <table border="1">
        <tr><th>ID</th><th>Nome</th><th>Email</th><th>Telefone</th></tr>
        <?php foreach ($clientes as $cliente): ?>
        <tr>
            <td><?= $cliente['id'] ?></td>
            <td><?= $cliente['nome'] ?></td>
            <td><?= $cliente['email'] ?></td>
            <td><?= $cliente['telefone'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <a href="index.php">Voltar</a>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
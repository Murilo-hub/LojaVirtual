    <?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Area ADM</title>
</head>
<body>
    <h1>Bem-vindo, <?php echo $_SESSION['admin_nome']; ?>!</h1>

    <ul>
        <li><a href="produtos.php">Gerenciar Produtos</a></li>
        <li><a href="pedidos.php">Gerenciar Pedidos</a></li>
        <li><a href="clientes.php">Ver Clientes</a></li>
        <li><a href="pedidos.php">Detalhes dos Pedidos</a></li>
        <li><a href="logout.php">Sair</a></li>
        <li><a href="../publico/index.php">Ir para pagina dos clientes</a></li>
    </ul>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
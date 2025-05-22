<?php
require '../includes/conn.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $senha = $_POST['senha'] ?? '';

    if ($email && $senha) {
        $stmt = $pdo->prepare("SELECT * FROM clientes WHERE email = ?");
        $stmt->execute([$email]);
        $cliente = $stmt->fetch();

        if ($cliente && password_verify($senha, $cliente['senha'])) {
            // Criar sessão do cliente
            $_SESSION['cliente_id'] = $cliente['id'];
            $_SESSION['cliente_nome'] = $cliente['nome'];

            header("Location: index.php"); // ou redirecione para uma página de boas-vindas
            exit;
        } else {
            $erro = "E-mail ou senha incorretos.";
        }
    } else {
        $erro = "Preencha todos os campos.";
    }
}
?>

<!-- Aqui entra o HTML do formulário de login -->
<?php if (isset($erro)): ?>
    <p style="color: red;"><?= $erro ?></p>
<?php endif; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../publico/css/style.css">
    <title>Login</title>
</head>
<body>
    <main>
        <?php include '../includes/header.php'; ?>

        <h2>Login</h2>

        <?php if (isset($erro)): ?>
            <p style="color: red;"><?= $erro ?></p>
        <?php endif; ?>

        <form method="POST" action="" class="formulario-cadastro">

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>

            <label for="senha">Senha:</label>
            <input type="password" name="senha" id="senha" required>

            <button type="submit">Entrar</button>
            <p>Não tem uma conta? <a href="cadastro.php">Cadastre-se</a></p>

        </form>

    </main>

    <?php include '../includes/footer.php'; ?>
    
</body>
</html>
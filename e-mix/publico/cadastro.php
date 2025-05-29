<?php
require '../includes/conn.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome     = trim($_POST['nome'] ?? '');
    $email    = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $telefone = trim($_POST['telefone'] ?? '');
    $endereco = trim($_POST['endereco'] ?? '');
    $senha    = $_POST['senha'] ?? '';

    // Validação básica
    if (!$nome || !$email || !$endereco || !$senha) {
        $erro = "Todos os campos são obrigatórios.";
    } else {
        // Verificar se email já está cadastrado
        $stmt = $pdo->prepare("SELECT id FROM clientes WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $erro = "E-mail já cadastrado.";
        } else {
            // Cadastrar novo cliente
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO clientes (nome, email, telefone, endereco, senha) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nome, $email, $telefone, $endereco, $senha_hash]);

            // Login automático (opcional)
            $_SESSION['cliente_id'] = $pdo->lastInsertId();
            $_SESSION['cliente_nome'] = $nome;

            header("Location: index.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../publico/css/style.css">
    <title>Cadastro</title>
</head>
<body>
    <main>
        <?php include '../includes/header.php'; ?>

        <h2>Cadastro</h2>

        <?php if (isset($erro)): ?>
            <p style="color: red;"><?= $erro ?></p>
        <?php endif; ?>

        <form method="POST" action="" class="formulario-cadastro">
            <label for="nome"><h4>Nome:</h4></label>
            <input type="text" name="nome" id="nome" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>

            <label for="telefone">Telefone:</label>
            <input type="text" name="telefone" id="telefone" required>

            <label for="endereco">Endereço:</label>
            <input type="text" name="endereco" id="endereco" required>

            <label for="senha">Senha:</label>
            <input type="password" name="senha" id="senha" required>

            <button type="submit">Cadastrar</button>

        </form>

        <?php include '../includes/footer.php'; ?>
    </main>
</body>
</html>
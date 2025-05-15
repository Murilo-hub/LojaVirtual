<?php

include ('../config/config.php');
include ('../models/usuarios.php');

$msg = "";
$erro = "";

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST')  {
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    // Verificar se o email tem um formato valido
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = "Por favor, insira um email válido.";
    }
    // Verificar se o email e senha estao corretos
    $sql = "SELECT * FROM clientes WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar se o email existe e se a senha esta correta
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($senha, $row['senha'])) {
            // Armazena o ID do usuario e redireciona para a pagina inicial
            $_SESSION['cliente_id'] = $row['id'];
            $_SESSION['cliente_nome'] = $row['nome'];
            header("Location: ../publico/index.php");
            exit();
        } else {
            $msg = "Credenciais inválidas.";
        }
    } else {
        $msg = "Credenciais inválidas.";
    }

    $stmt->close(); 

    $conn->close();
}

?>

<!-- Formulário de login -->
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include_once('../includes/header.php'); ?>
    <main>
        <div class="container">
            <h1>Login</h1>
            <?php if ($msg): ?>
                <div class="alert">
                    <?php echo htmlspecialchars($msg); ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" required>
                </div>
                <div class="form-group">
                    <label for="senha">Senha:</label>
                    <input type="password" name="senha" id="senha" required>
                </div>
                <button type="submit">Entrar</button>
                <p>Não tem uma conta? <a href="cadastro.php">Cadastre-se</a></p>
            </form>
        </div>
    </main>
    <?php include_once('../includes/footer.php'); ?>
</body>
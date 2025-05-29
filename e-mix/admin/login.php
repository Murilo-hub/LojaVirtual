<?php
require '../includes/conn.php';
require '../includes/auth.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $senha = $_POST['senha'] ?? '';

    if ($email && $senha) {
        $stmt = $pdo->prepare("SELECT * FROM usuarios_admin WHERE email = ?");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($senha, $admin['senha_hash'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_nome'] = $admin['nome'];
            $_SESSION['admin_perfil'] = $admin['perfil']; // opcional se quiser diferenciar permissões

            header("Location: index.php");
            exit;
        } else {
            $erro = "Email ou senha incorretos.";
        }
    } else {
        $erro = "Preencha todos os campos corretamente.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ADM</title>
</head>
<body>
    <h2>Login Administrativo</h2>
    <?php if (isset($erro)) echo "<p style='color: red;'>$erro</p>"; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="E-mail" required><br>
        <input type="password" name="senha" placeholder="Senha" required><br>
        <button type="submit">Entrar</button>
    </form>

    <a href="index.php">Voltar</a>
    <?php include '../includes/footer.php'; ?>
</body>
</html>

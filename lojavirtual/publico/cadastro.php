<?php

include('../config/config.php');
include('../models/usuarios.php');

$msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST')  {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $telefone = trim($_POST['telefone']);
    $endereco = trim($_POST['endereco']);
    $senha = $_POST['senha'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = "Email inválido.";
    } else {
        // Verifica se o email ja existe
        if(buscarClientePorEmail($conn, $email)) {
            $msg = "Email já cadastrado.";
        } else {
            // Cria o cliente
            if (criarCliente($conn, $nome, $email, $senha, $telefone, $endereco)) {
                $msg = "Cadastro realizado com sucesso!";
            } else {
                $msg = "Erro ao cadastrar. Tente novamente.";
            }
        }
    }

    $conn->close();
}

?>

<!-- Formulário de cadastro -->
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include_once('../includes/header.php'); ?>
    <main>
        <div class="container">
            <h1>Cadastro</h1>
            <?php if ($msg): ?>
                <div class="alert">
                    <?php echo htmlspecialchars($msg); ?>
                </div>
            <?php endif; ?>


            <form method="POST" action="">
                <div class="form-group">
                    <label for="nome">Nome:</label>
                    <input type="text" name="nome" id="nome" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" required>
                </div>
                <div class="form-group">
                    <label for="telefone">Telefone:</label>
                    <input type="text" name="telefone" id="telefone" required>
                </div>
                <div class="form-group">
                    <label for="endereco">Endereço:</label>
                    <input type="text" name="endereco" id="endereco" required>
                </div>
                <div class="form-group">
                    <label for="senha">Senha:</label>
                    <input type="password" name="senha" id="senha" required>
                </div>

                <button type="submit">Cadastrar</button>
            </form>
        </div>
    </main>
    
</body>
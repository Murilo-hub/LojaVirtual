<?php
session_start();
require '../includes/conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produto_id = filter_input(INPUT_POST, 'produto_id', FILTER_VALIDATE_INT);
    $quantidade = filter_input(INPUT_POST, 'quantidade', FILTER_VALIDATE_INT);

    if (!$produto_id || $quantidade < 1) {
        die("Dados inválidos.");
    }

    // Consulta para verificar se o produto existe
    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
    $stmt->execute([$produto_id]);
    $produto = $stmt->fetch();

    if (!$produto) {
        die("Produto não encontrado.");
    }

    // Cria carrinho na sessão se não existir
    if (!isset($_SESSION['carrinho'])) {
        $_SESSION['carrinho'] = [];
    }

    // Se o produto já estiver no carrinho, soma
    if (isset($_SESSION['carrinho'][$produto_id])) {
        $_SESSION['carrinho'][$produto_id] += $quantidade;
    } else {
        $_SESSION['carrinho'][$produto_id] = $quantidade;
    }

    header("Location: ../publico/carrinho.php");
    exit;
}

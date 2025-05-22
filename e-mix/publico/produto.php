<?php
require '../includes/conn.php';
session_start();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    echo "Produto inválido.";
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
$stmt->execute([$id]);
$produto = $stmt->fetch();

if (!$produto) {
    echo "Produto não encontrado.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($produto['nome']) ?></title>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <h2><?= htmlspecialchars($produto['nome']) ?></h2>

    <?php if (!empty($produto['imagem'])): ?>
        <img src="../<?= htmlspecialchars($produto['imagem']) ?>" alt="Imagem do produto" width="300"><br>
    <?php endif; ?>

    <p><strong>Preço:</strong> R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>
    <p><strong>Descrição:</strong> <?= nl2br(htmlspecialchars($produto['descricao'])) ?></p>
    <p><strong>Estoque disponível:</strong> <?= $produto['quantidade_estoque'] ?></p>

    <a href="index.php">Voltar para a loja</a>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
<?php
require '../includes/conn.php';
session_start();

// Buscar produtos do banco
$stmt = $pdo->query("SELECT * FROM produtos LIMIT 20");
$produtos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos em Destaque</title>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <h2>Produtos em destaque</h2>

    <div class="produtos">
        <?php foreach ($produtos as $produto): ?>
            <div class="produto">
                <h3><?= htmlspecialchars($produto['nome']) ?></h3>
                <p><?= htmlspecialchars(substr($produto['descricao'], 0, 100)) ?>...</p>
                <strong>R$ <?= number_format($produto['preco'], 2, ',', '.') ?></strong><br>

                <?php if (!empty($produto['imagem'])): ?>
                    <img src="../<?= htmlspecialchars($produto['imagem']) ?>" alt="Imagem de <?= htmlspecialchars($produto['nome']) ?>" width="150"><br>
                <?php endif; ?>

                <a href="produto.php?id=<?= $produto['id'] ?>">Ver detalhes</a>
            </div>
        <?php endforeach; ?>
    </div>
    <a href="logout.php">Sair</a>
    <?php include '../includes/footer.php'; ?>
</body>
</html>

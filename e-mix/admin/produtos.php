<?php
    session_start();
    require '../includes/conn.php';
    if (!isset($_SESSION['admin_id'])) {
        header("Location: login.php");
        exit;
    }
    $categorias = $pdo->query("SELECT * FROM categorias")->fetchAll(PDO::FETCH_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nome = $_POST['nome'] ?? '';
        $descricao = $_POST['descricao'] ?? '';
        $preco = $_POST['preco'] ?? 0;
        $estoque = $_POST['estoque'] ?? 0;
        $categoria_id = $_POST['categoria_id'] ?? null;
        $imagem_caminho = null;

        // Verifica se há imagem
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
            $nome_arquivo = uniqid('produto_', true) . '.' . $ext;
            $destino = "../uploads/" . $nome_arquivo;

            if (move_uploaded_file($_FILES['imagem']['tmp_name'], $destino)) {
                $imagem_caminho = "uploads/" . $nome_arquivo;
            } else {
                echo "<p>Erro ao salvar a imagem.</p>";
            }
        }

        $stmt = $pdo->prepare("INSERT INTO produtos (nome, descricao, preco, quantidade_estoque, categoria_id, imagem) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nome, $descricao, $preco, $estoque, $categoria_id, $imagem_caminho]);

        echo "<p>Produto cadastrado com sucesso!</p>";
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos - ADM</title>
</head>
<body>
    <h2>Adicionar Produto</h2>
    <form method="POST" enctype="multipart/form-data">
    <input type="text" name="nome" placeholder="Nome do produto" required><br>
    <textarea name="descricao" placeholder="Descrição" required></textarea><br>
    <input type="number" step="0.01" name="preco" placeholder="Preço" required><br>
    <input type="number" name="estoque" placeholder="Quantidade em estoque" required><br>

    <select name="categoria_id" required>
        <option value="">Selecione a categoria</option>
        <?php foreach ($categorias as $cat): ?>
            <option value="<?= $cat['id'] ?>"><?= $cat['nome'] ?></option>
        <?php endforeach; ?>
    </select><br>

    <label>Imagem:</label>
    <input type="file" name="imagem" accept="image/*"><br>

    <button type="submit">Cadastrar</button>
</form>
    <a href="index.php">Voltar</a>
    <?php include '../includes/footer.php'; ?>
</body>
</html>

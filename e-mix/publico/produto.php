<?php
session_start();

require '../includes/conn.php';
require '../publico/models/carrinho.php';
require '../publico/models/produto.php';

if (isset($_GET['adicionar'])) {
    // Obtendo o ID do produto da URL
    $produtoid = $_GET['adicionar'];

    // Verificando se o ID do produto é válido
    if (filter_var($produtoid, FILTER_VALIDATE_INT)) {
        // Buscando o produto com o método correto
        $produto = Produto::buscarProdutoId($produtoid);

        // Se o produto foi encontrado
        if ($produto) {
            if(!isset($_SESSION['cliente_id'])) {
                echo "<script>";
                echo "alert('Você precisa estar cadastrado e logado para adicionar itens ao carrinho!');";
                echo "window.location.href = 'login.php';"; // MODIFIQUE AQUI O CAMINHO PARA SUA PÁGINA DE LOGIN, SE NECESSÁRIO
                echo "</script>";
                exit;
            } else{
                $clienteId = $_SESSION['cliente_id'];
                // Adicionando o produto ao carrinho
                Carrinho::adicionarAoCarrinho($produto, $clienteId);
                echo "<script>alert('Produto adicionado ao carrinho!');</script>";
            }
        } else {
            // Se o produto não for encontrado, exibe uma mensagem
            echo "<script>alert('Produto não encontrado!');</script>";
        }
    } else {
        echo "<script>alert('ID inválido!');</script>";
    }
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// Verificando se o ID do produto é válido
if ($id) {
    // Recuperando os dados do produto do banco de dados
    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
    $stmt->execute([$id]);
    $produto = $stmt->fetch();

    // Verificando se o produto foi encontrado
    if (!$produto) {
        echo "Produto não encontrado.";
        exit;
    }
} else {
    if (!isset($_GET['adicionar'])) { // Só mostra "ID inválido" se não for uma ação de adicionar
        echo "ID do produto não fornecido para visualização.";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title><?= isset($produto['nome']) ? htmlspecialchars($produto['nome']) : 'Detalhes do Produto' ?></title>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <?php if (isset($produto) && $produto): ?>
        <h2><?= htmlspecialchars($produto['nome']) ?></h2>

        <?php if (!empty($produto['imagem'])): ?>
            <img src="../<?= htmlspecialchars($produto['imagem']) ?>" alt="Imagem do produto" width="300"><br>
        <?php endif; ?>

        <p><strong>Preço:</strong> R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>
        <p><strong>Descrição:</strong> <?= nl2br(htmlspecialchars($produto['descricao'])) ?></p>
        <p><strong>Estoque disponível:</strong> <?= $produto['quantidade_estoque'] ?></p>
        
        <a href="?id=<?= $produto['id'] ?>&adicionar=<?= $produto['id'] ?>">
            <button>Adicionar ao Carrinho</button>
        </a>
    <?php else: ?>
        <?php if (!isset($_GET['adicionar'])): // Só mostra se não foi uma tentativa de adicionar ?>
            <p>Produto não encontrado ou ID inválido.</p>
        <?php endif; ?>
    <?php endif; ?>
    <br>
    <a href="index.php">Voltar para a loja</a>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
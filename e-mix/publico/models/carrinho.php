<?php
require '../includes/conn.php';

class Carrinho {
    public static function adicionarAoCarrinho($produto, $clienteId) {
        global $pdo;
        
        if(empty($clienteId)){
            trigger_error("ID do cliente não fornecido para adicionar ao carrinho.", E_USER_WARNING);
            return false;
        }

        $produto = $produto->getId();

        // Verificando se o produto já está no carrinho
        $stmt = $pdo->prepare("SELECT * FROM carrinhos WHERE produto_id = :produto_id AND cliente_id = :cliente_id");
        $stmt->bindParam(':produto_id', $produto);
        $stmt->bindParam(':cliente_id', $clienteId);
        $stmt->execute();

        // Se o produto já estiver no carrinho, apenas aumenta a quantidade
        if ($stmt->rowCount() > 0) {
            $stmt = $pdo->prepare("UPDATE carrinhos SET quantidade = quantidade + 1 WHERE produto_id = :produto_id AND cliente_id = :cliente_id");
            $stmt->bindParam(':produto_id', $produto);
            $stmt->bindParam(':cliente_id', $clienteId);
            $stmt->execute();
        } else {
            // Caso contrário, adiciona o produto ao carrinho
            $stmt = $pdo->prepare("INSERT INTO carrinhos (produto_id, cliente_id, quantidade) VALUES (:produto_id, :cliente_id, 1)");
            $stmt->bindParam(':produto_id', $produto);
            $stmt->bindParam(':cliente_id', $clienteId);
            $stmt->execute();
        }
    }

    public static function getItensDoCliente($clienteId) {
        global $pdo;

        if(empty($clienteId)){
            return [];
        }

        $stmt = $pdo->prepare("
            SELECT p.id, p.nome, p.preco, p.imagem, c.quantidade AS quantidade_no_carrinho
            FROM carrinhos c
            JOIN produtos p ON c.produto_id = p.id
            WHERE c.cliente_id = :cliente_id
        ");
        $stmt->bindParam(':cliente_id', $clienteId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function limparDoCarrinho($clienteId) {
        global $pdo;

        if(empty($clienteId)){
            return false;
        }

        try{
            $stmt = $pdo->prepare("DELETE FROM carrinhos WHERE cliente_id = :cliente_id");
            $stmt->bindParam(':cliente_id', $clienteId, PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            trigger_error("Erro ao limpar carrinho: " . $e->getMessage(), E_USER_WARNING);
            return false;
        }
        
    }
}


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

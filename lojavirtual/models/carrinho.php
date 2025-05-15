<?php
require_once __DIR__ . '/../config/config.php';

function obterCarrinhoId($clienteId) {
    global $conn;

    // Verifica se já existe um carrinho
    $sql = "SELECT id FROM carrinhos WHERE cliente_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $clienteId);
    $stmt->execute();
    $result = $stmt->get_result();
    $carrinho = $result->fetch_assoc();

    // Se não existir, cria um novo carrinho
    if (!$carrinho) {
        $sql = "INSERT INTO carrinhos (cliente_id) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $clienteId);
        $stmt->execute();
        return $stmt->insert_id;
    }

    return $carrinho['id'];
}

function adicionarAoCarrinho($clienteId, $produtoId, $quantidade) {
    global $conn;

    $carrinhoId = obterCarrinhoId($clienteId);

    // Verifica se o produto já está no carrinho
    $sql = "SELECT id, quantidade FROM itens_carrinho WHERE carrinho_id = ? AND produto_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $carrinhoId, $produtoId);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();

    if ($item) {
        // Atualiza a quantidade
        $novaQuantidade = $item['quantidade'] + $quantidade;
        $sql = "UPDATE itens_carrinho SET quantidade = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $novaQuantidade, $item['id']);
        $stmt->execute();
    } else {
        // Adiciona novo item
        $sql = "INSERT INTO itens_carrinho (carrinho_id, produto_id, quantidade) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $carrinhoId, $produtoId, $quantidade);
        $stmt->execute();
    }
}

function removerDoCarrinho($clienteId, $produtoId) {
    global $conn;

    $carrinhoId = obterCarrinhoId($clienteId);

    $sql = "DELETE FROM itens_carrinho WHERE carrinho_id = ? AND produto_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $carrinhoId, $produtoId);
    $stmt->execute();
}

function listarCarrinho($clienteId) {
    global $conn;

    $carrinhoId = obterCarrinhoId($clienteId);

    $sql = "
        SELECT ic.id, p.nome, p.preco, ic.quantidade, (p.preco * ic.quantidade) AS subtotal
        FROM itens_carrinho ic
        JOIN produtos p ON ic.produto_id = p.id
        WHERE ic.carrinho_id = ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $carrinhoId);
    $stmt->execute();
    $result = $stmt->get_result();

    $itens = [];
    while ($row = $result->fetch_assoc()) {
        $itens[] = $row;
    }

    return $itens;
}

function limparCarrinho($clienteId) {
    global $conn;

    $carrinhoId = obterCarrinhoId($clienteId);

    $sql = "DELETE FROM itens_carrinho WHERE carrinho_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $carrinhoId);
    $stmt->execute();
}

<?php

require_once __DIR__ . '/../config/config.php';

class Produtos {
    private $conn;

    // Construtor para conexão com o banco de dados
    public function __construct() {
        global $conn; // Conectar ao banco
        $this->conn = $conn;
    }

    // Método para buscar todos os produtos
    public function buscarTodosProdutos() {
        $sql = "SELECT p.*, c.nome AS categoria_nome 
                FROM produtos p 
                JOIN categorias c ON p.categoria_id = c.id";

        $result = $this->conn->query($sql);

        $produtos = [];
        while ($row = $result->fetch_assoc()) {
            $produtos[] = $row;
        }

        return $produtos;
    }

    // Método para buscar produto por ID
    public function buscarProdutoPorId($id) {
        $sql = "SELECT p.*, c.nome AS categoria_nome 
                FROM produtos p 
                JOIN categorias c ON p.categoria_id = c.id
                WHERE p.id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Método para buscar produtos por categoria
    public function buscarProdutosPorCategoria($categoria_id) {
        $sql = "SELECT p.*, c.nome AS categoria_nome 
                FROM produtos p 
                JOIN categorias c ON p.categoria_id = c.id
                WHERE c.id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $categoria_id);
        $stmt->execute();

        $result = $stmt->get_result();

        $produtos = [];
        while ($row = $result->fetch_assoc()) {
            $produtos[] = $row;
        }

        return $produtos;
    }
}
?>

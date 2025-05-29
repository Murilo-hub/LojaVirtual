<?php
require '../includes/conn.php';

class Produto{
    private $id;
    private $nome;
    private $preco;
    private $descricao;
    private $imagem;

    public function __construct($id, $nome, $preco, $descricao, $imagem){
        $this->id = $id;
        $this->nome = $nome;
        $this->preco = $preco;
        $this->descricao = $descricao;
        $this->imagem = $imagem;
    }

    public function getId(){
        return $this->id;
    }

    public function getNome(){
        return $this->nome;
    }

    public function getPreco(){
        return $this->preco;
    }

    public function getDescricao(){
        return $this->descricao;
    }

    public function getImagem(){
        return $this->imagem;
    }

    public static function buscarProdutoId($id){
        global $pdo;

        $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $produto = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($produto){
            return new Produto($produto['id'], $produto['nome'], $produto['preco'], $produto['descricao'], $produto['imagem']);

        }

        return null;
    }
}

?>
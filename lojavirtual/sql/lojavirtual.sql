CREATE DATABASE lojavirtual;

USE lojavirtual;

CREATE TABLE cliente(
	id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    telefone VARCHAR(20),
    endereco TEXT NOT NULL
);

CREATE TABLE produtos(
	id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT NOT NULL,
    preco DECIMAL(10,2) NOT NULL CHECK (preco >= 0),
    quantidade_estoque INT NOT NULL CHECK (quantidade_estoque >= 0),
    categoria_id INT NOT NULL,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);

CREATE TABLE categorias(
	id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE pedidos(
	id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    data_pedido DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Aguardando Pagamento', 'Pagamento Confirmado', 'Em Processamento', 'Enviado', 'Cancelado', 'Pagamento Falhou') NOT NULL DEFAULT 'Aguardando Pagamento',
    cupom_id INT,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id),
    FOREIGN KEY (cupom_id) REFERENCES cupons(id)
);

CREATE TABLE item_pedido(
	id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade INT NOT NULL CHECK (quantidade > 0),
    preco_unitario DECIMAL(10,2) NOT NULL CHECK (preco_unitario >= 0),
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
    FOREIGN KEY (produto_id) REFERENCES produtos(id)
);

CREATE TABLE pagamentos(
	id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL UNIQUE,
    valor DECIMAL(10,2) NOT NULL CHECK (valor >= 0),
    data_pagamento DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    metodo_pagamento ENUM('Cartão', 'Boleto', 'Transferência') NOT NULL,
    status ENUM('Pendente', 'Pago', 'Falhou') NOT NULL DEFAULT 'Pendente',
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id)
);

CREATE TABLE cupons(
	id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) NOT NULL UNIQUE,
    tipo ENUM('Percentual', 'Valor Fixo') NOT NULL,
    valor DECIMAL(10,2) NOT NULL CHECK (valor >= 0),
    usos_totais INT NOT NULL CHECK (usos_totais >= 0),
    usos_restantes INT NOT NULL CHECK (usos_restantes >= 0),
    ativo BOOLEAN NOT NULL DEFAULT TRUE
);

CREATE TABLE carrinhos(
	id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    data_criacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id)
);

CREATE TABLE itens_carrinho(
	id INT AUTO_INCREMENT PRIMARY KEY,
    carrinho_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade INT NOT NULL CHECK (quantidade > 0),
    FOREIGN KEY (carrinho_id) REFERENCES carrinhos(id),
    FOREIGN KEY (produto_id) REFERENCES produtos(id)
);

CREATE TABLE usuarios_admin(
	id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,
    perfil ENUM('Administrador', 'Estoquista', 'Analista') NOT NULL
);

ALTER TABLE clientes
ADD COLUMN senha VARCHAR(255) NOT NULL;

SELECT * FROM clientes
<?php

$localhost = 'localhost';
$database = 'lojavirtual';
$username = 'root';
$password = 'root';

$conn = new mysqli($localhost, $username, $password, $database);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

?>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se existe algum usuário logado
function estaLogado() {
    return isset($_SESSION['cliente_id']) || isset($_SESSION['admin_id']);
}

// Verifica se é cliente
function ehCliente() {
    return isset($_SESSION['cliente_id']);
}

// Verifica se é admin
function ehAdmin() {
    return isset($_SESSION['admin_id']);
}

// Redireciona se ninguém estiver logado
function exigirLogin() {
    if (!estaLogado()) {
        header("Location: ../public/login.php"); // ou admin/login.php, dependendo do contexto
        exit;
    }
}

// Redireciona se não for admin
function exigirAdmin() {
    if (!ehAdmin()) {
        header("Location: ../public/login.php"); // ou alguma página pública
        exit;
    }
}

// Redireciona se não for cliente
function exigirCliente() {
    if (!ehCliente()) {
        header("Location: login.php"); // ou para index.php
        exit;
    }
}

function nomeUsuario() {
    if (ehCliente()) {
        return $_SESSION['cliente_nome'];
    } elseif (ehAdmin()) {
        return $_SESSION['admin_nome'];
    }
    return 'Visitante';
}

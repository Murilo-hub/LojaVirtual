<?php
// Protege páginas do cliente
function verificarLoginCliente() {
    if (!isset($_SESSION['cliente_id'])) {
        header("Location: ../publico/login.php");
        exit;
    }
}

// Protege páginas do administrador
function verificarLoginAdmin() {
    if (!isset($_SESSION['admin_id'])) {
        header("Location: ../admin/login.php");
        exit;
    }
}

// Verifica se o admin tem um perfil específico
function verificarPerfil($perfilPermitido) {
    if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== $perfilPermitido) {
        echo "Acesso negado. Perfil insuficiente.";
        exit;
    }
}

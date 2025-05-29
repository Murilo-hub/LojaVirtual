<?php

// Formata valores monetários (ex: R$ 59,99)
function formatar_preco($valor) {
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

// Converte o status do pedido para algo mais amigável
function traduzir_status_pedido($status) {
    $mapa = [
        'Aguardando Pagamento' => '🕒 Aguardando Pagamento',
        'Pagamento Confirmado' => '✅ Pagamento Confirmado',
        'Em Processamento' => '🔧 Em Processamento',
        'Enviado' => '📦 Enviado',
        'Cancelado' => '❌ Cancelado',
        'Pagamento Falhou' => '⚠️ Pagamento Falhou'
    ];
    return $mapa[$status] ?? $status;
}

// Cálculo do total de um pedido (soma dos itens)
function calcular_total_pedido($itens) {
    $total = 0;
    foreach ($itens as $item) {
        $total += $item['quantidade'] * $item['preco_unitario'];
    }
    return $total;
}

// Verifica se o admin atual tem um determinado perfil
function admin_tem_permissao($perfilNecessario) {
    return isset($_SESSION['admin_perfil']) && $_SESSION['admin_perfil'] === $perfilNecessario;
}

// Gera um código de cupom aleatório
function gerar_codigo_cupom($tamanho = 8) {
    $caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $codigo = '';
    for ($i = 0; $i < $tamanho; $i++) {
        $codigo .= $caracteres[rand(0, strlen($caracteres) - 1)];
    }
    return $codigo;
}

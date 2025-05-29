<?php

$senha = 'admin123';
$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

echo $senha_hash

?>
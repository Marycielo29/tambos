<?php
require_once 'config.php';

$cliente = obtenerCliente();

if ($cliente) {
    echo json_encode([
        'logged_in' => true,
        'cliente' => $cliente
    ]);
} else {
    echo json_encode([
        'logged_in' => false,
        'message' => 'No hay sesión activa'
    ]);
}
?>
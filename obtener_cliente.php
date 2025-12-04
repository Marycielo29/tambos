<?php
require_once 'config.php';

verificarLogin();

$cliente_id = $_SESSION['cliente_id'];

try {
    $conn = conectarDB();
    $stmt = $conn->prepare("SELECT id, nombre_completo, email, telefono, direccion, 
                           DATE_FORMAT(fecha_registro, '%d/%m/%Y %H:%i') as fecha_registro 
                           FROM clientes WHERE id = ?");
    $stmt->execute([$cliente_id]);
    $cliente = $stmt->fetch();
    
    if ($cliente) {
        echo json_encode([
            'success' => true,
            'cliente' => $cliente
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Cliente no encontrado'
        ]);
    }
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
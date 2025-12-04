<?php
require_once 'config.php';

// Verificar que el usuario esté logueado
verificarLogin();

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente = obtenerCliente();
    
    // Obtener datos del pedido
    $productos_json = $_POST['productos'] ?? '[]';
    $total = floatval($_POST['total'] ?? 0);
    $metodo_pago = $_POST['metodo_pago'] ?? 'efectivo';
    
    if ($total <= 0) {
        $response['message'] = 'El carrito está vacío';
        echo json_encode($response);
        exit();
    }
    
    try {
        $conn = conectarDB();
        
        // Insertar pedido
        $stmt = $conn->prepare("INSERT INTO pedidos (cliente_id, productos, total, metodo_pago) 
                               VALUES (?, ?, ?, ?)");
        $stmt->execute([$cliente['id'], $productos_json, $total, $metodo_pago]);
        
        $pedido_id = $conn->lastInsertId();
        
        $response['success'] = true;
        $response['message'] = 'Pedido registrado exitosamente';
        $response['pedido_id'] = $pedido_id;
        
    } catch(PDOException $e) {
        $response['message'] = 'Error al procesar el pedido: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Método no permitido';
}

header('Content-Type: application/json');
echo json_encode($response);
?>
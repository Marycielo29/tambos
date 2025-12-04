<?php
require_once 'config.php';

verificarLogin();

$cliente_id = $_SESSION['cliente_id'];

try {
    $conn = conectarDB();
    
    // Obtener pedidos del cliente
    $stmt = $conn->prepare("SELECT id, productos, total, metodo_pago, estado, 
                           DATE_FORMAT(fecha_pedido, '%d/%m/%Y %H:%i') as fecha_pedido_formateada,
                           fecha_pedido 
                           FROM pedidos 
                           WHERE cliente_id = ? 
                           ORDER BY fecha_pedido DESC");
    $stmt->execute([$cliente_id]);
    $pedidos = $stmt->fetchAll();
    
    // Procesar cada pedido para agregar detalles de productos
    foreach ($pedidos as &$pedido) {
        $productos = json_decode($pedido['productos'], true);
        $pedido['productos_detalle'] = $productos;
        
        // Calcular cantidad total de productos
        $total_productos = 0;
        if (is_array($productos)) {
            foreach ($productos as $producto) {
                $total_productos += $producto['cantidad'] ?? 1;
            }
        }
        $pedido['total_productos'] = $total_productos;
    }
    
    echo json_encode([
        'success' => true,
        'pedidos' => $pedidos
    ]);
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
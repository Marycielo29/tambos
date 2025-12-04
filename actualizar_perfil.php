<?php
require_once 'config.php';

verificarLogin();

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $cliente_id = $_SESSION['cliente_id'];
    
    if (empty($nombre)) {
        $response['message'] = 'El nombre es requerido';
        echo json_encode($response);
        exit();
    }
    
    try {
        $conn = conectarDB();
        $stmt = $conn->prepare("UPDATE clientes SET nombre_completo = ?, telefono = ? WHERE id = ?");
        $stmt->execute([$nombre, $telefono, $cliente_id]);
        
        // Actualizar sesión
        $_SESSION['cliente_nombre'] = $nombre;
        
        $response['success'] = true;
        $response['message'] = 'Perfil actualizado correctamente';
        
    } catch(PDOException $e) {
        $response['message'] = 'Error: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Método no permitido';
}

header('Content-Type: application/json');
echo json_encode($response);
?>
<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    mostrarError('Método no permitido');
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    mostrarError('Email y contraseña son requeridos');
}

try {
    $conn = conectarDB();
    
    // Buscar cliente
    $stmt = $conn->prepare("SELECT id, nombre_completo, email, password FROM clientes 
                           WHERE email = ? AND activo = 1");
    $stmt->execute([$email]);
    $cliente = $stmt->fetch();
    
    if (!$cliente) {
        mostrarError('Email no registrado');
    }
    
    // Verificar contraseña
    if (!password_verify($password, $cliente['password'])) {
        mostrarError('Contraseña incorrecta');
    }
    
    // Iniciar sesión
    $_SESSION['cliente_id'] = $cliente['id'];
    $_SESSION['cliente_nombre'] = $cliente['nombre_completo'];
    $_SESSION['cliente_email'] = $cliente['email'];
    
    mostrarExito('Inicio de sesión exitoso', [
        'cliente' => [
            'id' => $cliente['id'],
            'nombre' => $cliente['nombre_completo'],
            'email' => $cliente['email']
        ]
    ]);
    
} catch(PDOException $e) {
    mostrarError('Error en el servidor: ' . $e->getMessage());
}
?>
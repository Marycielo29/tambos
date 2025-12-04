<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    mostrarError('Método no permitido');
}

// Obtener y limpiar datos
$nombre = trim($_POST['nombre'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$telefono = trim($_POST['telefono'] ?? '');
$direccion = trim($_POST['direccion'] ?? '');

// Validaciones
if (empty($nombre) || empty($email) || empty($password)) {
    mostrarError('Nombre, email y contraseña son requeridos');
}

if ($password !== $confirm_password) {
    mostrarError('Las contraseñas no coinciden');
}

if (strlen($password) < 6) {
    mostrarError('La contraseña debe tener al menos 6 caracteres');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    mostrarError('Email no válido');
}

try {
    $conn = conectarDB();
    
    // Verificar si email ya existe
    $stmt = $conn->prepare("SELECT id FROM clientes WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->fetch()) {
        mostrarError('Este email ya está registrado');
    }
    
    // Hashear contraseña
    $hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Insertar cliente
    $stmt = $conn->prepare("INSERT INTO clientes (nombre_completo, email, password, telefono, direccion) 
                           VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$nombre, $email, $hash, $telefono, $direccion]);
    
    // Obtener ID del nuevo cliente
    $cliente_id = $conn->lastInsertId();
    
    // Iniciar sesión automáticamente
    $_SESSION['cliente_id'] = $cliente_id;
    $_SESSION['cliente_nombre'] = $nombre;
    $_SESSION['cliente_email'] = $email;
    
    mostrarExito('Registro exitoso. Bienvenido ' . $nombre, [
        'cliente' => [
            'id' => $cliente_id,
            'nombre' => $nombre,
            'email' => $email
        ]
    ]);
    
} catch(PDOException $e) {
    mostrarError('Error en el servidor: ' . $e->getMessage());
}
?>
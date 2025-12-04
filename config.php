<?php
session_start();

// Configuración de la base de datos
const BD_HOST = 'localhost';
const BD_NAME = 'tambos_db';
const BD_USER = 'root';
const BD_PASSWORD = ''; // Cambia si es diferente
const BD_CHARSET = 'utf8mb4';

// Conexión a la base de datos
function conectarDB() {
    try {
        $dsn = "mysql:host=" . BD_HOST . ";dbname=" . BD_NAME . ";charset=" . BD_CHARSET;
        $opciones = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ];
        return new PDO($dsn, BD_USER, BD_PASSWORD, $opciones);
    } catch(PDOException $e) {
        die("Error de conexión: " . $e->getMessage());
    }
}

// Verificar si el usuario está logueado
function verificarLogin() {
    if (!isset($_SESSION['cliente_id'])) {
        $url_actual = urlencode($_SERVER['REQUEST_URI']);
        header('Location: login.html?redirect=' . $url_actual);
        exit();
    }
    return true;
}

// Obtener datos del cliente logueado
function obtenerCliente() {
    if (isset($_SESSION['cliente_id'])) {
        return [
            'id' => $_SESSION['cliente_id'],
            'nombre' => $_SESSION['cliente_nombre'],
            'email' => $_SESSION['cliente_email']
        ];
    }
    return null;
}

// Mostrar error JSON
function mostrarError($mensaje) {
    echo json_encode(['success' => false, 'message' => $mensaje]);
    exit();
}

// Mostrar éxito JSON
function mostrarExito($mensaje, $datos = []) {
    echo json_encode(array_merge(['success' => true, 'message' => $mensaje], $datos));
    exit();
}
?>
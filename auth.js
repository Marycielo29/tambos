// auth.js - Sistema de autenticación para TODAS las páginas

// ============================================
// FUNCIONES GLOBALES DE AUTENTICACIÓN
// ============================================

// Verificar si hay usuario logueado
function estaLogueado() {
    return localStorage.getItem('cliente_tambos') !== null;
}

// Obtener datos del cliente actual
function obtenerClienteActual() {
    const cliente = localStorage.getItem('cliente_tambos');
    return cliente ? JSON.parse(cliente) : null;
}

// Cerrar sesión
function cerrarSesion() {
    if (confirm('¿Estás seguro de que deseas cerrar sesión?')) {
        localStorage.removeItem('cliente_tambos');
        
        // También cerrar sesión en el servidor (opcional)
        fetch('logout.php').then(() => {
            window.location.href = 'index.html';
        });
    }
}

// Verificar autenticación antes de acceder al carrito
function verificarAutenticacion() {
    if (!estaLogueado()) {
        // Mostrar mensaje y redirigir
        alert('Debes iniciar sesión para acceder al carrito de compras');
        window.location.href = 'login.html?redirect=carrito';
        return false;
    }
    return true;
}

// Actualizar el navbar con estado del usuario
function actualizarNavbar() {
    const cliente = obtenerClienteActual();
    const navbar = `
        <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
            <div class="container">
                <a class="navbar-brand" href="index.html">
                    <i class="fas fa-wine-bottle"></i> TAMBOS
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto align-items-center">
                        <li class="nav-item"><a class="nav-link" href="index.html">Inicio</a></li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="categoriasDropdown" role="button" data-bs-toggle="dropdown">
                                Categorías
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="categoriasDropdown">
                                <li><a class="dropdown-item" href="ron.html">Ron</a></li>
                                <li><a class="dropdown-item" href="pisco.html">Pisco</a></li>
                                <li><a class="dropdown-item" href="whisky.html">Whisky</a></li>
                            </ul>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="promociones.html">Promociones</a></li>
                        <li class="nav-item"><a class="nav-link" href="combos.html">Combos</a></li>
                        <li class="nav-item"><a class="nav-link" href="blog.html">Blog</a></li>
                        <li class="nav-item"><a class="nav-link" href="contacto.html">Contacto</a></li>
                        
                        <!-- Estado del Usuario -->
                        ${cliente ? `
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-user-check"></i> ${cliente.nombre.split(' ')[0]}
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">
                                        <i class="fas fa-user-circle"></i> Mi Cuenta
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" onclick="cerrarSesion()">
                                        <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                                    </a></li>
                                </ul>
                            </li>
                        ` : `
                            <li class="nav-item">
                                <a class="nav-link" href="login.html">
                                    <i class="fas fa-user"></i> Iniciar Sesión
                                </a>
                            </li>
                        `}
                        
                        <!-- Carrito -->
                        <li class="nav-item ms-3">
                            <div class="cart-icon">
                                <a class="nav-link" href="carrito.html" onclick="return verificarAutenticacion()">
                                    🛒 <span id="contadorCarrito" class="badge bg-danger">0</span>
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    `;
    
    // Insertar navbar al inicio del body
    document.body.insertAdjacentHTML('afterbegin', navbar);
}

// Función para agregar productos al carrito (verifica login primero)
function agregarAlCarritoSeguro(producto) {
    if (!estaLogueado()) {
        const respuesta = confirm('Debes iniciar sesión para agregar productos al carrito.\n\n¿Deseas ir al inicio de sesión?');
        if (respuesta) {
            // Guardar producto para después del login
            sessionStorage.setItem('producto_pendiente', JSON.stringify(producto));
            window.location.href = 'login.html';
        }
        return false;
    }
    
    // Si está logueado, agregar al carrito normalmente
    agregarAlCarrito(producto);
    return true;
}

// ============================================
// INICIALIZACIÓN AL CARGAR LA PÁGINA
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    // Actualizar navbar en todas las páginas
    actualizarNavbar();
    
    // Verificar si hay producto pendiente después del login
    const productoPendiente = sessionStorage.getItem('producto_pendiente');
    if (productoPendiente && estaLogueado()) {
        const producto = JSON.parse(productoPendiente);
        sessionStorage.removeItem('producto_pendiente');
        
        // Esperar y agregar el producto
        setTimeout(() => {
            agregarAlCarrito(producto);
            alert(`✅ ${producto.nombre} agregado al carrito`);
        }, 1000);
    }
    
    // En la página del carrito, verificar autenticación
    if (window.location.pathname.includes('carrito.html')) {
        verificarAutenticacion();
    }
});
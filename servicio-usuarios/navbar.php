<?php
$rol = $_SESSION['usuario_rol'] ?? 'Cajero';
$nombre = $_SESSION['usuario_nombre'] ?? 'Usuario';
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-danger shadow-sm mb-4">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold fs-4" href="../servicio-ventas/ventas.php">
            <i class="fa-solid fa-pizza-slice me-2"></i>Pizzería La Napo
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                
                <!-- 1. Módulo Ventas / Caja (Todos tienen acceso) -->
                <li class="nav-item">
                    <a class="nav-link text-white fw-bold" href="../servicio-ventas/ventas.php">
                        <i class="fa-solid fa-cash-register me-1"></i> Ventas / Caja
                    </a>
                </li>

                <!-- 2. Módulo Inventario (Admin, Cajero) -->
                <li class="nav-item">
                    <a class="nav-link text-white fw-bold" href="../servicio-inventario/inventario.php">
                        <i class="fa-solid fa-boxes-packing me-1"></i> Inventario
                    </a>
                </li>

                <!-- 2b. Alertas de Inventario (Todos tienen acceso) -->
                <li class="nav-item">
                    <a class="nav-link text-white fw-bold" href="../servicio-inventario/alertas.php">
                        <i class="fa-solid fa-bell-concierge me-1"></i> Alertas
                    </a>
                </li>

                <!-- 3. Módulo Productos (Solo Admin) -->
                <?php if ($rol === 'Admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link text-white fw-bold" href="../servicio-productos/productos.php">
                            <i class="fa-solid fa-list me-1"></i> Gestión de Productos
                        </a>
                    </li>
                <?php endif; ?>

                <!-- 3b. Promociones (Todos tienen acceso) -->
                <li class="nav-item">
                    <a class="nav-link text-white fw-bold" href="../servicio-productos/promociones.php">
                        <i class="fa-solid fa-tag me-1"></i> Promociones
                    </a>
                </li>

                <!-- 4. Módulo Usuarios / Empleados (Solo Admin) -->
                <?php if ($rol === 'Admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link text-white fw-bold" href="../servicio-usuarios/usuarios.php">
                            <i class="fa-solid fa-users me-1"></i> Gestión de Usuarios
                        </a>
                    </li>
                <?php endif; ?>

            </ul>

            <!-- Info Usuario y Logout -->
            <div class="d-flex align-items-center text-white">
                <span class="me-3 badge bg-light text-dark fw-bold px-3 py-2">
                    <i class="fa-solid fa-user-gear me-1 text-danger"></i> <?php echo htmlspecialchars($nombre); ?> (<?php echo htmlspecialchars($rol); ?>)
                </span>
                <a href="../servicio-usuarios/logout.php" class="btn btn-outline-light btn-sm fw-bold">
                    <i class="fa-solid fa-right-from-bracket me-1"></i> Salir
                </a>
            </div>
        </div>
    </div>
</nav>
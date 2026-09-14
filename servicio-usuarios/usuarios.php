<?php
session_start();
require_once 'conexion.php';

// Validar inicio de sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// RESTROCCIÓN DE ROL: Solo Administrador
if ($_SESSION['usuario_rol'] !== 'Admin') {
    header("Location: ../servicio-ventas/ventas.php");
    exit;
}

$mensaje = '';
$tipo_alerta = '';

// Registrar nuevo usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrar'])) {
    $nombre = trim($_POST['nombre']);
    $usuario = trim($_POST['usuario']);
    $password = password_hash(trim($_POST['password']), PASSWORD_BCRYPT);
    $rol = $_POST['rol'];

    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, usuario, password, rol) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nombre, $usuario, $password, $rol);

    if ($stmt->execute()) {
        $mensaje = "Usuario registrado correctamente.";
        $tipo_alerta = "success";
    } else {
        $mensaje = "Error al registrar el usuario (puede que el usuario ya exista).";
        $tipo_alerta = "danger";
    }
}

// Consultar usuarios
$resultado = $conn->query("SELECT id, nombre, usuario, rol, fecha_creacion FROM usuarios ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Pizzería La Napo</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar-custom {
            background-color: #d90429;
        }
        .badge-admin { background-color: #7209b7; }
        .badge-cajero { background-color: #3a86ff; }
        .badge-repartidor { background-color: #ff006e; }
        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>

<!-- Navbar Superior -->
<?php include 'navbar.php'; ?>

<div class="container mb-5">
    
    <?php if ($mensaje): ?>
        <div class="alert alert-<?php echo $tipo_alerta; ?> alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($mensaje); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Columna Formulario -->
        <div class="col-lg-4">
            <div class="card card-custom p-3">
                <div class="card-body">
                    <h5 class="card-title fw-bold text-dark mb-3">
                        <i class="fa-solid fa-user-plus text-danger me-2"></i>Nuevo Empleado
                    </h5>
                    <form method="POST" action="">
                        <input type="hidden" name="registrar" value="1">
                        
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Nombre Completo</label>
                            <input type="text" name="nombre" class="form-control" placeholder="Ej. Juan Pérez" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Nombre de Usuario</label>
                            <input type="text" name="usuario" class="form-control" placeholder="Ej. jperez" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Contraseña</label>
                            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label font-weight-bold">Rol de Usuario</label>
                            <select name="rol" class="form-select" required>
                                <option value="Cajero" selected>Cajero</option>
                                <option value="Admin">Administrador</option>
                                <option value="Repartidor">Repartidor</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-danger w-100 fw-bold">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Guardar Usuario
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Columna Tabla -->
        <div class="col-lg-8">
            <div class="card card-custom p-3">
                <div class="card-body">
                    <h5 class="card-title fw-bold text-dark mb-3">
                        <i class="fa-solid fa-users text-danger me-2"></i>Personal Registrado
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Usuario</th>
                                    <th>Rol</th>
                                    <th>Fecha Registro</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($user = $resultado->fetch_assoc()): ?>
                                    <tr>
                                        <td><strong>#<?php echo $user['id']; ?></strong></td>
                                        <td><?php echo htmlspecialchars($user['nombre']); ?></td>
                                        <td><code><?php echo htmlspecialchars($user['usuario']); ?></code></td>
                                        <td>
                                            <?php 
                                                $badge_class = 'badge-cajero';
                                                if ($user['rol'] === 'Admin') $badge_class = 'badge-admin';
                                                if ($user['rol'] === 'Repartidor') $badge_class = 'badge-repartidor';
                                            ?>
                                            <span class="badge <?php echo $badge_class; ?> px-2 py-1">
                                                <?php echo htmlspecialchars($user['rol']); ?>
                                            </span>
                                        </td>
                                        <td><small class="text-muted"><?php echo $user['fecha_creacion']; ?></small></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
session_start();
require_once '../servicio-usuarios/conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../servicio-usuarios/login.php");
    exit;
}

// RESTRICCIÓN: Solo Admin puede gestionar/modificar productos
if ($_SESSION['usuario_rol'] !== 'Admin') {
    header("Location: ../servicio-ventas/ventas.php");
    exit;
}

$mensaje = '';
$tipo_alerta = '';

// Registrar nuevo producto o promo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_producto'])) {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $precio = floatval($_POST['precio']);
    $categoria = $_POST['categoria'];

    $stmt = $conn->prepare("INSERT INTO productos (nombre, descripcion, precio, categoria) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssds", $nombre, $descripcion, $precio, $categoria);

    if ($stmt->execute()) {
        $mensaje = "Producto registrado correctamente.";
        $tipo_alerta = "success";
    } else {
        $mensaje = "Error al registrar producto.";
        $tipo_alerta = "danger";
    }
}

// Inactivar / Activar producto
if (isset($_GET['cambiar_estado'])) {
    $id_prod = intval($_GET['cambiar_estado']);
    $nuevo_estado = $_GET['estado'] === 'Activo' ? 'Inactivo' : 'Activo';

    $stmt = $conn->prepare("UPDATE productos SET estado = ? WHERE id = ?");
    $stmt->bind_param("si", $nuevo_estado, $id_prod);
    $stmt->execute();
    header("Location: productos.php");
    exit;
}

// Consultar productos
$resultado = $conn->query("SELECT * FROM productos ORDER BY categoria ASC, id DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Productos - La Napo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .navbar-custom { background-color: #d90429; }
        .card-custom { border: none; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<?php include '../servicio-usuarios/navbar.php'; ?>

<div class="container mb-5">
    <?php if ($mensaje): ?>
        <div class="alert alert-<?php echo $tipo_alerta; ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($mensaje); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Formulario -->
        <div class="col-lg-4">
            <div class="card card-custom p-3">
                <div class="card-body">
                    <h5 class="card-title fw-bold text-dark mb-3">
                        <i class="fa-solid fa-plus text-danger me-2"></i>Nuevo Producto / Promo
                    </h5>
                    <form method="POST" action="">
                        <input type="hidden" name="guardar_producto" value="1">

                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Nombre</label>
                            <input type="text" name="nombre" class="form-control" placeholder="Ej. Pizza Pepperoni 12&quot;" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Descripción / Ingredientes</label>
                            <textarea name="descripcion" class="form-control" rows="2" placeholder="Ingredientes o detalles..." required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Precio ($)</label>
                            <input type="number" step="0.01" name="precio" class="form-control" placeholder="229.00" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label font-weight-bold">Categoría</label>
                            <select name="categoria" class="form-select">
                                <option value="Pizza">Pizza 12"</option>
                                <option value="Promocion">Promoción</option>
                                <option value="Bebida">Bebida</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-danger w-100 fw-bold">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Guardar Producto
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabla de Productos -->
        <div class="col-lg-8">
            <div class="card card-custom p-3">
                <div class="card-body">
                    <h5 class="card-title fw-bold text-dark mb-3">
                        <i class="fa-solid fa-list text-danger me-2"></i>Catálogo de Menú y Promociones
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Producto</th>
                                    <th>Categoría</th>
                                    <th>Precio</th>
                                    <th>Estado</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($prod = $resultado->fetch_assoc()): ?>
                                    <tr>
                                        <td><strong>#<?php echo $prod['id']; ?></strong></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($prod['nombre']); ?></strong><br>
                                            <small class="text-muted"><?php echo htmlspecialchars($prod['descripcion']); ?></small>
                                        </td>
                                        <td><span class="badge bg-secondary"><?php echo $prod['categoria']; ?></span></td>
                                        <td><strong class="text-success">$<?php echo number_format($prod['precio'], 2); ?></strong></td>
                                        <td>
                                            <span class="badge <?php echo $prod['estado'] === 'Activo' ? 'bg-success' : 'bg-danger'; ?>">
                                                <?php echo $prod['estado']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="productos.php?cambiar_estado=<?php echo $prod['id']; ?>&estado=<?php echo $prod['estado']; ?>" 
                                               class="btn btn-sm <?php echo $prod['estado'] === 'Activo' ? 'btn-outline-danger' : 'btn-outline-success'; ?>">
                                                <?php echo $prod['estado'] === 'Activo' ? 'Inactivar' : 'Activar'; ?>
                                            </a>
                                        </td>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
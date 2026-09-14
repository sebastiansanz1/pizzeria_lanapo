<?php
session_start();
require_once '../servicio-usuarios/conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../servicio-usuarios/login.php");
    exit;
}

// Consultar únicamente los insumos que están por debajo del mínimo
$alertas = $conn->query("SELECT * FROM inventario WHERE cantidad <= stock_minimo ORDER BY cantidad ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alertas de Inventario - La Napo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .navbar-custom { background-color: #d90429; }
    </style>
</head>
<body>

<?php include '../servicio-usuarios/navbar.php'; ?>

<div class="container">
    <div class="mb-3">
        <a href="inventario.php" class="btn btn-outline-danger btn-sm">
            <i class="fa-solid fa-arrow-left me-2"></i>Volver a Inventario
        </a>
    </div>

    <div class="text-center mb-4">
        <h2 class="fw-bold text-danger">
            <i class="fa-solid fa-bell-concierge me-2"></i>Alertas Automáticas de Bajo Inventario
        </h2>
        <p class="text-muted">Lista de materia prima que requiere reabastecimiento urgente por proveedor.</p>
    </div>

    <?php if ($alertas->num_rows > 0): ?>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="alert alert-danger shadow-sm border-danger" role="alert">
                    <h5 class="alert-heading fw-bold"><i class="fa-solid fa-circle-exclamation me-2"></i>Atención Inmediata Requerida</h5>
                    <p class="mb-0">Los siguientes insumos alcanzaron o rebasaron el umbral mínimo permitido:</p>
                </div>

                <div class="list-group shadow-sm">
                    <?php while ($item = $alertas->fetch_assoc()): ?>
                        <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3">
                            <div>
                                <h5 class="mb-1 fw-bold text-dark"><?php echo htmlspecialchars($item['insumo']); ?></h5>
                                <small class="text-danger fw-bold">
                                    Stock Actual: <?php echo $item['cantidad'] . " " . $item['unidad_medida']; ?>
                                </small>
                            </div>
                            <span class="badge bg-danger rounded-pill px-3 py-2">
                                Mínimo: <?php echo $item['stock_minimo'] . " " . $item['unidad_medida']; ?>
                            </span>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="row justify-content-center">
            <div class="col-md-6 text-center py-5">
                <i class="fa-solid fa-circle-check fa-4x text-success mb-3"></i>
                <h4 class="fw-bold text-secondary">¡Todo en orden!</h4>
                <p class="text-muted">Todos los insumos cuentan con existencias suficientes por encima del límite mínimo.</p>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
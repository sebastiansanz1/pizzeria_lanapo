<?php
session_start();
require_once '../servicio-usuarios/conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../servicio-usuarios/login.php");
    exit;
}

$promos = $conn->query("SELECT * FROM productos WHERE categoria = 'Promocion' AND estado = 'Activo'");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Promociones Vigentes - La Napo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
        .navbar-custom { background-color: #d90429; }
        .card-promo { border: 2px dashed #d90429; border-radius: 15px; background: #fff; }
    </style>
</head>
<body>

<?php include '../servicio-usuarios/navbar.php'; ?>

<div class="container">
    <div class="mb-3">
        <a href="productos.php" class="btn btn-outline-danger btn-sm">
            <i class="fa-solid fa-arrow-left me-2"></i>Volver a Productos
        </a>
    </div>

    <h2 class="fw-bold text-center text-danger mb-4">
        <i class="fa-solid fa-fire me-2"></i>Promociones Vigentes de Pizzería La Napo
    </h2>

    <div class="row g-4">
        <?php while ($p = $promos->fetch_assoc()): ?>
            <div class="col-md-4">
                <div class="card card-promo p-4 text-center shadow-sm h-100">
                    <i class="fa-solid fa-pizza-slice fa-3x text-danger mb-3"></i>
                    <h4 class="fw-bold"><?php echo htmlspecialchars($p['nombre']); ?></h4>
                    <p class="text-muted"><?php echo htmlspecialchars($p['descripcion']); ?></p>
                    <h3 class="text-success fw-bold">$<?php echo number_format($p['precio'], 2); ?></h3>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

</body>
</html>
<?php
session_start();
require_once '../servicio-usuarios/conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../servicio-usuarios/login.php");
    exit;
}

$mensaje = '';
$tipo_alerta = '';

// Registrar Movimiento de Inventario (Entrada o Salida)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['movimiento'])) {
    $id_insumo = intval($_POST['insumo_id']);
    $tipo = $_POST['tipo_movimiento'];
    $cantidad = floatval($_POST['cantidad']);

    if ($cantidad > 0) {
        if ($tipo === 'Entrada') {
            $stmt = $conn->prepare("UPDATE inventario SET cantidad = cantidad + ? WHERE id = ?");
        } else {
            $stmt = $conn->prepare("UPDATE inventario SET cantidad = GREATEST(0, cantidad - ?) WHERE id = ?");
        }
        $stmt->bind_param("di", $cantidad, $id_insumo);

        if ($stmt->execute()) {
            $mensaje = "Movimiento de $tipo registrado exitosamente.";
            $tipo_alerta = "success";
        } else {
            $mensaje = "Error al actualizar el inventario.";
            $tipo_alerta = "danger";
        }
    } else {
        $mensaje = "La cantidad debe ser mayor a cero.";
        $tipo_alerta = "warning";
    }
}

// Consultar todo el inventario
$inventario = $conn->query("SELECT * FROM inventario ORDER BY insumo ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Inventario - La Napo</title>
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
        <!-- Formulario Entradas / Salidas -->
        <div class="col-lg-4">
            <div class="card card-custom p-3">
                <div class="card-body">
                    <h5 class="card-title fw-bold text-dark mb-3">
                        <i class="fa-solid fa-boxes-packing text-danger me-2"></i>Registrar Entrada / Salida
                    </h5>
                    <form method="POST" action="">
                        <input type="hidden" name="movimiento" value="1">

                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Insumo</label>
                            <select name="insumo_id" class="form-select" required>
                                <?php 
                                $insumos_combo = $conn->query("SELECT id, insumo, unidad_medida FROM inventario ORDER BY insumo ASC");
                                while($i = $insumos_combo->fetch_assoc()): 
                                ?>
                                    <option value="<?php echo $i['id']; ?>">
                                        <?php echo htmlspecialchars($i['insumo']) . " (" . $i['unidad_medida'] . ")"; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Tipo de Operación</label>
                            <select name="tipo_movimiento" class="form-select" required>
                                <option value="Entrada">Entrada (Compra a Proveedor)</option>
                                <option value="Salida">Salida (Uso / Merma)</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label font-weight-bold">Cantidad</label>
                            <input type="number" step="0.01" name="cantidad" class="form-control" placeholder="Ej. 10.5" required>
                        </div>

                        <button type="submit" class="btn btn-danger w-100 fw-bold">
                            <i class="fa-solid fa-arrows-rotate me-1"></i> Actualizar Stock
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabla Stock Actual -->
        <div class="col-lg-8">
            <div class="card card-custom p-3">
                <div class="card-body">
                    <h5 class="card-title fw-bold text-dark mb-3">
                        <i class="fa-solid fa-warehouse text-danger me-2"></i>Existencias de Materia Prima
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Insumo</th>
                                    <th>Stock Actual</th>
                                    <th>Mínimo Requerido</th>
                                    <th>Estado</th>
                                    <th>Última Act.</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($item = $inventario->fetch_assoc()): ?>
                                    <?php $bajo_stock = $item['cantidad'] <= $item['stock_minimo']; ?>
                                    <tr class="<?php echo $bajo_stock ? 'table-danger' : ''; ?>">
                                        <td><strong><?php echo htmlspecialchars($item['insumo']); ?></strong></td>
                                        <td>
                                            <span class="fs-6 fw-bold <?php echo $bajo_stock ? 'text-danger' : 'text-dark'; ?>">
                                                <?php echo $item['cantidad'] . " " . $item['unidad_medida']; ?>
                                            </span>
                                        </td>
                                        <td><small class="text-muted"><?php echo $item['stock_minimo'] . " " . $item['unidad_medida']; ?></small></td>
                                        <td>
                                            <?php if ($bajo_stock): ?>
                                                <span class="badge bg-danger"><i class="fa-solid fa-triangle-exclamation me-1"></i>Bajo Stock</span>
                                            <?php else: ?>
                                                <span class="badge bg-success"><i class="fa-solid fa-circle-check me-1"></i>Suficiente</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><small class="text-muted"><?php echo $item['ultima_actualizacion']; ?></small></td>
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
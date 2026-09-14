<?php
session_start();
require_once '../servicio-usuarios/conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../servicio-usuarios/login.php");
    exit;
}

$mensaje = '';
$tipo_alerta = '';

// 1. Registrar una nueva venta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrar_venta'])) {
    $tipo_pedido = $_POST['tipo_pedido'];
    $productos_seleccionados = $_POST['productos'] ?? [];
    $cantidades = $_POST['cantidades'] ?? [];
    $usuario_id = $_SESSION['usuario_id'];

    if (!empty($productos_seleccionados)) {
        $conn->begin_transaction();
        try {
            // Insertar encabezado de la venta
            $stmt_venta = $conn->prepare("INSERT INTO ventas (usuario_id, tipo_pedido, total, estatus) VALUES (?, ?, 0, 'En Horno')");
            $stmt_venta->bind_param("is", $usuario_id, $tipo_pedido);
            $stmt_venta->execute();
            $venta_id = $conn->insert_id;

            $total_general = 0;

            // Insertar detalles y calcular total
            $stmt_detalle = $conn->prepare("INSERT INTO detalle_ventas (venta_id, producto_id, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)");

            foreach ($productos_seleccionados as $prod_id) {
                $cant = intval($cantidades[$prod_id] ?? 1);
                if ($cant > 0) {
                    $res_p = $conn->query("SELECT precio FROM productos WHERE id = $prod_id");
                    $p_data = $res_p->fetch_assoc();
                    $precio_u = floatval($p_data['precio']);
                    $subtotal = $precio_u * $cant;
                    $total_general += $subtotal;

                    $stmt_detalle->bind_param("iiidd", $venta_id, $prod_id, $cant, $precio_u, $subtotal);
                    $stmt_detalle->execute();
                }
            }

            // Actualizar total final de la venta
            $stmt_update = $conn->prepare("UPDATE ventas SET total = ? WHERE id = ?");
            $stmt_update->bind_param("di", $total_general, $venta_id);
            $stmt_update->execute();

            $conn->commit();
            $mensaje = "Venta #$venta_id registrada con éxito. Total: $$total_general";
            $tipo_alerta = "success";
        } catch (Exception $e) {
            $conn->rollback();
            $mensaje = "Error al procesar la venta: " . $e->getMessage();
            $tipo_alerta = "danger";
        }
    } else {
        $mensaje = "Debes seleccionar al menos un producto.";
        $tipo_alerta = "warning";
    }
}

// 2. Actualizar estatus del pedido
if (isset($_GET['cambiar_estatus'])) {
    $v_id = intval($_GET['cambiar_estatus']);
    $nuevo_estatus = $_GET['nuevo_estatus'];
    $stmt_e = $conn->prepare("UPDATE ventas SET estatus = ? WHERE id = ?");
    $stmt_e->bind_param("si", $nuevo_estatus, $v_id);
    $stmt_e->execute();
    header("Location: ventas.php");
    exit;
}

// Consultar productos activos para el menú de cobro
$productos_menu = $conn->query("SELECT * FROM productos WHERE estado = 'Activo' ORDER BY categoria ASC");

// Consultar historial de ventas
$historial_ventas = $conn->query("SELECT v.*, u.nombre as cajero FROM ventas v JOIN usuarios u ON v.usuario_id = u.id ORDER BY v.id DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Punto de Venta - La Napo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
        .navbar-custom { background-color: #d90429; }
        .card-custom { border: none; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<?php include '../servicio-usuarios/navbar.php'; ?>

<div class="container-fluid px-4 mb-5">
    <?php if ($mensaje): ?>
        <div class="alert alert-<?php echo $tipo_alerta; ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($mensaje); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Columna Registro de Pedido -->
        <div class="col-lg-5">
            <div class="card card-custom p-3">
                <div class="card-body">
                    <h5 class="card-title fw-bold text-dark mb-3">
                        <i class="fa-solid fa-cart-plus text-danger me-2"></i>Registrar Nuevo Pedido
                    </h5>
                    <form method="POST" action="">
                        <input type="hidden" name="registrar_venta" value="1">

                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Tipo de Pedido</label>
                            <select name="tipo_pedido" class="form-select" required>
                                <option value="Mostrador">Mostrador / Para llevar</option>
                                <option value="Domicilio">Entrega a Domicilio</option>
                            </select>
                        </div>

                        <label class="form-label font-weight-bold mb-2">Seleccionar Menú / Promos:</label>
                        <div class="table-responsive style-scroll mb-3" style="max-height: 280px; overflow-y: auto;">
                            <table class="table table-sm table-hover align-middle">
                                <tbody>
                                    <?php while ($p = $productos_menu->fetch_assoc()): ?>
                                        <tr>
                                            <td width="5%">
                                                <input class="form-check-input" type="checkbox" name="productos[]" value="<?php echo $p['id']; ?>">
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($p['nombre']); ?></strong><br>
                                                <small class="text-success">$<?php echo number_format($p['precio'], 2); ?></small>
                                            </td>
                                            <td width="30%">
                                                <input type="number" name="cantidades[<?php echo $p['id']; ?>]" value="1" min="1" class="form-control form-control-sm" placeholder="Cant.">
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>

                        <button type="submit" class="btn btn-danger w-100 fw-bold btn-lg">
                            <i class="fa-solid fa-cash-register me-1"></i> Cobrar y Generar Pedido
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Columna Historial y Control de Estatus -->
        <div class="col-lg-7">
            <div class="card card-custom p-3">
                <div class="card-body">
                    <h5 class="card-title fw-bold text-dark mb-3">
                        <i class="fa-solid fa-clock-rotate-left text-danger me-2"></i>Historial de Pedidos y Estatus
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Folio</th>
                                    <th>Tipo</th>
                                    <th>Total</th>
                                    <th>Estatus Actual</th>
                                    <th>Ticket</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($v = $historial_ventas->fetch_assoc()): ?>
                                    <tr>
                                        <td><strong>#<?php echo $v['id']; ?></strong></td>
                                        <td>
                                            <span class="badge bg-secondary"><?php echo $v['tipo_pedido']; ?></span><br>
                                            <small class="text-muted"><?php echo $v['cajero']; ?></small>
                                        </td>
                                        <td><strong class="text-success">$<?php echo number_format($v['total'], 2); ?></strong></td>
                                        <td>
                                            <div class="dropdown">
                                                <?php 
                                                    $btn_color = 'btn-warning';
                                                    if ($v['estatus'] === 'En Camino') $btn_color = 'btn-info text-white';
                                                    if ($v['estatus'] === 'Entregado') $btn_color = 'btn-success';
                                                ?>
                                                <button class="btn btn-sm <?php echo $btn_color; ?> dropdown-toggle fw-bold" type="button" data-bs-toggle="dropdown">
                                                    <?php echo $v['estatus']; ?>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-menu-item dropdown-item" href="ventas.php?cambiar_estatus=<?php echo $v['id']; ?>&nuevo_estatus=En Horno">En Horno</a></li>
                                                    <li><a class="dropdown-menu-item dropdown-item" href="ventas.php?cambiar_estatus=<?php echo $v['id']; ?>&nuevo_estatus=En Camino">En Camino</a></li>
                                                    <li><a class="dropdown-menu-item dropdown-item" href="ventas.php?cambiar_estatus=<?php echo $v['id']; ?>&nuevo_estatus=Entregado">Entregado</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="ticket.php?id=<?php echo $v['id']; ?>" target="_blank" class="btn btn-sm btn-outline-dark">
                                                <i class="fa-solid fa-print me-1"></i> Imprimir
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
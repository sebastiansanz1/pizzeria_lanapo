<?php
session_start();
require_once '../servicio-usuarios/conexion.php';

if (!isset($_SESSION['usuario_id']) || !isset($_GET['id'])) {
    die("Acceso no autorizado o folio de venta no especificado.");
}

$venta_id = intval($_GET['id']);

// Consultar datos de la venta
$stmt_v = $conn->prepare("SELECT v.*, u.nombre as cajero FROM ventas v JOIN usuarios u ON v.usuario_id = u.id WHERE v.id = ?");
$stmt_v->bind_param("i", $venta_id);
$stmt_v->execute();
$venta = $stmt_v->get_result()->fetch_assoc();

if (!$venta) {
    die("Venta no encontrada.");
}

// Consultar detalles de la venta
$stmt_d = $conn->prepare("SELECT d.*, p.nombre FROM detalle_ventas d JOIN productos p ON d.producto_id = p.id WHERE d.venta_id = ?");
$stmt_d->bind_param("i", $venta_id);
$stmt_d->execute();
$detalles = $stmt_d->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket #<?php echo $venta_id; ?> - La Napo</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            width: 300px;
            margin: 0 auto;
            padding: 15px;
            background-color: #fff;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .linea { border-bottom: 1px dashed #000; margin: 10px 0; }
        table { width: 100%; font-size: 13px; }
        .btn-imprimir {
            margin-top: 15px;
            width: 100%;
            padding: 8px;
            background: #d90429;
            color: #fff;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }
        @media print {
            .btn-imprimir { display: none; }
        }
    </style>
</head>
<body>

<div class="text-center">
    <h3 style="margin: 0;">PIZZERÍA LA NAPO</h3>
    <small>Pizzería Artesanal</small><br>
    <small>Folio Venta: #<?php echo $venta['id']; ?></small>
</div>

<div class="linea"></div>

<div>
    <small>Fecha: <?php echo $venta['fecha_venta']; ?></small><br>
    <small>Tipo: <?php echo $venta['tipo_pedido']; ?></small><br>
    <small>Atendió: <?php echo htmlspecialchars($venta['cajero']); ?></small>
</div>

<div class="linea"></div>

<table>
    <thead>
        <tr>
            <th class="text-left">Cant / Art.</th>
            <th class="text-right">Total</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($d = $detalles->fetch_assoc()): ?>
            <tr>
                <td><?php echo $d['cantidad']; ?>x <?php echo htmlspecialchars($d['nombre']); ?></td>
                <td class="text-right">$<?php echo number_format($d['subtotal'], 2); ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<div class="linea"></div>

<h3 class="text-right" style="margin: 5px 0;">TOTAL: $<?php echo number_format($venta['total'], 2); ?></h3>

<div class="linea"></div>

<div class="text-center">
    <small>¡Gracias por tu compra!</small><br>
    <small>www.pizzerialanapo.com</small>
</div>

<button class="btn-imprimir" onclick="window.print()">Imprimir Ticket</button>

</body>
</html>
<?php
session_start();
require_once 'conexion.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario']);
    $password = trim($_POST['password']);

    if (!empty($usuario) && !empty($password)) {
        $stmt = $conn->prepare("SELECT id, nombre, usuario, password, rol FROM usuarios WHERE usuario = ?");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            $user = $resultado->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                $_SESSION['usuario_id'] = $user['id'];
                $_SESSION['usuario_nombre'] = $user['nombre'];
                $_SESSION['usuario_rol'] = $user['rol']; // <--- Guardamos el rol

                // Redirección por rol
                if ($user['rol'] === 'Admin') {
                    header("Location: usuarios.php");
                } elseif ($user['rol'] === 'Cajero') {
                    header("Location: ../servicio-ventas/ventas.php");
                } else {
                    header("Location: ../servicio-ventas/ventas.php");
                }
                exit;
            } else {
                $error = "Contraseña incorrecta.";
            }
        } else {
            $error = "El usuario no existe.";
        }
    } else {
        $error = "Por favor completa todos los campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Pizzería La Napo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; display: flex; align-items: center; justify-content: center; height: 100vh; font-family: 'Segoe UI', sans-serif; }
        .card-login { border: none; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        .btn-custom { background-color: #d90429; border: none; }
        .btn-custom:hover { background-color: #ef233c; }
    </style>
</head>
<body>

<div class="card card-login p-4">
    <div class="card-body text-center">
        <i class="fa-solid fa-pizza-slice fa-3x text-danger mb-3"></i>
        <h4 class="fw-bold mb-4">Pizzería La Napo</h4>

        <?php if ($error): ?>
            <div class="alert alert-danger p-2 fs-6" role="alert">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3 text-start">
                <label class="form-label fw-bold">Usuario</label>
                <input type="text" name="usuario" class="form-control" placeholder="Ej. admin" required>
            </div>
            <div class="mb-4 text-start">
                <label class="form-label fw-bold">Contraseña</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-custom text-white fw-bold w-100 py-2">Iniciciar Sesión</button>
        </form>
    </div>
</div>

</body>
</html>
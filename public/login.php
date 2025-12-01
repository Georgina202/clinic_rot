<?php
session_start();

// Si ya está logueado, lo mando al dashboard
if (isset($_SESSION['usuario'])) {
    header("Location: dashboard.php");
    exit;
}

// Ver si viene mensaje de error por GET
$mensaje = "";
if (isset($_GET['error']) && $_GET['error'] === '1') {
    $mensaje = "Usuario o contraseña incorrectos.";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="../css/pastel.css">
</head>
<body>

<div class="container">
    <div class="card">
        <h1>Sistema de Rotaciones Clínicas</h1>
        <p class="subtitle">Acceso para coordinación y control de prácticas</p>

        <h2>Iniciar sesión</h2>

        <?php if (!empty($mensaje)): ?>
            <p style="color:#b91c1c; margin-bottom: 1rem;"><?= $mensaje ?></p>
        <?php endif; ?>

        <form action="../login/process_login.php" method="post">
            <label>Usuario</label>
            <input type="text" name="username" required>

            <label>Contraseña</label>
            <input type="password" name="password" required>

            <button type="submit">Entrar</button>
        </form>
    </div>
</div>

</body>
</html>


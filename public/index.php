<?php
session_start();
require_once "../data/db.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Rotaciones Clínicas</title>
</head>
<body>
    <h1>Sistema de Control de Rotaciones Clínicas</h1>
    <p>Conexión a la base de datos establecida ✔</p>

    <?php if (isset($_SESSION['usuario'])): ?>
        <p>Ya has iniciado sesión como <strong><?= htmlspecialchars($_SESSION['usuario']['username']) ?></strong>.</p>
        <p><a href="dashboard.php">Ir al panel principal</a></p>
        <p><a href="logout.php">Cerrar sesión</a></p>
    <?php else: ?>
        <p><a href="login.php">Iniciar sesión</a></p>
    <?php endif; ?>
</body>
</html>

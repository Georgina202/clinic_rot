<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$usuario = $_SESSION['usuario'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel principal - Rotaciones Clínicas</title>
</head>
<body>
    <h1>Bienvenida, <?= htmlspecialchars($usuario['username']) ?></h1>
    <p>Rol: <?= htmlspecialchars($usuario['rol']) ?></p>

    <ul>
        <li><a href="rotaciones_list.php">Gestionar rotaciones clínicas</a> </li>
        <li><a href="logout.php">Cerrar sesión</a></li>
    </ul>
    <link rel="stylesheet" href="../css/pastel.css">

<div class="container">

    <div class="top-bar">
        <h1>Sistema de Rotaciones Clínicas</h1>
        <p class="small">Sesión: <strong><?= $usuario['username'] ?></strong> (<?= $usuario['rol'] ?>) • 
        <a class="link" href="logout.php">Cerrar sesión</a></p>
    </div>

    <div class="card">
        <h2>Bienvenida, <?= $usuario['username'] ?></h2>
        <p class="subtitle">Desde aquí puedes gestionar las rotaciones de Raymeli López y Snoopy de Jesús.</p>

        <a href="rotaciones_list.php" class="btn">Gestionar rotaciones clínicas</a>
    </div>

</div>

</body>
</html>

<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$usuario = $_SESSION['usuario'];

require_once "../data/db.php";

// Traer estudiantes y centros para los selects
$estudiantes = $conn->query("SELECT id, nombre_completo FROM estudiantes ORDER BY nombre_completo");
$centros     = $conn->query("SELECT id, nombre_centro FROM centros_clinicos ORDER BY nombre_centro");

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $estudiante_id = $_POST['estudiante_id'] ?? '';
    $centro_id     = $_POST['centro_id'] ?? '';
    $area          = trim($_POST['area'] ?? '');
    $fecha_inicio  = $_POST['fecha_inicio'] ?? '';
    $fecha_fin     = $_POST['fecha_fin'] ?? '';
    $turno         = $_POST['turno'] ?? '';
    $estado        = $_POST['estado'] ?? 'Pendiente';
    $observaciones = trim($_POST['observaciones'] ?? '');

    if ($estudiante_id === '' || $centro_id === '' || $area === '' || $fecha_inicio === '' || $fecha_fin === '' || $turno === '') {
        $errores[] = "Todos los campos marcados son obligatorios.";
    }

    if ($fecha_inicio !== '' && $fecha_fin !== '' && $fecha_fin < $fecha_inicio) {
        $errores[] = "La fecha de fin no puede ser anterior a la fecha de inicio.";
    }

    if (empty($errores)) {
        $stmt = $conn->prepare("INSERT INTO rotaciones_clinicas
            (estudiante_id, centro_id, area, fecha_inicio, fecha_fin, turno, estado, observaciones)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param(
            "iissssss",
            $estudiante_id,
            $centro_id,
            $area,
            $fecha_inicio,
            $fecha_fin,
            $turno,
            $estado,
            $observaciones
        );

        if ($stmt->execute()) {
            header("Location: rotaciones_list.php");
            exit;
        } else {
            $errores[] = "Error al guardar: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Añadir rotación clínica</title>
    <link rel="stylesheet" href="../css/pastel.css">
</head>
<body>

<div class="container">

    <div class="top-bar">
        <a class="link" href="rotaciones_list.php">← Volver al listado</a>
        <p class="small">
            Sesión: <strong><?= htmlspecialchars($usuario['username']) ?></strong>
            (<?= htmlspecialchars($usuario['rol']) ?>)
        </p>
    </div>

    <div class="card">
        <h2>Añadir rotación</h2>

        <?php if ($errores): ?>
            <ul class="error-list" style="color:#c0392b; margin-bottom:1rem;">
                <?php foreach ($errores as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <!-- Un solo formulario, el pastel, que postea al MISMO archivo -->
        <form method="post" action="">

            <label>Estudiante*</label>
            <select name="estudiante_id" required>
                <option value="">-- Seleccione --</option>
                <?php if ($estudiantes): ?>
                    <?php while ($e = $estudiantes->fetch_assoc()): ?>
                        <option value="<?= $e['id'] ?>">
                            <?= htmlspecialchars($e['nombre_completo']) ?>
                        </option>
                    <?php endwhile; ?>
                <?php endif; ?>
            </select>

            <label>Centro clínico*</label>
            <select name="centro_id" required>
                <option value="">-- Seleccione --</option>
                <?php if ($centros): ?>
                    <?php while ($c = $centros->fetch_assoc()): ?>
                        <option value="<?= $c['id'] ?>">
                            <?= htmlspecialchars($c['nombre_centro']) ?>
                        </option>
                    <?php endwhile; ?>
                <?php endif; ?>
            </select>

            <label>Área / Servicio*</label>
            <input type="text" name="area" required>

            <label>Fecha inicio*</label>
            <input type="date" name="fecha_inicio" required>

            <label>Fecha fin*</label>
            <input type="date" name="fecha_fin" required>

            <label>Turno*</label>
            <select name="turno" required>
                <option value="">-- Seleccione --</option>
                <option value="Mañana">Mañana</option>
                <option value="Tarde">Tarde</option>
                <option value="Noche">Noche</option>
            </select>

            <label>Estado</label>
            <select name="estado">
                <option value="Pendiente">Pendiente</option>
                <option value="En curso">En curso</option>
                <option value="Finalizada">Finalizada</option>
                <option value="Cancelada">Cancelada</option>
            </select>

            <label>Observaciones</label>
            <textarea name="observaciones" rows="3"></textarea>

            <button type="submit" class="btn" style="margin-top:1rem;">
                Guardar rotación
            </button>

        </form>
    </div>

</div>

</body>
</html>

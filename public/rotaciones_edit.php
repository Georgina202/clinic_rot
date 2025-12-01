<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$usuario = $_SESSION['usuario'];

require_once "../data/db.php";

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: rotaciones_list.php");
    exit;
}

// Traer la rotación actual
$stmt = $conn->prepare("SELECT * FROM rotaciones_clinicas WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$rotacion = $result->fetch_assoc();

if (!$rotacion) {
    header("Location: rotaciones_list.php");
    exit;
}

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
        $stmt = $conn->prepare("UPDATE rotaciones_clinicas
            SET estudiante_id = ?, centro_id = ?, area = ?, fecha_inicio = ?, fecha_fin = ?, turno = ?, estado = ?, observaciones = ?
            WHERE id = ?");

        $stmt->bind_param(
            "iissssssi",
            $estudiante_id,
            $centro_id,
            $area,
            $fecha_inicio,
            $fecha_fin,
            $turno,
            $estado,
            $observaciones,
            $id
        );

        if ($stmt->execute()) {
            header("Location: rotaciones_list.php");
            exit;
        } else {
            $errores[] = "Error al guardar los cambios: " . $conn->error;
        }
    }

    // Si hubo errores, actualizamos $rotacion para que el formulario mantenga lo que el usuario intentó guardar
    $rotacion['estudiante_id'] = $estudiante_id;
    $rotacion['centro_id']     = $centro_id;
    $rotacion['area']          = $area;
    $rotacion['fecha_inicio']  = $fecha_inicio;
    $rotacion['fecha_fin']     = $fecha_fin;
    $rotacion['turno']         = $turno;
    $rotacion['estado']        = $estado;
    $rotacion['observaciones'] = $observaciones;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar rotación #<?= htmlspecialchars($id) ?></title>
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
        <h2>Editar rotación #<?= htmlspecialchars($id) ?></h2>

        <?php if ($errores): ?>
            <ul class="error-list" style="color:#c0392b; margin-bottom:1rem;">
                <?php foreach ($errores as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form method="post" action="">
            <label>Estudiante*</label>
            <select name="estudiante_id" required>
                <option value="">-- Seleccione --</option>
                <?php if ($estudiantes): ?>
                    <?php while ($e = $estudiantes->fetch_assoc()): ?>
                        <option value="<?= $e['id'] ?>"
                            <?= ($e['id'] == $rotacion['estudiante_id']) ? 'selected' : '' ?>>
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
                        <option value="<?= $c['id'] ?>"
                            <?= ($c['id'] == $rotacion['centro_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['nombre_centro']) ?>
                        </option>
                    <?php endwhile; ?>
                <?php endif; ?>
            </select>

            <label>Área / Servicio*</label>
            <input type="text" name="area" required
                   value="<?= htmlspecialchars($rotacion['area']) ?>">

            <label>Fecha inicio*</label>
            <input type="date" name="fecha_inicio" required
                   value="<?= htmlspecialchars($rotacion['fecha_inicio']) ?>">

            <label>Fecha fin*</label>
            <input type="date" name="fecha_fin" required
                   value="<?= htmlspecialchars($rotacion['fecha_fin']) ?>">

            <label>Turno*</label>
            <select name="turno" required>
                <option value="">-- Seleccione --</option>
                <option value="Mañana"  <?= $rotacion['turno'] === 'Mañana' ? 'selected' : '' ?>>Mañana</option>
                <option value="Tarde"   <?= $rotacion['turno'] === 'Tarde' ? 'selected' : '' ?>>Tarde</option>
                <option value="Noche"   <?= $rotacion['turno'] === 'Noche' ? 'selected' : '' ?>>Noche</option>
            </select>

            <label>Estado*</label>
            <select name="estado" required>
                <option value="Pendiente"  <?= $rotacion['estado'] === 'Pendiente' ? 'selected' : '' ?>>Pendiente</option>
                <option value="En curso"   <?= $rotacion['estado'] === 'En curso' ? 'selected' : '' ?>>En curso</option>
                <option value="Finalizada" <?= $rotacion['estado'] === 'Finalizada' ? 'selected' : '' ?>>Finalizada</option>
                <option value="Cancelada"  <?= $rotacion['estado'] === 'Cancelada' ? 'selected' : '' ?>>Cancelada</option>
            </select>

            <label>Observaciones</label>
            <textarea name="observaciones" rows="3"><?= htmlspecialchars($rotacion['observaciones'] ?? '') ?></textarea>

            <button type="submit" class="btn" style="margin-top:1rem;">
                Guardar cambios
            </button>
        </form>

    </div>

</div>

</body>
</html>

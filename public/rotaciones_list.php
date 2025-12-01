
<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$usuario = $_SESSION['usuario'];

require_once "../data/db.php";


$sql = "SELECT r.id,
               e.nombre_completo AS estudiante,
               c.nombre_centro   AS centro,
               r.area,
               r.fecha_inicio,
               r.fecha_fin,
               r.turno,
               r.estado
        FROM rotaciones_clinicas r
        JOIN estudiantes e ON r.estudiante_id = e.id
        JOIN centros_clinicos c ON r.centro_id = c.id
        ORDER BY r.fecha_inicio DESC";

$result = $conn->query($sql);


$rotaciones = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $rotaciones[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Rotaciones Clínicas - Listado</title>
    <link rel="stylesheet" href="../css/pastel.css">
</head>
<body>

<div class="container">

    <div class="top-bar">
        <h1>Sistema de Rotaciones Clínicas</h1>
        <p class="small">
            Sesión: <strong><?= htmlspecialchars($usuario['username']) ?></strong>
            (<?= htmlspecialchars($usuario['rol']) ?>)
        </p>
    </div>

    <p><a class="link" href="dashboard.php">← Volver al panel</a></p>
    <p><a class="link" href="rotaciones_create.php">➕ Registrar nueva rotación</a></p>

    <div class="card" style="margin-top: 1.5rem;">
        <h2>Listado de rotaciones</h2>

        <a href="rotaciones_create.php" class="btn" style="margin-bottom:1rem;">
         Añadir nueva rotación</a>


        <?php if (empty($rotaciones)): ?>
            <p class="small">No hay rotaciones registradas.</p>
        <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Estudiante</th>
                        <th>Centro</th>
                        <th>Área</th>
                        <th>Fecha inicio</th>
                        <th>Fecha fin</th>
                        <th>Turno</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($rotaciones as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['estudiante']) ?></td>
                            <td><?= htmlspecialchars($row['centro']) ?></td>
                            <td><?= htmlspecialchars($row['area']) ?></td>
                            <td><?= htmlspecialchars($row['fecha_inicio']) ?></td>
                            <td><?= htmlspecialchars($row['fecha_fin']) ?></td>
                            <td><?= htmlspecialchars($row['turno']) ?></td>
                            <td><?= htmlspecialchars($row['estado']) ?></td>
                            <td>
                                <a href="rotaciones_edit.php?id=<?= $row['id'] ?>">Editar</a> |
                                <a href="rotaciones_delete.php?id=<?= $row['id'] ?>"
                                   onclick="return confirm('¿Seguro que quieres eliminar esta rotación?');">
                                    Eliminar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </div>

</div>

</body>
</html>


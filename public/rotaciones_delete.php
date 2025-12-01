<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

require_once "../data/db.php";

$id = $_GET['id'] ?? '';
if ($id === '' || !ctype_digit($id)) {
    header("Location: rotaciones_list.php");
    exit;
}

// Eliminación directa (ya confirmamos en el enlace con JS)
$stmt = $conn->prepare("DELETE FROM rotaciones_clinicas WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: rotaciones_list.php");
exit;

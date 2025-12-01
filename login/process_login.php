<?php
session_start();
require_once "../data/db.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../public/login.php");
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($username === '' || $password === '') {
    header("Location: ../public/login.php?error=1");
    exit;
}

// Buscamos el usuario en la BD
$stmt = $conn->prepare("SELECT id, username, password_hash, rol FROM usuarios WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    // Usuario no encontrado
    header("Location: ../public/login.php?error=1");
    exit;
}

// Para esta tarea comparamos texto plano (password_hash guarda la clave tal cual)
if ($password !== $user['password_hash']) {
    header("Location: ../public/login.php?error=1");
    exit;
}

// Login correcto: guardamos datos en sesión
$_SESSION['usuario'] = [
    'id'       => $user['id'],
    'username' => $user['username'],
    'rol'      => $user['rol'],
];

header("Location: ../public/dashboard.php");
exit;

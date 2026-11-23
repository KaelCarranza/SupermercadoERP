<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/registarBitacora.php";

if (!isset($_SESSION["userId"])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Método no permitido");
}

// Validar token CSRF
$token = $_POST["csrf_token"] ?? "";

if (
    !is_string($token) ||
    !isset($_SESSION["csrf_token"]) ||
    !hash_equals($_SESSION["csrf_token"], $token)
) {
    http_response_code(403);
    exit("Solicitud no autorizada");
}

// Comprobar administrador activo
$adminId = (int) $_SESSION["userId"];

$sql = "SELECT u.usuario, r.nombre AS rol
        FROM usuarios u
        INNER JOIN roles r ON u.rol_id = r.id
        WHERE u.id = :id
        AND u.estado = 'activo'";

$query = $conexion->prepare($sql);
$query->execute([":id" => $adminId]);

$admin = $query->fetch(PDO::FETCH_ASSOC);

if (
    !$admin ||
    $admin["rol"] !== "Administrador"
) {
    http_response_code(403);
    exit("Acceso denegado");
}
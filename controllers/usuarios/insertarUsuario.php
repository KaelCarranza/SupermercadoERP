<?php
session_start();

require_once __DIR__ . "../../../config/db.php";
require_once __DIR__ . "../../../includes/registarBitacora.php";

if (!isset($_SESSION["userId"])) {
    header("Location: ../../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../../usuarios.php");
    exit();
}

// Usuario que realiza la acción
$adminId = (int) $_SESSION["userId"];

$sqlAdmin = "SELECT u.usuario, r.nombre AS rol
             FROM usuarios u
             INNER JOIN roles r ON u.rol_id = r.id
             WHERE u.id = :id
               AND u.estado = 'activo'";

$queryAdmin = $conexion->prepare($sqlAdmin);
$queryAdmin->execute([":id" => $adminId]);

$admin = $queryAdmin->fetch(PDO::FETCH_ASSOC);

if (!$admin || $admin["rol"] !== "Administrador") {
    http_response_code(403);
    exit("Acceso denegado");
}

$usuario = trim($_POST["nombre"] ?? "");
$contrasena = $_POST["password"] ?? "";
$rol_id = filter_input(
    INPUT_POST,
    "rol_id",
    FILTER_VALIDATE_INT
);

// Validar datos
if (
    $usuario === "" ||
    mb_strlen($usuario) > 50 ||
    strlen($contrasena) < 8 ||
    !$rol_id
) {
    header("Location: ../../usuarios.php?mensaje=datos_invalidos");
    exit();
}

try {

    $conexion->beginTransaction();

    // Verificar que el rol exista
    $sqlRol = "SELECT nombre FROM roles WHERE id = :id";
    $queryRol = $conexion->prepare($sqlRol);
    $queryRol->execute([":id" => $rol_id]);

    $nombreRol = $queryRol->fetchColumn();

    if ($nombreRol === false) {
        throw new Exception("Rol inválido");
    }

    // Generar hash
    $hash = password_hash(
        $contrasena,
        PASSWORD_DEFAULT
    );

    // Insertar usuario
    $sql = "INSERT INTO usuarios
            (usuario, contrasena, rol_id)
            VALUES
            (:usuario, :contrasena, :rol_id)";

    $query = $conexion->prepare($sql);

    $query->execute([
        ":usuario" => $usuario,
        ":contrasena" => $hash,
        ":rol_id" => $rol_id
    ]);

    // Obtener ID del usuario creado
    $nuevoId = $conexion->lastInsertId();

    // Registrar acción en bitácora
    registrarBitacora(
        $conexion,
        $adminId,
        $admin["usuario"],
        "USUARIO_CREADO",
        "Se creó el usuario $usuario (ID: $nuevoId) con rol $nombreRol"
    );
    $conexion->commit();

    header("Location: ../../usuarios.php?mensaje=exito");
    exit();

} catch (Throwable $e) {

    if ($conexion->inTransaction()) {
        $conexion->rollBack();
    }

    error_log("Error al registrar usuario: " . $e->getMessage());

    header("Location: ../../usuarios.php?mensaje=error");
    exit();
}
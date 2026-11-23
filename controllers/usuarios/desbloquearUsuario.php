<?php

require_once __DIR__ . "/../../includes/protegerUsuarios.php";

$id = filter_input(
    INPUT_POST,
    "id",
    FILTER_VALIDATE_INT
);

if (!$id) {
    header("Location: /proyecto/usuarios.php?mensaje=datos_invalidos");
    exit();
}

try {

    $conexion->beginTransaction();

    // Obtener usuario bloqueado
    $sql = "SELECT usuario, estado
            FROM usuarios
            WHERE id = :id
            FOR UPDATE";

    $query = $conexion->prepare($sql);
    $query->execute([":id" => $id]);

    $usuario = $query->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        throw new Exception("El usuario no existe");
    }

    if ($usuario["estado"] !== "bloqueado") {
        throw new Exception("El usuario no está bloqueado");
    }

    // Desbloquear cuenta
    $sql = "UPDATE usuarios
            SET estado = 'activo',
                intentos_fallidos = 0,
                fecha_bloqueo = NULL
            WHERE id = :id";

    $query = $conexion->prepare($sql);
    $query->execute([":id" => $id]);

    // Registrar en bitácora
    registrarBitacora(
        $conexion,
        $adminId,
        $admin["usuario"],
        "USUARIO_DESBLOQUEADO",
        "Desbloqueó al usuario " .
        $usuario["usuario"] .
        " (ID: $id)"
    );

    $conexion->commit();

    header("Location: /proyecto/usuarios.php?mensaje=desbloqueado");
    exit();

} catch (Throwable $e) {

    if ($conexion->inTransaction()) {
        $conexion->rollBack();
    }

    error_log("Error al desbloquear: " . $e->getMessage());

    header("Location: /proyecto/usuarios.php?mensaje=error");
    exit();
}
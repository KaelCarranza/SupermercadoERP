<?php

require_once __DIR__ . "/../../includes/protegerUsuarios.php";

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: /proyecto/usuarios.php?mensaje=datos_invalidos");
    exit();
}

// Evitar modificar la propia cuenta
if ($id === $adminId) {
    header("Location: /proyecto/usuarios.php?mensaje=accion_prohibida");
    exit();
}

try {

    $conexion->beginTransaction();

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

    if ($usuario["estado"] === "activo") {

        $nuevoEstado = "inhabilitado";
        $evento = "USUARIO_INHABILITADO";

    } elseif ($usuario["estado"] === "inhabilitado") {

        $nuevoEstado = "activo";
        $evento = "USUARIO_HABILITADO";

    } else {

        throw new Exception(
            "No se puede cambiar el estado de un usuario bloqueado"
        );
    }

    $sql = "UPDATE usuarios
            SET estado = :estado
            WHERE id = :id";

    $query = $conexion->prepare($sql);

    $query->execute([
        ":estado" => $nuevoEstado,
        ":id" => $id
    ]);

    registrarBitacora(
        $conexion,
        $adminId,
        $admin["usuario"],
        $evento,
        "Se cambió el estado del usuario " .
        $usuario["usuario"] .
        " (ID: $id) a $nuevoEstado"
    );

    $conexion->commit();

    header("Location: /proyecto/usuarios.php?mensaje=estado_actualizado");
    exit();

} catch (Throwable $e) {

    if ($conexion->inTransaction()) {
        $conexion->rollBack();
    }

    error_log("Error al cambiar estado: " . $e->getMessage());

    header("Location: /proyecto/usuarios.php?mensaje=error");
    exit();
}
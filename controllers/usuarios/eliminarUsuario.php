<?php

require_once __DIR__ . "../../../includes/protegerUsuarios.php";

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: ../../usuarios.php?mensaje=datos_invalidos");
    exit();
}

// Evitar eliminar la propia cuenta
if ($id === $adminId) {
    header("Location: ../../usuarios.php?mensaje=accion_prohibida");
    exit();
}

try {
    $conexion->beginTransaction();

    $sql = "SELECT usuario
            FROM usuarios
            WHERE id = :id
            FOR UPDATE";

    $query = $conexion->prepare($sql);
    $query->execute([":id" => $id]);

    $usuario = $query->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        $conexion->rollBack();
        header("Location: ../../usuarios.php?mensaje=no_existe");
        exit();
    }

    // Evitar eliminar usuarios que aparezcan
    // como autores de eventos en la bitácora
    $sql = "SELECT COUNT(*)
            FROM bitacora
            WHERE usuario_id = :id";

    $query = $conexion->prepare($sql);
    $query->execute([":id" => $id]);

    if ((int) $query->fetchColumn() > 0) {
        $conexion->rollBack();
        header("Location: ../../usuarios.php?mensaje=tiene_historial");
        exit();
    }

    // Eliminar usuario
    $sql = "DELETE FROM usuarios
            WHERE id = :id";

    $query = $conexion->prepare($sql);
    $query->execute([":id" => $id]);

    // Registrar eliminacion en la bitacoria
    registrarBitacora(
        $conexion,
        $adminId,
        $admin["usuario"],
        "USUARIO_ELIMINADO",
        "Se eliminó al usuario " . $usuario["usuario"] . " (ID: $id)"
    );

    $conexion->commit();

    header("Location: ../../usuarios.php?mensaje=eliminado");
    exit();

} catch (Throwable $e) {

    if ($conexion->inTransaction()) {
        $conexion->rollBack();
    }

    error_log($e->getMessage());

    header("Location: ../../usuarios.php?mensaje=error");
    exit();
}
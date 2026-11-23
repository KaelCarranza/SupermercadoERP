<?php

function registrarBitacora(
    PDO $conexion,
    ?int $usuarioId,
    string $usuario,
    string $evento,
    string $detalle
) {
    $ip = $_SERVER["REMOTE_ADDR"] ?? null;

    $sql = "INSERT INTO bitacora
            (usuario_id, usuario, evento, ip, detalle)
            VALUES
            (:usuario_id, :usuario, :evento, :ip, :detalle)";

    $query = $conexion->prepare($sql);

    $query->execute([
        ":usuario_id" => $usuarioId,
        ":usuario" => $usuario,
        ":evento" => $evento,
        ":ip" => $ip,
        ":detalle" => $detalle
    ]);
}
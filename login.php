<?php

session_start();

require_once __DIR__ . "/config/db.php";
require_once __DIR__ . "/../proyecto/includes/registarBitacora.php";

// Máximo de intentos permitidos
define("MAX_INTENTOS", 3);

$mensaje = "";

// Bloqueo visual de una sola carga.
// F5 elimina el bloqueo visual, no el de MySQL.
$formBloqueado = false;

if (isset($_SESSION["bloqueo_visual"])) {

    $formBloqueado = true;
    $mensaje = $_SESSION["bloqueo_visual"];

    unset($_SESSION["bloqueo_visual"]);
}

// Redirigir si ya inició sesión
if (isset($_SESSION["userId"])) {
    header("Location: index.php");
    exit();
}

// PROCESAR LOGIN
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombreUsuario = trim($_POST["usuario"] ?? "");
    $password = $_POST["contrasena"] ?? "";

    if ($nombreUsuario === "" || $password === "") {

        $mensaje = "Ingrese usuario y contraseña.";
    } else {

        try {

            $conexion->beginTransaction();

            // Buscar usuario
            $sql = "SELECT
                        u.id,
                        u.usuario,
                        u.contrasena,
                        u.rol_id,
                        u.estado,
                        u.intentos_fallidos,
                        r.nombre AS rol
                    FROM usuarios u
                    INNER JOIN roles r ON u.rol_id = r.id
                    WHERE u.usuario = ?
                    FOR UPDATE";

            $consulta = $conexion->prepare($sql);
            $consulta->execute([$nombreUsuario]);

            $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

            // CASO 1: USUARIO INEXISTENTE

            if (!$usuario) {

                registrarBitacora(
                    $conexion,
                    null,
                    $nombreUsuario,
                    "LOGIN_FALLIDO",
                    "Intento con usuario inexistente"
                );

                registrarBitacora(
                    $conexion,
                    null,
                    $nombreUsuario,
                    "FORMULARIO_BLOQUEADO",
                    "Formulario bloqueado por usuario incorrecto"
                );

                $conexion->commit();

                $_SESSION["bloqueo_visual"] =
                    "El usuario no existe. Formulario bloqueado.";

                header("Location: login.php");
                exit();
            }

            $id = (int)$usuario["id"];
            $estado = $usuario["estado"];

            // CASO 2: USUARIO BLOQUEADO

            if ($estado === "bloqueado") {

                registrarBitacora(
                    $conexion,
                    $id,
                    $usuario["usuario"],
                    "LOGIN_RECHAZADO",
                    "Intento de acceso con cuenta bloqueada"
                );

                $conexion->commit();

                $mensaje =
                    "Esta cuenta está bloqueada. Contacte a un administrador.";

                // CASO 3: USUARIO INHABILITADO

            } elseif ($estado === "inhabilitado") {

                registrarBitacora(
                    $conexion,
                    $id,
                    $usuario["usuario"],
                    "LOGIN_RECHAZADO",
                    "Intento de acceso con cuenta inhabilitada"
                );

                $conexion->commit();

                $mensaje =
                    "Esta cuenta está inhabilitada. Contacte a un administrador.";

                // CASO 4: CONTRASEÑA CORRECTA

            } elseif (password_verify(
                $password,
                $usuario["contrasena"]
            )) {

                // Reiniciar intentos fallidos
                $sql = "UPDATE usuarios
                        SET intentos_fallidos = 0
                        WHERE id = ?";

                $consulta = $conexion->prepare($sql);
                $consulta->execute([$id]);

                // Registrar ingreso exitoso
                registrarBitacora(
                    $conexion,
                    $id,
                    $usuario["usuario"],
                    "LOGIN_EXITOSO",
                    "Inicio de sesión exitoso"
                );

                $conexion->commit();

                session_regenerate_id(true);

                $_SESSION["userId"] = $id;
                $_SESSION["usuario"] = $usuario["usuario"];
                $_SESSION["role"] = $usuario["rol"];

                header("Location: index.php");
                exit();

                // CASO 5: CONTRASEÑA INCORRECTA

            } else {

                $intentos =
                    (int)$usuario["intentos_fallidos"] + 1;

                // Registrar intento fallido
                registrarBitacora(
                    $conexion,
                    $id,
                    $usuario["usuario"],
                    "LOGIN_FALLIDO",
                    "Contraseña incorrecta. Intento $intentos de 3"
                );

                // TERCER INTENTO
                if ($intentos >= MAX_INTENTOS) {

                    // Bloquear cuenta
                    $sql = "UPDATE usuarios
                            SET estado = 'bloqueado',
                                intentos_fallidos = ?,
                                fecha_bloqueo = NOW()
                            WHERE id = ?";

                    $consulta = $conexion->prepare($sql);
                    $consulta->execute([$intentos, $id]);

                    // Registrar bloqueo de cuenta
                    registrarBitacora(
                        $conexion,
                        $id,
                        $usuario["usuario"],
                        "USUARIO_BLOQUEADO",
                        "Cuenta bloqueada por alcanzar 3 intentos fallidos"
                    );

                    // Registrar bloqueo visual
                    registrarBitacora(
                        $conexion,
                        $id,
                        $usuario["usuario"],
                        "FORMULARIO_BLOQUEADO",
                        "Formulario bloqueado por 3 intentos fallidos"
                    );

                    $conexion->commit();

                    $_SESSION["bloqueo_visual"] =
                        "Ha alcanzado los 3 intentos fallidos. Su cuenta fue bloqueada. Solo un administrador puede desbloquearla.";

                    header("Location: login.php");
                    exit();
                } else {

                    // Actualizar intentos
                    $sql = "UPDATE usuarios
                            SET intentos_fallidos = ?
                            WHERE id = ?";

                    $consulta = $conexion->prepare($sql);
                    $consulta->execute([$intentos, $id]);

                    $conexion->commit();

                    $restantes = MAX_INTENTOS - $intentos;

                    $mensaje =
                        "Contraseña incorrecta. Le quedan $restantes intentos.";
                }
            }
        } catch (Throwable $e) {

            if ($conexion->inTransaction()) {
                $conexion->rollBack();
            }

            error_log("Error al iniciar sesión: " . $e->getMessage());

            $mensaje = "Ocurrió un error al iniciar sesión.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Login - Supermercado ERP</title>

</head>

<body>

    <h2>Iniciar sesión</h2>

    <!-- MENSAJES -->
    <?php if ($mensaje !== ""): ?>

        <p style="color: red;">
            <?= htmlspecialchars($mensaje) ?>
        </p>

    <?php endif; ?>

    <!-- FORMULARIO -->
    <form action="login.php" method="POST">

        <input
            type="text"
            name="usuario"
            placeholder="Ingrese su usuario"
            autocomplete="username"
            required
            <?= $formBloqueado ? "disabled" : "" ?>>

        <input
            type="password"
            name="contrasena"
            placeholder="Ingrese su contraseña"
            autocomplete="current-password"
            required
            <?= $formBloqueado ? "disabled" : "" ?>>

        <button
            type="submit"
            <?= $formBloqueado ? "disabled" : "" ?>>
            Ingresar
        </button>

    </form>

    <?php if ($formBloqueado): ?>

        <p>
            El formulario está temporalmente bloqueado.
        </p>

    <?php endif; ?>

</body>

</html>
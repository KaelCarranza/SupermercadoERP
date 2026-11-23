<?php

session_start();

require_once __DIR__ . "/config/db.php";

if (!isset($_SESSION["userId"])) {
    header("Location: login.php");
    exit();
}

$sqlAdmin = "SELECT r.nombre AS rol
             FROM usuarios u
             INNER JOIN roles r ON u.rol_id = r.id
             WHERE u.id = :id
             AND u.estado = 'activo'";

$queryAdmin = $conexion->prepare($sqlAdmin);
$queryAdmin->execute([
    ":id" => (int) $_SESSION["userId"]
]);

$admin = $queryAdmin->fetch(PDO::FETCH_ASSOC);

if (!$admin || $admin["rol"] !== "Administrador") {
    http_response_code(403);
    exit("Acceso denegado");
}

if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

$sql = "SELECT u.id, u.usuario, r.nombre AS rol,
               u.fecha_creacion, u.estado,
               u.intentos_fallidos
        FROM usuarios u
        INNER JOIN roles r ON u.rol_id = r.id
        ORDER BY u.id DESC";

$query = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Usuarios - ERP</title>

    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/styles.css">

</head>

<body>

    <div class="layout">

        <?php include "includes/sidebar.php"; ?>

        <main class="main-content">

            <h1>Usuarios</h1>

            <p>Bienvenido al módulo de gestión de usuarios.</p>

            <div>
                <form action="controllers/usuarios/insertarUsuario.php" method="POST">

                    <label>Usuario:</label>
                    <input type="text" name="nombre" required>

                    <label>Contraseña:</label>
                    <input type="password" name="password" minlength="8" required>

                    <label>Rol:</label>
                    <select name="rol_id" required>
                        <option value="">Seleccione un rol</option>

                        <?php
                        $roles = $conexion->query("SELECT id, nombre FROM roles");

                        while ($rol = $roles->fetch(PDO::FETCH_ASSOC)) {
                        ?>
                            <option value="<?= (int)$rol["id"] ?>">
                                <?= htmlspecialchars($rol["nombre"]) ?>
                            </option>
                        <?php } ?>
                    </select>

                    <button type="submit">Agregar usuario</button>

                </form>

                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Rol</th>
                            <th>Fecha Creación</th>
                            <th>Estado</th>
                            <th>Intentos fallidos</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php while ($datos = $query->fetch(PDO::FETCH_ASSOC)) { ?>
                            <tr>
                                <td><?= (int)$datos["id"] ?></td>
                                <td><?= htmlspecialchars($datos["usuario"]) ?></td>
                                <td><?= htmlspecialchars($datos["rol"]) ?></td>
                                <td><?= htmlspecialchars($datos["fecha_creacion"]) ?></td>
                                <td><?= htmlspecialchars($datos["estado"]) ?></td>
                                <td><?= (int)$datos["intentos_fallidos"] ?></td>

                                <td>

                                    <?php if ($datos["estado"] === "bloqueado") { ?>

                                        <!-- DESBLOQUEAR USUARIO -->
                                        <form action="controllers/usuarios/desbloquearUsuario.php"
                                            method="POST">

                                            <input
                                                type="hidden"
                                                name="csrf_token"
                                                value="<?= htmlspecialchars($_SESSION["csrf_token"]) ?>">

                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?= (int)$datos["id"] ?>">

                                            <button type="submit">
                                                Desbloquear
                                            </button>

                                        </form>

                                    <?php } elseif ($datos["estado"] === "activo") { ?>

                                        <!-- INHABILITAR USUARIO -->
                                        <form action="controllers/usuarios/estadoUsuario.php"
                                            method="POST">

                                            <input
                                                type="hidden"
                                                name="csrf_token"
                                                value="<?= htmlspecialchars($_SESSION["csrf_token"]) ?>">

                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?= (int)$datos["id"] ?>">

                                            <button type="submit">
                                                Inhabilitar
                                            </button>

                                        </form>

                                    <?php } elseif ($datos["estado"] === "inhabilitado") { ?>

                                        <!-- HABILITAR USUARIO -->
                                        <form action="controllers/usuarios/estadoUsuario.php"
                                            method="POST">

                                            <input
                                                type="hidden"
                                                name="csrf_token"
                                                value="<?= htmlspecialchars($_SESSION["csrf_token"]) ?>">

                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?= (int)$datos["id"] ?>">

                                            <button type="submit">
                                                Habilitar
                                            </button>

                                        </form>

                                    <?php } ?>
                                    <form action="controllers/usuarios/eliminarUsuario.php"
                                        method="POST"
                                        onsubmit="return confirm('¿Está seguro de eliminar este usuario?');">

                                        <input type="hidden"
                                            name="csrf_token"
                                            value="<?= htmlspecialchars($_SESSION["csrf_token"]) ?>">

                                        <input type="hidden"
                                            name="id"
                                            value="<?= (int)$datos["id"] ?>">

                                        <button type="submit">
                                            Eliminar
                                        </button>

                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                </form>
            </div>
        </main>
    </div>
</body>

</html>
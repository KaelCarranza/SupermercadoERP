<?php
session_start();

if (!isset($_SESSION["userId"])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Configuracion - ERP</title>

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <link rel="stylesheet" href="css/styles.css">
</head>

<body>

    <div class="layout">

        <?php include "includes/sidebar.php"; ?>

        <main class="main-content">

            <h1>Configuracion</h1>

            <p>Bienvenido al módulo de configuracion.</p>

        </main>

    </div>

</body>
</html>
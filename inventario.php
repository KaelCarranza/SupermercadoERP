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
    <title>Inventario - ERP</title>

    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <link rel="stylesheet" href="css/styles.css">
</head>

<body>

    <div class="layout">

        <?php include "includes/sidebar.php"; ?>

        <main class="main-content">

            <h1>Inventario</h1>

            <p>Bienvenido al módulo de Inventario.</p>
            <p>Control visual de existencias y niveles mínimos.</p>
            <br><br>
            <button>+ Agregar Producto</button>

            <div class="panel-title">
                <h2>Catálogo de productos</h2><span></span>
            </div>
            <div class="row g-2 mb-3">
                <div class="col-md-6"><input class="form-control"
                        placeholder="Buscar por nombre, código o categoría..."></div>
                <div class="col-md-3"><select class="form-select">
                        <option>Todas las categorías</option>
                    </select></div>
                <div class="col-md-3"><select class="form-select">
                        <option>Todos los estados</option>
                    </select></div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Codigo de barras</th>
                        <th>Categoria</th>
                        <th>Precio costo</th>
                        <th>Precio venta</th>
                        <th>Stock</th>
                        <th>Stock minimo</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>Frijoles Molidos</th>
                        <th>FM-001</th>
                        <th>Abarrotes</th>
                        <th>₡850</th>
                        <th>₡1.250</th>
                        <th>250</th>
                        <th>25</th>
                        <th>Disponible</th>
                        <th><a href="">Eliminar/Editar</a></th>
                    </tr>
                    <tr>
                        <th>Arroz Blanco</th>
                        <th>AB-002</th>
                        <th>Abarrotes</th>
                        <th>₡850</th>
                        <th>₡1.250</th>
                        <th>20</th>
                        <th>25</th>
                        <th>Stock bajo</th>
                        <th><a href="">Eliminar/Editar</a></th>
                    </tr>
                </tbody>
            </table>

        </main>
    </div>
</body>

</html>
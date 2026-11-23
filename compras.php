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
    <title>Compras - ERP</title>

    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <link rel="stylesheet" href="css/styles.css">
</head>

<body>

    <div class="layout">

        <?php include "includes/sidebar.php"; ?>

        <main class="main-content">

            <h1>Compras</h1>

            <p>Bienvenido al módulo de Compras.</p>

            <div class="workspace">
                <div class="page-heading">
                    <div>
                        <p>Consulta órdenes, recepciones y necesidades de reposición.</p>
                        <div class="row g-3">
                            <div class="col-xl-8">
                                <section class="panel ">
                                    <div class="panel-title">
                                        <h2>Órdenes de compra</h2><span></span>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Orden</th>
                                                    <th>Proveedor</th>
                                                    <th>Fecha</th>
                                                    <th>Contenido</th>
                                                    <th>Monto</th>
                                                    <th>Estado</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><strong>#OC-0082</strong></td>
                                                    <td>Distribuidora Central</td>
                                                    <td>18/11/2026</td>
                                                    <td>24 artículos</td>
                                                    <td>₡ 125.400</td>
                                                    <td><span class="tag yellow">Pendiente</span></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>#OC-0081</strong></td>
                                                    <td>Alimentos del Valle</td>
                                                    <td>17/11/2026</td>
                                                    <td>13 artículos</td>
                                                    <td>₡ 82.000</td>
                                                    <td><span class="tag blue">En tránsito</span></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>#OC-0080</strong></td>
                                                    <td>Comercial La Unión</td>
                                                    <td>15/11/2026</td>
                                                    <td>36 artículos</td>
                                                    <td>₡ 240.850</td>
                                                    <td><span class="tag">Recibida</span></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </section>
                            </div>
                            <div class="col-xl-4">
                                <section class="panel ">
                                    <div class="panel-title">
                                        <h2>Nueva orden · vista previa</h2><span></span>
                                    </div><label class="form-label">Proveedor</label><select class="form-select mb-3">
                                        <option>Seleccionar proveedor...</option>
                                        <option>Distribuidora Central</option>
                                        <option>Alimentos del Valle</option>
                                        <option>Comercial La Unión</option>
                                    </select><label class="form-label">Producto</label><input class="form-control mb-3"
                                        placeholder="Buscar producto">
                                    <div class="row g-2">
                                        <div class="col-6"><label class="form-label">Cantidad</label><input class="form-control"
                                                placeholder="0"></div>
                                        <div class="col-6"><label class="form-label">Costo unitario</label><input
                                                class="form-control" placeholder="₡ 0,00"></div>
                                    </div>
                                    <div class="surface-soft mt-3 mb-3">Total estimado <strong class="float-end">₡ 0,00</strong>
                                    </div><button class="btn btn-brand" type="button"><i
                                            class="bi bi-plus-circle me-2"></i>Crear orden</button>
                                </section>
                            </div>
                        </div>
        </main>

    </div>

</body>

</html>
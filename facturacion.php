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
    <title>Facturación - ERP</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/styles.css">
    
</head>

<body>

    <div class="layout">

        <?php include "includes/sidebar.php"; ?>

        <main class="main-content">

            <h1>Facturación</h1>

            <p>Bienvenido al módulo de facturación.</p>

            <div class="app">
                <main class="main">
                    <div class="workspace">
                        <div class="page-heading">
                            <div>
                                <p>Registra artículos y visualiza diferentes medios de pago.</p>
                            </div><button class="btn btn-outline-brand" type="button"><i
                                    class="bi bi-arrow-clockwise me-2"></i>Nueva factura</button>
                        </div>
                        <div class="col-xl-7">
                            <section class="panel ">
                                <div class="panel-title">
                                    <h2>Detalle de la venta</h2><span></span>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-7"><label class="form-label">Buscar producto o código de
                                            barras</label><input class="form-control"
                                            placeholder="Ej. arroz, AB-001 o código..."></div>
                                    <div class="col-md-5"><label class="form-label">Cliente</label><select
                                            class="form-select">
                                            <option>Juanito Pérez</option>
                                            <option>María López</option>
                                            <option>Carlos Rodríguez</option>
                                        </select></div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Producto</th>
                                                <th>Cantidad</th>
                                                <th>Precio unitario</th>
                                                <th>Subtotal</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><strong>Arroz blanco 1 kg</strong><br><small class="muted">SKU:
                                                        AB-001</small></td>
                                                <td><span class="badge text-bg-light border">2</span></td>
                                                <td>₡ 1.250</td>
                                                <td>₡ 2.500</td>
                                                <td><i class="bi bi-x-lg muted"></i></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Leche entera 1 L</strong><br><small class="muted">SKU:
                                                        LE-011</small></td>
                                                <td><span class="badge text-bg-light border">3</span></td>
                                                <td>₡ 950</td>
                                                <td>₡ 2.850</td>
                                                <td><i class="bi bi-x-lg muted"></i></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Café molido 250 g</strong><br><small class="muted">SKU:
                                                        CA-023</small></td>
                                                <td><span class="badge text-bg-light border">1</span></td>
                                                <td>₡ 2.800</td>
                                                <td>₡ 2.800</td>
                                                <td><i class="bi bi-x-lg muted"></i></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </section>
                        </div>
                        <div class="col-xl-5">
                            <section class="panel ">
                                <div class="panel-title">
                                    <h2>Resumen y cobro</h2><span></span>
                                </div>
                                <div class="label-kpi">Factura #FAC-1053 · Caja 01</div>
                                <div class="summary-line"><span>Subtotal</span><strong>₡ 8.150</strong></div>
                                <div class="summary-line"><span>IVA (ejemplo 13%)</span><strong>₡ 1.059,50</strong></div>
                                <div class="summary-line total"><span>Total a pagar</span><strong class="big-total">₡
                                        9.209,50</strong></div>
                                <div class="summary-line"><span>Equivalente en USD</span><strong>$ 17,71</strong></div>
                                <div class="divider"></div><label class="form-label">Métodos de pago</label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="payment-choice selected"><i class="bi bi-cash-stack"></i> Colones</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="payment-choice"><i class="bi bi-currency-dollar"></i> Dólares</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="payment-choice"><i class="bi bi-credit-card"></i> Tarjeta</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="payment-choice"><i class="bi bi-wallet2"></i> Mixto</div>
                                    </div>
                                </div>
                                <div class="row g-2 mt-2">
                                    <div class="col-6"><label class="form-label">Recibido en colones</label><input
                                            class="form-control" value="₡ 10.000" readonly></div>
                                    <div class="col-6"><label class="form-label">Recibido en dólares</label><input
                                            class="form-control" value="$ 0,00" readonly></div>
                                </div>
                                <div class="surface-soft mt-3">
                                    <div class="summary-line"><span>Saldo pendiente</span><strong>₡ 0,00</strong></div>
                                    <div class="summary-line"><span>Vuelto en colones</span><strong style="color:#168268">₡
                                            790,50</strong></div>
                                </div><button class="btn btn-brand w-100 mt-3" type="button"><i
                                        class="bi bi-check2-circle me-2"></i>Registrar venta</button>
                            </section>
                        </div>
                    </div>
            </div>
        </main>
    </div>

    </main>

    </div>

</body>

</html>
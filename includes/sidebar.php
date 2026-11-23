<?php
$paginaActual = basename($_SERVER["SCRIPT_NAME"]);
?>

<aside class="sidebar">

    <a href="index.php" class="brand">
        <div class="brand-mark">
            <i class="bi bi-shop-window"></i>
        </div>

        <div>
            <strong>Supermercado</strong>
            <small>ERP</small>
        </div>
    </a>

    <div class="nav-label">GENERAL</div>

    <nav>

        <a class="side-link <?= $paginaActual == 'index.php' ? 'active' : '' ?>"
           href="index.php">
            <i class="bi bi-grid-1x2"></i>
            <span>Panel principal</span>
        </a>

        <a class="side-link <?= $paginaActual == 'facturacion.php' ? 'active' : '' ?>"
           href="facturacion.php">
            <i class="bi bi-receipt-cutoff"></i>
            <span>Facturación</span>
        </a>

        <a class="side-link <?= $paginaActual == 'compras.php' ? 'active' : '' ?>"
           href="compras.php">
            <i class="bi bi-bag-check"></i>
            <span>Compras</span>
        </a>

        <a class="side-link <?= $paginaActual == 'inventario.php' ? 'active' : '' ?>"
           href="inventario.php">
            <i class="bi bi-boxes"></i>
            <span>Inventario</span>
        </a>

        <a class="side-link <?= $paginaActual == 'usuarios.php' ? 'active' : '' ?>"
           href="usuarios.php">
            <i class="bi bi-people"></i>
            <span>Usuarios</span>
        </a>

        <a class="side-link <?= $paginaActual == 'bitacora.php' ? 'active' : '' ?>"
           href="bitacora.php">
            <i class="bi bi-clock-history"></i>
            <span>Bitácora</span>
        </a>

        <a class="side-link <?= $paginaActual == 'configuracion.php' ? 'active' : '' ?>"
           href="configuracion.php">
            <i class="bi bi-gear"></i>
            <span>Configuración</span>
        </a>

    </nav>

    <div class="side-footer">

        <div class="avatar">
            <i class="bi bi-person-fill"></i>
        </div>

        <div class="user-info">
            <strong>
                <?= htmlspecialchars($_SESSION["usuario"] ?? "Usuario") ?>
            </strong>

            <small>
                <?= htmlspecialchars($_SESSION["role"] ?? "") ?>
            </small>
        </div>

        <a href="logout.php"
           class="logout-btn"
           title="Cerrar sesión">

            <i class="bi bi-box-arrow-right"></i>

        </a>

    </div>

</aside>
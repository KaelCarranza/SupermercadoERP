<?php
session_start();

if (!isset($_SESSION["userId"])) {
    header("Location: login.php");
    exit();
}

// Solo administradores
if (($_SESSION["role"] ?? "") !== "Administrador") {
    http_response_code(403);
    exit("Acceso denegado. No tiene permisos para ver la bitácora.");
}

require_once "config/db.php";

// Obtener filtros
$buscar = trim($_GET["buscar"] ?? "");
$usuarioFiltro = trim($_GET["usuario"] ?? "");
$eventoFiltro = trim($_GET["evento"] ?? "");
$fechaDesde = trim($_GET["desde"] ?? "");
$fechaHasta = trim($_GET["hasta"] ?? "");

// Validar fechas
function fechaValida($fecha) {
    if ($fecha === "") {
        return true;
    }

    $d = DateTime::createFromFormat("!Y-m-d", $fecha);

    return $d && $d->format("Y-m-d") === $fecha;
}

if (!fechaValida($fechaDesde) || !fechaValida($fechaHasta)) {
    http_response_code(400);
    exit("Formato de fecha inválido.");
}

// Consultar usuarios y eventos disponibles
$usuarios = $conexion->query(
    "SELECT DISTINCT usuario
     FROM bitacora
     ORDER BY usuario ASC"
)->fetchAll(PDO::FETCH_COLUMN);

$eventos = $conexion->query(
    "SELECT DISTINCT evento
     FROM bitacora
     ORDER BY evento ASC"
)->fetchAll(PDO::FETCH_COLUMN);

// Construir filtros de búsqueda
$condiciones = [];
$parametros = [];

if ($usuarioFiltro !== "") {
    $condiciones[] = "usuario = :usuario";
    $parametros["usuario"] = $usuarioFiltro;
}

if ($eventoFiltro !== "") {
    $condiciones[] = "evento = :evento";
    $parametros["evento"] = $eventoFiltro;
}

if ($fechaDesde !== "") {
    $condiciones[] = "fecha_hora >= :desde";
    $parametros["desde"] = $fechaDesde . " 00:00:00";
}

if ($fechaHasta !== "") {
    $condiciones[] = "fecha_hora < :hasta";
    $fechaFin = (new DateTimeImmutable($fechaHasta))
        ->modify("+1 day");

    $parametros["hasta"] = $fechaFin->format("Y-m-d 00:00:00");
}

$where = "";

if (count($condiciones) > 0) {
    $where = " WHERE " . implode(" AND ", $condiciones);
}

// Contar resultados
$sqlTotal = "SELECT COUNT(*) FROM bitacora" . $where;

$consultaTotal = $conexion->prepare($sqlTotal);
$consultaTotal->execute($parametros);

$totalRegistros = (int) $consultaTotal->fetchColumn();

// Paginación
$porPagina = 20;
$totalPaginas = max(1, (int) ceil($totalRegistros / $porPagina));

$pagina = max(1, (int) ($_GET["pagina"] ?? 1));
$pagina = min($pagina, $totalPaginas);

$offset = ($pagina - 1) * $porPagina;

// Obtener registros
$sql = "SELECT
            id,
            fecha_hora,
            usuario,
            evento,
            ip,
            detalle
        FROM bitacora"
        . $where .
        " ORDER BY fecha_hora DESC, id DESC
          LIMIT :limite OFFSET :offset";

$consulta = $conexion->prepare($sql);

foreach ($parametros as $clave => $valor) {
    $consulta->bindValue(":" . $clave, $valor, PDO::PARAM_STR);
}

$consulta->bindValue(":limite", $porPagina, PDO::PARAM_INT);
$consulta->bindValue(":offset", $offset, PDO::PARAM_INT);

$consulta->execute();

$registros = $consulta->fetchAll(PDO::FETCH_ASSOC);

// Mantener filtros al cambiar de página
function enlacePagina($numero) {
    $parametros = $_GET;
    $parametros["pagina"] = $numero;

    return "bitacora.php?" . http_build_query($parametros);
}

// Escapar texto para HTML
function e($valor) {
    return htmlspecialchars(
        (string) ($valor ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Bitácora - Supermercado ERP</title>

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/bitacora.css">
</head>

<body>

<div class="layout">

    <?php include "includes/sidebar.php"; ?>

    <main class="main-content">

        <!-- ENCABEZADO -->
        <div class="bitacora-header">

            <div>
                <h1>Bitácora del sistema</h1>
                <p>
                    Registro de actividades y eventos
                    realizados en el ERP.
                </p>
                <h1>"Terminado"</h1>
            </div>

            <div class="contador-registros">
                <i class="bi bi-clock-history"></i>
                <?= number_format($totalRegistros) ?>
                registros
            </div>

        </div>

        <!-- FILTROS -->
        <section class="bitacora-panel">

            <h2>
                <i class="bi bi-funnel"></i>
                Filtros de búsqueda
            </h2>

            <form action="bitacora.php"
                  method="GET"
                  class="filtros-form">

                <div class="campo">

                    <label for="evento">Evento</label>

                    <select name="evento" id="evento">

                        <option value="">Todos los eventos</option>

                        <?php foreach ($eventos as $evento): ?>

                            <option
                                value="<?= e($evento) ?>"
                                <?= $eventoFiltro === $evento ? "selected" : "" ?>
                            >
                                <?= e($evento) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <div class="campo">

                    <label for="desde">Desde</label>

                    <input
                        type="date"
                        name="desde"
                        id="desde"
                        value="<?= e($fechaDesde) ?>"
                    >

                </div>

                <div class="campo">

                    <label for="hasta">Hasta</label>

                    <input
                        type="date"
                        name="hasta"
                        id="hasta"
                        value="<?= e($fechaHasta) ?>"
                    >

                </div>

                <div class="filtros-acciones">

                    <button type="submit" class="btn-filtrar">
                        <i class="bi bi-search"></i>
                        Buscar
                    </button>

                    <a href="bitacora.php" class="btn-limpiar">
                        <i class="bi bi-arrow-counterclockwise"></i>
                        Limpiar
                    </a>

                </div>

            </form>

        </section>

        <!-- REGISTROS -->
        <section class="bitacora-panel">

            <div class="tabla-encabezado">

                <div>
                    <h2>Historial de eventos</h2>
                    <p>
                        Mostrando
                        <?= count($registros) ?>
                        de
                        <?= number_format($totalRegistros) ?>
                        registros encontrados
                    </p>
                </div>

                <span class="solo-lectura">
                    <i class="bi bi-shield-lock"></i>
                    Solo lectura
                </span>

            </div>

            <div class="tabla-contenedor">

                <table class="tabla-bitacora">

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha y hora</th>
                            <th>Usuario</th>
                            <th>Evento</th>
                            <th>Detalle</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php if (count($registros) > 0): ?>

                            <?php foreach ($registros as $registro): ?>

                                <tr>

                                    <td>
                                        #<?= (int) $registro["id"] ?>
                                    </td>

                                    <td class="fecha-tabla">
                                        <?= e($registro["fecha_hora"]) ?>
                                    </td>

                                    <td>
                                        <span class="usuario-tabla">
                                            <i class="bi bi-person-circle"></i>
                                            <?= e($registro["usuario"]) ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?php
                                        $evento = $registro["evento"];

                                        $clase = "evento-default";

                                        if ($evento === "LOGIN_EXITOSO") {
                                            $clase = "evento-exito";
                                        } elseif (
                                            str_contains($evento, "FALLIDO") ||
                                            str_contains($evento, "RECHAZADO") ||
                                            str_contains($evento, "DENEGADO")
                                        ) {
                                            $clase = "evento-error";
                                        } elseif (
                                            str_contains($evento, "BLOQUEADO") ||
                                            str_contains($evento, "INHABILITADO")
                                        ) {
                                            $clase = "evento-alerta";
                                        }
                                        ?>

                                        <span class="evento <?= $clase ?>">
                                            <?= e($evento) ?>
                                        </span>
                                    </td>

                                    <td class="detalle-tabla">
                                        <?= e($registro["detalle"] ?: "-") ?>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>
                                <td colspan="6" class="sin-registros">
                                    <i class="bi bi-inbox"></i>
                                    <p>No se encontraron registros.</p>
                                </td>
                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

            <!-- PAGINACIÓN -->
            <?php if ($totalPaginas > 1): ?>

                <div class="paginacion">

                    <?php if ($pagina > 1): ?>

                        <a href="<?= e(enlacePagina($pagina - 1)) ?>">
                            <i class="bi bi-chevron-left"></i>
                            Anterior
                        </a>

                    <?php endif; ?>

                    <span>
                        Página <?= $pagina ?>
                        de <?= $totalPaginas ?>
                    </span>

                    <?php if ($pagina < $totalPaginas): ?>

                        <a href="<?= e(enlacePagina($pagina + 1)) ?>">
                            Siguiente
                            <i class="bi bi-chevron-right"></i>
                        </a>

                    <?php endif; ?>

                </div>

            <?php endif; ?>

        </section>

    </main>

</div>

</body>
</html>

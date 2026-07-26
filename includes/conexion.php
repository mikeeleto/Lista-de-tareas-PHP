<?php
/**
 * Conexión a la base de datos con mysqli.
 * Este archivo se incluye desde index.php, agregar.php, completar.php y borrar.php.
 */

$conexion = new mysqli("localhost", "root", "", "mi_base_de_datos");

if ($conexion->connect_error) {
    die("Algo salió mal: " . $conexion->connect_error);
}

// Para que los acentos y las ñ se guarden y se muestren bien
$conexion->set_charset("utf8mb4");

/**
 * Vuelve a index.php y termina el script.
 * Se usa después de cada INSERT / UPDATE / DELETE para que al recargar
 * la página el navegador no repita la acción (patrón Post/Redirect/Get).
 */
function volver_al_inicio($mensaje = null)
{
    $destino = "index.php";
    if ($mensaje !== null) {
        $destino .= "?msg=" . urlencode($mensaje);
    }
    header("Location: " . $destino);
    exit;
}

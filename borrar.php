<?php
/**
 * Borra una tarea de la base de datos (DELETE).
 */
require_once __DIR__ . "/includes/conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    volver_al_inicio();
}

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id) {
    volver_al_inicio("error");
}

$sentencia = $conexion->prepare("DELETE FROM tareas WHERE id = ?");
$sentencia->bind_param("i", $id); // "i" = integer
$sentencia->execute();
$sentencia->close();

volver_al_inicio("borrada");

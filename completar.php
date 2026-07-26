<?php
/**
 * Marca una tarea como completada o la devuelve a pendiente (UPDATE).
 */
require_once __DIR__ . "/includes/conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    volver_al_inicio();
}

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id) {
    volver_al_inicio("error");
}

// NOT completada invierte el valor: 0 pasa a 1 y 1 pasa a 0
$sentencia = $conexion->prepare("UPDATE tareas SET completada = NOT completada WHERE id = ?");
$sentencia->bind_param("i", $id); // "i" = integer
$sentencia->execute();
$sentencia->close();

volver_al_inicio("actualizada");

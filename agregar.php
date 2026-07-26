<?php
/**
 * Guarda una tarea nueva en la base de datos (INSERT).
 */
require_once __DIR__ . "/includes/conexion.php";

// Solo aceptamos el formulario por POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    volver_al_inicio();
}

$tarea = trim($_POST["tarea"] ?? "");

// Validación: que no esté vacía y que no sea más larga que la columna
if ($tarea === "") {
    volver_al_inicio("vacia");
}

if (mb_strlen($tarea) > 255) {
    $tarea = mb_substr($tarea, 0, 255);
}

// prepare + bind_param evita la inyección SQL: el texto nunca se pega dentro de la consulta
$sentencia = $conexion->prepare("INSERT INTO tareas (tarea, completada) VALUES (?, 0)");
$sentencia->bind_param("s", $tarea); // "s" = string
$sentencia->execute();
$sentencia->close();

volver_al_inicio("agregada");

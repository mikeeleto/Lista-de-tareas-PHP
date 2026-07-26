<?php
/**
 * Cambia el texto de una tarea que ya existe (UPDATE).
 */
require_once __DIR__ . "/includes/conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    volver_al_inicio();
}

$id    = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
$tarea = trim($_POST["tarea"] ?? "");

if (!$id) {
    volver_al_inicio("error");
}

// Si borró todo el texto, lo mandamos de vuelta al modo edición para que escriba algo
if ($tarea === "") {
    header("Location: index.php?editar=" . $id . "&msg=vacia");
    exit;
}

if (mb_strlen($tarea) > 255) {
    $tarea = mb_substr($tarea, 0, 255);
}

$sentencia = $conexion->prepare("UPDATE tareas SET tarea = ? WHERE id = ?");
$sentencia->bind_param("si", $tarea, $id); // "s" = string, "i" = integer
$sentencia->execute();
$sentencia->close();

volver_al_inicio("editada");

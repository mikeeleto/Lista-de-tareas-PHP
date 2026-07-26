<?php
/**
 * Página principal: muestra el formulario y la lista de tareas.
 */
require_once __DIR__ . "/includes/conexion.php";

// Traemos todas las tareas: primero las pendientes, y dentro de cada grupo las más nuevas arriba
$resultado = $conexion->query("SELECT * FROM tareas ORDER BY completada ASC, id DESC");

// fetch_assoc() saca una fila a la vez; las vamos guardando en un array
$tareas = [];
while ($fila = $resultado->fetch_assoc()) {
    $tareas[] = $fila;
}

$total       = count($tareas);
$completadas = 0;
foreach ($tareas as $t) {
    if ($t["completada"]) {
        $completadas++;
    }
}
$pendientes = $total - $completadas;

// Si viene ?editar=5 en la URL, esa tarea se muestra como un campo de texto
$editando = filter_input(INPUT_GET, 'editar', FILTER_VALIDATE_INT);

// Mensajes que llegan por la URL después de agregar / editar / completar / borrar
$mensajes = [
    'agregada'    => ['texto' => 'Tarea agregada.',            'tipo' => 'ok'],
    'editada'     => ['texto' => 'Tarea modificada.',          'tipo' => 'ok'],
    'actualizada' => ['texto' => 'Tarea actualizada.',         'tipo' => 'ok'],
    'borrada'     => ['texto' => 'Tarea borrada.',             'tipo' => 'ok'],
    'vacia'       => ['texto' => 'Escribe algo antes de guardar.', 'tipo' => 'error'],
    'error'       => ['texto' => 'No se pudo realizar la acción.', 'tipo' => 'error'],
];
$clave   = $_GET['msg'] ?? '';
$mensaje = $mensajes[$clave] ?? null;

/** Atajo para escapar texto y evitar HTML inyectado (XSS). */
function e($texto)
{
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Tareas</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

<main class="tarjeta">

    <header class="cabecera">
        <h1>Lista de Tareas</h1>
        <p class="subtitulo">Organiza tu día, una tarea a la vez.</p>
    </header>

    <?php if ($mensaje): ?>
        <p class="aviso aviso--<?= e($mensaje['tipo']) ?>"><?= e($mensaje['texto']) ?></p>
    <?php endif; ?>

    <form action="agregar.php" method="POST" class="formulario">
        <input
            type="text"
            name="tarea"
            placeholder="¿Qué tienes que hacer?"
            maxlength="255"
            autocomplete="off"
            <?= $editando ? '' : 'autofocus' ?>
            required>
        <button type="submit" class="boton boton--agregar">Agregar</button>
    </form>

    <?php if ($total > 0): ?>
        <div class="resumen">
            <span><strong><?= $total ?></strong> en total</span>
            <span><strong><?= $pendientes ?></strong> pendientes</span>
            <span><strong><?= $completadas ?></strong> completadas</span>
        </div>
    <?php endif; ?>

    <?php if ($total === 0): ?>

        <p class="vacio">Todavía no hay tareas. ¡Agrega la primera!</p>

    <?php else: ?>

        <ul class="lista">
            <?php foreach ($tareas as $tarea): ?>
                <li class="tarea <?= $tarea['completada'] ? 'tarea--completada' : '' ?>">

                    <?php if ($editando === (int) $tarea['id']): ?>

                        <!-- Modo edición: el texto se cambia por un campo -->
                        <form action="editar.php" method="POST" class="edicion">
                            <input type="hidden" name="id" value="<?= (int) $tarea['id'] ?>">
                            <input
                                type="text"
                                name="tarea"
                                value="<?= e($tarea['tarea']) ?>"
                                maxlength="255"
                                autocomplete="off"
                                autofocus
                                required>
                            <button type="submit" class="boton boton--guardar">Guardar</button>
                            <a href="index.php" class="boton boton--cancelar">Cancelar</a>
                        </form>

                    <?php else: ?>

                        <form action="completar.php" method="POST" class="tarea__accion">
                            <input type="hidden" name="id" value="<?= (int) $tarea['id'] ?>">
                            <button
                                type="submit"
                                class="marca"
                                title="<?= $tarea['completada'] ? 'Marcar como pendiente' : 'Marcar como completada' ?>">
                                <?= $tarea['completada'] ? '✔' : '' ?>
                            </button>
                        </form>

                        <span class="tarea__texto"><?= e($tarea['tarea']) ?></span>

                        <a
                            href="index.php?editar=<?= (int) $tarea['id'] ?>"
                            class="boton boton--editar"
                            title="Editar tarea">✎</a>

                        <form
                            action="borrar.php"
                            method="POST"
                            class="tarea__accion"
                            onsubmit="return confirm('¿Seguro que quieres borrar esta tarea?');">
                            <input type="hidden" name="id" value="<?= (int) $tarea['id'] ?>">
                            <button type="submit" class="boton boton--borrar" title="Borrar tarea">✕</button>
                        </form>

                    <?php endif; ?>

                </li>
            <?php endforeach; ?>
        </ul>

    <?php endif; ?>

</main>

<footer class="pie">Hecho con PHP y MySQL</footer>

</body>
</html>

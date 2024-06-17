<?php
require 'functions.php';

$permisos = ['Administrador','Profesor'];
permisos($permisos);

// Consulta los alumnos para el listado de alumnos
$alumnos = $conn->prepare("SELECT a.id, a.num_lista, a.nombres, a.apellidos, a.genero, b.nombre as grado, c.nombre as seccion FROM alumnos AS a INNER JOIN grados AS b ON a.id_grado = b.id INNER JOIN secciones AS c ON a.id_seccion = c.id ORDER BY a.apellidos");
$alumnos->execute();
$alumnos = $alumnos->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Alumnos | Registro de Notas</title>
    <meta name="description" content="Registro de Notas - Guaman Poma de Ayala" />
    <link rel="stylesheet" href="css/styleListadoAlumnos.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
<header class="header">
    <div class="container">
        <h1>Registro de Notas - Guaman Poma de Ayala"</h1>
        <h3>Usuario: <?php echo htmlspecialchars($_SESSION["username"]); ?></h3>
    </div>
</header>
<nav class="navbar">
    <div class="container">
        <ul>
            <li class="active"><a href="inicio.view.php"><i class="fas fa-home"></i> Inicio</a></li>
            <li><a href="alumnos.view.php"><i class="fas fa-user-graduate"></i> Registro de Alumnos</a></li>
            <li><a href="listadoalumnos.view.php"><i class="fas fa-list"></i> Listado de Alumnos</a></li>
            <li><a href="notas.view.php"><i class="fas fa-book"></i> Registro de Notas</a></li>
            <li><a href="listadonotas.view.php"><i class="fas fa-search"></i> Consulta de Notas</a></li>
            <li><a href="notas_predichas.view.php"><i class="fas fa-search"></i> Predicciones</a></li>
            <li class="right"><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Salir</a></li>
        </ul>
    </div>
</nav>
<main class="main-content">
    <div class="container">
        <div class="panel">
            <h4>Listado de Alumnos</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th>No de lista</th>
                        <th>Apellidos</th>
                        <th>Nombres</th>
                        <th>Genero</th>
                        <th>Grado</th>
                        <th>Seccion</th>
                        <th>Editar</th>
                        <th>Eliminar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($alumnos as $alumno): ?>
                        <tr>
                            <td align="center"><?php echo htmlspecialchars($alumno['num_lista']); ?></td>
                            <td><?php echo htmlspecialchars($alumno['apellidos']); ?></td>
                            <td><?php echo htmlspecialchars($alumno['nombres']); ?></td>
                            <td align="center"><?php echo htmlspecialchars($alumno['genero']); ?></td>
                            <td align="center"><?php echo htmlspecialchars($alumno['grado']); ?></td>
                            <td align="center"><?php echo htmlspecialchars($alumno['seccion']); ?></td>
                            <td><a href="alumnoedit.view.php?id=<?php echo htmlspecialchars($alumno['id']); ?>">Editar</a></td>
                            <td><a href="alumnodelete.php?id=<?php echo htmlspecialchars($alumno['id']); ?>">Eliminar</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="buttons">
                <a class="btn-link" href="alumnos.view.php">Agregar Alumno</a>
            </div>
            <?php if (isset($_GET['err'])): ?>
                <div class="error">Error al almacenar el registro</div>
            <?php elseif (isset($_GET['info'])): ?>
                <div class="success">Registro almacenado correctamente!</div>
            <?php endif; ?>
        </div>
    </div>
</main>
</body>
</html>

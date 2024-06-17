<!DOCTYPE html>
<?php
require 'functions.php';
$permisos = ['Administrador', 'Profesor'];
permisos($permisos);
if (isset($_GET['id'])) {

    $id_alumno = $_GET['id'];

    $alumno = $conn->prepare("select * from alumnos where id = " . $id_alumno);
    $alumno->execute();
    $alumno = $alumno->fetch();

    //consulta las secciones
    $secciones = $conn->prepare("select * from secciones");
    $secciones->execute();
    $secciones = $secciones->fetchAll();

    //consulta de grados
    $grados = $conn->prepare("select * from grados");
    $grados->execute();
    $grados = $grados->fetchAll();

} else {
    Die('Ha ocurrido un error');
}
?>
<html>

<head>
    <title>Editar Alumno | Registro de Notas</title>
    <meta name="description" content="Registro de Notas - Guaman Poma de Ayala" />
    <link rel="stylesheet" href="css/styleEdidt.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body>
    <div class="header">
        <h1>Registro de Notas - Guaman Poma de Ayala</h1>
        <h3>Usuario: <?php echo $_SESSION["username"] ?></h3>
    </div>
   
<nav class="navbar">
    <style>
        .navbar {
            background-color: #333;
        }

        .navbar ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            
        }

        .navbar li {
            margin: 0;
        }

        .navbar li a {
            display: block;
            color: #f4f4f4;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
        }

        .navbar li a:hover,
        .navbar li a.active {
            background-color: #555;
        }

        .navbar .right {
            margin-left: auto; /* Mueve el último elemento a la derecha */
        }
    </style>
    <div class="container">
        <ul>
            <li class="active"><a href="inicio.view.php"><i class="fas fa-home"></i> Inicio</a></li>
            <li><a href="alumnos.view.php"><i class="fas fa-user-graduate"></i> Registro de Alumnos</a></li>
            <li><a href="listadoalumnos.view.php"><i class="fas fa-list"></i> Listado de Alumnos</a></li>
            <li><a href="notas.view.php"><i class="fas fa-book"></i> Registro de Notas</a></li>
            <li><a href="listadonotas.view.php"><i class="fas fa-search"></i> Consulta de Notas</a></li>
            <li class="right"><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Salir</a></li>
        </ul>
    </div>
</nav>

    <div class="body">
        <div class="panel">
            <h4>Edición de Alumnos</h4>
            <form method="post" class="form" action="procesaralumno.php">
                <!--colocamos un campo oculto que tiene el id del alumno-->
                <input type="hidden" value="<?php echo $alumno['id'] ?>" name="id">
                <label>Nombres</label><br>
                <input type="text" required name="nombres" value="<?php echo $alumno['nombres'] ?>" maxlength="45">
                <br>
                <label>Apellidos</label><br>
                <input type="text" required name="apellidos" value="<?php echo $alumno['apellidos'] ?>" maxlength="45">
                <br><br>
                <label>No de Lista</label><br>
                <input type="number" min="1" class="number" value="<?php echo $alumno['num_lista'] ?>" name="numlista">
                <br><br>
                <label>Sexo</label><br><input required type="radio" name="genero" <?php if ($alumno['genero'] == 'M') {
                                                                                            echo "checked";
                                                                                        } ?> value="M"> Masculino
                <input type="radio" name="genero" required value="F" <?php if ($alumno['genero'] == 'F') {
                                                                        echo "checked";
                                                                    } ?>> Femenino
                <br><br>
                <label>Grado</label><br>
                <select name="grado" required>
                    <?php foreach ($grados as $grado) : ?>
                        <option value="<?php echo $grado['id'] ?>" <?php if ($alumno['id_grado'] == $grado['id']) {
                                                                            echo "selected";
                                                                        } ?>><?php echo $grado['nombre'] ?></option>
                    <?php endforeach; ?>
                </select>
                <br><br>
                <label>Seccion</label><br>

                <?php foreach ($secciones as $seccion) : ?>
                    <input type="radio" name="seccion" <?php if ($alumno['id_seccion'] == $seccion['id']) {
                                                            echo "checked";
                                                        } ?> required value="<?php echo $seccion['id'] ?>">Seccion <?php echo $seccion['nombre'] ?>
                <?php endforeach; ?>

                <br><br>
                <button type="submit" name="modificar">Guardar Cambios</button> <a class="btn-link" href="listadoalumnos.view.php">Ver Listado</a>
                <br><br>
                <!--mostrando los mensajes que recibe a través de los parámetros en la URL-->
                <?php
                if (isset($_GET['err']))
                    echo '<span class="error">Error al editar el registro</span>';
                if (isset($_GET['info']))
                    echo '<span class="success">Registro modificado correctamente!</span>';
                ?>

            </form>
        </div>
    </div>

</body>

</html>

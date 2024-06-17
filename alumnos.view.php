<!DOCTYPE html>
<?php
require 'functions.php';
// Define quiénes tienen permiso en este archivo
$permisos = ['Administrador','Profesor','Padre'];
permisos($permisos);

// Consulta las secciones
$secciones = $conn->prepare("SELECT * FROM secciones");
$secciones->execute();
$secciones = $secciones->fetchAll();

// Consulta de grados
$grados = $conn->prepare("SELECT * FROM grados");
$grados->execute();
$grados = $grados->fetchAll();
?>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio | Registro de Notas</title>
    <meta name="description" content="Registro de Notas - Guaman Poma de Ayala" />
    <link rel="stylesheet" href="css/styleAlumnosWiew.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
<header class="header">
    <div class="container">
        <h1>Institución Educativa - Guaman Poma de Ayala</h1>
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
            <li class="right"><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Salir</a></li>
        </ul>
    </div>
</nav>

<main class="main-content">
    <div class="container">
        <div class="panel">
            <h4>Registro de Alumnos</h4>
            <form method="post" class="form" action="procesaralumno.php">
                <label>Nombres</label>
                <input type="text" required name="nombres" maxlength="45">
                
                <label>Apellidos</label>
                <input type="text" required name="apellidos" maxlength="45">
                
                <label>No de Lista</label>
                <input type="number" min="1" class="number" name="numlista">
                
                <label>Sexo</label>
                <label class="inline"><input required type="radio" name="genero" value="M"> Masculino</label>
                <label class="inline"><input type="radio" name="genero" required value="F"> Femenino</label>
                
                <label>Grado</label>
                <select name="grado" required>
                    <?php foreach ($grados as $grado):?>
                        <option value="<?php echo $grado['id'] ?>"><?php echo $grado['nombre'] ?></option>
                    <?php endforeach;?>
                </select>
                
                <label>Sección</label>
                <?php foreach ($secciones as $seccion):?>
                    <label class="inline"><input type="radio" name="seccion" required value="<?php echo $seccion['id'] ?>"> Sección <?php echo $seccion['nombre'] ?></label>
                <?php endforeach;?>
                
                <div class="buttons">
                    <button type="submit" name="insertar">Guardar</button>
                    <button type="reset">Limpiar</button>
                    <a class="btn-link" href="listadoalumnos.view.php">Ver Listado</a>
                </div>
                
                <?php
                if(isset($_GET['err'])) {
                    echo '<span class="error">Error al almacenar el registro</span>';
                }
                if(isset($_GET['info'])) {
                    echo '<span class="success">Registro almacenado correctamente!</span>';
                }
                ?>
            </form>
        </div>
    </div>
</main>


</body>
</html>

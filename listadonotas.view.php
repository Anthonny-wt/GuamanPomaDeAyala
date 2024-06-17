<!DOCTYPE html>
<?php
require 'functions.php';

$permisos = ['Administrador', 'Profesor', 'Padre'];
permisos($permisos);

// Consulta las materias
$materias = $conn->query("SELECT * FROM materias")->fetchAll(PDO::FETCH_ASSOC);

// Consulta de grados
$grados = $conn->query("SELECT * FROM grados")->fetchAll(PDO::FETCH_ASSOC);

// Consulta las secciones
$secciones = $conn->query("SELECT * FROM secciones")->fetchAll(PDO::FETCH_ASSOC);

// Inicialización de variables
$alumnos = [];
$promediototal = 0.0;
$num_eval = 0;

if (isset($_GET['consultar'])) {
    $id_materia = $_GET['materia'];
    $id_grado = $_GET['grado'];
    $id_seccion = $_GET['seccion'];

    // Extraer el número de evaluaciones para la materia seleccionada
    $num_eval = $conn->query("SELECT num_evaluaciones FROM materias WHERE id = $id_materia")->fetchColumn();

    // Consulta para obtener los datos de los alumnos y sus notas
    $sql_alumnos = "SELECT a.id, a.num_lista, a.apellidos, a.nombres, b.nota, b.observaciones, AVG(b.nota) AS promedio 
                    FROM alumnos AS a 
                    LEFT JOIN notas AS b ON a.id = b.id_alumno
                    WHERE a.id_grado = $id_grado AND a.id_seccion = $id_seccion
                    GROUP BY a.id";
    $alumnos = $conn->query($sql_alumnos)->fetchAll(PDO::FETCH_ASSOC);

    // Calcular el promedio total de todas las notas
    foreach ($alumnos as $alumno) {
        $promediototal += $alumno['promedio'];
    }
}

?>
<html>
<head>
    <title>Notas | Registro de Notas</title>
    <meta name="description" content="Registro de Notas - Guaman Poma de Ayala" />
    <link rel="stylesheet" href="css/styloListadoNotas.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="header">
    <h1>Registro de Notas - Guaman Poma de Ayala</h1>
    <h3>Usuario: <?php echo htmlspecialchars($_SESSION["username"]); ?></h3>
</div>

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

<div class="body">
    <div class="panel">
        <h3>Consulta de Notas</h3>
        <?php if (!isset($_GET['consultar'])) : ?>
            <p>Seleccione el grado, la materia y la sección</p>
            <form method="get" class="form" action="listadonotas.view.php">
                <label>Seleccione el Grado</label><br>
                <select name="grado" required>
                    <?php foreach ($grados as $grado) : ?>
                        <option value="<?php echo $grado['id'] ?>"><?php echo $grado['nombre'] ?></option>
                    <?php endforeach; ?>
                </select>
                <br><br>
                <label>Seleccione la Materia</label><br>
                <select name="materia" required>
                    <?php foreach ($materias as $materia) : ?>
                        <option value="<?php echo $materia['id'] ?>"><?php echo $materia['nombre'] ?></option>
                    <?php endforeach; ?>
                </select>
                <br><br>
                <label>Seleccione la Sección</label><br><br>
                <?php foreach ($secciones as $seccion) : ?>
                    <input type="radio" name="seccion" required value="<?php echo $seccion['id'] ?>">Sección <?php echo $seccion['nombre'] ?>
                <?php endforeach; ?>
                <br><br>
                <button type="submit" name="consultar" value="1">Consultar Notas</button>
                <br><br>
            </form>
        <?php else : ?>
            <br>
            <a href="listadonotas.view.php"><strong><< Volver</strong></a>
            <br><br>
            <table class="table" cellpadding="5" cellspacing="0" border="1">
                <tr style="background-color: #f2f2f2;">
                    <th>No de lista</th>
                    <th>Apellidos</th>
                    <th>Nombres</th>
                    <?php for ($i = 1; $i <= $num_eval; $i++) : ?>
                        <th>Nota <?php echo $i ?></th>
                    <?php endfor; ?>
                    <th>Promedio</th>
                    <th>Fase</th>
                </tr>
                <?php foreach ($alumnos as $alumno) : ?>
                    <tr>
                        <td align="center"><?php echo $alumno['num_lista'] ?></td>
                        <td><?php echo $alumno['apellidos'] ?></td>
                        <td><?php echo $alumno['nombres'] ?></td>
                        <?php
                        // Consultar las notas del alumno
                        $notas = $conn->query("SELECT id, nota FROM notas WHERE id_alumno = " . $alumno['id'] . " AND id_materia = $id_materia")->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($notas as $nota) {
                            echo '<td align="center">' . $nota['nota'] . '</td>';
                        }
                        echo '<td align="center">' . number_format($alumno['promedio'], 2) . '</td>';
                        echo '<td>' . $alumno['observaciones'] . '</td>';
                        ?>
                    </tr>
                <?php endforeach; ?>
                <tr style="background-color: #f2f2f2;">
                    <td colspan="<?php echo $num_eval + 3; ?>"></td>
                    <td align="center"><?php echo number_format($promediototal / count($alumnos), 2) ?></td>
                </tr>
            </table>

            <!-- Gráfico de Promedio de Notas -->
            <canvas id="promedioNotasChart" width="800" height="400"></canvas>
            <script>
                var alumnosData = <?php echo json_encode($alumnos); ?>;
                var labels = alumnosData.map(alumno => alumno.num_lista);
                var promedios = alumnosData.map(alumno => alumno.promedio);

                var ctx = document.getElementById('promedioNotasChart').getContext('2d');
                var promedioNotasChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Promedio de Notas',
                            data: promedios,
                            backgroundColor: 'rgba(54, 162, 235, 0.6)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            </script>
        <?php endif; ?>
    </div>
</div>
</body>
</html>

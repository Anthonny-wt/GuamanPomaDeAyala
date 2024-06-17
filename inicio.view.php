<?php
require 'functions.php';
$permisos = ['Administrador', 'Profesor', 'Padre'];
permisos($permisos);

// Consulta para obtener los datos de los alumnos por género
$alumnos_genero = $conn->prepare("SELECT a.genero, COUNT(*) as count FROM alumnos a GROUP BY a.genero");
$alumnos_genero->execute();
$alumnos_genero_data = $alumnos_genero->fetchAll(PDO::FETCH_ASSOC);

// Consulta para obtener los datos de los alumnos por grado
$alumnos_grado = $conn->prepare("SELECT b.nombre as grado, COUNT(*) as count FROM alumnos a INNER JOIN grados b ON a.id_grado = b.id GROUP BY b.nombre");
$alumnos_grado->execute();
$alumnos_grado_data = $alumnos_grado->fetchAll(PDO::FETCH_ASSOC);

// Consulta para obtener los datos de los alumnos por sección
$alumnos_seccion = $conn->prepare("SELECT c.nombre as seccion, COUNT(*) as count FROM alumnos a INNER JOIN secciones c ON a.id_seccion = c.id GROUP BY c.nombre");
$alumnos_seccion->execute();
$alumnos_seccion_data = $alumnos_seccion->fetchAll(PDO::FETCH_ASSOC);

// Preparar datos para los gráficos
function prepararDatos($data) {
    $labels = [];
    $counts = [];
    foreach ($data as $row) {
        $labels[] = $row['genero'] ?? $row['grado'] ?? $row['seccion'];
        $counts[] = $row['count'];
    }
    return [$labels, $counts];
}

list($labels_genero, $data_genero) = prepararDatos($alumnos_genero_data);
list($labels_grado, $data_grado) = prepararDatos($alumnos_grado_data);
list($labels_seccion, $data_seccion) = prepararDatos($alumnos_seccion_data);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio | Registro de Notas</title>
    <meta name="description" content="Registro de Notas - Guaman Poma de Ayala" />
    <link rel="stylesheet" href="css/inicio.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<header class="header">
    <div class="container">
        <h1>Institución Educativa - Guaman Poma de Ayala</h1>
        <h3>Usuario: <?php echo htmlspecialchars($_SESSION["username"]); ?></h3>
    </div>
</header>

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
            <li><a href="notas_predichas.view.php"><i class="fas fa-search"></i> Predicciones</a></li>
            <li class="right"><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Salir</a></li>
        </ul>
    </div>
</nav>
<main class="main-content">
<div class="container">
        <div class="panel">
            <h1 class="text-center">Guaman Poma de Ayala</h1>
            <?php if (isset($_GET['err'])): ?>
                <h3 class="error">ERROR: Usuario no autorizado</h3>
            <?php endif; ?>

            <!-- Gráfico de Alumnos por Género -->
            <div class="chart-container">
                <h2 class="text-center">Distribución de Alumnos por Género</h2>
                <canvas id="alumnosGeneroChart" width="400" height="200"></canvas>
            </div>

            <style>
                .chart-container {
                    max-width: 400px;
                    margin: 20px auto;
                }
            </style>

            <script>
                const ctxGenero = document.getElementById('alumnosGeneroChart').getContext('2d');
                const alumnosGeneroChart = new Chart(ctxGenero, {
                    type: 'pie',
                    data: {
                        labels: <?php echo json_encode($labels_genero); ?>,
                        datasets: [{
                            label: 'Número de Alumnos por Género',
                            data: <?php echo json_encode($data_genero); ?>,
                            backgroundColor: ['#ff6384', '#36a2eb'],
                            borderColor: ['#ff6384', '#36a2eb'],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        label += context.raw;
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            </script>

            <!-- Gráfico de Alumnos por Grado -->
            <div class="chart-container">
                <h2 class="text-center">Distribución de Alumnos por Grado</h2>
                <canvas id="alumnosGradoChart" width="400" height="200"></canvas>
            </div>

            <!-- Gráfico de Alumnos por Sección -->
            <div class="chart-container">
                <h2 class="text-center">Distribución de Alumnos por Sección</h2>
                <canvas id="alumnosSeccionChart" width="400" height="200"></canvas>
            </div>

            <style>
                .chart-container {
                    max-width: 400px;
                    margin: 20px auto;
                }
            </style>
       <style>
                .charts-container {
                    display: flex;
                    justify-content: space-around;
                    margin-bottom: 20px;
                }

                .chart-container {
                    max-width: 400px;
                    margin: 0 auto 20px auto;
                }
            </style>

            <script>
                // Gráfico de Alumnos por Grado
                const ctxGrado = document.getElementById('alumnosGradoChart').getContext('2d');
                const alumnosGradoChart = new Chart(ctxGrado, {
                    type: 'pie',
                    data: {
                        labels: <?php echo json_encode($labels_grado); ?>,
                        datasets: [{
                            label: 'Número de Alumnos por Grado',
                            data: <?php echo json_encode($data_grado); ?>,
                            backgroundColor: ['#ff6384', '#36a2eb', '#ffcd56', '#4bc0c0', '#9966ff', '#ff9f40'],
                            borderColor: ['#ff6384', '#36a2eb', '#ffcd56', '#4bc0c0', '#9966ff', '#ff9f40'],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        label += context.raw;
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });

                // Gráfico de Alumnos por Sección
                const ctxSeccion = document.getElementById('alumnosSeccionChart').getContext('2d');
                const alumnosSeccionChart = new Chart(ctxSeccion, {
                    type: 'pie',
                    data: {
                        labels: <?php echo json_encode($labels_seccion); ?>,
                        datasets: [{
                            label: 'Número de Alumnos por Sección',
                            data: <?php echo json_encode($data_seccion); ?>,
                            backgroundColor: ['#ff6384', '#36a2eb', '#ffcd56', '#4bc0c0', '#9966ff', '#ff9f40'],
                            borderColor: ['#ff6384', '#36a2eb', '#ffcd56', '#4bc0c0', '#9966ff', '#ff9f40'],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        label += context.raw;
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            </script>
            
        </div>
    </div>
</main>

</body>
</html>

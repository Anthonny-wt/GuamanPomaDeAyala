<!DOCTYPE html>
<?php
require 'functions.php';

$permisos = ['Administrador', 'Profesor', 'Padre'];
permisos($permisos);

// Consulta para obtener los datos de los alumnos con buena nota y mala nota
$sql_buena_nota = "SELECT a.id, a.num_lista, a.apellidos, a.nombres, b.nota, AVG(b.nota) AS promedio 
                   FROM alumnos AS a 
                   LEFT JOIN notas AS b ON a.id = b.id_alumno
                   GROUP BY a.id
                   HAVING AVG(b.nota) >= 10
                   ORDER BY AVG(b.nota) DESC
                   LIMIT 10"; // Los 10 primeros con mejor promedio

$stmt_buena_nota = $conn->prepare($sql_buena_nota);
$stmt_buena_nota->execute();
$alumnos_buena_nota = $stmt_buena_nota->fetchAll(PDO::FETCH_ASSOC);

$sql_mala_nota = "SELECT a.id, a.num_lista, a.apellidos, a.nombres, b.nota, AVG(b.nota) AS promedio 
                  FROM alumnos AS a 
                  LEFT JOIN notas AS b ON a.id = b.id_alumno
                  GROUP BY a.id
                  HAVING AVG(b.nota) < 10
                  ORDER BY AVG(b.nota) ASC
                  LIMIT 10"; // Los 10 primeros con peor promedio

$stmt_mala_nota = $conn->prepare($sql_mala_nota);
$stmt_mala_nota->execute();
$alumnos_mala_nota = $stmt_mala_nota->fetchAll(PDO::FETCH_ASSOC);

$sql_potencial_nota = "SELECT a.id, a.num_lista, a.apellidos, a.nombres, b.nota, AVG(b.nota) AS promedio 
                      FROM alumnos AS a 
                      LEFT JOIN notas AS b ON a.id = b.id_alumno
                      WHERE b.nota >= 10 AND b.nota <= 13
                      GROUP BY a.id
                      HAVING AVG(b.nota) < 14
                      ORDER BY AVG(b.nota) ASC
                      LIMIT 10";

$stmt_potencial_nota = $conn->prepare($sql_potencial_nota);
$stmt_potencial_nota->execute();
$alumnos_potencial_nota = $stmt_potencial_nota->fetchAll(PDO::FETCH_ASSOC);

try {
    // Consulta para obtener los alumnos con las notas más altas
    $stmt = $conn->prepare("SELECT a.id, a.num_lista, a.apellidos, a.nombres, b.nota 
                            FROM alumnos AS a 
                            LEFT JOIN notas AS b ON a.id = b.id_alumno
                            WHERE b.nota IS NOT NULL
                            ORDER BY b.nota DESC
                            LIMIT 5"); // Obtener los primeros 5 alumnos con las notas más altas
    $stmt->execute();
    $top_alumnos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
try {
    // Consulta para obtener los alumnos con las notas más bajas
    $stmt = $conn->prepare("SELECT a.id, a.num_lista, a.apellidos, a.nombres, b.nota 
                            FROM alumnos AS a 
                            LEFT JOIN notas AS b ON a.id = b.id_alumno
                            WHERE b.nota IS NOT NULL
                            ORDER BY b.nota ASC
                            LIMIT 5"); // Obtener los primeros 5 alumnos con las notas más bajas
    $stmt->execute();
    $peores_alumnos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio | Registro de Notas</title>
    <meta name="description" content="Registro de Notas - Guaman Poma de Ayala" />
    <link rel="stylesheet" href="css/prediciones.css">
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


            <!-- Gráfico de Ranking de Alumnos con Buena Nota -->
            <div class="chart-container">
                <h2 class="text-center">Ranking de Alumnos con Buena Nota</h2>
                <canvas id="buenaNotaChart" width="400" height="200"></canvas>
            </div>

            <!-- Gráfico de Ranking de Alumnos con Mala Nota -->
            <div class="chart-container">
                <h2 class="text-center">Ranking de Alumnos con Mala Nota</h2>
                <canvas id="malaNotaChart" width="400" height="200"></canvas>
            </div>

            <style>
                .chart-container {
                    max-width: 400px;
                    margin: 20px auto;
                }
            </style>

            <script>
                // Datos para el gráfico de Buena Nota
                var alumnosBuenaNota = <?php echo json_encode($alumnos_buena_nota); ?>;
                var labelsBuenaNota = alumnosBuenaNota.map(alumno => alumno.num_lista);
                var promediosBuenaNota = alumnosBuenaNota.map(alumno => alumno.promedio);

                const ctxBuenaNota = document.getElementById('buenaNotaChart').getContext('2d');
                const buenaNotaChart = new Chart(ctxBuenaNota, {
                    type: 'bar',
                    data: {
                        labels: labelsBuenaNota,
                        datasets: [{
                            label: 'Promedio de Nota',
                            data: promediosBuenaNota,
                            backgroundColor: 'rgba(54, 162, 235, 0.6)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Datos para el gráfico de Mala Nota
                var alumnosMalaNota = <?php echo json_encode($alumnos_mala_nota); ?>;
                var labelsMalaNota = alumnosMalaNota.map(alumno => alumno.num_lista);
                var promediosMalaNota = alumnosMalaNota.map(alumno => alumno.promedio);

                const ctxMalaNota = document.getElementById('malaNotaChart').getContext('2d');
                const malaNotaChart = new Chart(ctxMalaNota, {
                    type: 'bar',
                    data: {
                        labels: labelsMalaNota,
                        datasets: [{
                            label: 'Promedio de Nota',
                            data: promediosMalaNota,
                            backgroundColor: 'rgba(255, 99, 132, 0.6)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
                
            </script>
            
           <!-- Gráfico de Top Alumnos -->
<div class="chart-container">
    <h2 class="text-center">Top 5 Alumnos con Mejores Notas</h2>
    <div class="chart-with-legends">
        <canvas id="topNotasChart" width="1000" height="500"></canvas>
        <div class="legends">
            <h3>Predicion de google Colab</h3>
            <div class="legend-item">
                <span class="color-box" style="background-color: #4CAF50;"></span>
                <span>Excelente (90-100)</span>
            </div>
            <div class="legend-item">
                <span class="color-box" style="background-color: #FFC107;"></span>
                <span>Bueno (80-89)</span>
            </div>
            <div class="legend-item">
                <span class="color-box" style="background-color: #FF5722;"></span>
                <span>Regular (70-79)</span>
            </div>
            <div class="legend-item">
                <span class="color-box" style="background-color: #F44336;"></span>
                <span>Deficiente (&lt;70)</span>
            </div>
        </div>
    </div>
</div>

<style>
    .chart-container {
        max-width: 1000px;
        margin: 20px auto;
    }
    .chart-with-legends {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .legends {
        flex: 0 0 30%;
        padding: 20px;
        background-color: #f2f2f2;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    .legend-item {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }
    .color-box {
        display: inline-block;
        width: 20px;
        height: 20px;
        margin-right: 10px;
        border-radius: 5px;
    }
</style>

<script>
    // Datos de ejemplo (deberían ser los datos reales obtenidos de la base de datos)
    var labels = <?php echo json_encode(array_map(function($alumno) {
        return $alumno['num_lista'] . ' - ' . $alumno['nombres'] . ' ' . $alumno['apellidos'];
    }, $top_alumnos)); ?>;
    var data = <?php echo json_encode(array_column($top_alumnos, 'nota')); ?>;

    // Colores basados en el rango de notas (0-20)
    var backgroundColors = [];
    var borderColors = [];
    data.forEach(function(nota) {
        if (nota >= 18) {
            backgroundColors.push('#4CAF50'); // Verde para excelente
            borderColors.push('#4CAF50');
        } else if (nota >= 15) {
            backgroundColors.push('#FFC107'); // Amarillo para bueno
            borderColors.push('#FFC107');
        } else if (nota >= 12) {
            backgroundColors.push('#FF5722'); // Naranja para regular
            borderColors.push('#FF5722');
        } else {
            backgroundColors.push('#F44336'); // Rojo para deficiente
            borderColors.push('#F44336');
        }
    });

    // Dibujar el gráfico
    var ctx = document.getElementById('topNotasChart').getContext('2d');
    var topNotasChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Notas',
                data: data,
                backgroundColor: backgroundColors,
                borderColor: borderColors,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        callback: function(value) {
                            return value; // Mostrar números enteros en el eje Y
                        }
                    }
                }
            }
        }
    });
</script>

            <!-- Gráfico de Peores Alumnos -->
            <div class="chart-container">
                <h2 class="text-center">Top 5 Alumnos con Peores Notas</h2>
                <div class="chart-with-legends">
                    <canvas id="peoresNotasChart" width="1000" height="500"></canvas>
                    <div class="legends">
                        <h3>Predicion de google Colab</h3>
                        <div class="legend-item">
                            <span class="color-box" style="background-color: #F44336;"></span>
                            <span>Deficiente (&lt;70)</span>
                        </div>
                        <div class="legend-item">
                            <span class="color-box" style="background-color: #FF5722;"></span>
                            <span>Regular (70-79)</span>
                        </div>
                        <div class="legend-item">
                            <span class="color-box" style="background-color: #FFC107;"></span>
                            <span>Bueno (80-89)</span>
                        </div>
                        <div class="legend-item">
                            <span class="color-box" style="background-color: #4CAF50;"></span>
                            <span>Excelente (90-100)</span>
                        </div>
                    </div>
                </div>
            </div>

            <style>
                .chart-container {
                    max-width: 1000px;
                    margin: 20px auto;
                }
                .chart-with-legends {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }
                .legends {
                    flex: 0 0 30%;
                    padding: 20px;
                    background-color: #f2f2f2;
                    border-radius: 5px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                }
                .legend-item {
                    display: flex;
                    align-items: center;
                    margin-bottom: 10px;
                }
                .color-box {
                    display: inline-block;
                    width: 20px;
                    height: 20px;
                    margin-right: 10px;
                    border-radius: 5px;
                }
            </style>
            

            <script>
                // Datos de ejemplo (deberían ser los datos reales obtenidos de la base de datos)
                var labels = <?php echo json_encode(array_map(function($alumno) {
                    return $alumno['num_lista'] . ' - ' . $alumno['nombres'] . ' ' . $alumno['apellidos'];
                }, $peores_alumnos)); ?>;
                var data = <?php echo json_encode(array_column($peores_alumnos, 'nota')); ?>;

                // Colores basados en el rango de notas (ejemplo)
                var backgroundColors = [];
                var borderColors = [];
                data.forEach(function(nota) {
                    if (nota < 70) {
                        backgroundColors.push('#F44336'); // Rojo para deficiente
                        borderColors.push('#F44336');
                    } else if (nota < 80) {
                        backgroundColors.push('#FF5722'); // Naranja para regular
                        borderColors.push('#FF5722');
                    } else if (nota < 90) {
                        backgroundColors.push('#FFC107'); // Amarillo para bueno
                        borderColors.push('#FFC107');
                    } else {
                        backgroundColors.push('#4CAF50'); // Verde para excelente
                        borderColors.push('#4CAF50');
                    }
                });

                // Dibujar el gráfico
                var ctx = document.getElementById('peoresNotasChart').getContext('2d');
                var peoresNotasChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Notas',
                            data: data,
                            backgroundColor: backgroundColors,
                            borderColor: borderColors,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 10,
                                    callback: function(value) {
                                        return value; // Mostrar números enteros en el eje Y
                                    }
                                }
                            }
                        }
                    }
                });
            </script>
             <h2 class="text-center">Alumnos Potenciales</h2>
    <div class="chart-container">
        <canvas id="potencialChart" width="800" height="400"></canvas>
    </div>
</div>

<script>
    // Datos de ejemplo (deberían ser los datos reales obtenidos de la base de datos)
    var nombres = <?php echo json_encode(array_map(function($alumno) {
        return $alumno['num_lista'] . ' - ' . $alumno['nombres'] . ' ' . $alumno['apellidos'];
    }, $alumnos_potencial_nota)); ?>;
    var promedios = <?php echo json_encode(array_column($alumnos_potencial_nota, 'promedio')); ?>;

    // Colores personalizados para el gráfico
    var backgroundColors = [];
    var borderColors = [];
    promedios.forEach(function(promedio) {
        if (promedio >= 12) {
            backgroundColors.push('#4CAF50'); // Verde para promedios altos
            borderColors.push('#4CAF50');
        } else if (promedio >= 10.5) {
            backgroundColors.push('#FFC107'); // Amarillo para promedios medios
            borderColors.push('#FFC107');
        } else {
            backgroundColors.push('#F44336'); // Rojo para promedios bajos
            borderColors.push('#F44336');
        }
    });

    // Dibujar el gráfico
    var ctx = document.getElementById('potencialChart').getContext('2d');
    var potencialChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: nombres,
            datasets: [{
                label: 'Promedio de Notas',
                data: promedios,
                backgroundColor: backgroundColors,
                borderColor: borderColors,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        callback: function(value) {
                            return value; // Mostrar números enteros en el eje Y
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

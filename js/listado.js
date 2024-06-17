<?php
if(isset($_GET['consultar'])){
    // Configuración de datos para el gráfico
    $alumnos = <?php echo json_encode($alumnos); ?>;
    $labels = $alumnos.map(alumno => alumno.num_lista);
    $promedios = $alumnos.map(alumno => alumno.promedio);

    // Dibujar el gráfico
    var ctx = document.getElementById('notasChart').getContext('2d');
    var notasChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($labels); ?>,
            datasets: [{
                label: 'Promedio de Notas',
                data: <?php echo json_encode($promedios); ?>,
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
}
?>
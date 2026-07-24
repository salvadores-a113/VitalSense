<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Consumo por Sala</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="css/styles.css" rel="stylesheet">
    <!-- En index.html -->
    <script src="script.js"></script>

    <!-- En dispositivos.html -->
    <script src="dispositivos.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Reporte de Consumo Energético</h1>
        <p class="text-muted text-center mb-4">Análisis del consumo por sala y dispositivos</p>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">Consumo Total por Sala (kWh)</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="graficoBarras" height="300"></canvas>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">Distribución por Salas</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="graficoPastel" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0">Detalle de Consumo</h5>
                <small id="ultimaActualizacion" class="text-white-50"></small>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="tablaConsumos">
                        <thead class="table-dark">
                            <tr>
                                <th>Sala</th>
                                <th>Dispositivos</th>
                                <th>Potencia Total (W)</th>
                                <th>Consumo (kWh)</th>
                                <th>Costo Aprox.</th>
                            </tr>
                        </thead>
                        <tbody id="tablaDatos">
                            <!-- Datos se cargarán con JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Variables para los gráficos
        let barChart, pieChart;
        
        // Función para formatear números
        const formatNumber = (num) => {
            return num ? Number(num).toFixed(2) : '0.00';
        };
        
        // Función para formatear moneda
        const formatCurrency = (num) => {
            return num ? '$' + Number(num).toFixed(2) : '$0.00';
        };
        
        // Cargar y mostrar los datos
        async function cargarDatos() {
            try {
                // Mostrar estado de carga
                document.getElementById('tablaDatos').innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                            <p class="mt-2">Cargando datos...</p>
                        </td>
                    </tr>`;
                
                const response = await fetch('get_consumo_salas.php');
                const data = await response.json();
                
                if (!data.success) {
                    throw new Error(data.error || 'Error desconocido al cargar los datos');
                }
                
                // Actualizar la tabla
                mostrarDatosEnTabla(data.data);
                
                // Actualizar gráficos
                actualizarGraficos(data.data);
                
                // Mostrar última actualización
                document.getElementById('ultimaActualizacion').textContent = 
                    `Última actualización: ${data.last_updated || new Date().toLocaleString()}`;
                
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('tablaDatos').innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center text-danger">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            Error al cargar los datos: ${error.message}
                        </td>
                    </tr>`;
            }
        }
        
        // Mostrar datos en la tabla
        function mostrarDatosEnTabla(data) {
            let html = '';
            
            data.forEach(sala => {
                html += `
                    <tr>
                        <td>${sala.sala}</td>
                        <td>${sala.cantidad_dispositivos}</td>
                        <td>${formatNumber(sala.potencia_total)}</td>
                        <td>${formatNumber(sala.consumo_total_kwh)}</td>
                        <td>${formatCurrency(sala.costo_aproximado)}</td>
                    </tr>`;
            });
            
            document.getElementById('tablaDatos').innerHTML = html;
        }
        
        // Actualizar gráficos
        function actualizarGraficos(data) {
            const ctxBar = document.getElementById('graficoBarras').getContext('2d');
            const ctxPie = document.getElementById('graficoPastel').getContext('2d');
            
            // Preparar datos
            const labels = data.map(item => item.sala);
            const consumos = data.map(item => item.consumo_total_kwh || 0);
            const colores = generarColores(data.length);
            
            // Destruir gráficos anteriores si existen
            if (barChart) barChart.destroy();
            if (pieChart) pieChart.destroy();
            
            // Gráfico de barras
            barChart = new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Consumo (kWh)',
                        data: consumos,
                        backgroundColor: colores,
                        borderColor: colores.map(c => c.replace('0.7', '1')),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.dataset.label}: ${formatNumber(context.raw)}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: { display: true, text: 'kWh' }
                        }
                    }
                }
            });
            
            // Gráfico de pastel
            pieChart = new Chart(ctxPie, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: consumos,
                        backgroundColor: colores,
                        borderColor: '#fff',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'right' },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const total = consumos.reduce((a, b) => a + b, 0);
                                    const value = context.raw || 0;
                                    const percentage = Math.round((value / total) * 100);
                                    return `${context.label}: ${formatNumber(value)} kWh (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }
        
        // Generar colores dinámicos
        function generarColores(count) {
            const colores = [];
            const hueStep = 360 / count;
            
            for (let i = 0; i < count; i++) {
                const hue = i * hueStep;
                colores.push(`hsla(${hue}, 70%, 60%, 0.7)`);
            }
            
            return colores;
        }
        
        // Cargar datos al iniciar
        document.addEventListener('DOMContentLoaded', cargarDatos);
        
        // Opcional: Recargar cada 5 minutos
        setInterval(cargarDatos, 5 * 60 * 1000);
    </script>
</body>
</html>

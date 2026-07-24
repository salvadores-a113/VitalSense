<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
redirectIfNotLoggedIn();

// Obtener datos del paciente
$stmt = $pdo->prepare("SELECT * FROM patients WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$patient = $stmt->fetch();

// Obtener últimos registros de ritmo cardíaco
$heartRateStmt = $pdo->prepare("SELECT * FROM heart_rate WHERE patient_id = ? ORDER BY timestamp DESC LIMIT 10");
$heartRateStmt->execute([$_SESSION['user_id']]);
$heartRates = $heartRateStmt->fetchAll();

// Obtener últimas anomalías
$anomaliesStmt = $pdo->prepare("SELECT * FROM anomalies WHERE patient_id = ? ORDER BY timestamp DESC LIMIT 5");
$anomaliesStmt->execute([$_SESSION['user_id']]);
$anomalies = $anomaliesStmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/howler/2.2.3/howler.min.js"></script>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container">
        <h1>Bienvenido, <?php echo htmlspecialchars($patient['full_name']); ?></h1>
        
        <div class="dashboard-grid">
            <!-- Información del Paciente -->
            <div class="card patient-info">
                <h2>Tu Información</h2>
                <p><strong>Edad:</strong> <?php echo htmlspecialchars($patient['age']); ?></p>
                <p><strong>Peso:</strong> <?php echo htmlspecialchars($patient['weight']); ?> kg</p>
                <p><strong>Altura:</strong> <?php echo htmlspecialchars($patient['height']); ?> m</p>
                <p><strong>Enfermedad:</strong> <?php echo htmlspecialchars($patient['disease'] ?: 'Ninguna'); ?></p>
                <p><strong>Trastorno:</strong> <?php echo htmlspecialchars($patient['disorder'] ?: 'Ninguno'); ?></p>
                <a href="profile.php" class="btn">Editar Perfil</a>
            </div>
            
            <!-- Monitor de Ritmo Cardíaco -->
            <div class="card heart-rate">
                <h2>Monitor de Ritmo Cardíaco</h2>
                <div class="heart-rate-display">
                    <canvas id="heartRateChart"></canvas>
                </div>
                <div class="heart-container">
                    <div class="pulse-effect"></div>
                    <div class="heart" id="heart"></div>
                    <div class="current-rate">
                        <span id="currentRate">--</span> <small>bpm</small>
                    </div>
                    <div class="heart-beat-info" id="heartBeatInfo">Esperando datos...</div>
                </div>
                <button id="simulateHeartRate" class="btn">Simular Ritmo</button>
                <button id="stopSimulation" class="btn btn-danger" style="display:none; background-color: #e74c3c;">Detener Simulación</button>
            </div>
            
            <!-- Novedades/Anomalías -->
            <div class="card anomalies">
                <h2>Novedades y Movimientos Anormales</h2>
                <?php if (empty($anomalies)): ?>
                    <p>No se han detectado anomalías recientes.</p>
                <?php else: ?>
                    <ul class="anomaly-list">
                        <?php foreach ($anomalies as $anomaly): ?>
                            <li class="anomaly severity-<?php echo htmlspecialchars($anomaly['severity']); ?>">
                                <span class="timestamp"><?php echo date('d/m/Y H:i', strtotime($anomaly['timestamp'])); ?></span>
                                <span class="description"><?php echo htmlspecialchars($anomaly['description']); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                <button id="simulateAnomaly" class="btn">Simular Anomalía</button>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <!-- Audio para el latido (usando Howler.js) -->
    <script>
        // Configuración del sonido del latido
        const heartBeatSound = new Howl({
            src: ['https://assets.mixkit.co/sfx/preview/mixkit-single-heartbeat-965.mp3'],
            volume: 0.5
        });
        
        // Variables de control
        let simulationInterval;
        let currentSimulatedRate = 0;
    </script>
    
    <script>
        // Datos para el gráfico
        const heartRateData = {
            labels: <?php echo json_encode(array_map(function($hr) { 
                return date('H:i', strtotime($hr['timestamp'])); 
            }, $heartRates)); ?>,
            datasets: [{
                label: 'Ritmo Cardíaco (bpm)',
                data: <?php echo json_encode(array_column($heartRates, 'rate')); ?>,
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                tension: 0.4,
                fill: true
            }]
        };

        // Configuración del gráfico
        const config = {
            type: 'line',
            data: heartRateData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `Ritmo: ${context.parsed.y} bpm`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        suggestedMin: 50,
                        suggestedMax: 120,
                        ticks: {
                            callback: function(value) {
                                return value + ' bpm';
                            }
                        }
                    }
                }
            },
        };

        // Inicializar el gráfico
        const heartRateChart = new Chart(
            document.getElementById('heartRateChart'),
            config
        );

        // Función para simular ritmo cardíaco
        function simulateHeartRate() {
            // Generar un ritmo cardíaco aleatorio entre 60-100 (normal) o 40-180 (anormal ocasional)
            const isNormal = Math.random() > 0.2; // 80% normal, 20% anormal
            currentSimulatedRate = isNormal 
                ? Math.floor(Math.random() * 40) + 60  // 60-100 bpm
                : Math.floor(Math.random() * 140) + 40; // 40-180 bpm
            
            updateHeartRateDisplay(currentSimulatedRate);
            
            // Mostrar información del ritmo
            const heartBeatInfo = document.getElementById('heartBeatInfo');
            let statusText = '';
            let statusClass = '';
            
            if (currentSimulatedRate < 60) {
                statusText = `Bradicardia (${currentSimulatedRate} bpm)`;
                statusClass = 'alert-danger';
            } else if (currentSimulatedRate > 100) {
                statusText = `Taquicardia (${currentSimulatedRate} bpm)`;
                statusClass = 'alert-danger';
            } else {
                statusText = `Ritmo normal (${currentSimulatedRate} bpm)`;
                statusClass = 'alert-success';
            }
            
            heartBeatInfo.innerHTML = `<span class="${statusClass}">${statusText}</span>`;
            
            // Enviar al servidor (simulación)
            fetch('simulate_heart_rate.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ rate: currentSimulatedRate })
            });
            
            // Agregar al gráfico
            const now = new Date();
            const timeString = now.getHours() + ':' + (now.getMinutes() < 10 ? '0' : '') + now.getMinutes();
            
            heartRateData.labels.push(timeString);
            heartRateData.datasets[0].data.push(currentSimulatedRate);
            
            if (heartRateData.labels.length > 15) {
                heartRateData.labels.shift();
                heartRateData.datasets[0].data.shift();
            }
            
            heartRateChart.update();
            
            // Si es anormal, simular una anomalía
            if (!isNormal) {
                setTimeout(() => {
                    simulateAnomaly(currentSimulatedRate);
                }, 1500);
            }
        }
        
        // Función para actualizar la visualización del ritmo
        function updateHeartRateDisplay(rate) {
            document.getElementById('currentRate').textContent = rate;
            
            // Calcular intervalo de latido (en ms)
            const beatInterval = 60000 / rate; // 60,000 ms = 1 minuto
            
            // Controlar la animación
            const heart = document.getElementById('heart');
            const pulseEffect = document.querySelector('.pulse-effect');
            
            heart.style.animationDuration = `${beatInterval}ms`;
            pulseEffect.style.animationDuration = `${beatInterval}ms`;
            
            // Reiniciar animaciones
            heart.style.animationPlayState = 'paused';
            pulseEffect.style.animationPlayState = 'paused';
            void heart.offsetWidth; // Truco para reiniciar la animación
            void pulseEffect.offsetWidth;
            
            heart.style.animationPlayState = 'running';
            pulseEffect.style.opacity = '0.7';
            pulseEffect.style.animationPlayState = 'running';
            
            // Controlar el sonido
            heartBeatSound.stop();
            heartBeatSound.rate(rate / 60); // Ajustar velocidad del sonido
            heartBeatSound.play();
            
            // Reproducir sonido en el intervalo correcto
            if (simulationInterval) {
                clearInterval(simulationInterval);
            }
            
            simulationInterval = setInterval(() => {
                heartBeatSound.stop();
                heartBeatSound.play();
            }, beatInterval);
        }
        
        // Función para simular anomalía
        function simulateAnomaly(rate) {
            let description = '';
            let severity = 'low';
            
            if (rate < 40) {
                description = "Ritmo cardíaco extremadamente bajo (Bradicardia severa)";
                severity = "high";
            } else if (rate < 60) {
                description = "Ritmo cardíaco bajo (Bradicardia)";
                severity = "medium";
            } else if (rate > 140) {
                description = "Ritmo cardíaco extremadamente alto (Taquicardia ventricular)";
                severity = "high";
            } else if (rate > 120) {
                description = "Ritmo cardíaco muy alto (Taquicardia supraventricular)";
                severity = "high";
            } else if (rate > 100) {
                description = "Ritmo cardíaco elevado (Taquicardia sinusal)";
                severity = "medium";
            } else {
                description = "Ritmo cardíaco irregular detectado";
                severity = "low";
            }
            
            // Enviar al servidor
            fetch('simulate_anomaly.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ 
                    description: description,
                    severity: severity
                })
            }).then(response => response.json())
              .then(data => {
                  if (data.success) {
                      // Mostrar notificación
                      const notification = document.createElement('div');
                      notification.className = `alert alert-${severity === 'high' ? 'danger' : (severity === 'medium' ? 'warning' : 'info')}`;
                      notification.textContent = `Nueva anomalía: ${description}`;
                      document.querySelector('.anomalies').prepend(notification);
                      
                      // Actualizar lista después de 1 segundo
                      setTimeout(() => {
                          location.reload();
                      }, 1000);
                  }
              });
        }
        
        // Event listeners
        document.getElementById('simulateHeartRate').addEventListener('click', function() {
            this.style.display = 'none';
            document.getElementById('stopSimulation').style.display = 'inline-block';
            
            // Iniciar simulación
            simulateHeartRate();
            
            // Cambiar cada 5-15 segundos
            const changeInterval = setInterval(simulateHeartRate, Math.random() * 10000 + 5000);
            
            // Botón para detener
            document.getElementById('stopSimulation').onclick = function() {
                clearInterval(changeInterval);
                clearInterval(simulationInterval);
                heartBeatSound.stop();
                
                document.getElementById('heart').style.animationPlayState = 'paused';
                document.querySelector('.pulse-effect').style.animationPlayState = 'paused';
                document.querySelector('.pulse-effect').style.opacity = '0';
                
                document.getElementById('simulateHeartRate').style.display = 'inline-block';
                this.style.display = 'none';
                
                document.getElementById('heartBeatInfo').textContent = 'Simulación detenida';
            };
        });
        
        document.getElementById('simulateAnomaly').addEventListener('click', function() {
            simulateAnomaly(Math.random() > 0.5 ? 45 : 130); // Simular bradicardia o taquicardia
        });
        
        // Iniciar simulación automáticamente al cargar (opcional)
        window.addEventListener('load', function() {
            setTimeout(() => {
                document.getElementById('simulateHeartRate').click();
            }, 1000);
        });
    </script>
</body>
</html>
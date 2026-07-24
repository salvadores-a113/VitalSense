document.addEventListener('DOMContentLoaded', function() {
    const nombreHabitacion = document.getElementById('nombre-habitacion');
    const listaDispositivos = document.getElementById('lista-dispositivos');
    const graficoConsumoAparatosCanvas = document.getElementById('grafico-consumo-aparatos');
    
    // Obtener el ID de la sala de la URL
    const params = new URLSearchParams(window.location.search);
    const salaId = params.get('sala_id');

    // Función para cargar dispositivos de la sala
    async function cargarDispositivosSala() {
        try {
            // Mostrar carga
            listaDispositivos.innerHTML = '<li>Cargando dispositivos...</li>';
            
            // Obtener datos de la API
            const response = await fetch(`api_dispositivos.php?sala_id=${salaId}`);
            const data = await response.json();
            
            if (data.error) {
                throw new Error(data.error);
            }
            
            // Mostrar nombre de la sala
            nombreHabitacion.textContent = data.nombre_sala || 'Sala Desconocida';
            
            // Mostrar lista de dispositivos
            if (data.dispositivos && data.dispositivos.length > 0) {
                listaDispositivos.innerHTML = '';
                data.dispositivos.forEach(dispositivo => {
                    const item = document.createElement('li');
                    item.innerHTML = `
                        <strong>${dispositivo.nombre}</strong>
                        <div>Marca: ${dispositivo.marca}</div>
                        <div>Modelo: ${dispositivo.modelo}</div>
                        <div>Consumo: ${dispositivo.consumo_watts}W</div>
                    `;
                    listaDispositivos.appendChild(item);
                });
                
                // Crear gráfico si hay dispositivos
                crearGraficoConsumo(data.dispositivos);
            } else {
                listaDispositivos.innerHTML = '<li>No hay dispositivos registrados en esta sala.</li>';
            }
        } catch (error) {
            console.error('Error:', error);
            listaDispositivos.innerHTML = `<li class="error">Error al cargar dispositivos: ${error.message}</li>`;
        }
    }

    // Función para crear el gráfico de consumo
    function crearGraficoConsumo(dispositivos) {
        const nombres = dispositivos.map(d => d.nombre);
        const consumos = dispositivos.map(d => d.consumo_watts / 1000); // Convertir W a kW
        
        new Chart(graficoConsumoAparatosCanvas, {
            type: 'bar',
            data: {
                labels: nombres,
                datasets: [{
                    label: 'Consumo (kW)',
                    data: consumos,
                    backgroundColor: dispositivos.map((_, i) => 
                        `hsl(${(i * 360 / dispositivos.length)}, 70%, 60%)`),
                    borderColor: dispositivos.map((_, i) => 
                        `hsl(${(i * 360 / dispositivos.length)}, 70%, 40%)`),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.dataset.label}: ${context.raw.toFixed(2)} kW`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Consumo (kW)'
                        }
                    }
                }
            }
        });
    }

    // Iniciar carga
    if (salaId) {
        cargarDispositivosSala();
    } else {
        listaDispositivos.innerHTML = '<li class="error">No se especificó una sala.</li>';
    }
});
document.addEventListener('DOMContentLoaded', function() {
    const planoCasa = document.getElementById('plano-casa');
    const graficoConsumoBarrasCanvas = document.getElementById('grafico-consumo-barras');
    const graficoConsumoLineasCanvas = document.getElementById('grafico-consumo-lineas');
    
    let graficoConsumoBarras, graficoConsumoLineas;

    // Función para cargar datos de salas
    async function cargarDatosSalas() {
        try {
            const response = await fetch('api_salas.php');
            const data = await response.json();
            
            if (data.error) {
                throw new Error(data.error);
            }
            
            // Crear indicadores en el plano
            data.salas.forEach(sala => {
                crearIndicadorSala(sala);
            });
            
            // Crear gráficos
            crearGraficos(data.salas);
            
            // Actualizar periódicamente
            setInterval(actualizarDatos, 5000);
            
        } catch (error) {
            console.error('Error al cargar datos:', error);
        }
    }

    // Función para crear indicadores en el plano
    function crearIndicadorSala(sala) {
        // Aquí debes ajustar las coordenadas según tu plano SVG
        // Esto es un ejemplo - necesitarás mapear tus salas a posiciones en el plano
        const posiciones = {
            '1': { x: 210, y: 130 }, // Sala
            '2': { x: 325, y: 125 }, // Cocina
            '3': { x: 525, y: 170 }, // Habitación
            '4': { x: 350, y: 450 }  // Oficina
        };
        
        const pos = posiciones[sala.id] || { x: 100, y: 100 };
        
        const indicador = document.createElement('div');
        indicador.className = 'indicador-energia';
        indicador.style.left = `${pos.x}px`;
        indicador.style.top = `${pos.y}px`;
        indicador.title = `${sala.nombre}\nConsumo: ${sala.consumo_total.toFixed(2)} kWh`;
        
        // Color según consumo (ejemplo)
        const umbral = 5; // kWh - ajusta según tus necesidades
        indicador.style.backgroundColor = sala.consumo_total > umbral ? 'red' : 'green';
        
        // Enlace a dispositivos.html
        indicador.addEventListener('click', () => {
            window.location.href = `dispositivos.html?sala_id=${sala.id}`;
        });
        
        document.getElementById('mapa-container').appendChild(indicador);
    }

    // Función para crear gráficos
    function crearGraficos(salas) {
        // Gráfico de barras
        graficoConsumoBarras = new Chart(graficoConsumoBarrasCanvas, {
            type: 'bar',
            data: {
                labels: salas.map(s => s.nombre),
                datasets: [{
                    label: 'Consumo Total (kWh)',
                    data: salas.map(s => s.consumo_total),
                    backgroundColor: salas.map(s => 
                        s.consumo_total > 5 ? 'rgba(255, 99, 132, 0.7)' : 'rgba(75, 192, 192, 0.7)'),
                    borderColor: salas.map(s => 
                        s.consumo_total > 5 ? 'rgba(255, 99, 132, 1)' : 'rgba(75, 192, 192, 1)'),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Consumo (kWh)'
                        }
                    }
                }
            }
        });

        // Gráfico de líneas (histórico)
        graficoConsumoLineas = new Chart(graficoConsumoLineasCanvas, {
            type: 'line',
            data: {
                labels: ['6 días atrás', '5 días atrás', '4 días atrás', '3 días atrás', '2 días atrás', 'Ayer', 'Hoy'],
                datasets: salas.map((sala, i) => ({
                    label: sala.nombre,
                    data: sala.historial || Array(7).fill(0),
                    borderColor: `hsl(${(i * 360 / salas.length)}, 70%, 50%)`,
                    fill: false
                }))
            },
            options: {
                responsive: true
            }
        });
    }

    // Función para actualizar datos
    async function actualizarDatos() {
        try {
            const response = await fetch('api_salas.php');
            const data = await response.json();
            
            if (data.error) throw new Error(data.error);
            
            // Actualizar gráficos
            graficoConsumoBarras.data.datasets[0].data = data.salas.map(s => s.consumo_total);
            graficoConsumoBarras.update();
            
            // Actualizar indicadores
            document.querySelectorAll('.indicador-energia').forEach(ind => {
                const sala = data.salas.find(s => s.id === ind.dataset.salaId);
                if (sala) {
                    ind.title = `${sala.nombre}\nConsumo: ${sala.consumo_total.toFixed(2)} kWh`;
                    ind.style.backgroundColor = sala.consumo_total > 5 ? 'red' : 'green';
                }
            });
            
        } catch (error) {
            console.error('Error al actualizar:', error);
        }
    }

    // Iniciar carga
    cargarDatosSalas();
});

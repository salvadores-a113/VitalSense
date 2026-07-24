const nombreHabitacion = document.getElementById('nombre-habitacion');
const listaDispositivos = document.getElementById('lista-dispositivos');
const graficoConsumoAparatosCanvas = document.getElementById('grafico-consumo-aparatos');

// Obtener el nombre de la habitación de la URL
const params = new URLSearchParams(window.location.search);
const habitacionId = params.get('habitacion');

// Datos de ejemplo (reemplazar con datos de tu base de datos)
const dispositivosPorHabitacion = {
    'cocina': ['Refrigerador', 'Horno', 'Estufa', 'Licuadora'],
    'sala': ['Televisor', 'Consola de videojuegos', 'Equipo de sonido'],
    'dormitorio1': ['Lámpara', 'Ventilador'],
    'dormitorio2': ['Lámpara', 'Computadora'],
    'baño': ['Secador de pelo', 'Calentador de agua'],
    'jardin': ['Iluminación exterior', 'Sistema de riego'],
    'dormitorio3': ['Lámpara', 'Ventilador'],
    'sala_de_estar': ['Televisor', 'Consola de videojuegos'],
};

// Datos de consumo de ejemplo (reemplazar con datos de tu base de datos)
const consumoAparatos = {
    'Refrigerador': 0.3, // kWh
    'Horno': 1.5, // kWh
    'Estufa': 1.2, // kWh
    'Licuadora': 0.5, // kWh
    'Televisor': 0.2, // kWh
    'Consola de videojuegos': 0.15, // kWh
    'Equipo de sonido': 0.1, // kWh
    'Lámpara': 0.05, // kWh
    'Ventilador': 0.08, // kWh
    'Computadora': 0.25, // kWh
    'Secador de pelo': 1.0, // kWh
    'Calentador de agua': 2.0, // kWh
    'Iluminación exterior': 0.3, // kWh
    'Sistema de riego': 0.15, // kWh
};
// Mostrar el nombre de la habitación
nombreHabitacion.textContent = habitacionId;

// Mostrar la lista de dispositivos
if (dispositivosPorHabitacion[habitacionId]) {
    dispositivosPorHabitacion[habitacionId].forEach(dispositivo => {
        const item = document.createElement('li');
        item.textContent = dispositivo;
        listaDispositivos.appendChild(item);
    });
} else {
    listaDispositivos.innerHTML = '<li>No hay dispositivos registrados para esta habitación.</li>';
}

// Gráfico de Consumo de Aparatos
const nombresAparatos = dispositivosPorHabitacion[habitacionId] || [];
const consumos = nombresAparatos.map(aparato => consumoAparatos[aparato] || 0);

const graficoConsumoAparatos = new Chart(graficoConsumoAparatosCanvas, {
    type: 'bar',
    data: {
        labels: nombresAparatos,
        datasets: [{
            label: 'Consumo (kWh)',
            data: consumos,
            backgroundColor: 'rgba(54, 162, 235, 0.7)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        animation: {
            duration: 1500,
            easing: 'easeOutQuad'
        }
    }
});
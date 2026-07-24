const areas = [
    { id: 'baño', x: 210, y: 130, consumo: 0, umbral: 100, unidad: 'kWh', historial: [0, 0, 0, 0, 0, 0, 0], editable: false },
    { id: 'cocina', x: 325, y: 125, consumo: 0, umbral: 500, unidad: 'kWh', historial: [0, 0, 0, 0, 0, 0, 0], editable: false },
    { id: 'dormitorio1', x: 525, y: 170, consumo: 0, umbral: 80, unidad: 'kWh', historial: [0, 0, 0, 0, 0, 0, 0], editable: false },
    { id: 'dormitorio2', x: 550, y: 450, consumo: 0, umbral: 120, unidad: 'kWh', historial: [0, 0, 0, 0, 0, 0, 0], editable: false },
    { id: 'dormitorio3', x: 350, y: 330, consumo: 0, umbral: 120, unidad: 'kWh', historial: [0, 0, 0, 0, 0, 0, 0], editable: false },
    { id: 'jardin', x: 200, y: 350, consumo: 0, umbral: 300, unidad: 'kWh', historial: [0, 0, 0, 0, 0, 0, 0], editable: false },
    { id: 'sala', x: 540, y: 270, consumo: 0, umbral: 200, unidad: 'kWh', historial: [0, 0, 0, 0, 0, 0, 0], editable: false },
    { id: 'sala_de_estar', x: 350, y: 450, consumo: 0, umbral: 200, unidad: 'kWh', historial: [0, 0, 0, 0, 0, 0, 0], editable: false },
    // Agrega más áreas...
];

const coloresLineas = [
    'rgb(255, 99, 132)',   // Rojo
    'rgb(54, 162, 235)',   // Azul
    'rgb(255, 206, 86)',   // Amarillo
    'rgb(75, 192, 192)',   // Verde
    'rgb(153, 102, 255)',  // Morado
    'rgb(255, 159, 64)',   // Naranja
    'rgb(0, 128, 0)',      // Verde Oscuro
    'rgb(128, 0, 128)',    // Púrpura
    // Agrega más colores si tienes más áreas...
];

const planoCasa = document.getElementById('plano-casa');
const panelInfo = document.getElementById('panel-info');
const botonCambiarDatos = document.getElementById('boton-cambiar-datos');
const panelDatosManuales = document.getElementById('panel-datos-manuales');
const graficoConsumoBarrasCanvas = document.getElementById('grafico-consumo-barras');
const graficoConsumoLineasCanvas = document.getElementById('grafico-consumo-lineas');
const mapaContainer = document.getElementById('mapa-container'); // Obtener el contenedor del mapa

let datosManuales = false;
let graficoConsumoBarras;
let graficoConsumoLineas;
let lineasVisibles = areas.map(() => true); // Inicialmente todas las líneas son visibles
let soloUnaLineaVisible = false;

function generarIndicador(area) {
    const indicador = document.createElement('div');
    indicador.classList.add('indicador-energia');
    indicador.style.left = area.x + 'px';
    indicador.style.top = area.y + 'px';
    indicador.addEventListener('click', () => {
        window.location.href = `dispositivos.html?habitacion=${area.id}`;
    });
    mapaContainer.appendChild(indicador); // Agregar al contenedor del mapa

    // Agregar tooltip
    indicador.title = area.id;

    return indicador;
}

function actualizarIndicador(area, indicador) {
    let color = 'green';
    if (area.consumo > area.umbral) {
        color = 'red';
    } else if (area.consumo > area.umbral * 0.8) {
        color = 'yellow';
    }
    indicador.style.backgroundColor = color;
}

function mostrarInfoArea(area) {
    panelInfo.innerHTML = `
        <h3>${area.id}</h3>
        <p>Consumo: ${area.consumo.toFixed(2)} kWh</p>
        <p>Umbral: ${area.umbral.toFixed(2)} kWh</p>
        <p>Limitación: ${area.consumo > area.umbral ? 'Activa' : 'Inactiva'}</p>
    `;
    actualizarGraficoConsumoBarras();
    actualizarGraficoConsumoLineas();
}

function simularConsumo() {
    areas.forEach(area => {
        area.consumo = Math.random() * area.umbral * 1.2;
        area.historial.shift();
        area.historial.push(area.consumo);
        const indicador = document.querySelector(`.indicador-energia[data-id="${area.id}"]`);
        actualizarIndicador(area, indicador);
    });
    actualizarGraficoConsumoBarras();
    actualizarGraficoConsumoLineas();
}

function generarControlesManuales() {
    panelDatosManuales.innerHTML = areas.map(area => `
        <label for="${area.id}-consumo">${area.id} Consumo (${area.unidad}):</label>
        <input type="number" id="${area.id}-consumo" value="${area.consumo}">
    `).join('');
}

botonCambiarDatos.addEventListener('click', () => {
    datosManuales = !datosManuales;
    if (datosManuales) {
        generarControlesManuales();
        panelDatosManuales.style.display = 'block';
    } else {
        panelDatosManuales.style.display = 'none';
    }
});

areas.forEach(area => {
    area.indicador = generarIndicador(area);
    area.indicador.dataset.id = area.id;
});

// Gráfico de Barras
graficoConsumoBarras = new Chart(graficoConsumoBarrasCanvas, {
    type: 'bar',
    data: {
        labels: areas.map(area => area.id),
        datasets: [{
            label: 'Consumo Actual',
            data: areas.map(area => area.consumo),
            backgroundColor: areas.map(area => {
                if (area.consumo > area.umbral) {
                    return 'red';
                } else if (area.consumo > area.umbral * 0.8) {
                    return 'yellow';
                } else {
                    return 'green';
                }
            }),
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

function actualizarGraficoConsumoBarras() {
    graficoConsumoBarras.data.datasets[0].data = areas.map(area => area.consumo);
    graficoConsumoBarras.data.datasets[0].backgroundColor = areas.map(area => {
        if (area.consumo > area.umbral) {
            return 'red';
        } else if (area.consumo > area.umbral * 0.8) {
            return 'yellow';
        } else {
            return 'green';
        }
    });
    graficoConsumoBarras.update();
}

// Gráfico de Líneas
graficoConsumoLineas = new Chart(graficoConsumoLineasCanvas, {
    type: 'line',
    data: {
        labels: ['-6', '-5', '-4', '-3', '-2', '-1', 'Ahora'],
        datasets: areas.map((area, index) => ({
            label: area.id,
            data: area.historial,
            borderColor: coloresLineas[index % coloresLineas.length],
            fill: false,
            hidden: !lineasVisibles[index] // Ocultar líneas según el estado de visibilidad
        }))
    },
    options: {
        animation: {
            duration: 1500,
            easing: 'easeOutQuad',
        },
        plugins: {
            legend: {
                onClick: (evt, legendItem, legend) => {
                    const index = legendItem.datasetIndex;
                    if (soloUnaLineaVisible) {
                        lineasVisibles = lineasVisibles.map(() => true); // Mostrar todas las líneas
                        soloUnaLineaVisible = false;
                    } else {
                        lineasVisibles = lineasVisibles.map((_, i) => i === index); // Mostrar solo la línea seleccionada
                        soloUnaLineaVisible = true;
                    }
                    graficoConsumoLineas.data.datasets.forEach((dataset, i) => {
                        dataset.hidden = !lineasVisibles[i];
                    });
                    graficoConsumoLineas.update();
                }
            }
        }
    }
});

function actualizarGraficoConsumoLineas() {
    graficoConsumoLineas.data.datasets.forEach(dataset => {
        dataset.data = areas.find(area => area.id === dataset.label).historial;
    });
    graficoConsumoLineas.update();
}

setInterval(() => {
    if (!datosManuales) {
        simularConsumo();
    }
}, 2000);
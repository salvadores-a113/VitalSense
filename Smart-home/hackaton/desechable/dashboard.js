function cargarReporteSalas() {
    fetch("get_reporte_salas.php")
        .then(response => response.json())
        .then(data => {
            let contenido = "<h3>Consumo Total por Sala</h3><ul>";
            data.forEach(sala => {
                contenido += `<li>${sala.nombre}: ${sala.total_consumo} kWh</li>`;
            });
            contenido += "</ul>";
            document.getElementById("reporteSalas").innerHTML = contenido;
        })
        .catch(error => console.error("Error al cargar el reporte:", error));
}
function mostrarGraficoSalas() {
    fetch("get_reporte_salas.php")
        .then(response => response.json())
        .then(data => {
            let ctx = document.getElementById("graficoSalas").getContext("2d");

            let nombres = data.map(sala => sala.nombre);
            let consumos = data.map(sala => sala.total_consumo);

            new Chart(ctx, {
                type: "bar",
                data: {
                    labels: nombres,
                    datasets: [{
                        label: "Consumo por Sala (kWh)",
                        data: consumos,
                        backgroundColor: "blue"
                    }]
                }
            });
        });
}

document.addEventListener("DOMContentLoaded", mostrarGraficoSalas);
document.addEventListener("DOMContentLoaded", cargarReporteSalas);

//estancos_mapa.js

let mapaEstancos;
let marcadoresEstancos = [];
// Coordenadas exactas del centro de Molins de Rei
const centroMolins = [41.4137, 2.0158]; 

// Estancos REALES en Molins de Rei (ajustados)
const estancosMolins = [
    { nombre: "Estanc Molins nº1 - Pi i Margall", lat: 41.4136, lng: 2.0164 },
    { nombre: "Estanc nº2 - Avinguda de València", lat: 41.4115, lng: 2.0190 },
    { nombre: "Estanc nº3 - Carrer Jacint Verdaguer", lat: 41.4104, lng: 2.0145 }
];

const redIcon = new L.Icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
});

function iniciarMapaEstancos() {
    // Si ya existe un mapa, lo eliminamos para evitar errores de duplicado
    if (mapaEstancos) { mapaEstancos.remove(); }

    mapaEstancos = L.map('mapa-estancos-contenedor').setView(centroMolins, 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(mapaEstancos);
    
    dibujarEstancos();
    configurarBuscador();
}

function dibujarEstancos() {
    estancosMolins.forEach(estanco => {
        const m = L.marker([estanco.lat, estanco.lng], { icon: redIcon })
            .addTo(mapaEstancos)
            .bindPopup(`<b>${estanco.nombre}</b><br>Molins de Rei`);
        marcadoresEstancos.push(m);
    });
}

function configurarBuscador() {
    const input = document.getElementById("estancos-input-buscador");
    const boton = document.querySelector(".estancos-buscar-btn");

    if (!input || !boton) return;

    const ejecutarBusqueda = () => {
        const texto = input.value.toLowerCase().trim();

        if (texto === "estanco" || texto === "estancos") {
            // Si busca "estanco", vamos directos al centro de Molins donde están los puntos
            mapaEstancos.flyTo(centroMolins, 16);
            marcadoresEstancos[0].openPopup(); 
        } else if (texto !== "") {
            // Si busca una calle, busca en Molins de Rei
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(texto + ", Molins de Rei")}&limit=1`)
                .then(res => res.json())
                .then(data => {
                    if (data.length > 0) {
                        mapaEstancos.flyTo([data[0].lat, data[0].lon], 18);
                    }
                });
        }
    };

    boton.addEventListener('click', ejecutarBusqueda);
    input.addEventListener('keypress', (e) => { if (e.key === 'Enter') ejecutarBusqueda(); });
}

document.addEventListener('DOMContentLoaded', iniciarMapaEstancos);
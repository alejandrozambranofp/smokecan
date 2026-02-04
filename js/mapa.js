//mapa.js
let mapaHome;
let marcadoresZonas = [];
let marcadoresEstancos = [];

// Coordenadas EXACTAS del centro de Molins de Rei
const centroMolins = [41.4137, 2.0158];

// 1. ZONAS LIBRES DE HUMO (Solo en Molins)
const zonasMolins = [
    { nombre: "Parque de la Mariona", lat: 41.4152, lng: 2.0140, tipo: "parque" },
    { nombre: "Plaça de la Vila", lat: 41.4138, lng: 2.0158, tipo: "edificio" },
    { nombre: "Biblioteca El Molí", lat: 41.4120, lng: 2.0145, tipo: "edificio" }
];

// 2. ESTANCOS (Solo en Molins)
const estancosMolins = [
    { nombre: "Estanc Molins nº1 - Pi i Margall", lat: 41.4136, lng: 2.0164 },
    { nombre: "Estanc nº2 - Avinguda de València", lat: 41.4115, lng: 2.0190 },
    { nombre: "Estanc nº3 - Carrer Jacint Verdaguer", lat: 41.4104, lng: 2.0145 }
];

// Icono Rojo para identificar los estancos
const iconoEstanco = new L.Icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
});

function iniciarMapa() {
    // Si ya hay un mapa lo quitamos para que no de error
    if (mapaHome) { mapaHome.remove(); }

    mapaHome = L.map('mapa-contenedor').setView(centroMolins, 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(mapaHome);

    cargarTodo();
    configurarBuscador();
}

function cargarTodo() {
    // Cargar Zonas (Azules por defecto)
    zonasMolins.forEach(z => {
        const m = L.marker([z.lat, z.lng], { tipo: z.tipo })
            .addTo(mapaHome)
            .bindPopup(`<b>${z.nombre}</b><br>Molins de Rei`);
        marcadoresZonas.push(m);
    });

    // Cargar Estancos (Rojos)
    estancosMolins.forEach(e => {
        const m = L.marker([e.lat, e.lng], { icon: iconoEstanco, tipo: 'estanco' })
            .addTo(mapaHome)
            .bindPopup(`<b>${e.nombre}</b><br>Estanco en Molins`);
        marcadoresEstancos.push(m);
    });
}

function configurarBuscador() {
    const input = document.getElementById("input-buscador");
    if (!input) return;

    input.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            const busqueda = input.value.toLowerCase().trim();

            if (busqueda === "estanco" || busqueda === "estancos") {
                // Al buscar "estanco", vamos al primero de la lista y abrimos su información
                mapaHome.flyTo([estancosMolins[0].lat, estancosMolins[0].lng], 17);
                marcadoresEstancos[0].openPopup();
            } else {
                // Búsqueda normal de calles
                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(busqueda + ", Molins de Rei")}&limit=1`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.length > 0) {
                            mapaHome.flyTo([data[0].lat, data[0].lon], 18);
                        }
                    });
            }
        }
    });
}

// Para que los botones de filtro funcionen
function filtrarMarcadores(tipo) {
    // Unimos todas las listas para filtrar
    const todos = [...marcadoresZonas, ...marcadoresEstancos];
    todos.forEach(m => {
        if (tipo === 'todos' || m.options.tipo === tipo) {
            mapaHome.addLayer(m);
        } else {
            mapaHome.removeLayer(m);
        }
    });
}

document.addEventListener('DOMContentLoaded', iniciarMapa);
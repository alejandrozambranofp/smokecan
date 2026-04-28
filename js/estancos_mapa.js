let mapaEstancos;
let marcadoresEstancos = [];
let circulosZonasEstancos = [];

// Coordenadas centrales de Molins de Rei
const centroMolins = [41.4138, 2.0158];

// Capa Premium
const TILE_URL = 'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png';
const TILE_ATTRIBUTION = '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>';

// Base de datos (Coordenadas verificadas)
const datosEstancos = [
    { nombre: "Estanco Passeig de Pi i Margall", lat: 41.4136, lng: 2.0164, direccion: "Passeig de Pi i Margall" },
    { nombre: "Estanc Molins Avinguda de València", lat: 41.4110, lng: 2.0180, direccion: "Avinguda de València" }, 
    { nombre: "Tabacs L'Estanc Jacint Verdaguer", lat: 41.4150, lng: 2.0145, direccion: "Carrer Jacint Verdaguer" } 
];

const zonasLibresDeHumo = [
    { nombre: "Parc de la Mariona", lat: 41.4057, lng: 2.0221, radio: 110 },
    { nombre: "Parc del Pont de la Cadena", lat: 41.4080, lng: 2.0192, radio: 85 },
    { nombre: "CAP Molins de Rei", lat: 41.4145, lng: 2.0166, radio: 65 },
    { nombre: "Ajuntament de Molins", lat: 41.4138, lng: 2.0158, radio: 50 },
    { nombre: "Escola Josep Maria Madorell", lat: 41.4162, lng: 2.0135, radio: 75 },
    { nombre: "Passeig de Pi i Margall", lat: 41.4136, lng: 2.0164, radio: 90 },
    { nombre: "Terraza El Racó", lat: 41.4148, lng: 2.0170, radio: 30 }
];

function iniciarMapaEstancos() {
    mapaEstancos = L.map('mapa-estancos-contenedor', {
        zoomControl: false
    }).setView(centroMolins, 16);

    L.control.zoom({ position: 'topright' }).addTo(mapaEstancos);

    L.tileLayer(TILE_URL, {
        attribution: TILE_ATTRIBUTION,
        maxZoom: 20
    }).addTo(mapaEstancos);

    setTimeout(() => { mapaEstancos.invalidateSize(); }, 300);

    dibujarMapaEstancos('todos');
    configurarBuscadorEstancos();
}

function dibujarMapaEstancos(filtro) {
    marcadoresEstancos.forEach(m => mapaEstancos.removeLayer(m));
    circulosZonasEstancos.forEach(c => mapaEstancos.removeLayer(c));
    marcadoresEstancos = [];
    circulosZonasEstancos = [];

    const colorPrincipal = '#00796B';

    if (filtro === 'todos' || filtro === 'zona') {
        zonasLibresDeHumo.forEach(zona => {
            const circulo = L.circle([zona.lat, zona.lng], {
                color: colorPrincipal,
                weight: 2,
                fillColor: colorPrincipal,
                fillOpacity: 0.2,
                radius: zona.radio
            }).addTo(mapaEstancos);
            
            circulo.bindPopup(`
                <div class="popup-smokecan">
                    <div class="popup-header" style="background:${colorPrincipal};">ZONA PROTEGIDA</div>
                    <div class="popup-body">
                        <b>🚫 ${zona.nombre}</b>
                        <p>Respirar aire limpio es un derecho</p>
                    </div>
                </div>
            `);
            circulosZonasEstancos.push(circulo);
        });
    }

    if (filtro === 'todos' || filtro === 'estanco') {
        datosEstancos.forEach((estanco, index) => {
            const iconoHTML = L.divIcon({
                html: `<div class="marcador-premium"><i class="fa-solid fa-smoking"></i></div>`,
                className: 'custom-div-icon',
                iconSize: [36, 36],
                iconAnchor: [18, 18]
            });

            const marker = L.marker([estanco.lat, estanco.lng], { icon: iconoHTML })
                .bindPopup(`
                    <div class="popup-body" style="text-align:center;">
                        <b style="color:${colorPrincipal};">${estanco.nombre}</b>
                        <p>${estanco.direccion}</p>
                    </div>
                `)
                .addTo(mapaEstancos);
            
            marcadoresEstancos.push(marker);
        });
    }

    // Actualizar eventos de botones laterales
    document.querySelectorAll('.estanco-btn').forEach(boton => {
        boton.onclick = (e) => {
            const index = e.target.getAttribute('data-index');
            const estanco = datosEstancos[index];
            if (estanco) {
                mapaEstancos.flyTo([estanco.lat, estanco.lng], 18, { animate: true, duration: 1.5 });
                const marker = marcadoresEstancos.find(m => m.getLatLng().lat === estanco.lat);
                if (marker) marker.openPopup();
            }
        };
    });
}

function filtrarEstancos(tipo) {
    const nombresFiltros = { 'todos': 'Mostrando Todo', 'estanco': 'Estancos', 'zona': 'Zonas Libres de Humo' };
    mostrarNotificacion(nombresFiltros[tipo] || 'Filtro aplicado');
    dibujarMapaEstancos(tipo);
}

function mostrarNotificacion(texto) {
    const notificacion = document.getElementById('filtro-notificacion');
    if (notificacion) {
        notificacion.textContent = texto;
        notificacion.classList.remove('oculto', 'visible');
        void notificacion.offsetWidth;
        notificacion.classList.add('visible');
        setTimeout(() => { notificacion.classList.remove('visible'); }, 1500);
    }
}

function toggleLeyenda() {
    const leyenda = document.getElementById('leyenda-mapa');
    if (leyenda) leyenda.classList.toggle('colapsado');
}

function configurarBuscadorEstancos() {
    const inputBusqueda = document.getElementById('estancos-input-buscador');
    const btnBusqueda = document.querySelector('.estancos-buscar-btn');
    if(!inputBusqueda || !btnBusqueda) return;

    const buscar = () => {
        const texto = inputBusqueda.value.toLowerCase().trim();
        if (texto !== "") {
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(texto + ", Molins de Rei")}&limit=1`)
                .then(res => res.json())
                .then(data => {
                    if (data.length > 0) mapaEstancos.flyTo([data[0].lat, data[0].lon], 18, { animate: true });
                })
                .catch(err => console.error(err));
        }
    };
    btnBusqueda.onclick = buscar;
    inputBusqueda.onkeypress = (e) => { if (e.key === 'Enter') buscar(); };
}

document.addEventListener('DOMContentLoaded', iniciarMapaEstancos);
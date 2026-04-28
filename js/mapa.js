let mapaPrincipal;
let circulosZonas = [];
let marcadoresPuntos = [];

// Coordenadas centrales de Molins de Rei
const centroMolins = [41.4138, 2.0158];

// Capa de Mapa Premium (CartoDB Voyager - Limpia y profesional)
const TILE_URL = 'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png';
const TILE_ATTRIBUTION = '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>';

// Base de datos de Puntos y Zonas (Coordenadas verificadas)
const baseDeDatos = {
    zonas: [
        { nombre: "Parc de la Mariona", lat: 41.4057, lng: 2.0221, radio: 110, tipo: "parque", nivel: "prohibido" },
        { nombre: "Parc del Pont de la Cadena", lat: 41.4080, lng: 2.0192, radio: 85, tipo: "parque", nivel: "prohibido" },
        { nombre: "CAP Molins de Rei (Hospital)", lat: 41.4145, lng: 2.0166, radio: 65, tipo: "hospital", nivel: "prohibido" },
        { nombre: "Ajuntament de Molins", lat: 41.4138, lng: 2.0158, radio: 50, tipo: "edificio", nivel: "prohibido" },
        { nombre: "Escola Josep Maria Madorell", lat: 41.4162, lng: 2.0135, radio: 75, tipo: "colegio", nivel: "prohibido" },
        { nombre: "Passeig de Pi i Margall", lat: 41.4136, lng: 2.0164, radio: 90, tipo: "terraza", nivel: "no-recomendado" },
        { nombre: "Terraza El Racó", lat: 41.4148, lng: 2.0170, radio: 30, tipo: "terraza", nivel: "no-recomendado" },
        { nombre: "Biblioteca El Molí", lat: 41.4129, lng: 2.0147, radio: 55, tipo: "edificio", nivel: "prohibido" },
        { nombre: "Zona Deportiva Municipal", lat: 41.4165, lng: 2.0110, radio: 110, tipo: "parque", nivel: "prohibido" }
    ],
    puntos: [
        { nombre: "Estanco Passeig de Pi i Margall", lat: 41.4136, lng: 2.0164, tipo: "estanco" },
        { nombre: "Estanc Molins Avinguda de València", lat: 41.4110, lng: 2.0180, tipo: "estanco" },
        { nombre: "Tabacs L'Estanc Jacint Verdaguer", lat: 41.4150, lng: 2.0145, tipo: "estanco" },
        { nombre: "Parada Bus L1", lat: 41.4130, lng: 2.0170, tipo: "bus" },
        { nombre: "Parada Bus L2", lat: 41.4140, lng: 2.0140, tipo: "bus" }
    ]
};

// Configuración de iconos (FontAwesome)
const iconos = {
    parque: '<i class="fa-solid fa-tree"></i>',
    hospital: '<i class="fa-solid fa-hospital"></i>',
    colegio: '<i class="fa-solid fa-school"></i>',
    estanco: '<i class="fa-solid fa-smoking"></i>',
    bus: '<i class="fa-solid fa-bus"></i>',
    edificio: '<i class="fa-solid fa-building"></i>',
    terraza: '<i class="fa-solid fa-chair"></i>'
};

function iniciarMapa() {
    // Inicializar el mapa con opciones de limpieza
    mapaPrincipal = L.map('mapa-contenedor', {
        zoomControl: false, // Lo moveremos para que no estorbe
        scrollWheelZoom: true
    }).setView(centroMolins, 16);

    // Añadir controles de zoom en una posición más limpia
    L.control.zoom({ position: 'topright' }).addTo(mapaPrincipal);

    // Capa visual Premium
    L.tileLayer(TILE_URL, {
        attribution: TILE_ATTRIBUTION,
        maxZoom: 20
    }).addTo(mapaPrincipal);

    // Invalida el tamaño para evitar errores de renderizado
    setTimeout(() => { mapaPrincipal.invalidateSize(); }, 300);

    dibujarMapa('todos');
    configurarBuscadorPrincipal();
}

function dibujarMapa(filtro) {
    // Limpiar capas existentes
    circulosZonas.forEach(c => mapaPrincipal.removeLayer(c));
    marcadoresPuntos.forEach(m => mapaPrincipal.removeLayer(m));
    circulosZonas = [];
    marcadoresPuntos = [];

    const colorPrincipal = '#00796B';
    const colorSecundario = '#4db6ac';

    // 1. Dibujar Zonas de Humo
    baseDeDatos.zonas.forEach(zona => {
        if (filtro === 'todos' || zona.tipo === filtro || (filtro === 'prohibido' && zona.nivel === 'prohibido')) {
            const esProhibido = zona.nivel === 'prohibido';
            const color = esProhibido ? colorPrincipal : colorSecundario; 
            
            const circulo = L.circle([zona.lat, zona.lng], {
                color: color,
                weight: 2,
                fillColor: color,
                fillOpacity: 0.2,
                radius: zona.radio
            }).addTo(mapaPrincipal);

            circulo.bindPopup(`
                <div class="popup-smokecan">
                    <div class="popup-header" style="background:${color};">
                        ${esProhibido ? 'ZONA PROHIBIDA' : 'NO RECOMENDADO'}
                    </div>
                    <div class="popup-body">
                        <b>${iconos[zona.tipo] || ''} ${zona.nombre}</b>
                        <p>Distancia de seguridad requerida</p>
                    </div>
                </div>
            `);
            circulosZonas.push(circulo);
        }
    });

    // 2. Dibujar Puntos de Interés
    baseDeDatos.puntos.forEach(punto => {
        if (filtro === 'todos' || punto.tipo === filtro) {
            const iconoHTML = L.divIcon({
                html: `<div class="marcador-premium">${iconos[punto.tipo]}</div>`,
                className: 'custom-div-icon',
                iconSize: [36, 36],
                iconAnchor: [18, 18]
            });

            const marcador = L.marker([punto.lat, punto.lng], { icon: iconoHTML }).addTo(mapaPrincipal);
            
            marcador.bindPopup(`
                <div class="popup-body" style="text-align:center;">
                    <b style="color:${colorPrincipal}; font-size:14px;">${punto.nombre}</b><br>
                    <small>Punto verificado</small>
                </div>
            `);
            marcadoresPuntos.push(marcador);
        }
    });
}

function filtrarMarcadores(tipo) {
    const nombresFiltros = {
        'todos': 'Mostrando Todo',
        'prohibido': 'Zonas Prohibidas',
        'terraza': 'Zonas No Recomendadas',
        'estanco': 'Estancos',
        'hospital': 'Hospitales',
        'colegio': 'Colegios',
        'bus': 'Paradas de Bus'
    };

    mostrarNotificacion(nombresFiltros[tipo] || 'Filtro aplicado');
    dibujarMapa(tipo);
}

function mostrarNotificacion(texto) {
    const notificacion = document.getElementById('filtro-notificacion');
    if (notificacion) {
        notificacion.textContent = texto;
        notificacion.classList.remove('oculto', 'visible');
        void notificacion.offsetWidth; 
        notificacion.classList.add('visible');
        
        setTimeout(() => {
            notificacion.classList.remove('visible');
            notificacion.classList.add('oculto');
        }, 1500);
    }
}

function configurarBuscadorPrincipal() {
    const input = document.getElementById("input-buscador");
    const btn = document.getElementById("btn-buscar-mapa");
    if (!input) return;

    const realizarBusqueda = () => {
        const texto = input.value.toLowerCase().trim();
        if (texto !== "") {
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(texto + ", Molins de Rei")}&limit=1`)
                .then(res => res.json())
                .then(data => {
                    if (data.length > 0) {
                        mapaPrincipal.flyTo([data[0].lat, data[0].lon], 18, { animate: true, duration: 1.5 });
                    }
                })
                .catch(err => console.error("Error en búsqueda:", err));
        }
    };

    input.addEventListener('keypress', (e) => { if (e.key === 'Enter') realizarBusqueda(); });
    if (btn) btn.addEventListener('click', realizarBusqueda);
}

function toggleLeyenda() {
    const leyenda = document.getElementById('leyenda-mapa');
    if (leyenda) leyenda.classList.toggle('colapsado');
}

document.addEventListener('DOMContentLoaded', iniciarMapa);
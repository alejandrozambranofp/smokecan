let mapaPrincipal;
let circulosZonas = [];
let marcadoresPuntos = [];

// Coordenadas centrales de Molins de Rei
const centroMolins = [41.4137, 2.0158];

// Base de datos de Puntos y Zonas
const baseDeDatos = {
    zonas: [
        { nombre: "Parc de la Mariona", lat: 41.4072, lng: 2.0225, radio: 110, tipo: "parque", nivel: "prohibido" },
        { nombre: "Parc del Pont de la Cadena", lat: 41.4083, lng: 2.0163, radio: 85, tipo: "parque", nivel: "prohibido" },
        { nombre: "CAP Molins de Rei (Hospital)", lat: 41.4111, lng: 2.0108, radio: 65, tipo: "hospital", nivel: "prohibido" },
        { nombre: "Ajuntament de Molins", lat: 41.4144, lng: 2.0163, radio: 50, tipo: "edificio", nivel: "prohibido" },
        { nombre: "Escola Josep Maria Madorell", lat: 41.4161, lng: 2.0135, radio: 75, tipo: "colegio", nivel: "prohibido" },
        { nombre: "Passeig de Pi i Margall", lat: 41.4136, lng: 2.0164, radio: 90, tipo: "terraza", nivel: "no-recomendado" },
        { nombre: "Terraza El Racó", lat: 41.4148, lng: 2.0170, radio: 30, tipo: "terraza", nivel: "no-recomendado" },
        { nombre: "Biblioteca El Molí", lat: 41.4051, lng: 2.0203, radio: 55, tipo: "edificio", nivel: "prohibido" },
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

// Configuración de iconos
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
    mapaPrincipal = L.map('mapa-contenedor').setView(centroMolins, 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19
    }).addTo(mapaPrincipal);

    setTimeout(() => { mapaPrincipal.invalidateSize(); }, 200);

    dibujarMapa('todos');
    configurarBuscadorPrincipal();
}

function dibujarMapa(filtro) {
    // Borrar todo lo anterior
    circulosZonas.forEach(c => mapaPrincipal.removeLayer(c));
    marcadoresPuntos.forEach(m => mapaPrincipal.removeLayer(m));
    circulosZonas = [];
    marcadoresPuntos = [];

    // 1. Dibujar Zonas (Círculos)
    baseDeDatos.zonas.forEach(zona => {
        if (filtro === 'todos' || zona.tipo === filtro || (filtro === 'prohibido' && zona.nivel === 'prohibido')) {
            const color = zona.nivel === 'prohibido' ? '#00796B' : '#4db6ac'; 
            const label = zona.nivel === 'prohibido' ? 'ZONA PROHIBIDA' : 'NO RECOMENDADO';
            
            const circulo = L.circle([zona.lat, zona.lng], {
                color: color,
                fillColor: color,
                fillOpacity: 0.3,
                radius: zona.radio
            }).addTo(mapaPrincipal);

            circulo.bindPopup(`
                <div style="text-align:center; min-width: 150px;">
                    <div style="background:${color}; color:white; padding:5px; border-radius:5px 5px 0 0; font-weight:bold;">
                        ${label}
                    </div>
                    <div style="padding:10px; border:1px solid ${color}; border-top:none;">
                        <span style="font-size:16px;">${iconos[zona.tipo] || ''} <b>${zona.nombre}</b></span><br>
                        <hr style="margin:8px 0; opacity:0.2;">
                        <small style="color:#666;">Distancia de seguridad obligatoria</small>
                    </div>
                </div>
            `);
            circulosZonas.push(circulo);
        }
    });

    // 2. Dibujar Puntos (Marcadores)
    baseDeDatos.puntos.forEach(punto => {
        if (filtro === 'todos' || punto.tipo === filtro) {
            const iconoHTML = L.divIcon({
                html: `<div style="background:white; color:#00796B; width:30px; height:30px; border-radius:50%; display:flex; justify-content:center; align-items:center; border:2px solid #00796B; box-shadow:0 2px 5px rgba(0,0,0,0.2);">${iconos[punto.tipo]}</div>`,
                className: 'custom-div-icon',
                iconSize: [30, 30],
                iconAnchor: [15, 15]
            });

            const marcador = L.marker([punto.lat, punto.lng], { icon: iconoHTML }).addTo(mapaPrincipal);
            
            marcador.bindPopup(`
                <div style="text-align:center;">
                    <b style="color:#00796B;">${iconos[punto.tipo]} ${punto.nombre}</b><br>
                    <small>Disponible en Molins de Rei</small>
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
        void notificacion.offsetWidth; // Force reflow
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

    input.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') realizarBusqueda();
    });

    if (btn) {
        btn.addEventListener('click', realizarBusqueda);
    }
}

function toggleLeyenda() {
    const leyenda = document.getElementById('leyenda-mapa');
    if (leyenda) {
        leyenda.classList.toggle('colapsado');
    }
}

document.addEventListener('DOMContentLoaded', iniciarMapa);
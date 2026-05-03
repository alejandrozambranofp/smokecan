let mapaPrincipal;
let circulosZonas = [];
let marcadoresPuntos = [];
let marcadoresUsuarios = []; // Nuevas zonas marcadas por usuarios
let marcadorUsuario;
let marcadorTemporal; // Para cuando el usuario hace clic para crear una zona

// Coordenadas centrales de Molins de Rei
const centroMolins = [41.4137, 2.0158];

// Base de datos de Puntos y Zonas
const baseDeDatos = {
    zonas: [
        { nombre: "CAP Molins de Rei", lat: 41.418514, lng: 2.012755, radio: 75, tipo: "hospital", nivel: "prohibido" },
        { nombre: "Parc de la Mariona", lat: 41.405747, lng: 2.021982, radio: 110, tipo: "parque", nivel: "prohibido" },
        { nombre: "Escola El Palau", lat: 41.411630, lng: 2.016379, radio: 80, tipo: "colegio", nivel: "prohibido" },
        { nombre: "Institut Bernat el Ferrer", lat: 41.410682, lng: 2.027206, radio: 100, tipo: "colegio", nivel: "prohibido" },
        { nombre: "Escola l'Alzina", lat: 41.414016, lng: 2.022790, radio: 70, tipo: "colegio", nivel: "prohibido" },
        { nombre: "Escola Castell Ciuró", lat: 41.411030, lng: 2.026207, radio: 75, tipo: "colegio", nivel: "prohibido" },
        { nombre: "Escola Pont de la Cadena", lat: 41.406292, lng: 2.018489, radio: 85, tipo: "colegio", nivel: "prohibido" },
        { nombre: "Parc de la Sèquia del Molí", lat: 41.417575, lng: 2.014021, radio: 90, tipo: "parque", nivel: "prohibido" },
        { nombre: "Escola la Sínia", lat: 41.41836, lng: 2.01169, radio: 80, tipo: "colegio", nivel: "prohibido" }
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
    mapaPrincipal = L.map('mapa-contenedor', { zoomControl: false }).setView(centroMolins, 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19
    }).addTo(mapaPrincipal);

    setTimeout(() => { mapaPrincipal.invalidateSize(); }, 200);

    dibujarMapa('todos');
    cargarZonasUsuarios(); // Cargar zonas de la comunidad

    // Evento de clic en el mapa para añadir zona
    mapaPrincipal.on('click', function(e) {
        mostrarPopupCrearZona(e.latlng);
    });

    // configurarBuscadorPrincipal();
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

function cargarZonasUsuarios() {
    fetch('obtener_zonas_usuarios.php')
        .then(res => res.json())
        .then(zonas => {
            // Limpiar anteriores
            marcadoresUsuarios.forEach(m => mapaPrincipal.removeLayer(m));
            marcadoresUsuarios = [];

            zonas.forEach(zona => {
                const totalVotos = parseInt(zona.votos_si) + parseInt(zona.votos_no);
                const porcentajeSi = totalVotos > 0 ? Math.round((zona.votos_si / totalVotos) * 100) : 0;
                
                // Color basado en veracidad (verde si es muy votado, gris si es nuevo o dudoso)
                const colorVeracidad = porcentajeSi > 70 ? '#00796B' : '#757575';

                const circulo = L.circle([zona.lat, zona.lng], {
                    color: colorVeracidad,
                    fillColor: colorVeracidad,
                    fillOpacity: 0.2,
                    radius: 50,
                    dashArray: '5, 10' // Línea discontinua para indicar que es de usuario
                }).addTo(mapaPrincipal);

                circulo.bindPopup(`
                    <div style="text-align:center; min-width: 180px;">
                        <div style="background:#424242; color:white; padding:5px; border-radius:5px 5px 0 0; font-weight:bold; font-size:12px;">
                            ZONA REPORTADA POR USUARIO
                        </div>
                        <div style="padding:10px; border:1px solid #424242; border-top:none;">
                            <b style="color:#00796B;">Zona marcada recientemente</b><br>
                            <small>Por: ${zona.autor}</small><br>
                            <hr style="margin:8px 0; opacity:0.1;">
                            <div style="margin-bottom:10px;">
                                <b>¿Es esta zona libre de humo?</b><br>
                                <small>${zona.votos_si} Sí | ${zona.votos_no} No</small>
                            </div>
                            <div style="display:flex; gap:5px; justify-content:center;">
                                <button onclick="votarZona(${zona.id}, 1)" style="background:#00796B; color:white; border:none; padding:5px 10px; border-radius:15px; cursor:pointer; font-size:11px;"><i class="fa fa-check"></i> Sí</button>
                                <button onclick="votarZona(${zona.id}, 0)" style="background:#d32f2f; color:white; border:none; padding:5px 10px; border-radius:15px; cursor:pointer; font-size:11px;"><i class="fa fa-times"></i> No</button>
                            </div>
                        </div>
                    </div>
                `);
                marcadoresUsuarios.push(circulo);
            });
        });
}

function mostrarPopupCrearZona(latlng) {
    // Si ya hay un marcador temporal, lo movemos
    if (marcadorTemporal) {
        marcadorTemporal.setLatLng(latlng);
    } else {
        marcadorTemporal = L.marker(latlng).addTo(mapaPrincipal);
    }

    marcadorTemporal.bindPopup(`
        <div style="text-align:center; padding:5px;">
            <b>¿Quieres marcar este punto como zona libre de humo?</b><br>
            <p style="font-size:12px; color:#666; margin:8px 0;">Ayuda a la comunidad reportando nuevas zonas.</p>
            <button onclick="confirmarNuevaZona(${latlng.lat}, ${latlng.lng})" style="background:#00796B; color:white; border:none; padding:8px 15px; border-radius:20px; cursor:pointer; font-weight:bold;">Sí, marcar zona</button>
        </div>
    `).openPopup();
}

function confirmarNuevaZona(lat, lng) {
    fetch('guardar_zona.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ lat: lat, lng: lng })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            mostrarNotificacion("¡Zona marcada con éxito!");
            mapaPrincipal.removeLayer(marcadorTemporal);
            marcadorTemporal = null;
            cargarZonasUsuarios();
        } else {
            alert(data.message);
        }
    });
}

function votarZona(id, valor) {
    fetch('votar_zona.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ zona_id: id, voto: valor })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            mostrarNotificacion("¡Voto registrado!");
            cargarZonasUsuarios();
        } else {
            alert(data.message);
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

function localizarUsuario() {
    if (!navigator.geolocation) {
        alert("Tu navegador no soporta geolocalización.");
        return;
    }

    mostrarNotificacion("Buscando tu ubicación...");

    navigator.geolocation.getCurrentPosition(
        (pos) => {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;

            // Si ya existe el marcador, lo movemos
            if (marcadorUsuario) {
                marcadorUsuario.setLatLng([lat, lng]);
            } else {
                // Crear un icono azul especial para el usuario
                const iconoUsuario = L.divIcon({
                    html: `
                        <div class="user-location-marker">
                            <div class="user-dot"></div>
                            <div class="user-pulse"></div>
                        </div>
                    `,
                    className: 'custom-user-icon',
                    iconSize: [20, 20],
                    iconAnchor: [10, 10]
                });

                marcadorUsuario = L.marker([lat, lng], { icon: iconoUsuario }).addTo(mapaPrincipal);
                marcadorUsuario.bindPopup("<b>Estás aquí</b><br>Consulta las zonas libres de humo a tu alrededor.");
            }

            mapaPrincipal.flyTo([lat, lng], 17, { animate: true, duration: 1.5 });
            marcadorUsuario.openPopup();
        },
        (err) => {
            console.error("Error de geolocalización:", err);
            alert("No se pudo obtener tu ubicación. Asegúrate de dar permisos.");
        },
        { enableHighAccuracy: true }
    );
}

document.addEventListener('DOMContentLoaded', iniciarMapa);
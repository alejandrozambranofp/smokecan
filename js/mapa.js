let mapaPrincipal;
let circulosZonas = [];
let marcadoresPuntos = [];
let marcadoresUsuarios = []; // Nuevas zonas marcadas por usuarios
let marcadorUsuario;
let marcadorTemporal; // Para cuando el usuario hace clic para crear una zona

// Coordenadas centrales de Molins de Rei
const centroMolins = [41.4137, 2.0158];

// Base de datos local (solo estancos, las zonas vienen de la DB)
const baseDeDatos = {
    puntos: [
        { nombre: "Estanco Passeig de Pi i Margall", lat: 41.4136, lng: 2.0164, tipo: "estanco" },
        { nombre: "Estanc Molins Avinguda de València", lat: 41.4110, lng: 2.0180, tipo: "estanco" },
        { nombre: "Tabacs L'Estanc Jacint Verdaguer", lat: 41.4150, lng: 2.0145, tipo: "estanco" }
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

    // Cargar todas las zonas desde la base de datos (unificado)
    cargarZonasDesdeDB('todos');

    // Evento de clic en el mapa para añadir zona
    mapaPrincipal.on('click', function(e) {
        if (e.originalEvent && (e.originalEvent.target.closest('.leaflet-popup') || e.originalEvent.target.closest('.leaflet-popup-pane'))) {
            return;
        }
        mostrarPopupCrearZona(e.latlng);
    });

    mapaPrincipal.on('popupopen', function(e) {
        var el = e.popup._container || e.popup.getElement();
        if (el) {
            L.DomEvent.disableClickPropagation(el);
        }
    });

    // Buscar parámetro 'zona' en la URL para enfocar una zona específica
    const urlParams = new URLSearchParams(window.location.search);
    const zonaNombre = urlParams.get('zona');
    if (zonaNombre) {
        setTimeout(() => {
            enfocarZonasPorNombre(zonaNombre);
        }, 1000);
    }
}

function enfocarZonasPorNombre(nombre) {
    // Buscar todas las zonas que coincidan con el nombre y hacer zoom
    fetch('obtener_todas_las_zonas.php?t=' + new Date().getTime())
        .then(res => res.json())
        .then(zonas => {
            const zonasCoincidentes = zonas.filter(z => 
                (z.nombre_sitio && z.nombre_sitio.toLowerCase() === nombre.toLowerCase()) ||
                (z.sitio && z.sitio.toLowerCase() === nombre.toLowerCase())
            );
            
            if (zonasCoincidentes.length > 0) {
                const zona = zonasCoincidentes[0];
                mapaPrincipal.setView([zona.lat, zona.lng], 17);
                
                // Abrir popup de la zona
                setTimeout(() => {
                    const popup = L.popup()
                        .setLatLng([zona.lat, zona.lng])
                        .setContent(`
                            <div style="text-align:center; min-width: 180px;">
                                <div style="background:#424242; color:white; padding:5px; border-radius:5px 5px 0 0; font-weight:bold; font-size:12px;">
                                    ZONA REPORTADA POR USUARIO
                                </div>
                                <div style="padding:10px; border:1px solid #424242; border-top:none;">
                                    <small style="color:#888;"><strong>ID:</strong> #${zona.id}</small><br>
                                    <b style="color:#00796B;">${zona.nombre_sitio || 'Zona marcada'}</b><br>
                                    <small>Por: ${zona.autor}</small>
                                </div>
                            </div>
                        `)
                        .openOn(mapaPrincipal);
                }, 100);
            }
        });
}

function cargarZonasDesdeDB(filtro = 'todos') {
    fetch('obtener_todas_las_zonas.php?t=' + new Date().getTime())
        .then(res => res.json())
        .then(zonas => {
            // Limpiar capas actuales
            circulosZonas.forEach(c => mapaPrincipal.removeLayer(c));
            marcadoresPuntos.forEach(m => mapaPrincipal.removeLayer(m));
            marcadoresUsuarios.forEach(m => mapaPrincipal.removeLayer(m));
            circulosZonas = [];
            marcadoresPuntos = [];
            marcadoresUsuarios = [];

            zonas.forEach(zona => {
                let coincideFiltro = false;
                if (filtro === 'todos') {
                    coincideFiltro = true;
                } else if (filtro === 'prohibido') {
                    if (zona.origen === 'oficial' && (zona.tipo === 'hospital' || zona.tipo === 'colegio' || zona.tipo === 'parque')) {
                        coincideFiltro = true;
                    }
                } else if (filtro === 'para_fumar') {
                    if (zona.origen === 'usuario_fumar' || zona.tipo_zona === 'para_fumar') {
                        coincideFiltro = true;
                    }
                } else if (filtro === 'libre_humo') {
                    if (zona.origen === 'usuario' && (zona.tipo_zona === 'libre_humo' || !zona.tipo_zona)) {
                        coincideFiltro = true;
                    }
                } else {
                    if (zona.tipo === filtro) {
                        coincideFiltro = true;
                    }
                }

                if (!coincideFiltro) return;

                if (zona.origen === 'oficial') {
                    dibujarZonaOficial(zona);
                } else {
                    dibujarZonaUsuario(zona);
                }
            });

            // Dibujar los estancos (que siguen siendo estáticos por ahora)
            baseDeDatos.puntos.forEach(punto => {
                if (filtro === 'todos' || punto.tipo === filtro) {
                    const iconoHTML = L.divIcon({
                        html: `<div style="background:white; color:#00796B; width:30px; height:30px; border-radius:50%; display:flex; justify-content:center; align-items:center; border:2px solid #00796B; box-shadow:0 2px 5px rgba(0,0,0,0.2);">${iconos[punto.tipo]}</div>`,
                        className: 'custom-div-icon',
                        iconSize: [30, 30],
                        iconAnchor: [15, 15]
                    });

                    const marcador = L.marker([punto.lat, punto.lng], { icon: iconoHTML }).addTo(mapaPrincipal);
                    marcador.bindPopup(`<b>${punto.nombre}</b><br>Estanco en Molins de Rei`);
                    marcadoresPuntos.push(marcador);
                }
            });
        });
}

function dibujarZonaOficial(zona) {
    const color = zona.nivel === 'prohibido' ? '#d32f2f' : '#fbc02d';
    const label = zona.nivel === 'prohibido' ? 'ZONA PROHIBIDA' : 'NO RECOMENDADO';
    
    const circulo = L.circle([zona.lat, zona.lng], {
        color: color,
        fillColor: color,
        fillOpacity: 0.2,
        radius: zona.radio
    }).addTo(mapaPrincipal);

    circulo.bindPopup(`
        <div style="text-align:center; min-width: 150px;">
            <div style="background:${color}; color:white; padding:5px; border-radius:5px 5px 0 0; font-weight:bold; font-size:12px;">
                ${label}
            </div>
            <div style="padding:10px; border:1px solid ${color}; border-top:none;">
                <span style="font-size:14px;">${iconos[zona.tipo] || ''} <b>${zona.nombre}</b></span><br>
                <hr style="margin:8px 0; opacity:0.1;">
                <small style="color:#666;">Zona oficial protegida</small>
            </div>
        </div>
    `);
    circulosZonas.push(circulo);
}

function dibujarZonaUsuario(zona) {
    // Si es zona para fumar, dibujar como marcador
    if (zona.origen === 'usuario_fumar' || zona.tipo_zona === 'para_fumar') {
        const iconoHTML = L.divIcon({
            html: `<div style="background:white; color:#FF6B35; width:30px; height:30px; border-radius:50%; display:flex; justify-content:center; align-items:center; border:2px solid #FF6B35; box-shadow:0 2px 5px rgba(0,0,0,0.2);"><i class="fa-solid fa-smoking"></i></div>`,
            className: 'custom-div-icon',
            iconSize: [30, 30],
            iconAnchor: [15, 15]
        });

        const marcador = L.marker([zona.lat, zona.lng], { icon: iconoHTML }).addTo(mapaPrincipal);
        
        marcador.bindPopup(`
            <div style="text-align:center; min-width: 180px;">
                <div style="background:#FF6B35; color:white; padding:5px; border-radius:5px 5px 0 0; font-weight:bold; font-size:12px;">
                    ZONA PARA FUMAR
                </div>
                <div style="padding:10px; border:1px solid #FF6B35; border-top:none;">
                    <small style="color:#888;"><strong>ID:</strong> #${zona.id}</small><br>
                    <b style="color:#FF6B35;">${zona.nombre_sitio || 'Zona para fumar'}</b><br>
                    <small>Por: ${zona.autor}</small>
                </div>
            </div>
        `);
        
        marcadoresUsuarios.push(marcador);
    } else {
        // Zona libre de humo (comportamiento original)
        const totalVotos = parseInt(zona.votos_si) + parseInt(zona.votos_no);
        const porcentajeSi = totalVotos > 0 ? Math.round((zona.votos_si / totalVotos) * 100) : 0;
        const colorVeracidad = porcentajeSi > 70 ? '#00796B' : '#757575';

        const circulo = L.circle([zona.lat, zona.lng], {
            color: colorVeracidad,
            fillColor: colorVeracidad,
            fillOpacity: 0.2,
            radius: 60,
            dashArray: '5, 10'
        }).addTo(mapaPrincipal);

        circulo.bindPopup(`
            <div style="text-align:center; min-width: 180px;">
                <div style="background:#424242; color:white; padding:5px; border-radius:5px 5px 0 0; font-weight:bold; font-size:12px;">
                    ZONA REPORTADA POR USUARIO
                </div>
                <div style="padding:10px; border:1px solid #424242; border-top:none;">
                    <small style="color:#888;"><strong>ID:</strong> #${zona.id}</small><br>
                    <b style="color:#00796B;">${zona.nombre_sitio || 'Zona marcada recientemente'}</b><br>
                    <small>Por: ${zona.autor}</small><br>
                    <hr style="margin:8px 0; opacity:0.1;">
                    <div style="margin-bottom:10px;">
                        <b>¿Es esta zona libre de humo?</b><br>
                        <small>${zona.votos_si} Sí | ${zona.votos_no} No</small>
                    </div>
                    <div style="display:flex; gap:5px; justify-content:center;">
                        <button onclick="votarZona(event, ${zona.id}, 1)" style="background:#00796B; color:white; border:none; padding:5px 10px; border-radius:15px; cursor:pointer; font-size:11px;"><i class="fa fa-check"></i> Sí</button>
                        <button onclick="votarZona(event, ${zona.id}, 0)" style="background:#d32f2f; color:white; border:none; padding:5px 10px; border-radius:15px; cursor:pointer; font-size:11px;"><i class="fa fa-times"></i> No</button>
                    </div>
                </div>
            </div>
        `);
        marcadoresUsuarios.push(circulo);
    }
}

function mostrarPopupCrearZona(latlng) {
    // Solo permitir si el usuario está logueado
    if (document.cookie.indexOf('usuario_logeado=1') === -1) {
        L.popup()
            .setLatLng(latlng)
            .setContent('<div style="text-align:center; padding:10px;"><b>¡Únete a la comunidad!</b><br>Inicia sesión para marcar esta zona como libre de humo.<br><br><a href="login.php" style="background:#00796B; color:white; padding:5px 10px; border-radius:15px; text-decoration:none; font-size:12px;">Iniciar Sesión</a></div>')
            .openOn(mapaPrincipal);
        return;
    }

    if (marcadorTemporal) {
        mapaPrincipal.removeLayer(marcadorTemporal);
    }

    marcadorTemporal = L.marker(latlng).addTo(mapaPrincipal);
    
    marcadorTemporal.bindPopup(`
        <div style="text-align:center; min-width: 250px;">
            <b style="color:#00796B;">¿Qué tipo de zona quieres marcar?</b><br>
            <p style="font-size:11px; margin:10px 0;">Selecciona el tipo de zona</p>
            <div style="display:flex; gap:10px; justify-content:center; flex-direction:column;">
                <button onclick="mostrarFormularioZona(event, ${latlng.lat}, ${latlng.lng}, 'libre_humo')" style="background:#00796B; color:white; border:none; padding:10px 15px; border-radius:10px; cursor:pointer; width:100%;"><i class="fa-solid fa-leaf"></i> Zona Libre de Humo</button>
                <button onclick="mostrarFormularioZona(event, ${latlng.lat}, ${latlng.lng}, 'para_fumar')" style="background:#FF6B35; color:white; border:none; padding:10px 15px; border-radius:10px; cursor:pointer; width:100%;"><i class="fa-solid fa-smoking"></i> Zona para Fumar</button>
            </div>
        </div>
    `).openPopup();
}

function mostrarFormularioZona(e, lat, lng, tipo) {
    if (e && e.stopPropagation) {
        e.stopPropagation();
    }
    marcadorTemporal.setPopupContent(`
        <div style="text-align:center; min-width: 200px;">
            <b style="color:#00796B;">¿Quieres marcar este punto?</b><br>
            <p style="font-size:11px; margin:10px 0;">Tipo: ${tipo === 'libre_humo' ? 'Zona Libre de Humo' : 'Zona para Fumar'}</p>
            <input type="text" id="nombre-sitio-input" placeholder="Nombre del sitio" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:5px; margin-bottom:10px; box-sizing:border-box;">
            <button onclick="confirmarNuevaZona(event, ${lat}, ${lng}, '${tipo}')" style="background:#00796B; color:white; border:none; padding:8px 15px; border-radius:20px; cursor:pointer; width:100%;">Confirmar Punto</button>
        </div>
    `);
}

function confirmarNuevaZona(e, lat, lng, tipo) {
    if (e && e.stopPropagation) {
        e.stopPropagation();
    }
    const nombreSitio = document.getElementById('nombre-sitio-input').value.trim();
    
    if (!nombreSitio) {
        alert('Por favor, ingresa el nombre del sitio');
        return;
    }
    
    fetch('guardar_zona.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ lat: lat, lng: lng, nombre_sitio: nombreSitio, tipo_zona: tipo })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            mostrarNotificacion("¡Zona marcada con éxito!");
            mapaPrincipal.removeLayer(marcadorTemporal);
            marcadorTemporal = null;
            cargarZonasDesdeDB();
        } else {
            alert(data.message);
        }
    });
}

function votarZona(e, id, valor) {
    if (e && e.stopPropagation) {
        e.stopPropagation();
    }
    fetch('votar_zona.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ zona_id: id, voto: valor })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            mostrarNotificacion("¡Voto registrado!");
            cargarZonasDesdeDB();
        } else {
            alert(data.message);
        }
    });
}

function filtrarMarcadores(tipo) {
    const nombresFiltros = {
        'todos': 'Mostrando Todo',
        'prohibido': 'Zonas Prohibidas',
        'estanco': 'Estancos',
        'hospital': 'Hospitales',
        'colegio': 'Colegios',
        'para_fumar': 'Zonas para Fumar',
        'libre_humo': 'Zonas Libres de Humo'
    };

    mostrarNotificacion(nombresFiltros[tipo] || 'Filtro aplicado');
    cargarZonasDesdeDB(tipo);
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

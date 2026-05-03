let mapaEstancos;
let marcadoresEstancos = [];
let circulosZonasEstancos = [];
let marcadorUsuarioEstancos;

// Coordenadas centrales de Molins de Rei
const centroMolins = [41.4137, 2.0158];

// Base de datos de Estancos en Molins de Rei
const datosEstancos = [
    { nombre: "Estanco Passeig de Pi i Margall", lat: 41.4136, lng: 2.0164, direccion: "Passeig de Pi i Margall" },
    { nombre: "Estanc Molins Avinguda de València", lat: 41.4110, lng: 2.0180, direccion: "Avinguda de València" }, 
    { nombre: "Tabacs L'Estanc Jacint Verdaguer", lat: 41.4150, lng: 2.0145, direccion: "Carrer Jacint Verdaguer" } 
];

// Base de datos de Zonas Libres de Humo (Las mismas del mapa principal)
const zonasLibresDeHumo = [
    { nombre: "Parc de la Mariona", lat: 41.4056, lng: 2.0220, radio: 110 },
    { nombre: "Parc del Pont de la Cadena", lat: 41.4128, lng: 2.0145, radio: 85 },
    { nombre: "CAP Molins de Rei", lat: 41.4145, lng: 2.0223, radio: 65 },
    { nombre: "Ajuntament de Molins", lat: 41.4139, lng: 2.0158, radio: 50 },
    { nombre: "Escola Josep Maria Madorell", lat: 41.4129, lng: 2.0187, radio: 75 },
    { nombre: "Passeig de Pi i Margall", lat: 41.4136, lng: 2.0164, radio: 90 },
    { nombre: "Terraza El Racó", lat: 41.4148, lng: 2.0170, radio: 30 }
];

function iniciarMapaEstancos() {
    // 1. Inicializar el mapa en el contenedor de estancos
    mapaEstancos = L.map('mapa-estancos-contenedor').setView(centroMolins, 15);

    // 2. Añadir la capa visual
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap',
        maxZoom: 19
    }).addTo(mapaEstancos);

    // Evitar que el mapa se vea cortado al cargar
    setTimeout(() => { mapaEstancos.invalidateSize(); }, 200);

    // 3. Dibujar inicialmente todo
    dibujarMapaEstancos('todos');

    // 6. Configurar el buscador de la página de estancos
    configurarBuscadorEstancos();
}

function dibujarMapaEstancos(filtro) {
    // Borrar anteriores
    marcadoresEstancos.forEach(m => mapaEstancos.removeLayer(m));
    circulosZonasEstancos.forEach(c => mapaEstancos.removeLayer(c));
    marcadoresEstancos = [];
    circulosZonasEstancos = [];

    // 1. Dibujar Zonas Libres de Humo (Verdes)
    if (filtro === 'todos' || filtro === 'zona') {
        zonasLibresDeHumo.forEach(zona => {
            const color = zona.radio > 60 ? '#00796B' : '#4db6ac';
            const circulo = L.circle([zona.lat, zona.lng], {
                color: color,
                fillColor: color,
                fillOpacity: 0.25,
                radius: zona.radio
            }).bindPopup(`
                <div style="text-align:center;">
                    <b style="color: ${color};">🚫 ${zona.nombre}</b><br>
                    <span style="font-size: 12px; color: #666;">Zona Protegida</span>
                </div>
            `).addTo(mapaEstancos);
            circulosZonasEstancos.push(circulo);
        });
    }

    // 2. Dibujar los Estancos (Marcadores con icono de cigarrillo)
    if (filtro === 'todos' || filtro === 'estanco') {
        datosEstancos.forEach((estanco, index) => {
            const iconoHTML = L.divIcon({
                html: `<div style="background:white; color:#00796B; width:30px; height:30px; border-radius:50%; display:flex; justify-content:center; align-items:center; border:2px solid #00796B; box-shadow:0 2px 5px rgba(0,0,0,0.2);"><i class="fa-solid fa-smoking"></i></div>`,
                className: 'custom-div-icon',
                iconSize: [30, 30],
                iconAnchor: [15, 15]
            });

            const marker = L.marker([estanco.lat, estanco.lng], { icon: iconoHTML })
                .bindPopup(`
                    <div style="text-align:center;">
                        <b style="color:#00796B;">🚬 ${estanco.nombre}</b><br>
                        <span style="font-size: 12px; color: #555;">${estanco.direccion}</span>
                    </div>
                `)
                .addTo(mapaEstancos);
            
            marcadoresEstancos.push(marker);
        });
    }

    // Conectar botones "Ir" (solo si están visibles los estancos)
    const botonesIr = document.querySelectorAll('.estanco-btn');
    botonesIr.forEach(boton => {
        boton.replaceWith(boton.cloneNode(true)); // Limpiar eventos anteriores
    });
    
    document.querySelectorAll('.estanco-btn').forEach(boton => {
        boton.addEventListener('click', (e) => {
            const index = e.target.getAttribute('data-index');
            if (index !== null && datosEstancos[index]) {
                const estanco = datosEstancos[index];
                if (filtro !== 'todos' && filtro !== 'estanco') {
                    // Si no están visibles, los mostramos para poder ir
                    dibujarMapaEstancos('todos');
                }
                mapaEstancos.flyTo([estanco.lat, estanco.lng], 18, { animate: true, duration: 1.5 });
                // Re-buscar el marcador en la lista actualizada
                const marker = marcadoresEstancos.find(m => m.getLatLng().lat === estanco.lat && m.getLatLng().lng === estanco.lng);
                if (marker) marker.openPopup();
            }
        });
    });
}

function filtrarEstancos(tipo) {
    const nombresFiltros = {
        'todos': 'Mostrando Todo',
        'estanco': 'Estancos',
        'zona': 'Zonas Libres de Humo'
    };

    mostrarNotificacion(nombresFiltros[tipo] || 'Filtro aplicado');
    dibujarMapaEstancos(tipo);
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

function toggleLeyenda() {
    const leyenda = document.getElementById('leyenda-mapa');
    if (leyenda) {
        leyenda.classList.toggle('colapsado');
    }
}

function localizarUsuarioEstancos() {
    if (!navigator.geolocation) {
        alert("Tu navegador no soporta geolocalización.");
        return;
    }

    mostrarNotificacion("Buscando tu ubicación...");

    navigator.geolocation.getCurrentPosition(
        (pos) => {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;

            if (marcadorUsuarioEstancos) {
                marcadorUsuarioEstancos.setLatLng([lat, lng]);
            } else {
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

                marcadorUsuarioEstancos = L.marker([lat, lng], { icon: iconoUsuario }).addTo(mapaEstancos);
                marcadorUsuarioEstancos.bindPopup("<b>Estás aquí</b><br>Busca el estanco más cercano fuera de zona.");
            }

            mapaEstancos.flyTo([lat, lng], 17, { animate: true, duration: 1.5 });
            marcadorUsuarioEstancos.openPopup();
        },
        (err) => {
            console.error("Error de geolocalización:", err);
            alert("No se pudo obtener tu ubicación.");
        },
        { enableHighAccuracy: true }
    );
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
                    if (data.length > 0) {
                        mapaEstancos.flyTo([data[0].lat, data[0].lon], 17, { animate: true, duration: 1.5 });
                    } else {
                        alert("No se encontró la ubicación en Molins de Rei.");
                    }
                })
                .catch(err => console.error("Error en búsqueda:", err));
        }
    };

    btnBusqueda.addEventListener('click', buscar);
    inputBusqueda.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') buscar();
    });
}

// Iniciar el mapa cuando el documento esté listo
document.addEventListener('DOMContentLoaded', iniciarMapaEstancos);
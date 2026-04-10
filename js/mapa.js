let mapaPrincipal;
let circulosZonas = []; // Aquí guardaremos los círculos para poder filtrarlos

// Coordenadas centrales de Molins de Rei
const centroMolins = [41.4137, 2.0158];

// Base de datos simulada de Zonas Libres de Humo en Molins
const zonasLibresDeHumo = [ 
    // Parques (Radio más amplio)
    { nombre: "Parc de la Mariona", lat: 41.4115, lng: 2.0162, radio: 90, tipo: "parque" },
    { nombre: "Parc del Pont de la Cadena", lat: 41.4095, lng: 2.0205, radio: 75, tipo: "parque" },
    
    // Edificios (Hospitales, colegios, ayuntamiento)
    { nombre: "CAP Molins de Rei (Centro de Salud)", lat: 41.4131, lng: 2.0130, radio: 50, tipo: "edificio" },
    { nombre: "Plaça de l'Ajuntament", lat: 41.4142, lng: 2.0155, radio: 45, tipo: "edificio" },
    { nombre: "Escola Josep Maria Madorell", lat: 41.4155, lng: 2.0140, radio: 60, tipo: "edificio" },
    
    // Terrazas / Paseos
    { nombre: "Passeig de Pi i Margall", lat: 41.4136, lng: 2.0164, radio: 80, tipo: "terraza" },
    { nombre: "Terraza El Racó", lat: 41.4148, lng: 2.0170, radio: 25, tipo: "terraza" }
];

function iniciarMapa() {
    // 1. Inicializar el mapa
    mapaPrincipal = L.map('mapa-contenedor').setView(centroMolins, 15);

    // 2. Añadir la capa visual (el diseño del mapa)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap',
        maxZoom: 19
    }).addTo(mapaPrincipal);

    // Solución para que el mapa no se vea cortado al cargar
    setTimeout(() => { mapaPrincipal.invalidateSize(); }, 200);

    // 3. Dibujar las zonas iniciales (todas)
    dibujarZonas('todos');
    
    // 4. Configurar el buscador
    configurarBuscadorPrincipal();
}

// Función para pintar los círculos rojos
function dibujarZonas(filtro) {
    // Primero borramos los círculos que ya existan en el mapa (para que el filtro funcione limpio)
    circulosZonas.forEach(circulo => mapaPrincipal.removeLayer(circulo));
    circulosZonas = []; // Vaciamos la lista

    // Recorremos nuestras zonas y pintamos las que coincidan con el filtro
    zonasLibresDeHumo.forEach(zona => {
        if (filtro === 'todos' || zona.tipo === filtro) {
            
            // L.circle dibuja un radio en el mapa
            const circulo = L.circle([zona.lat, zona.lng], {
                color: '#ff4500',      // Color del borde del círculo (Naranja/Rojo Smokecan)
                fillColor: '#ff4500',  // Color de relleno
                fillOpacity: 0.35,     // Transparencia del relleno (0.0 a 1.0)
                radius: zona.radio     // Radio de prohibición en metros
            }).addTo(mapaPrincipal);

            // Añadimos el cartelito (Popup) al hacer clic en el círculo
            circulo.bindPopup(`
                <div style="text-align:center;">
                    <b style="color: #ff4500; font-size: 14px;">🚫 ${zona.nombre}</b><br>
                    <span style="font-size: 12px; color: #555;">Zona Libre de Humo</span><br>
                    <i style="font-size: 11px;">Multa estimada: 50€ - 500€</i>
                </div>
            `);
            
            // Guardamos el círculo en la lista
            circulosZonas.push(circulo);
        }
    });
}

// Esta función se llama desde los botones HTML (onclick="filtrarMarcadores('parque')")
function filtrarMarcadores(tipo) {
    dibujarZonas(tipo);
}

function configurarBuscadorPrincipal() {
    const input = document.getElementById("input-buscador");
    
    if (!input) return;

    input.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            const texto = input.value.toLowerCase().trim();
            if (texto !== "") {
                // Buscamos la calle usando Nominatim de OpenStreetMap
                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(texto + ", Molins de Rei")}&limit=1`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.length > 0) {
                            // Volamos hacia la calle buscada
                            mapaPrincipal.flyTo([data[0].lat, data[0].lon], 17, { animate: true, duration: 1.5 });
                        } else {
                            alert("No se encontró la ubicación en Molins de Rei. Prueba a poner el nombre de una calle.");
                        }
                    })
                    .catch(err => console.error("Error en búsqueda:", err));
            }
        }
    });
}

// Iniciar el mapa cuando la página termine de cargar
document.addEventListener('DOMContentLoaded', iniciarMapa);
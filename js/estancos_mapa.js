let mapaEstancos;
let marcadoresEstancos = [];

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
    { nombre: "Parc de la Mariona", lat: 41.4115, lng: 2.0162, radio: 90 },
    { nombre: "Parc del Pont de la Cadena", lat: 41.4095, lng: 2.0205, radio: 75 },
    { nombre: "CAP Molins de Rei", lat: 41.4131, lng: 2.0130, radio: 50 },
    { nombre: "Plaça de l'Ajuntament", lat: 41.4142, lng: 2.0155, radio: 45 },
    { nombre: "Escola Josep Maria Madorell", lat: 41.4155, lng: 2.0140, radio: 60 },
    { nombre: "Passeig de Pi i Margall", lat: 41.4136, lng: 2.0164, radio: 80 },
    { nombre: "Terraza El Racó", lat: 41.4148, lng: 2.0170, radio: 25 }
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

    // 3. Dibujar las Zonas Libres de Humo (Círculos Rojos)
    zonasLibresDeHumo.forEach(zona => {
        L.circle([zona.lat, zona.lng], {
            color: '#ff4500',
            fillColor: '#ff4500',
            fillOpacity: 0.30, // Un poco más transparente para que se vean bien las calles
            radius: zona.radio
        }).bindPopup(`
            <div style="text-align:center;">
                <b style="color: #ff4500;">🚫 ${zona.nombre}</b><br>
                <span style="font-size: 12px;">Zona Libre de Humo</span>
            </div>
        `).addTo(mapaEstancos);
    });

    // 4. Dibujar los Estancos (Marcadores clásicos)
    datosEstancos.forEach((estanco, index) => {
        const marker = L.marker([estanco.lat, estanco.lng])
            .bindPopup(`
                <b>🚬 ${estanco.nombre}</b><br>
                <span style="font-size: 12px; color: #555;">${estanco.direccion}</span>
            `)
            .addTo(mapaEstancos);
        
        marcadoresEstancos.push(marker); // Guardamos el marcador
    });

    // 5. Conectar los botones "Ir" del HTML con el mapa
    const botonesIr = document.querySelectorAll('.estanco-btn');
    botonesIr.forEach(boton => {
        boton.addEventListener('click', (e) => {
            const index = e.target.getAttribute('data-index');
            if (index !== null && marcadoresEstancos[index]) {
                const estanco = datosEstancos[index];
                // Hace un efecto de "vuelo" hacia el estanco
                mapaEstancos.flyTo([estanco.lat, estanco.lng], 18, { animate: true, duration: 1.5 });
                // Abre el cartelito del estanco automáticamente
                marcadoresEstancos[index].openPopup();
            }
        });
    });

    // 6. Configurar el buscador de la página de estancos
    configurarBuscadorEstancos();
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
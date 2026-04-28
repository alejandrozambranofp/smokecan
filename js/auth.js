document.addEventListener('DOMContentLoaded', function() {
    // 1. Buscamos el contenedor del icono de usuario
    const contenedorUsuario = document.querySelector('.icono-usuario');
    const enlaceUsuario = document.querySelector('.usuario-circulo a');
    
    // 2. Función para comprobar si existe la cookie de logueo
    const obtenerCookie = (nombre) => {
        const valor = `; ${document.cookie}`;
        const partes = valor.split(`; ${nombre}=`);
        if (partes.length === 2) return partes.pop().split(';').shift();
    };

    const estaLogueado = obtenerCookie('usuario_logeado');

    // 3. Lógica para el área de usuario (perfil / iniciar sesión)
    if (contenedorUsuario) {
        if (estaLogueado === "1") {
            // Si está logueado, nos aseguramos de que el icono lleve al perfil
            if (enlaceUsuario) {
                enlaceUsuario.href = 'perfil.php';
                // Añadimos un borde verde para indicar sesión activa
                enlaceUsuario.parentElement.style.border = "2px solid #00796B";
            }
        } else {
            // Si no está logueado, reemplazamos el círculo por un botón de "Iniciar sesión"
            contenedorUsuario.innerHTML = `
                <a href="login.php" style="
                    background-color: white; 
                    color: #00796B; 
                    padding: 8px 15px; 
                    border-radius: 20px; 
                    text-decoration: none; 
                    font-weight: bold;
                    font-size: 14px;
                    white-space: nowrap;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
                ">Iniciar sesión</a>
            `;
            contenedorUsuario.style.width = 'auto'; // Ajustamos el ancho para el texto
            contenedorUsuario.style.height = 'auto';
        }
    }
});
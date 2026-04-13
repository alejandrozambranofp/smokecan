document.addEventListener('DOMContentLoaded', function() {
    // 1. Buscamos el enlace dentro del círculo del usuario
    const enlaceUsuario = document.querySelector('.usuario-circulo a');
    
    // 2. Función para comprobar si existe la cookie de logueo
    const obtenerCookie = (nombre) => {
        const valor = `; ${document.cookie}`;
        const partes = valor.split(`; ${nombre}=`);
        if (partes.length === 2) return partes.pop().split(';').shift();
    };

    const estaLogueado = obtenerCookie('usuario_logeado');

    // 3. Lógica de redirección dinámica
    if (enlaceUsuario) {
        if (estaLogueado === "1") {
            // Si está logueado, el icono lleva al perfil
            enlaceUsuario.href = 'perfil.php';
            // Opcional: añadimos un borde naranja para indicar sesión activa
            enlaceUsuario.parentElement.style.border = "2px solid #ff4500";
        } else {
            // Si no está logueado, lleva al login
            enlaceUsuario.href = 'login.php';
        }
    }
});
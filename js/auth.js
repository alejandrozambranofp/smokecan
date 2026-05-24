document.addEventListener('DOMContentLoaded', async function() {
    const contenedorUsuario = document.querySelector('.icono-usuario');
    const enlaceUsuario = document.querySelector('.usuario-circulo a');

    let estadoSesion = { logeado: false, es_admin: false };

    try {
        const respuesta = await fetch('auth_status.php', {
            credentials: 'same-origin',
            cache: 'no-store'
        });

        if (respuesta.ok) {
            estadoSesion = await respuesta.json();
        }
    } catch (error) {
        console.error('No se pudo comprobar la sesion:', error);
    }

    if (contenedorUsuario) {
        if (estadoSesion.logeado) {
            if (enlaceUsuario) {
                enlaceUsuario.href = 'perfil.php';
                enlaceUsuario.parentElement.style.border = '2px solid #00796B';

                if (estadoSesion.es_admin) {
                    const navLinks = document.querySelector('.enlaces-nav');
                    if (navLinks && !document.getElementById('nav-admin-link')) {
                        const adminLink = document.createElement('div');
                        adminLink.className = 'enlace-item';
                        adminLink.id = 'nav-admin-link';
                        adminLink.innerHTML = '<a href="admin.php">Panel Admin</a>';
                        navLinks.appendChild(adminLink);
                    }
                }
            }
        } else {
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
                ">Iniciar sesion</a>
            `;
            contenedorUsuario.style.width = 'auto';
            contenedorUsuario.style.height = 'auto';
        }
    }
});

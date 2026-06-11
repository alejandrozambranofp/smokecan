document.addEventListener('DOMContentLoaded', async function() {
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

    const navLinks = document.querySelector('.enlaces-nav');
    if (navLinks) {
        const oldPerfil = document.getElementById('nav-perfil-link');
        const oldLogin = document.getElementById('nav-login-link');
        const oldRegistro = document.getElementById('nav-registro-link');
        const oldAdmin = document.getElementById('nav-admin-link');
        if (oldPerfil) oldPerfil.remove();
        if (oldLogin) oldLogin.remove();
        if (oldRegistro) oldRegistro.remove();
        if (oldAdmin) oldAdmin.remove();

        if (estadoSesion.logeado) {
            const perfilLink = document.createElement('div');
            perfilLink.className = 'enlace-item';
            perfilLink.id = 'nav-perfil-link';
            perfilLink.innerHTML = '<a href="perfil.php">Mi Perfil</a>';
            if (window.location.pathname.includes('perfil.php')) {
                perfilLink.querySelector('a').className = 'activo';
            }
            navLinks.appendChild(perfilLink);

            if (estadoSesion.es_admin) {
                const adminLink = document.createElement('div');
                adminLink.className = 'enlace-item';
                adminLink.id = 'nav-admin-link';
                adminLink.innerHTML = '<a href="admin.php">Panel Admin</a>';
                if (window.location.pathname.includes('admin.php')) {
                    adminLink.querySelector('a').className = 'activo';
                }
                navLinks.appendChild(adminLink);
            }
        } else {
            const loginLink = document.createElement('div');
            loginLink.className = 'enlace-item';
            loginLink.id = 'nav-login-link';
            loginLink.innerHTML = '<a href="login.php">Iniciar Sesión</a>';
            if (window.location.pathname.includes('login.php')) {
                loginLink.querySelector('a').className = 'activo';
            }
            navLinks.appendChild(loginLink);

            const registroLink = document.createElement('div');
            registroLink.className = 'enlace-item';
            registroLink.id = 'nav-registro-link';
            registroLink.innerHTML = '<a href="registro.php">Registrarse</a>';
            if (window.location.pathname.includes('registro.php')) {
                registroLink.querySelector('a').className = 'activo';
            }
            navLinks.appendChild(registroLink);
        }
    }

    const cabeceraDer = document.querySelector('.cabecera-espacio-der');
    if (cabeceraDer) {
        cabeceraDer.innerHTML = '';
        if (!estadoSesion.logeado) {
            const wrapper = document.createElement('div');
            wrapper.className = 'cabecera-botones-auth';
            wrapper.innerHTML = '<a href="login.php" class="btn-auth-header">Iniciar Sesión</a><a href="registro.php" class="btn-auth-header btn-registro-header">Registrarse</a>';
            cabeceraDer.appendChild(wrapper);
        }
    }

    const btnMenu = document.getElementById('btn-menu-hamburguesa');
    const navMenu = document.querySelector('.cabecera-navegacion');
    if (btnMenu && navMenu) {
        btnMenu.addEventListener('click', function() {
            btnMenu.classList.toggle('abierto');
            navMenu.classList.toggle('abierto');
        });
    }
});

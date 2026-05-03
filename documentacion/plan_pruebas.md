# Plan de Pruebas y Validación: Smokecan

Este documento detalla las pruebas realizadas en la fase de cierre del proyecto para garantizar que el producto funciona correctamente, ofrece una buena experiencia de usuario (UX) y mantiene un nivel adecuado de rendimiento y seguridad.

## 1. Pruebas Funcionales (Happy Path & Edge Cases)

### Registro de Usuario
- **Caso de Éxito:** Un usuario ingresa nombre, apellidos, correo válido y contraseña. Es redirigido al login correctamente.
- **Caso de Error (Email duplicado):** Un usuario intenta registrarse con un email ya existente. Se muestra el mensaje: "Este correo electrónico ya está registrado."
- **Seguridad (Validada):** El registro utiliza sentencias preparadas de MySQLi (Prepared Statements) para prevenir inyección SQL. Las contraseñas se guardan mediante `password_hash()`.

### Inicio de Sesión y Sesiones
- **Caso de Éxito:** Un usuario ingresa sus credenciales válidas. Es redirigido a `index.html` y la interfaz cambia (ej. icono de usuario logueado en lugar de login genérico).
- **Caso de Error:** Credenciales inválidas muestran "La contraseña es incorrecta" o "No existe cuenta con ese email".
- **Cierre de Sesión:** Hacer clic en logout destruye correctamente la sesión en el servidor y la cookie del lado del cliente, redirigiendo a `index.html`.

### Foro y Publicación de Comentarios
- **Publicación:** Un usuario autenticado publica un comentario con una calificación por estrellas. El comentario se guarda y el usuario es redirigido a la página del foro con el nuevo comentario visible.
- **Autoría:** El sistema asigna correctamente el nombre del usuario al comentario mediante `$_SESSION['nombre']` en lugar de publicarlo como "Invitado".
- **Seguridad (Validada):** Prevención contra ataques XSS utilizando `htmlspecialchars()` a la hora de renderizar el contenido del usuario en el foro.

## 2. Pruebas de Experiencia de Usuario (UX)

Se han realizado pruebas con usuarios simulados para observar su interacción con la plataforma:
- **Navegación del Mapa:** El mapa de Leaflet.js carga fluidamente y es el elemento central. Los usuarios identifican rápidamente cómo buscar una calle en Molins de Rei.
- **Claridad de la Leyenda:** Los filtros por color (Libre de humo, Permite fumar, etc.) se han probado asegurando que son inclusivos (colores contrastados).
- **Flujo hacia el Foro:** Los enlaces de navegación (Header y tarjetas) conducen intuitivamente hacia el foro donde los usuarios pueden interactuar.

## 3. Pruebas de Rendimiento y Despliegue

- **Tiempo de Carga de Imágenes:** Se recomienda comprimir las imágenes de la carpeta `img/` y del slider/populares para mejorar el tiempo de carga del primer pintado (LCP).
- **Archivos Estáticos:** Los archivos CSS y JS están separados y se cargan al final del HTML o de forma asíncrona cuando es posible.
- **Resiliencia (Failover / Hosting):** El sistema depende de una base de datos MySQL relacional. Se recomienda configurar copias de seguridad automáticas (backups diarios) y considerar un hosting que soporte balanceo de carga en caso de picos de tráfico en eventos locales o cambios normativos de la ley antitabaco.

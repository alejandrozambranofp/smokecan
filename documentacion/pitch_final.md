# Pitch Final: Smokecan (Guion y Estructura)

Este documento es una guía paso a paso para preparar la presentación oral de tu proyecto frente al tribunal. Recuerda que no debes leer textualmente, sino utilizar estos puntos como hilo conductor.

---

## 1. El Gancho y el Problema (1-2 Minutos)
*(Objetivo: Captar la atención y explicar por qué tu proyecto existe)*

- **Apertura:** "¿Cuántas veces habéis querido disfrutar de una terraza, un parque o un paseo, y os habéis tenido que ir porque el humo del tabaco o los vapeadores lo hacían imposible?"
- **Contexto:** Las normativas antitabaco están cambiando y cada vez hay más zonas libres de humo, pero la información está fragmentada y muchas veces los usuarios no saben a dónde ir.
- **El Problema:** Falta una herramienta local, clara y actualizada que mapee estas zonas y conecte a los usuarios.

## 2. La Solución y Propuesta de Valor: Smokecan (2 Minutos)
*(Objetivo: Presentar tu producto de forma clara)*

- **Presentación:** "Por eso he creado **Smokecan**: una plataforma web interactiva y comunitaria diseñada inicialmente para Molins de Rei."
- **Funcionalidades Clave:**
  - Un **mapa interactivo** en tiempo real (Leaflet.js) donde puedes buscar zonas y ver su estado respecto a la normativa.
  - Un **Foro Comunitario** donde los usuarios verificados pueden subir fotos, puntuar y dejar reseñas de establecimientos o parques, creando una red de información fiable.
- **El Valor Diferencial:** No es solo una app estática del ayuntamiento; es colaborativa. Los propios usuarios mantienen vivo el mapa.

## 3. Demostración en Vivo / "Demo" (2-3 Minutos)
*(Objetivo: Demostrar que funciona sin errores)*

*(Prepara el entorno local y ten la base de datos funcionando. Haz la demo dinámica)*
- **Paso 1:** Abre `index.html`. Muestra el mapa, interactúa un poco con él (haz zoom, muestra los marcadores o los filtros).
- **Paso 2:** Haz clic en el icono de usuario y realiza un inicio de sesión (`login.php`). Menciona que "ahora tengo permisos de la comunidad".
- **Paso 3:** Ve a la sección del Foro. Abre el modal de "Publicar nuevo comentario", ponle 5 estrellas a una terraza (ej: "Terraza El Racó"), sube una foto de prueba y dale a publicar.
- **Paso 4:** Muestra cómo el comentario aparece instantáneamente en el feed con tu nombre de usuario. ¡Magia!

## 4. Arquitectura y Viabilidad Técnica (1-2 Minutos)
*(Objetivo: Demostrar tus conocimientos técnicos y solidez)*

- **Tecnologías:** "Smokecan está desarrollado con HTML/CSS/JS nativo para una experiencia fluida, y respaldado por PHP y MySQL en el servidor."
- **Seguridad (Muy importante para el tribunal):** "He implementado **Consultas Preparadas (Prepared Statements)** en todo el sistema de autenticación para asegurar que la base de datos esté blindada contra inyecciones SQL, además del cifrado de contraseñas."
- **Eficiencia:** "La carga del mapa es independiente gracias a la API de Leaflet, lo que reduce la carga de nuestro servidor propio."

## 5. El Cierre (1 Minuto)
*(Objetivo: Dejar una impresión duradera y transmitir seguridad)*

- **Visión a Futuro:** "Smokecan es escalable. Hoy es Molins de Rei, mañana puede ser toda Barcelona, integrando notificaciones push o gamificación para premiar a los locales libres de humo."
- **Conclusión final:** "Hemos pasado de una idea a un producto real, seguro y funcional que soluciona una necesidad ciudadana. Smokecan es vuestra guía definitiva. Muchas gracias, estaré encantado de responder cualquier pregunta."

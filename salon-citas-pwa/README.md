# Belleza Citas — Sistema de citas para una peluquería

Proyecto académico de Programación Web Avanzada. Desarrollado con PHP nativo, MySQL, HTML, Bootstrap 5, CSS y JavaScript, sin utilizar frameworks PHP.

## 1. Función general

Permite a los clientes registrarse, solicitar citas, consultar su estado y cancelar citas futuras. El peluquero puede revisar su agenda y confirmar o rechazar solicitudes; también puede marcar una cita confirmada como completada.

## 2. Tecnologías

- HTML5 y Bootstrap 5.
- CSS3 propio y diseño adaptable.
- JavaScript para confirmaciones, instalación y registro del Service Worker.
- PHP 8.1 o superior, con PDO.
- MySQL / MariaDB, administrable mediante phpMyAdmin.
- PWA: `manifest.json`, iconos, Service Worker y página alternativa sin conexión.

## 3. Instalación local con XAMPP

1. Instala y abre XAMPP.
2. Inicia Apache y MySQL.
3. Copia la carpeta `salon-citas-pwa` dentro de `C:\xampp\htdocs\`.
4. Abre `http://localhost/phpmyadmin`.
5. Importa el archivo `database.sql` que está en la raíz del proyecto.
6. Revisa `config/database.php`. En XAMPP normalmente el usuario es `root` y la contraseña está vacía.
7. Revisa `config/config.php`. La dirección local inicial es `http://localhost/salon-citas-pwa`.
8. Abre `http://localhost/salon-citas-pwa`.

## 4. Cuentas de demostración

| Rol | Correo | Contraseña |
|---|---|---|
| Peluquero | peluquero@demo.com | Peluquero123! |
| Cliente | cliente@demo.com | Cliente123! |

El registro público crea únicamente cuentas de Cliente. La cuenta de Peluquero de demostración ya viene creada en la base de datos. Cambia las contraseñas antes de publicar el sistema.

## 5. Funcionalidades

### Cliente
- Registro e inicio de sesión.
- Consulta de servicios.
- Reserva de cita con peluquero, fecha y hora.
- Consulta de citas y estados.
- Cancelación de citas futuras pendientes o confirmadas.

### Peluquero
- Inicio de sesión.
- Consulta de citas asignadas.
- Confirmación o rechazo de solicitudes pendientes.
- Marcado de citas confirmadas como completadas.

## 6. Base de datos

- `roles`: perfiles Cliente y Peluquero.
- `usuarios`: datos de acceso y perfil de cada persona.
- `servicios`: catálogo, duración y precio.
- `citas`: cliente, peluquero, servicio, fecha, hora y estado.

Las claves foráneas relacionan las citas con los usuarios y servicios correspondientes.

## 7. Seguridad y organización

- Consultas preparadas con PDO.
- Contraseñas cifradas con `password_hash()` y verificadas con `password_verify()`.
- Validación de sesión y autorización por rol.
- Token CSRF en formularios que modifican información.
- Escape de datos mostrados mediante `htmlspecialchars()`.
- Código separado por configuración, autenticación, funciones, vistas y módulos.

## 8. Instalación como PWA

La PWA requiere HTTPS en el hosting o `localhost` durante el desarrollo. El navegador debe permitir la instalación. El Service Worker guarda los recursos estáticos y muestra `offline.html` cuando se intenta abrir una pantalla sin conexión. Las operaciones de reserva y administración requieren Internet y conexión con MySQL.

## 9. Publicación

1. Crea un repositorio de GitHub y sube el contenido de esta carpeta. El archivo `database.sql` debe permanecer en la raíz.
2. Crea una base de datos MySQL en el proveedor de hosting e importa `database.sql`.
3. Actualiza `config/database.php` con el host, nombre de base, usuario y contraseña del hosting.
4. Actualiza `APP_URL` en `config/config.php` con la URL real.
5. Sube los archivos PHP, CSS, JavaScript, iconos y manifest al hosting.
6. Comprueba el acceso con HTTPS, crea una cuenta cliente y prueba el flujo completo de una cita.
7. Copia las URL reales de GitHub y del sitio publicado en el documento Word de entrega.

## 10. Limitaciones del prototipo

- No procesa pagos en línea.
- No envía notificaciones por correo o WhatsApp.
- La disponibilidad se valida por coincidencia exacta de fecha, hora y peluquero; para producción se recomienda ampliar la validación considerando la duración de cada servicio y los horarios de atención.
- El contenido dinámico PHP requiere conexión al servidor y a MySQL.

## 11. Pruebas recomendadas

1. Registrar un nuevo cliente.
2. Iniciar sesión con el cliente de demostración.
3. Consultar los servicios.
4. Crear una cita con fecha futura.
5. Iniciar sesión como peluquero y confirmar la cita.
6. Volver al cliente y verificar el estado confirmado.
7. Cancelar una cita futura desde el cliente.
8. Probar que el cliente no pueda entrar a las acciones exclusivas del peluquero.
9. Probar el registro del Service Worker desde `localhost` o HTTPS.

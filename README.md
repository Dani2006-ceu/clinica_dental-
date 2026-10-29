# Dental Alfadent — Sistema de Gestión Odontológica

Sistema web en PHP + MySQL con arquitectura **MVC** para la Clínica Odontológica "Dental Alfadent".
Gestiona pacientes, odontólogos por especialidad, citas, diagnósticos, tratamientos, servicios, pagos
(efectivo, tarjeta de crédito, tarjeta de débito y Yape con QR) y reportes.

## Estructura

```
Odontologia_clase/
├── assetes/
│   ├── css/estilos.css      Estilos (responsive)
│   └── js/app.js            Comportamiento de la interfaz (menú, autocompletados, panel Yape)
├── config/                  Conexión PDO, funciones de apoyo e íconos
├── controller/              Un controlador por módulo (reciben la petición y eligen la vista)
├── database/                Script de la base de datos y migración
├── imagenes/                Imágenes generadas con Canva, logo y QR de Yape
├── model/                   Clases con las consultas preparadas a la base de datos
├── views/                   Vistas HTML/PHP (layout, auth y una carpeta por módulo)
├── index.php                Controlador frontal: index.php?c=<modulo>&a=<accion>
└── README.md
```

Las carpetas `config`, `controller`, `model`, `views` y `database` tienen un `.htaccess`
que impide abrirlas directamente desde el navegador.

## Instalación (XAMPP)

1. Copiar la carpeta en `C:\xampp\htdocs\`.
2. Encender **Apache** y **MySQL** en el panel de XAMPP.
3. En http://localhost/phpmyadmin importar `database/database.sql` y después `database/migracion_seguridad.sql`
   (antes, cambiar en ese archivo la clave del usuario `alfadent_app`).
4. Copiar `config/credenciales.ejemplo.php` como `config/credenciales.php` y poner esa misma clave.
5. Abrir http://localhost/Odontologia_clase/

## Seguridad (8 capas)

1. **Base de datos con permisos mínimos**: la app usa el usuario `alfadent_app` (solo SELECT/INSERT/UPDATE/DELETE), nunca root.
   Credenciales en `config/credenciales.php`, fuera de git. Consultas preparadas y sin consultas múltiples.
2. **Sesión blindada**: cookie HttpOnly + SameSite=Strict, ID nuevo al ingresar, cierre por 30 min de inactividad u 8 h,
   y la sesión se invalida si la cookie se usa desde otro navegador.
3. **Anti fuerza bruta**: 5 intentos fallidos por correo (o 15 por IP) bloquean el login 15 minutos.
4. **Cuentas con aprobación**: quien se registra queda inactivo hasta que el administrador lo active.
   Si un usuario es desactivado o eliminado, pierde el acceso al instante.
5. **Contraseñas robustas**: mínimo 8 caracteres con letras y números, bcrypt, re-cifrado automático y
   login con tiempo constante (no revela qué correos existen).
6. **Cabeceras HTTP**: Content-Security-Policy, anti-clickjacking, nosniff, sin caché de datos de pacientes. Además CSRF y escape XSS.
7. **Archivos protegidos**: `.htaccess` bloquea `.sql`, `.txt`, `.md`, archivos ocultos y listados de carpetas; en `assetes/` e
   `imagenes/` no se ejecuta PHP. Los errores no se muestran: se guardan en `logs/`.
8. **Bitácora de auditoría**: logins, fallos, bloqueos y toda operación que modifica datos, con usuario, IP y fecha.
   El administrador la ve en Usuarios → Bitácora y la tabla no se puede modificar ni borrar (triggers).

Usuario inicial: `admin@sonrisa.com` / `admin123`

> Si ya tenías la base de una versión anterior, en lugar de `database.sql` importa las migraciones
> que te falten para no perder datos: `database/migracion_diagnosticos.sql` y `database/migracion_pagos.sql`.

## Datos de la clínica

Nombre, dirección, correo de soporte y mapa se configuran en un solo lugar, al inicio de `config/funciones.php`
(`CLINICA_NOMBRE`, `CLINICA_DIRECCION`, `CLINICA_CORREO`, `CLINICA_MAPA`).

- Dirección: Carretera Federico Basadre, Neshuya — https://maps.app.goo.gl/7szxfNci3KPgKH6T7
- Soporte: daniizquierdo2006@gmail.com

## Pagos: no se eliminan, solo se anulan

Un pago es un comprobante, por eso el sistema no permite borrarlo:

- En la lista de pagos solo existen **Editar** y **Anular** (mientras el pago esté *Pagado*).
- Un pago **anulado** no se puede modificar y queda en el historial.
- La regla también está en la base de datos (triggers `trg_pagos_no_eliminar` y `trg_pagos_anulado_bloqueado`),
  así que se cumple aunque alguien intente borrar desde phpMyAdmin.
- Un tratamiento que ya tiene pagos no se puede eliminar.

## Rutas

| URL | Acción |
|-----|--------|
| `index.php?c=auth&a=login` | Iniciar sesión |
| `index.php?c=auth&a=registro` | Registrar usuario |
| `index.php?c=auth&a=logout` | Cerrar sesión |
| `index.php?c=pacientes` | Listado (igual para odontologos, citas, diagnosticos, tratamientos, servicios, pagos, usuarios) |
| `index.php?c=pacientes&a=nuevo` / `&a=editar&id=N` | Formularios |
| `index.php?c=reportes&tipo=atenciones\|ingresos\|pendientes` | Reportes |

## Pago con Yape

Al registrar un pago y elegir **Yape**, aparece el QR de la clínica con el **monto escrito en el formulario**
actualizado en tiempo real, un botón para copiar el monto y otro para mostrar el QR a pantalla completa al paciente.

El QR personal de Yape es estático: la app de Yape no permite precargar el monto desde el QR, por eso el
paciente debe escribir el monto que muestra el sistema y luego se registra el N° de operación.
Para cambiar el QR o el titular, reemplazar `imagenes/yape-qr.jpg` y editar `YAPE_TITULAR` en `config/funciones.php`.

## Boletas y reportes (imprimir, PDF y Excel)

- **Boleta de pago**: botón *Boleta* en la lista de pagos (también se abre sola al registrar un pago nuevo).
  Número `B001-00000001`, datos del paciente, detalle, total en letras, método de pago, N° de operación y
  estado de cuenta del tratamiento. Si el pago está anulado, la boleta sale con el sello **ANULADO**.
- **Reportes**: botón *Imprimir / PDF / Excel* en cada reporte. Abre el documento con membrete, resumen y tablas.
- Botones del documento: **Imprimir**, **Descargar PDF** (A4) y, en reportes, **Exportar Excel** (.xlsx real,
  montos como números con formato de soles).
- Las librerías están dentro del proyecto (`assetes/js/lib/html2pdf.bundle.min.js` y `xlsx.full.min.js`),
  así que funcionan sin internet. Estilos de los documentos: `assetes/css/documento.css`.

## Seguridad

- Consultas preparadas (PDO, `prepare` + `execute`) en todos los modelos.
- Contraseñas con `password_hash` / `password_verify`.
- Token CSRF en todos los formularios y acciones POST.
- Escapado de salida con `htmlspecialchars` (función `e()`).

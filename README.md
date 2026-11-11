# Gestión de Productos - MVC (PHP + PDO + MySQL + Bootstrap 5)

## Instalación en XAMPP
1. Copie la carpeta `3653_Guzman_Adriel` en `C:\xampp\htdocs\`.
2. Inicie **Apache** y **MySQL** desde el panel de XAMPP.
3. Abra `http://localhost/phpmyadmin`, vaya a **Importar** y seleccione `base_datos.sql`.
4. Si su MySQL usa usuario o clave distintos de `root` / vacío, edite `config/database.php`.
5. Abra `http://localhost/3653_Guzman_Adriel/index.php`.

Nota: Bootstrap, Bootstrap Icons y la fuente Outfit (Google Fonts) se cargan desde internet.

## Estructura
- `index.php`: punto de entrada; enruta cada acción al controlador.
- `config/`: conexión PDO (`database.php`) y funciones auxiliares (`app.php`).
- `models/`: consultas SQL preparadas sobre la tabla `productos`.
- `controllers/`: validación, lógica de cada operación y selección de vista.
- `views/`: plantillas HTML con Bootstrap 5 (cabecera, pie, listado y formulario).
- `css/estilos.css`: ajustes de identidad visual sobre Bootstrap.

## Paleta y tipografía
- Colores: `#31393C`, `#2176FF`, `#33A1FD`, `#FDCA40`, `#F79824`.
- Tipografía: Outfit (Google Fonts).
- Iconos: Bootstrap Icons.

## Operaciones
| Acción | Método | Descripción |
|---|---|---|
| listar | GET | Muestra todos los productos |
| buscar | GET | Busca por código o nombre; muestra mensaje si no hay coincidencias |
| nuevo / guardar | GET / POST | Formulario y registro (código automático) |
| editar / actualizar | GET / POST | Precarga datos y guarda cambios conservando el código |
| eliminar | POST | Elimina tras confirmación del usuario |

## Datos
La tabla `productos` tiene 4 campos (id, nombre, categoria, precio) y 20 registros: 5 originales y 15 adicionales.

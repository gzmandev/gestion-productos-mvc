API RESTful de Productos
Programación Web Básica - Examen práctico (Proceso de Aprendizaje II)

Estudiante: Guzmán, Adriel
NRC: 3653
Tecnologías: PHP nativo (sin frameworks), MySQL, PDO, XAMPP, Postman


1. CONTENIDO DEL PROYECTO

  api_productos/
  |-- config/conexion.php        Credenciales y conexión PDO (único lugar donde se definen)
  |-- api/productos.php          Endpoint con las operaciones GET, POST, PUT y DELETE
  |-- sql/evaluacion_api.sql     Crea la base de datos, la tabla y 6 productos de prueba
  |-- evidencias/                Capturas de Postman
  |-- README.txt                 Este archivo


2. PUESTA EN MARCHA

  1. Copiar la carpeta del proyecto dentro de C:\xampp\htdocs\
  2. Abrir el panel de XAMPP e iniciar Apache y MySQL.
  3. Entrar a http://localhost/phpmyadmin, abrir la pestaña "Importar" y cargar
     el archivo sql/evaluacion_api.sql. Esto crea la base "evaluacion_api",
     la tabla "productos" y los datos de prueba.
  4. Si el usuario o la contraseña de MySQL son distintos a los de XAMPP por
     defecto (root, sin contraseña), cambiarlos en config/conexion.php.
  5. URL base de la API:

     http://localhost/api_productos/api/productos.php

     (Si la carpeta se copia con otro nombre, por ejemplo 3653_Guzman_Adriel/api_productos,
      la URL pasa a ser http://localhost/3653_Guzman_Adriel/api_productos/api/productos.php)


3. ENDPOINTS

  Método   URL                      Body (JSON)   Respuestas
  -------  -----------------------  ------------  ---------------------------
  GET      productos.php            no            200
  GET      productos.php?id=3       no            200, 400, 404
  POST     productos.php            sí            201, 400, 409
  PUT      productos.php?id=3       sí            200, 400, 404, 409
  DELETE   productos.php?id=3       no            200, 400, 404

  Cualquier otro método devuelve 405 (Method Not Allowed).
  Si ocurre un error interno o de conexión se devuelve 500 con un mensaje
  genérico; el detalle técnico solo queda en el log del servidor.
  El id de PUT y DELETE se envía en la URL (?id=). Los datos van en el body.


4. CAMPOS DEL PRODUCTO

  codigo        Obligatorio. Solo letras y números, de 3 a 50 caracteres. No se repite.
  nombre        Obligatorio. De 3 a 100 caracteres.
  descripcion   Opcional. Texto.
  precio        Obligatorio. Número mayor que 0, máximo 2 decimales (hasta 99999999.99).
  stock         Obligatorio. Número entero mayor o igual a 0.

  id y fecha_registro los genera MySQL. Si llegan en el JSON se ignoran.
  En PUT se envían todos los campos (reemplazo completo) y fecha_registro no cambia.


5. EJEMPLOS

  POST  http://localhost/api_productos/api/productos.php
  Body -> raw -> JSON:

  {
    "codigo": "P007",
    "nombre": "Parlante Bluetooth",
    "descripcion": "Parlante portátil resistente al agua",
    "precio": 89.90,
    "stock": 15
  }

  Respuesta (201 Created):

  {
    "success": true,
    "message": "Producto registrado correctamente.",
    "data": {
      "id": 7,
      "codigo": "P007",
      "nombre": "Parlante Bluetooth",
      "descripcion": "Parlante portátil resistente al agua",
      "precio": "89.90",
      "stock": 15,
      "fecha_registro": "2026-10-10 18:16:18"
    }
  }

  Respuesta de error (400 Bad Request):

  {
    "success": false,
    "message": "Los datos enviados no son válidos.",
    "errors": {
      "precio": "El precio debe ser mayor que 0."
    }
  }


6. PRUEBAS PARA POSTMAN

  Para todas las peticiones con body: pestaña Body -> raw -> tipo JSON.
  Guardar las capturas en la carpeta evidencias/ (se muestran método, URL,
  body, código de respuesta y JSON devuelto).

  Prueba               Petición                                  Resultado esperado
  -------------------  ----------------------------------------  ------------------
  GET general          GET    productos.php                      200
  GET por ID           GET    productos.php?id=3                 200
  POST válido          POST   productos.php  (datos válidos)     201
  POST inválido        POST   productos.php  (codigo "P001")     409 (código repetido)
                       POST   productos.php  (precio -5)         400 (datos inválidos)
  PUT válido           PUT    productos.php?id=2  (datos válidos) 200
  PUT inexistente      PUT    productos.php?id=999                404
  DELETE válido        DELETE productos.php?id=7                 200
  DELETE inexistente   DELETE productos.php?id=999               404

  Body de ejemplo para PUT:

  {
    "codigo": "P002",
    "nombre": "Mouse inalámbrico Pro",
    "descripcion": "Mouse ergonómico de 1600 DPI",
    "precio": 29.99,
    "stock": 35
  }

  Nota: para probar DELETE válido hay que haber creado antes un producto con POST
  (en el ejemplo, el id 7).


7. SEGURIDAD Y MANEJO DE ERRORES

  - Todas las consultas usan PDO con parámetros preparados; nunca se concatena
    información enviada por el usuario dentro del SQL.
  - Los datos se validan (tipo, longitud y rango) antes de cualquier INSERT o UPDATE.
  - Los errores de PHP y de MySQL no se muestran al cliente.
  - Todas las respuestas se envían como JSON con Content-Type: application/json.

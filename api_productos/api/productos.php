<?php
// API RESTful de productos (PHP nativo + MySQL con PDO).
//
//   GET    productos.php          -> lista todos los productos
//   GET    productos.php?id=3     -> devuelve un producto
//   POST   productos.php          -> registra un producto (JSON en el body)
//   PUT    productos.php?id=3     -> actualiza un producto (JSON en el body)
//   DELETE productos.php?id=3     -> elimina un producto

header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', '0');   // los errores de PHP nunca deben llegar al cliente

require_once __DIR__ . '/../config/conexion.php';


// ---------------------------------------------------------------------
// Funciones de ayuda
// ---------------------------------------------------------------------

// Envía la respuesta en JSON con el código HTTP indicado y termina el script.
function responder(int $codigo, array $contenido): void
{
    http_response_code($codigo);
    echo json_encode($contenido, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function responderError(int $codigo, string $mensaje, array $errores = []): void
{
    $respuesta = ['success' => false, 'message' => $mensaje];
    if (!empty($errores)) {
        $respuesta['errors'] = $errores;
    }
    responder($codigo, $respuesta);
}

// Lee el parámetro ?id= de la URL.
// Devuelve null si no vino, o el número si es un entero mayor o igual a 1.
// Si vino pero es inválido, responde 400 directamente.
function leerId(): ?int
{
    if (!isset($_GET['id'])) {
        return null;
    }

    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);

    if ($id === false || $id < 1) {
        responderError(400, 'El id debe ser un número entero mayor o igual a 1.');
    }

    return $id;
}

// Lee el cuerpo de la petición y lo convierte de JSON a array.
function leerJson(): array
{
    $cuerpo = file_get_contents('php://input');
    $datos = json_decode($cuerpo, true);

    if (json_last_error() !== JSON_ERROR_NONE || !is_array($datos)) {
        responderError(400, 'El cuerpo de la petición no contiene un JSON válido.');
    }

    return $datos;
}

// Un campo se considera faltante si no existe, es null o es texto vacío.
function campoFaltante(array $datos, string $campo): bool
{
    if (!array_key_exists($campo, $datos) || $datos[$campo] === null) {
        return true;
    }
    return is_string($datos[$campo]) && trim($datos[$campo]) === '';
}

// Valida los datos de un producto (se usa en POST y en PUT).
// Devuelve [errores, producto]: si "errores" viene vacío, "producto" ya
// tiene los valores limpios y listos para guardar.
function validarProducto(array $datos): array
{
    $errores = [];
    $producto = [];

    // codigo: alfanumérico, de 3 a 50 caracteres
    if (campoFaltante($datos, 'codigo')) {
        $errores['codigo'] = 'El código es obligatorio.';
    } elseif (!is_string($datos['codigo'])) {
        $errores['codigo'] = 'El código debe ser texto.';
    } else {
        $codigo = trim($datos['codigo']);
        if (mb_strlen($codigo) < 3 || mb_strlen($codigo) > 50) {
            $errores['codigo'] = 'El código debe tener entre 3 y 50 caracteres.';
        } elseif (!preg_match('/^[A-Za-z0-9]+$/', $codigo)) {
            $errores['codigo'] = 'El código solo puede contener letras y números.';
        } else {
            $producto['codigo'] = $codigo;
        }
    }

    // nombre: de 3 a 100 caracteres
    if (campoFaltante($datos, 'nombre')) {
        $errores['nombre'] = 'El nombre es obligatorio.';
    } elseif (!is_string($datos['nombre'])) {
        $errores['nombre'] = 'El nombre debe ser texto.';
    } else {
        $nombre = trim($datos['nombre']);
        if (mb_strlen($nombre) < 3 || mb_strlen($nombre) > 100) {
            $errores['nombre'] = 'El nombre debe tener entre 3 y 100 caracteres.';
        } else {
            $producto['nombre'] = $nombre;
        }
    }

    // descripcion: opcional, pero si se envía debe ser texto
    $producto['descripcion'] = null;
    if (array_key_exists('descripcion', $datos) && $datos['descripcion'] !== null) {
        if (!is_string($datos['descripcion'])) {
            $errores['descripcion'] = 'La descripción debe ser texto.';
        } else {
            $descripcion = trim($datos['descripcion']);
            $producto['descripcion'] = ($descripcion === '') ? null : $descripcion;
        }
    }

    // precio: número mayor que 0, máximo 2 decimales y dentro de DECIMAL(10,2)
    if (campoFaltante($datos, 'precio')) {
        $errores['precio'] = 'El precio es obligatorio.';
    } else {
        $precio = $datos['precio'];
        $texto = null;

        if (is_int($precio)) {
            $texto = (string) $precio;
        } elseif (is_float($precio)) {
            // number_format evita notaciones raras como 1.0E+25
            $texto = rtrim(rtrim(number_format($precio, 10, '.', ''), '0'), '.');
        } elseif (is_string($precio)) {
            $texto = trim($precio);
        }

        if ($texto === null || !preg_match('/^-?\d+(\.\d+)?$/', $texto)) {
            $errores['precio'] = 'El precio debe ser un número.';
        } elseif ((float) $texto <= 0) {
            $errores['precio'] = 'El precio debe ser mayor que 0.';
        } elseif (!preg_match('/^\d{1,8}(\.\d{1,2})?$/', $texto)) {
            $errores['precio'] = 'El precio admite máximo 2 decimales y no puede superar 99999999.99.';
        } else {
            $producto['precio'] = number_format((float) $texto, 2, '.', '');
        }
    }

    // stock: entero mayor o igual a 0
    if (campoFaltante($datos, 'stock')) {
        $errores['stock'] = 'El stock es obligatorio.';
    } elseif (!is_int($datos['stock'])) {
        $errores['stock'] = 'El stock debe ser un número entero.';
    } elseif ($datos['stock'] < 0 || $datos['stock'] > 2147483647) {
        $errores['stock'] = 'El stock debe ser un entero entre 0 y 2147483647.';
    } else {
        $producto['stock'] = $datos['stock'];
    }

    return [$errores, $producto];
}

// Busca un producto por id. Devuelve el array del producto o false si no existe.
function buscarProducto(PDO $pdo, int $id)
{
    $stmt = $pdo->prepare('SELECT * FROM productos WHERE id = :id');
    $stmt->execute([':id' => $id]);
    return $stmt->fetch();
}

// Revisa si otro producto ya usa ese código.
// En PUT se pasa el id del producto que se está editando para no chocar consigo mismo.
function codigoEnUso(PDO $pdo, string $codigo, int $idExcluido = 0): bool
{
    $stmt = $pdo->prepare('SELECT id FROM productos WHERE codigo = :codigo AND id <> :id');
    $stmt->execute([':codigo' => $codigo, ':id' => $idExcluido]);
    return $stmt->fetch() !== false;
}

// Deja los tipos de cada campo como corresponde en el JSON.
function formatearProducto(array $fila): array
{
    $fila['id'] = (int) $fila['id'];
    $fila['stock'] = (int) $fila['stock'];
    return $fila;
}


// ---------------------------------------------------------------------
// Validar el método HTTP
// ---------------------------------------------------------------------

$metodo = $_SERVER['REQUEST_METHOD'];
$permitidos = ['GET', 'POST', 'PUT', 'DELETE'];

if (!in_array($metodo, $permitidos, true)) {
    header('Allow: ' . implode(', ', $permitidos));
    responderError(405, 'Método no permitido. Use GET, POST, PUT o DELETE.');
}


// ---------------------------------------------------------------------
// Lógica principal
// ---------------------------------------------------------------------

try {
    $pdo = obtenerConexion();

    switch ($metodo) {

        // ----- GET: consultar -----
        case 'GET':
            $id = leerId();

            if ($id === null) {
                // Lista completa
                $stmt = $pdo->query('SELECT * FROM productos ORDER BY id');
                $productos = array_map('formatearProducto', $stmt->fetchAll());

                responder(200, [
                    'success' => true,
                    'message' => empty($productos)
                        ? 'Todavía no hay productos registrados.'
                        : 'Productos obtenidos correctamente.',
                    'total'   => count($productos),
                    'data'    => $productos,
                ]);
            }

            // Un solo producto
            $producto = buscarProducto($pdo, $id);
            if (!$producto) {
                responderError(404, 'No existe un producto con el id ' . $id . '.');
            }

            responder(200, [
                'success' => true,
                'message' => 'Producto obtenido correctamente.',
                'data'    => formatearProducto($producto),
            ]);
            break;

        // ----- POST: registrar -----
        case 'POST':
            $datos = leerJson();
            [$errores, $producto] = validarProducto($datos);

            if (!empty($errores)) {
                responderError(400, 'Los datos enviados no son válidos.', $errores);
            }

            if (codigoEnUso($pdo, $producto['codigo'])) {
                responderError(409, 'Ya existe un producto con el código ' . $producto['codigo'] . '.');
            }

            $stmt = $pdo->prepare(
                'INSERT INTO productos (codigo, nombre, descripcion, precio, stock)
                 VALUES (:codigo, :nombre, :descripcion, :precio, :stock)'
            );
            $stmt->execute([
                ':codigo'      => $producto['codigo'],
                ':nombre'      => $producto['nombre'],
                ':descripcion' => $producto['descripcion'],
                ':precio'      => $producto['precio'],
                ':stock'       => $producto['stock'],
            ]);

            $nuevo = buscarProducto($pdo, (int) $pdo->lastInsertId());

            responder(201, [
                'success' => true,
                'message' => 'Producto registrado correctamente.',
                'data'    => formatearProducto($nuevo),
            ]);
            break;

        // ----- PUT: actualizar -----
        case 'PUT':
            $id = leerId();
            if ($id === null) {
                responderError(400, 'Debe indicar el id del producto en la URL (?id=).');
            }

            $datos = leerJson();

            if (!buscarProducto($pdo, $id)) {
                responderError(404, 'No existe un producto con el id ' . $id . '.');
            }

            [$errores, $producto] = validarProducto($datos);

            if (!empty($errores)) {
                responderError(400, 'Los datos enviados no son válidos.', $errores);
            }

            if (codigoEnUso($pdo, $producto['codigo'], $id)) {
                responderError(409, 'El código ' . $producto['codigo'] . ' pertenece a otro producto.');
            }

            // fecha_registro no se toca en la actualización
            $stmt = $pdo->prepare(
                'UPDATE productos
                 SET codigo = :codigo, nombre = :nombre, descripcion = :descripcion,
                     precio = :precio, stock = :stock
                 WHERE id = :id'
            );
            $stmt->execute([
                ':codigo'      => $producto['codigo'],
                ':nombre'      => $producto['nombre'],
                ':descripcion' => $producto['descripcion'],
                ':precio'      => $producto['precio'],
                ':stock'       => $producto['stock'],
                ':id'          => $id,
            ]);

            responder(200, [
                'success' => true,
                'message' => 'Producto actualizado correctamente.',
                'data'    => formatearProducto(buscarProducto($pdo, $id)),
            ]);
            break;

        // ----- DELETE: eliminar -----
        case 'DELETE':
            $id = leerId();
            if ($id === null) {
                responderError(400, 'Debe indicar el id del producto en la URL (?id=).');
            }

            if (!buscarProducto($pdo, $id)) {
                responderError(404, 'No existe un producto con el id ' . $id . '.');
            }

            $stmt = $pdo->prepare('DELETE FROM productos WHERE id = :id');
            $stmt->execute([':id' => $id]);

            responder(200, [
                'success' => true,
                'message' => 'Producto eliminado correctamente.',
            ]);
            break;
    }

} catch (PDOException $e) {
    // Detalle solo en el log del servidor, nunca en la respuesta.
    error_log('Error de base de datos en productos.php: ' . $e->getMessage());

    // 1062 = entrada duplicada (por si dos peticiones usan el mismo código a la vez)
    if (isset($e->errorInfo[1]) && $e->errorInfo[1] == 1062) {
        responderError(409, 'El código indicado ya está registrado.');
    }

    responderError(500, 'Ocurrió un error en el servidor. Inténtelo nuevamente más tarde.');
}

<?php
/**
 * Controlador: recibe la solicitud, valida los datos, invoca al modelo
 * y decide qué vista mostrar o a qué acción redirigir.
 */
class ProductoController
{
    private Producto $modelo;

    public function __construct()
    {
        $this->modelo = new Producto();
    }

    public function listar(): void
    {
        $this->renderizar('productos/listar', [
            'titulo'    => 'Listado de productos',
            'productos' => $this->modelo->listar(),
            'busqueda'  => '',
        ]);
    }

    public function buscar(): void
    {
        $termino = trim($_GET['q'] ?? '');
        if ($termino === '') {
            redirigir('listar');
        }

        $this->renderizar('productos/listar', [
            'titulo'    => 'Resultados de búsqueda',
            'productos' => $this->modelo->buscar($termino),
            'busqueda'  => $termino,
        ]);
    }

    public function nuevo(): void
    {
        $this->renderizar('productos/formulario', [
            'titulo'   => 'Registrar nuevo producto',
            'accion'   => 'guardar',
            'producto' => ['id' => null, 'nombre' => '', 'categoria' => '', 'precio' => ''],
            'errores'  => [],
        ]);
    }

    public function guardar(): void
    {
        $this->exigirPost();
        [$errores, $datos] = $this->validar($_POST);

        if ($errores) {
            $this->renderizar('productos/formulario', [
                'titulo'   => 'Registrar nuevo producto',
                'accion'   => 'guardar',
                'producto' => ['id' => null] + $datos,
                'errores'  => $errores,
            ]);
            return;
        }

        $id = $this->modelo->crear($datos['nombre'], $datos['categoria'], $datos['precio']);
        setFlash('exito', "Producto registrado correctamente con código $id.");
        redirigir('listar');
    }

    public function editar(): void
    {
        $id = $this->idDesdeSolicitud($_GET['id'] ?? null);
        $producto = $this->modelo->obtenerPorId($id);

        if ($producto === null) {
            setFlash('error', 'El producto solicitado no existe.');
            redirigir('listar');
        }

        $this->renderizar('productos/formulario', [
            'titulo'   => 'Actualizar producto',
            'accion'   => 'actualizar',
            'producto' => $producto,
            'errores'  => [],
        ]);
    }

    public function actualizar(): void
    {
        $this->exigirPost();
        $id = $this->idDesdeSolicitud($_POST['id'] ?? null);

        if ($this->modelo->obtenerPorId($id) === null) {
            setFlash('error', 'El producto a actualizar no existe.');
            redirigir('listar');
        }

        [$errores, $datos] = $this->validar($_POST);

        if ($errores) {
            $this->renderizar('productos/formulario', [
                'titulo'   => 'Actualizar producto',
                'accion'   => 'actualizar',
                'producto' => ['id' => $id] + $datos,
                'errores'  => $errores,
            ]);
            return;
        }

        $this->modelo->actualizar($id, $datos['nombre'], $datos['categoria'], $datos['precio']);
        setFlash('exito', "Producto con código $id actualizado correctamente.");
        redirigir('listar');
    }

    public function eliminar(): void
    {
        $this->exigirPost();
        $id = $this->idDesdeSolicitud($_POST['id'] ?? null);

        if ($this->modelo->eliminar($id)) {
            setFlash('exito', "Producto con código $id eliminado correctamente.");
        } else {
            setFlash('error', 'No se pudo eliminar: el producto no existe.');
        }
        redirigir('listar');
    }

    /* ---------- Métodos auxiliares privados ---------- */

    // Valida los campos del formulario y devuelve [errores, datosLimpios]
    private function validar(array $entrada): array
    {
        $errores   = [];
        $nombre    = trim($entrada['nombre'] ?? '');
        $categoria = trim($entrada['categoria'] ?? '');
        $precio    = str_replace(',', '.', trim($entrada['precio'] ?? ''));

        if ($nombre === '') {
            $errores[] = 'El nombre es obligatorio.';
        } elseif (mb_strlen($nombre) > 100) {
            $errores[] = 'El nombre no puede superar los 100 caracteres.';
        }

        if ($categoria === '') {
            $errores[] = 'La categoría es obligatoria.';
        } elseif (mb_strlen($categoria) > 80) {
            $errores[] = 'La categoría no puede superar los 80 caracteres.';
        }

        if ($precio === '') {
            $errores[] = 'El precio es obligatorio.';
        } elseif (!is_numeric($precio) || (float) $precio <= 0) {
            $errores[] = 'El precio debe ser un número mayor que cero.';
        } elseif ((float) $precio > 99999999.99) {
            $errores[] = 'El precio excede el máximo permitido.';
        }

        return [$errores, [
            'nombre'    => $nombre,
            'categoria' => $categoria,
            'precio'    => $precio,
        ]];
    }

    // Valida que el identificador sea un entero positivo; si no, redirige con error
    private function idDesdeSolicitud($valor): int
    {
        $id = filter_var($valor, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($id === false) {
            setFlash('error', 'El identificador del producto no es válido.');
            redirigir('listar');
        }
        return (int) $id;
    }

    // Las operaciones que modifican datos solo se aceptan por POST
    private function exigirPost(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            setFlash('error', 'Operación no permitida.');
            redirigir('listar');
        }
    }

    // Carga la vista dentro de la plantilla común (cabecera + vista + pie)
    private function renderizar(string $vista, array $datos = []): void
    {
        extract($datos);
        require __DIR__ . '/../views/layout/cabecera.php';
        require __DIR__ . '/../views/' . $vista . '.php';
        require __DIR__ . '/../views/layout/pie.php';
    }
}

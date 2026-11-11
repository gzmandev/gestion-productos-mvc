<div class="row g-3 align-items-center mb-4">
    <div class="col-lg-8">
        <form method="get" action="index.php" class="input-group">
            <input type="hidden" name="accion" value="buscar">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" name="q" class="form-control" placeholder="Buscar por código o nombre"
                   value="<?= e($busqueda) ?>" required aria-label="Buscar producto">
            <button type="submit" class="btn btn-primario">Buscar</button>
        </form>
    </div>
    <div class="col-lg-4 text-lg-end">
        <a class="btn btn-accion" href="index.php?accion=nuevo">
            <i class="bi bi-plus-lg me-1"></i>Nuevo
        </a>
    </div>
</div>

<?php if ($busqueda !== ''): ?>
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <p class="mb-0 text-secondary">Resultados para <strong class="text-dark"><?= e($busqueda) ?></strong></p>
        <a class="btn btn-sm btn-outline-dark" href="index.php?accion=listar">
            <i class="bi bi-arrow-left me-1"></i>Volver al listado completo
        </a>
    </div>
<?php endif; ?>

<?php if (empty($productos)): ?>
    <div class="alert alert-info d-flex align-items-center" role="alert">
        <i class="bi bi-info-circle-fill me-2"></i>
        <div>
            <?php if ($busqueda !== ''): ?>
                No se encontraron productos que coincidan con la búsqueda.
            <?php else: ?>
                No hay productos registrados. Use «Nuevo» para agregar uno.
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table tabla-productos align-middle mb-0">
            <thead>
                <tr>
                    <th scope="col">Código</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Categoría</th>
                    <th scope="col" class="text-end">Precio</th>
                    <th scope="col" class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($productos as $p): ?>
                <tr>
                    <td class="text-secondary"><?= (int) $p['id'] ?></td>
                    <td class="fw-semibold"><?= e($p['nombre']) ?></td>
                    <td><span class="badge badge-categoria"><?= e($p['categoria']) ?></span></td>
                    <td class="text-end">S/ <?= number_format((float) $p['precio'], 2) ?></td>
                    <td class="text-center text-nowrap">
                        <a class="btn btn-sm btn-outline-primary me-1"
                           href="index.php?accion=editar&amp;id=<?= (int) $p['id'] ?>">
                            <i class="bi bi-pencil-square me-1"></i>Editar
                        </a>
                        <form method="post" action="index.php?accion=eliminar" class="d-inline"
                              onsubmit="return confirm('¿Desea eliminar este producto? Esta acción no se puede deshacer.');">
                            <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-dark">
                                <i class="bi bi-trash3 me-1"></i>Eliminar
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

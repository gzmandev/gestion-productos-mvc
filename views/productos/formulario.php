<h2 class="h5 seccion-titulo mb-4"><?= e($titulo) ?></h2>

<?php if (!empty($errores)): ?>
    <div class="alert alert-danger" role="alert">
        <div class="d-flex align-items-start">
            <i class="bi bi-exclamation-triangle-fill me-2 mt-1"></i>
            <ul class="mb-0 ps-3">
                <?php foreach ($errores as $error): ?>
                    <li><?= e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
<?php endif; ?>

<form method="post" action="index.php?accion=<?= e($accion) ?>" class="row g-3 formulario-producto">
    <?php if ($accion === 'actualizar'): ?>
        <input type="hidden" name="id" value="<?= (int) $producto['id'] ?>">
    <?php endif; ?>

    <div class="col-md-4">
        <label class="form-label">Código (automático)</label>
        <input type="text" class="form-control" disabled
               value="<?= $accion === 'actualizar' ? (int) $producto['id'] : 'Se genera al guardar' ?>">
    </div>

    <div class="col-md-8">
        <label for="nombre" class="form-label">Nombre</label>
        <input type="text" id="nombre" name="nombre" class="form-control" maxlength="100" required
               value="<?= e($producto['nombre']) ?>">
    </div>

    <div class="col-md-6">
        <label for="categoria" class="form-label">Categoría</label>
        <input type="text" id="categoria" name="categoria" class="form-control" maxlength="80" required
               value="<?= e($producto['categoria']) ?>">
    </div>

    <div class="col-md-6">
        <label for="precio" class="form-label">Precio</label>
        <div class="input-group">
            <span class="input-group-text">S/</span>
            <input type="text" id="precio" name="precio" class="form-control" inputmode="decimal"
                   placeholder="0.00" required value="<?= e((string) $producto['precio']) ?>">
        </div>
    </div>

    <div class="col-12 d-flex flex-wrap gap-2 pt-3">
        <button type="submit" class="btn btn-primario">
            <i class="bi bi-save me-1"></i><?= $accion === 'actualizar' ? 'Guardar cambios' : 'Registrar' ?>
        </button>
        <a class="btn btn-outline-dark" href="index.php?accion=listar">
            <i class="bi bi-x-lg me-1"></i>Cancelar
        </a>
    </div>
</form>



<?php $__env->startSection('title'); ?>
    Unidades de Medida
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet"
        type="text/css" />
    <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Unidades de Medida</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="<?php echo e(url('/')); ?>">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Catálogo</a></li>
                        <li class="breadcrumb-item active">Unidades</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex align-items-center flex-wrap gap-2">
                    <h5 class="card-title mb-0 flex-grow-1 text-uppercase fw-bold">Listado de Unidades</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" id="btnExportarPDF"
                            class="btn btn-soft-danger waves-effect waves-light shadow-none d-flex align-items-center">
                            <i class="ri-file-pdf-line fs-18"></i> <span
                                class="d-none d-sm-inline ms-1 text-uppercase">PDF</span>
                        </button>
                        <button type="button" id="btnExportarExcel"
                            class="btn btn-soft-success waves-effect waves-light shadow-none d-flex align-items-center">
                            <i class="ri-file-excel-line fs-18"></i> <span
                                class="d-none d-sm-inline ms-1 text-uppercase">Excel</span>
                        </button>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('unidades.crear')): ?>
                            <button type="button" class="btn btn-primary d-flex align-items-center shadow-sm"
                                data-bs-toggle="modal" data-bs-target="#modalUnidad" onclick="limpiarFormulario()">
                                <i class="ri-add-line fs-18 me-1"></i> <span class="d-none d-md-inline text-uppercase">Nueva
                                    Unidad</span>
                                <span class="d-inline d-md-none text-uppercase">Nuevo</span>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>


                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tablaUnidades" class="table nowrap align-middle mb-0" style="width:100%">
                            <thead class="table-light text-muted">
                                <tr class="text-uppercase fs-12">
                                    <th>Código</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th style="width: 100px;">Estado</th>
                                    <th class="no-exportar" style="width: 150px;">Acciones</th>
                                    <th class="no-exportar" style="width: 50px;">On/Off</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php $__empty_1 = true; $__currentLoopData = $unidades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unidad): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr data-id="<?php echo e($unidad->id); ?>">
                                        <td><span class="badge bg-secondary fs-6"><?php echo e($unidad->codigo); ?></span></td>
                                        <td><strong><?php echo e($unidad->nombre); ?></strong></td>
                                        <td>
                                            <?php if($unidad->descripcion): ?>
                                                <i
                                                    class="bx bx-comment-dots me-1"></i><?php echo e(Str::limit($unidad->descripcion, 40)); ?>

                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td id="estado-badge-<?php echo e($unidad->id); ?>">
                                            <?php if($unidad->estado): ?>
                                                <span class="badge bg-success">Activo</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Inactivo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="no-exportar">
                                            <div class="d-flex gap-1">
                                                <button type="button" class="btn btn-sm btn-info"
                                                    onclick="verUnidad(<?php echo e($unidad->id); ?>)" title="Ver">
                                                    <i class="ri-eye-line"></i>
                                                </button>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('unidades.editar')): ?>
                                                    <button type="button" class="btn btn-sm btn-warning"
                                                        onclick="editarUnidad(<?php echo e($unidad->id); ?>)" title="Editar">
                                                        <i class="ri-pencil-line"></i>
                                                    </button>
                                                <?php endif; ?>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('unidades.eliminar')): ?>
                                                    <button type="button" class="btn btn-sm btn-danger"
                                                        onclick="eliminarUnidad(<?php echo e($unidad->id); ?>, '<?php echo e($unidad->nombre); ?>')"
                                                        title="Eliminar">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="no-exportar">
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('unidades.editar')): ?>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="toggle-estado-<?php echo e($unidad->id); ?>"
                                                        <?php echo e($unidad->estado ? 'checked' : ''); ?>

                                                        onchange="toggleEstado(<?php echo e($unidad->id); ?>)">
                                                </div>
                                            <?php else: ?>
                                                <span
                                                    class="badge <?php echo e($unidad->estado ? 'bg-success' : 'bg-danger'); ?>"><?php echo e($unidad->estado ? 'On' : 'Off'); ?></span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="ri-inbox-line fs-1 d-block mb-2"></i>
                                            No hay unidades registradas
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    
    <div class="modal fade" id="modalUnidad" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Nueva Unidad</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formUnidad" method="POST" action="<?php echo e(route('unidades.store')); ?>">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" id="formMethod" name="_method" value="POST">

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="codigo" class="form-label">Código <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control text-uppercase" id="codigo" name="codigo"
                                    placeholder="Ej: UND, KG, LT" maxlength="10" required>
                                <button type="button" class="btn btn-outline-secondary" onclick="generarCodigo()"
                                    title="Generar código">
                                    <i class="bx bxs-magic-wand"></i>
                                </button>
                            </div>
                            <small class="text-muted">Máximo 10 caracteres, solo letras y números</small>
                        </div>

                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombre" name="nombre"
                                placeholder="Ej: Unidad, Kilogramo, Litro" required>
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="2"
                                placeholder="Descripción opcional..."></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" id="btnGuardar" class="btn btn-primary">
                            <span id="btnGuardarTexto">
                                <i class="ri-save-line me-1"></i> Guardar
                            </span>
                            <span id="btnGuardarSpinner" class="d-none">
                                <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                Guardando...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="modalVer" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalle de Unidad</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-borderless">
                        <tr>
                            <th>ID:</th>
                            <td id="verID"></td>
                        </tr>
                        <tr>
                            <th>Código:</th>
                            <td id="verCodigo" class="fw-bold"></td>
                        </tr>
                        <tr>
                            <th>Nombre:</th>
                            <td id="verNombre" class="fw-bold"></td>
                        </tr>
                        <tr>
                            <th>Descripción:</th>
                            <td id="verDescripcion"></td>
                        </tr>
                        <tr>
                            <th>Estado:</th>
                            <td id="verEstado"></td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="modalEliminar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <i class="ri-error-warning-line text-danger" style="font-size: 4rem;"></i>
                    <p class="mt-3">¿Eliminar la unidad:</p>
                    <p class="fw-bold fs-5" id="nombreEliminar"></p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form id="formEliminar" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

    <script>
        window.UNIDADES_CONFIG = {
            unidades: <?php echo json_encode($unidades, 15, 512) ?>,
            routes: {
                store: "<?php echo e(route('unidades.store')); ?>"
            },
            csrfToken: "<?php echo e(csrf_token()); ?>"
        };
    </script>

    <script src="<?php echo e(URL::asset('js/modules/unidades/index.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\master\resources\views/unidades/index.blade.php ENDPATH**/ ?>
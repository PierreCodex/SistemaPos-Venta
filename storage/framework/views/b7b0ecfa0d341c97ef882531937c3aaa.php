

<?php $__env->startSection('title'); ?>
    Gestión de Compras
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet"
        type="text/css" />
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-1">
                        <i class="ri-shopping-bag-3-line me-2 text-primary"></i>Gestión de Compras
                    </h4>
                    <p class="text-muted mb-0">Administra y organiza tus compras de productos de manera eficiente.</p>
                </div>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="<?php echo e(url('/')); ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Inventario</a></li>
                        <li class="breadcrumb-item active">Compras</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros y Estadísticas -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form id="formFiltros" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                            <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio"
                                value="<?php echo e(request('fecha_inicio', now()->startOfMonth()->format('Y-m-d'))); ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="fecha_fin" class="form-label">Fecha Fin</label>
                            <input type="date" class="form-control" id="fecha_fin" name="fecha_fin"
                                value="<?php echo e(request('fecha_fin', now()->format('Y-m-d'))); ?>">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ri-filter-3-line me-1"></i> Filtrar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card bg-primary">
                <div class="card-body text-center text-white">
                    <p class="text-uppercase text-white-50 mb-1">Compras Totales</p>
                    <h3 class="text-white mb-1">S/ <?php echo e(number_format($estadisticas['compras_mes'] ?? 0, 2)); ?></h3>
                    <small class="text-white-75"><?php echo e($estadisticas['total_compras'] ?? 0); ?> compras</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Compras -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">
                        <i class="ri-file-list-3-line me-2"></i>Registros
                    </h5>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('compras.crear')): ?>
                        <a href="<?php echo e(route('compras.create')); ?>" class="btn btn-primary">
                            <i class="ri-add-line me-1"></i> NUEVA COMPRA
                        </a>
                    <?php endif; ?>
                </div>

                <div class="card-body">
                    <table id="tablaCompras" class="table table-hover nowrap align-middle" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th>CÓDIGO</th>
                                <th>PROVEEDOR</th>
                                <th>MÉTODO PAGO</th>
                                <th class="text-end">TOTAL</th>
                                <th class="text-center">FECHA</th>
                                <th class="text-center">ESTADO</th>
                                <th class="text-center">ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $compras; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $compra): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <span class="fw-medium text-primary"><?php echo e($compra->numero_comprobante); ?></span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-xs flex-shrink-0">
                                                <span class="avatar-title bg-primary-subtle text-primary rounded-circle">
                                                    <?php echo e(strtoupper(substr($compra->proveedor->nombre ?? 'P', 0, 1))); ?>

                                                </span>
                                            </div>
                                            <span><?php echo e($compra->proveedor->nombre ?? 'N/A'); ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if($compra->forma_pago === 'CONTADO'): ?>
                                            <span class="badge bg-success-subtle text-success">
                                                <i class="ri-money-dollar-circle-line me-1"></i>Efectivo
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-warning-subtle text-warning">
                                                <i class="ri-bank-card-line me-1"></i>Crédito
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end fw-semibold">S/ <?php echo e(number_format($compra->total, 2)); ?></td>
                                    <td class="text-center"><?php echo e($compra->fecha_emision->format('d/m/Y')); ?></td>
                                    <td class="text-center">
                                        <?php if($compra->estado === 'COMPLETADO'): ?>
                                            <span class="badge bg-success">Completado</span>
                                        <?php elseif($compra->estado === 'PENDIENTE'): ?>
                                            <span class="badge bg-warning">Pendiente</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Anulado</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <button type="button" class="btn btn-soft-info btn-sm"
                                                onclick="verCompra(<?php echo e($compra->id); ?>)" title="Ver detalle">
                                                <i class="ri-eye-line"></i>
                                            </button>
                                            <?php if($compra->estado !== 'ANULADO'): ?>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('compras.anular')): ?>
                                                    <button type="button" class="btn btn-soft-danger btn-sm"
                                                        onclick="anularCompra(<?php echo e($compra->id); ?>)" title="Anular">
                                                        <i class="ri-close-circle-line"></i>
                                                    </button>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="ri-inbox-2-line fs-1 d-block mb-2"></i>
                                        No hay compras para mostrar.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Ver Detalle -->
    <div class="modal fade" id="modalVerCompra" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="ri-eye-line me-2"></i>Detalle de Compra</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="contenidoDetalleCompra">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

    <script>
        // DataTable
        $('#tablaCompras').DataTable({
            responsive: true,
            order: [
                [4, 'desc']
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json',
                emptyTable: 'No hay compras para mostrar.'
            }
        });

        // Ver detalle
        function verCompra(id) {
            const modal = new bootstrap.Modal(document.getElementById('modalVerCompra'));
            modal.show();

            fetch(`<?php echo e(url('compras')); ?>/${id}`)
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        const c = data.compra;
                        let html = `
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Proveedor:</strong> ${c.proveedor?.nombre || 'N/A'}</p>
                                    <p class="mb-1"><strong>Comprobante:</strong> ${c.tipo_comprobante} - ${c.numero_comprobante}</p>
                                    <p class="mb-1"><strong>Forma de Pago:</strong> ${c.forma_pago}</p>
                                </div>
                                <div class="col-md-6 text-end">
                                    <p class="mb-1"><strong>Fecha:</strong> ${new Date(c.fecha_emision).toLocaleDateString('es-PE')}</p>
                                    <p class="mb-1"><strong>Estado:</strong> <span class="badge ${c.estado === 'COMPLETADO' ? 'bg-success' : 'bg-danger'}">${c.estado}</span></p>
                                </div>
                            </div>
                            <table class="table table-bordered mb-3">
                                <thead class="table-light">
                                    <tr>
                                        <th>Producto</th>
                                        <th class="text-end">Cant.</th>
                                        <th class="text-end">Costo Unit.</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${c.detalles.map(d => `
                                            <tr>
                                                <td>${d.producto?.nombre || 'N/A'}</td>
                                                <td class="text-end">${parseFloat(d.cantidad).toFixed(2)}</td>
                                                <td class="text-end">S/ ${parseFloat(d.costo_unitario).toFixed(2)}</td>
                                                <td class="text-end">S/ ${parseFloat(d.subtotal).toFixed(2)}</td>
                                            </tr>
                                        `).join('')}
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="3" class="text-end">TOTAL:</th>
                                        <th class="text-end">S/ ${parseFloat(c.total).toFixed(2)}</th>
                                    </tr>
                                </tfoot>
                            </table>
                            ${c.observaciones ? `<p class="text-muted"><i class="ri-information-line me-1"></i>${c.observaciones}</p>` : ''}
                        `;
                        document.getElementById('contenidoDetalleCompra').innerHTML = html;
                    }
                });
        }

        // Anular compra
        function anularCompra(id) {
            Swal.fire({
                title: '¿Anular esta compra?',
                text: 'Se revertirá el stock de los productos.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f06548',
                cancelButtonColor: '#878a99',
                confirmButtonText: 'Sí, anular',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`<?php echo e(url('compras')); ?>/${id}/anular`, {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('¡Anulado!', data.message, 'success').then(() => location.reload());
                            } else {
                                Swal.fire('Error', data.message, 'error');
                            }
                        });
                }
            });
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\master\resources\views/compras/index.blade.php ENDPATH**/ ?>
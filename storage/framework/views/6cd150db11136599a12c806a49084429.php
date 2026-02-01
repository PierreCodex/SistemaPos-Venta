

<?php $__env->startSection('title'); ?>
    Kardex - Historial de Movimientos
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet"
        type="text/css" />
    <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />

    
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        /* Asegurar que el dropdown de Select2 aparezca sobre otros elementos */
        .select2-container--open {
            z-index: 9999 !important;
        }

        .select2-dropdown {
            z-index: 9999 !important;
        }

        /* Ancho completo para Select2 */
        .select2-container {
            width: 100% !important;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Kardex - Historial de Movimientos</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="<?php echo e(url('/')); ?>">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Inventario</a></li>
                        <li class="breadcrumb-item active">Kardex</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Movimientos Hoy</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value"
                                    data-target="<?php echo e($estadisticas['movimientos_hoy'] ?? 0); ?>">0</span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-primary-subtle rounded fs-3">
                                <i class="ri-exchange-line text-primary"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Entradas (Mes)</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4 text-success">
                                +<span class="counter-value" data-target="<?php echo e($estadisticas['entradas_mes'] ?? 0); ?>">0</span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-success-subtle rounded fs-3">
                                <i class="ri-arrow-down-circle-line text-success"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Salidas (Mes)</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4 text-danger">
                                -<span class="counter-value"
                                    data-target="<?php echo e(abs($estadisticas['salidas_mes'] ?? 0)); ?>">0</span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-danger-subtle rounded fs-3">
                                <i class="ri-arrow-up-circle-line text-danger"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total Movimientos</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value"
                                    data-target="<?php echo e($estadisticas['total_movimientos'] ?? 0); ?>">0</span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-info-subtle rounded fs-3">
                                <i class="ri-database-2-line text-info"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Movimientos -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">Historial de Movimientos</h5>
                    <div class="d-flex flex-shrink-0 gap-2">
                        <button type="button" id="btnExportarPDF" class="btn btn-soft-danger waves-effect waves-light">
                            <i class="las la-file-pdf fs-3"></i><span>PDF</span>
                        </button>
                        <button type="button" id="btnExportarExcel" class="btn btn-soft-success waves-effect waves-light">
                            <i class="las la-file-excel fs-3"></i><span>Excel</span>
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filtros -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label class="form-label">Producto</label>
                            <select id="filtro_producto" class="form-select" style="width: 100%;">
                                <option value="">Todos los productos</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Tipo Movimiento</label>
                            <select id="filtro_tipo" class="form-select">
                                <option value="">-- Todos --</option>
                                <?php $__currentLoopData = $tiposMovimiento; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>"><?php echo e($label); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Desde</label>
                            <input type="date" class="form-control" id="fecha_desde"
                                value="<?php echo e(now()->subMonth()->format('Y-m-d')); ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Hasta</label>
                            <input type="date" class="form-control" id="fecha_hasta"
                                value="<?php echo e(now()->format('Y-m-d')); ?>">
                        </div>
                        <div class="col-md-3 d-flex align-items-end gap-2">
                            <button type="button" id="btnFiltrar" class="btn btn-primary">
                                <i class="ri-filter-3-line me-1"></i>Filtrar
                            </button>
                            <button type="button" id="btnLimpiar" class="btn btn-light">
                                <i class="ri-refresh-line me-1"></i>Limpiar
                            </button>
                        </div>
                    </div>

                    <table id="tablaKardex" class="table nowrap align-middle" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Producto</th>
                                <th>Tipo</th>
                                <th class="text-end">Cantidad</th>
                                <th class="text-end">Stock Ant.</th>
                                <th class="text-end">Stock Res.</th>
                                <th>Referencia</th>
                                <th>Usuario</th>
                                <th class="no-exportar">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $movimientos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mov): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($mov->created_at ? $mov->created_at->format('d/m/Y H:i') : '-'); ?></td>
                                    <td>
                                        <strong><?php echo e($mov->producto->nombre ?? 'N/A'); ?></strong>
                                        <br><small class="text-muted"><?php echo e($mov->producto->codigo ?? ''); ?></small>
                                    </td>
                                    <td>
                                        <?php
                                            $badgeColor = match ($mov->tipo_movimiento) {
                                                'COMPRA' => 'success',
                                                'VENTA' => 'primary',
                                                'AJUSTE_ENTRADA', 'AJUSTE_POSITIVO' => 'info',
                                                'AJUSTE_SALIDA', 'AJUSTE_NEGATIVO' => 'warning',
                                                'DEVOLUCION_PROVEEDOR', 'DEVOLUCION_CLIENTE' => 'secondary',
                                                'MERMA', 'CANCELACION' => 'danger',
                                                default => 'light',
                                            };
                                        ?>
                                        <span class="badge bg-<?php echo e($badgeColor); ?>-subtle text-<?php echo e($badgeColor); ?>">
                                            <?php echo e($tiposMovimiento[$mov->tipo_movimiento] ?? $mov->tipo_movimiento); ?>

                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <?php if($mov->cantidad >= 0): ?>
                                            <span
                                                class="text-success fw-semibold">+<?php echo e(number_format($mov->cantidad, 2)); ?></span>
                                        <?php else: ?>
                                            <span
                                                class="text-danger fw-semibold"><?php echo e(number_format($mov->cantidad, 2)); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end"><?php echo e(number_format($mov->stock_anterior, 2)); ?></td>
                                    <td class="text-end fw-semibold"><?php echo e(number_format($mov->stock_resultante, 2)); ?></td>
                                    <td>
                                        <small class="text-muted"><?php echo e(Str::limit($mov->observaciones, 30) ?? '-'); ?></small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-xs">
                                                <span class="avatar-title bg-primary-subtle text-primary rounded-circle">
                                                    <?php echo e(strtoupper(substr($mov->user->name ?? 'S', 0, 1))); ?>

                                                </span>
                                            </div>
                                            <span><?php echo e($mov->user->name ?? 'Sistema'); ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="<?php echo e(route('inventario.kardex.producto', $mov->producto_id)); ?>"
                                            class="btn btn-sm btn-info" title="Ver Kardex del Producto">
                                            <i class="ri-eye-line"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
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

    
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Inicializar DataTable
            var table = $('#tablaKardex').DataTable({
                responsive: true,
                order: [
                    [0, 'desc']
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json',
                    emptyTable: 'No hay movimientos registrados'
                },
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'excelHtml5',
                        text: '<i class="las la-file-excel fs-3"></i> Excel',
                        className: 'btn btn-soft-success d-none',
                        title: 'Kardex - Historial de Movimientos',
                        exportOptions: {
                            columns: ':not(.no-exportar)'
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="las la-file-pdf fs-3"></i> PDF',
                        className: 'btn btn-soft-danger d-none',
                        title: 'Kardex - Historial de Movimientos',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        exportOptions: {
                            columns: ':not(.no-exportar)'
                        }
                    }
                ]
            });

            // Conectar botones personalizados a DataTables
            $('#btnExportarExcel').on('click', function() {
                table.button('.buttons-excel').trigger();
            });

            $('#btnExportarPDF').on('click', function() {
                table.button('.buttons-pdf').trigger();
            });

            // Select2 para el tipo de movimiento
            $('#filtro_tipo').select2({
                placeholder: '-- Todos --',
                allowClear: true,
                width: '100%',
                minimumResultsForSearch: 0
            });

            // Select2 para buscar productos
            $('#filtro_producto').select2({
                placeholder: 'Buscar producto...',
                allowClear: true,
                width: '100%',
                ajax: {
                    url: '<?php echo e(route('inventario.kardex.api.buscar-productos')); ?>',
                    dataType: 'json',
                    delay: 250,
                    data: params => ({
                        q: params.term
                    }),
                    processResults: data => ({
                        results: data.map(p => ({
                            id: p.id,
                            text: `${p.nombre} (${p.codigo})`
                        }))
                    }),
                    cache: true
                },
                minimumInputLength: 2
            });

            // Filtrar por columnas
            $('#btnFiltrar').on('click', function() {
                var producto = $('#filtro_producto').val();
                var tipo = $('#filtro_tipo').val();

                // Filtrar por tipo
                table.column(2).search(tipo).draw();

                // Si hay producto seleccionado, filtrar
                if (producto) {
                    var productoText = $('#filtro_producto option:selected').text();
                    table.column(1).search(productoText.split(' (')[0]).draw();
                }
            });

            // Limpiar filtros
            $('#btnLimpiar').on('click', function() {
                $('#filtro_producto').val(null).trigger('change');
                $('#filtro_tipo').val('');
                $('#fecha_desde').val('<?php echo e(now()->subMonth()->format('Y-m-d')); ?>');
                $('#fecha_hasta').val('<?php echo e(now()->format('Y-m-d')); ?>');
                table.search('').columns().search('').draw();
            });

            // Counter animation
            document.querySelectorAll('.counter-value').forEach(counter => {
                const target = parseFloat(counter.getAttribute('data-target')) || 0;
                const animate = () => {
                    const current = parseFloat(counter.textContent) || 0;
                    const increment = target / 50;
                    if (current < target) {
                        counter.textContent = Math.ceil(current + increment);
                        setTimeout(animate, 20);
                    } else {
                        counter.textContent = Math.round(target);
                    }
                };
                animate();
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\master\resources\views/inventario/kardex/index.blade.php ENDPATH**/ ?>
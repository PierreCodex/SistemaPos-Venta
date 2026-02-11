

<?php $__env->startSection('title'); ?>
    Gestión de Ventas
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <link href="<?php echo e(URL::asset('build/libs/flatpickr/flatpickr.min.css')); ?>" rel="stylesheet" type="text/css">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Gestión de Ventas</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="<?php echo e(url('/')); ?>">Inicio</a></li>
                        <li class="breadcrumb-item active">Ventas</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row h-100 align-items-stretch">
        
        <div class="col-lg-8">
            <div class="card card-height-100">
                <div class="card-body p-4">
                    <?php if (isset($component)) { $__componentOriginal420aded84c4ad00d8b5500c4d4417d9f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal420aded84c4ad00d8b5500c4d4417d9f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filtros-fecha','data' => ['fechaInicio' => $fechaInicio,'fechaFin' => $fechaFin]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filtros-fecha'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['fechaInicio' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($fechaInicio),'fechaFin' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($fechaFin)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal420aded84c4ad00d8b5500c4d4417d9f)): ?>
<?php $attributes = $__attributesOriginal420aded84c4ad00d8b5500c4d4417d9f; ?>
<?php unset($__attributesOriginal420aded84c4ad00d8b5500c4d4417d9f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal420aded84c4ad00d8b5500c4d4417d9f)): ?>
<?php $component = $__componentOriginal420aded84c4ad00d8b5500c4d4417d9f; ?>
<?php unset($__componentOriginal420aded84c4ad00d8b5500c4d4417d9f); ?>
<?php endif; ?>
                </div>
            </div>
        </div>

        
        <div class="col-lg-4">
            <div class="card card-height-100">
                <div class="card-body p-4">
                    <div class="d-flex gap-3 h-100 align-items-center">
                        
                        <div class="text-center p-3 rounded-3 flex-fill bg-light">
                            <p class="text-success text-uppercase fw-bold mb-2 fs-12">Emitidas</p>
                            <h3 class="mb-1 fw-bold text-success">
                                <span class="fs-12 fw-normal text-muted me-1">S/.</span>
                                <span id="totalEmitidas"><?php echo e(number_format($estadisticas['emitidas']['total'], 2)); ?></span>
                            </h3>
                            <p class="text-muted mb-0 fs-13">
                                <span id="cantidadEmitidas"><?php echo e($estadisticas['emitidas']['cantidad']); ?></span> ventas
                            </p>
                        </div>
                        
                        <div class="text-center p-3 rounded-3 flex-fill bg-light">
                            <p class="text-danger text-uppercase fw-bold mb-2 fs-12">Anuladas</p>
                            <h3 class="mb-1 fw-bold text-danger">
                                <span class="fs-12 fw-normal text-muted me-1">S/.</span>
                                <span id="totalAnuladas"><?php echo e(number_format($estadisticas['anuladas']['total'], 2)); ?></span>
                            </h3>
                            <p class="text-muted mb-0 fs-13">
                                <span id="cantidadAnuladas"><?php echo e($estadisticas['anuladas']['cantidad']); ?></span> ventas
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex align-items-center flex-wrap gap-2">
                    <h5 class="card-title mb-0 flex-grow-1">Listado de Comprobantes</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('ventas.exportar')): ?>
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
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('ventas.crear')): ?>
                            <a href="<?php echo e(route('ventas.create')); ?>" class="btn btn-primary shadow-sm d-flex align-items-center">
                                <i class="ri-add-line fs-18 me-1"></i> <span class="d-none d-md-inline text-uppercase">Nueva
                                    Venta</span>
                                <span class="d-inline d-md-none text-uppercase">Nuevo</span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>


                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tablaVentas" class="table nowrap align-middle table-hover mb-0" style="width:100%">
                            <thead class="table-light text-muted">
                                <tr class="text-uppercase fs-12">
                                    <th>Comprobante</th>
                                    <th>Cliente</th>
                                    <th>Vendedor</th>
                                    <th>Método Pago</th>
                                    <th>Fecha</th>
                                    <th>Total</th>
                                    <th class="text-center">Estado</th>
                                    <th class="no-exportar text-center" style="width: 150px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $ventas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $venta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr data-id="<?php echo e($venta->id); ?>">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1">
                                                    <h6
                                                        class="fs-14 mb-0 fw-bold border-bottom border-primary border-opacity-25 d-inline-block text-uppercase">
                                                        <?php echo e($venta->comprobante); ?></h6>
                                                    <div class="text-muted fs-11">
                                                        <?php echo e($venta->serie); ?>-<?php echo e(str_pad($venta->numero, 8, '0', STR_PAD_LEFT)); ?>

                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-uppercase"><?php echo e($venta->nombre_cliente); ?></td>
                                        <td class="text-uppercase"><?php echo e($venta->vendedor->name ?? '-'); ?></td>
                                        <td>
                                            <span class="badge bg-info-subtle text-info p-2 text-uppercase">
                                                <i class="ri-wallet-3-line me-1 align-middle"></i><?php echo e($venta->metodo_pago); ?>

                                            </span>
                                        </td>
                                        <td class="text-nowrap text-uppercase">
                                            <div class="fw-medium"><?php echo e($venta->fecha_emision->format('d/m/Y')); ?></div>
                                            <div class="text-muted fs-11"><?php echo e($venta->fecha_emision->format('H:i:s')); ?>

                                            </div>
                                        </td>
                                        <td><span class="fw-bold text-primary fs-14">S/
                                                <?php echo e(number_format($venta->total, 2)); ?></span></td>
                                        <td class="text-center text-uppercase"><?php echo $venta->badge_estado; ?></td>
                                        <td class="text-center no-exportar">
                                            <div class="d-flex justify-content-center gap-1">
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('ventas.ver')): ?>
                                                    <button type="button"
                                                        class="btn btn-sm btn-soft-info btn-icon waves-effect waves-light"
                                                        onclick="verDetalles(<?php echo e($venta->id); ?>)" title="Ver detalles">
                                                        <i class="ri-eye-line fs-16"></i>
                                                    </button>
                                                <?php endif; ?>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('ventas.imprimir')): ?>
                                                    <button type="button"
                                                        class="btn btn-sm btn-soft-secondary btn-icon waves-effect waves-light"
                                                        onclick="imprimirVenta(<?php echo e($venta->id); ?>)" title="Imprimir">
                                                        <i class="ri-printer-line fs-16"></i>
                                                    </button>
                                                    <a href="https://api.whatsapp.com/send?text=<?php echo e(urlencode('Hola! Aquí tienes tu ticket de su compra: ' . $venta->url_publica)); ?>"
                                                        target="_blank"
                                                        class="btn btn-sm btn-soft-success btn-icon waves-effect waves-light"
                                                        title="Enviar WhatsApp">
                                                        <i class="ri-whatsapp-line fs-16"></i>
                                                    </a>
                                                    <button type="button"
                                                        class="btn btn-sm btn-soft-primary btn-icon waves-effect waves-light"
                                                        onclick="copiarLink('<?php echo e($venta->url_publica); ?>')"
                                                        title="Copiar Link">
                                                        <i class="ri-links-line fs-16"></i>
                                                    </button>
                                                <?php endif; ?>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('ventas.anular')): ?>
                                                    <?php if($venta->estado !== 'ANULADA'): ?>
                                                        <button type="button"
                                                            class="btn btn-sm btn-soft-danger btn-icon waves-effect waves-light"
                                                            onclick="anularVenta(<?php echo e($venta->id); ?>)" title="Anular">
                                                            <i class="ri-close-circle-line fs-16"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-5 text-uppercase">
                                            <i class="ri-inbox-line fs-48 d-block mb-2 opacity-25"></i>
                                            No hay ventas registradas
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

    
    <div class="modal fade" id="modalDetalles" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white">
                        <i class="ri-file-list-3-line me-2"></i>Detalles de Venta
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="contenidoDetalles">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="modalImpresion" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="fw-bold mb-1 text-dark text-uppercase fs-16">
                                Comprobante: <span id="printCodigoLabel" class="text-primary">#000-000000</span>
                            </h5>
                            <p class="text-muted fs-11 fw-medium text-uppercase mb-0" id="printTipoTexto">COMPROBANTE</p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    
                    <div class="row g-2 mb-4">
                        <div class="col-6">
                            <div class="bg-light p-2 rounded text-center border border-light">
                                <p class="text-muted fs-10 mb-1 text-uppercase fw-bold">Fecha Emisión</p>
                                <h6 class="fs-12 mb-0 fw-bold" id="printFecha">--/--/--</h6>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-light p-2 rounded text-center border border-light">
                                <p class="text-muted fs-10 mb-1 text-uppercase fw-bold">Total Venta</p>
                                <h6 class="fs-12 mb-0 fw-bold text-primary" id="printTotal">S/ 0.00</h6>
                            </div>
                        </div>
                    </div>

                    
                    <div class="d-flex justify-content-between align-items-center mb-4 gap-2">
                        <!-- A4 -->
                        <div class="text-center flex-grow-1">
                            <a href="javascript:void(0)" onclick="descargarPDFFormato('a4')"
                                class="d-block text-decoration-none group">
                                <div class="avatar-sm mx-auto mb-2">
                                    <div class="avatar-title bg-info rounded text-white fs-20 shadow-sm">
                                        <i class="ri-file-text-line"></i>
                                    </div>
                                </div>
                                <span class="text-muted fw-bold fs-11 text-uppercase">Imprimir A4</span>
                            </a>
                        </div>
                        <!-- 80mm -->
                        <div class="text-center flex-grow-1 border-start border-end border-light">
                            <a href="javascript:void(0)" onclick="descargarPDFFormato('80mm')"
                                class="d-block text-decoration-none group">
                                <div class="avatar-sm mx-auto mb-2">
                                    <div class="avatar-title bg-info rounded text-white fs-20 shadow-sm">
                                        <i class="ri-bill-line"></i>
                                    </div>
                                </div>
                                <span class="text-muted fw-bold fs-11 text-uppercase">Ticket 80mm</span>
                            </a>
                        </div>
                        <!-- 50mm -->
                        <div class="text-center flex-grow-1 border-end border-light">
                            <a href="javascript:void(0)" onclick="descargarPDFFormato('50mm')"
                                class="d-block text-decoration-none group">
                                <div class="avatar-sm mx-auto mb-2">
                                    <div class="avatar-title bg-info rounded text-white fs-20 shadow-sm">
                                        <i class="ri-coupon-3-line"></i>
                                    </div>
                                </div>
                                <span class="text-muted fw-bold fs-11 text-uppercase">Ticket 50mm</span>
                            </a>
                        </div>
                        <!-- A5 -->
                        <div class="text-center flex-grow-1">
                            <a href="javascript:void(0)" class="d-block text-decoration-none opacity-50">
                                <div class="avatar-sm mx-auto mb-2">
                                    <div class="avatar-title bg-secondary rounded text-white fs-20">
                                        <i class="ri-file-list-line"></i>
                                    </div>
                                </div>
                                <span class="text-muted fw-bold fs-11 text-uppercase">Imprimir A5</span>
                            </a>
                        </div>
                    </div>

                    
                    <div class="vstack gap-3 mt-2">
                        <!-- Email -->
                        <div class="input-group">
                            <input type="email" class="form-control border-light-subtle bg-light"
                                placeholder="Correo electrónico" id="inputEmailEnvio">
                            <button class="btn btn-outline-light border-light-subtle text-muted" type="button"
                                id="btnEnviarEmail">
                                <i class="ri-mail-send-line me-1"></i> Enviar
                            </button>
                        </div>
                        <!-- WhatsApp -->
                        <div class="input-group">
                            <span class="input-group-text border-light-subtle bg-light text-muted">+51</span>
                            <input type="text" class="form-control border-light-subtle bg-light"
                                placeholder="Número de celular" id="inputTelefonoWA">
                            <button class="btn btn-outline-light border-light-subtle text-muted" type="button"
                                onclick="enviarPorWhatsApp()">
                                Enviar <i class="ri-whatsapp-line ms-1 text-success"></i>
                            </button>
                        </div>
                    </div>

                    
                    <div class="mt-4 pt-2 border-top border-light border-dashed">
                        <div id="printEstado">
                            <p class="mb-0 fs-11 text-muted text-uppercase fw-medium" id="infoNotaVenta">
                                <i class="ri-error-warning-line me-1 text-warning"></i> No tiene validez tributaria
                            </p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light-subtle border-0 p-3">
                    <button type="button" class="btn btn-white border fw-bold w-100" data-bs-dismiss="modal">
                        CERRAR VENTANA
                    </button>
                </div>
            </div>
        </div>
    </div>



<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="<?php echo e(URL::asset('build/libs/flatpickr/flatpickr.min.js')); ?>"></script>
    <script>
        $(document).ready(function() {
            $('#tablaVentas').DataTable({
                responsive: false,
                scrollX: true,
                order: [
                    [4, 'desc']
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
                }
            });
        });

        const ROUTES = {
            show: '<?php echo e(route('ventas.show', ':id')); ?>',
            destroy: '<?php echo e(route('ventas.destroy', ':id')); ?>',
            filtrar: '<?php echo e(route('ventas.filtrar-fechas')); ?>'
        };

        // Ver detalles de una venta
        function verDetalles(id) {
            const modal = new bootstrap.Modal(document.getElementById('modalDetalles'));
            modal.show();

            fetch(ROUTES.show.replace(':id', id), {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const v = data.venta;
                        document.getElementById('contenidoDetalles').innerHTML = `
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Comprobante:</strong> ${v.comprobante}</p>
                                <p class="mb-1"><strong>Serie-Número:</strong> ${v.serie}-${v.numero}</p>
                                <p class="mb-1"><strong>Cliente:</strong> ${v.cliente?.nombre || 'Cliente General'}</p>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <p class="mb-1"><strong>Fecha:</strong> ${new Date(v.fecha_emision).toLocaleString()}</p>
                                <p class="mb-1"><strong>Vendedor:</strong> ${v.vendedor?.name || '-'}</p>
                                <p class="mb-1"><strong>Método:</strong> ${v.metodo_pago}</p>
                            </div>
                        </div>
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-center">Cant.</th>
                                    <th class="text-end">P. Unit.</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${v.detalles.map(d => `
                                                                                                                                                                                                                <tr>
                                                                                                                                                                                                                    <td>${d.producto?.nombre || 'Producto'}</td>
                                                                                                                                                                                                                    <td class="text-center">${parseFloat(d.cantidad).toFixed(2)}</td>
                                                                                                                                                                                                                    <td class="text-end">S/ ${parseFloat(d.precio_unitario).toFixed(2)}</td>
                                                                                                                                                                                                                    <td class="text-end">S/ ${parseFloat(d.subtotal).toFixed(2)}</td>
                                                                                                                                                                                                                </tr>
                                                                                                                                                                                                            `).join('')}
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                                    <td class="text-end">S/ ${parseFloat(v.subtotal).toFixed(2)}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>IGV (${v.igv_porcentaje}%):</strong></td>
                                    <td class="text-end">S/ ${parseFloat(v.igv_monto).toFixed(2)}</td>
                                </tr>
                                ${v.descuento > 0 ? `
                                                                                                                                                                                                            <tr>
                                                                                                                                                                                                                <td colspan="3" class="text-end"><strong>Descuento:</strong></td>
                                                                                                                                                                                                                <td class="text-end text-danger">- S/ ${parseFloat(v.descuento).toFixed(2)}</td>
                                                                                                                                                                                                            </tr>` : ''}
                                <tr class="table-primary">
                                    <td colspan="3" class="text-end"><strong>TOTAL:</strong></td>
                                    <td class="text-end"><strong>S/ ${parseFloat(v.total).toFixed(2)}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    `;
                    }
                })
                .catch(() => {
                    document.getElementById('contenidoDetalles').innerHTML =
                        '<p class="text-danger text-center">Error al cargar los detalles</p>';
                });
        }

        // Anular venta
        function anularVenta(id) {
            Swal.fire({
                title: '¿Anular esta venta?',
                text: 'Esta acción devolverá el stock de los productos',
                icon: 'warning',
                input: 'textarea',
                inputLabel: 'Motivo de anulación',
                inputPlaceholder: 'Ingrese el motivo...',
                showCancelButton: true,
                confirmButtonColor: '#f06548',
                confirmButtonText: 'Sí, anular',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(ROUTES.destroy.replace(':id', id), {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                motivo: result.value || 'Sin motivo especificado'
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Anulada', data.message, 'success').then(() => location.reload());
                            } else {
                                Swal.fire('Error', data.message, 'error');
                            }
                        });
                }
            });
        }

        // Variables globales para el modal de impresión
        let ventaSeleccionada = null;

        // Imprimir venta - Abre el modal de opciones (Versión Compacta)
        function imprimirVenta(id) {
            fetch(ROUTES.show.replace(':id', id), {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        ventaSeleccionada = data.venta;

                        // Datos básicos
                        document.getElementById('printCodigoLabel').textContent =
                            `${ventaSeleccionada.serie}-${ventaSeleccionada.numero.toString().padStart(8, '0')}`;
                        document.getElementById('printTipoTexto').textContent = ventaSeleccionada.comprobante ||
                            'COMPROBANTE';
                        document.getElementById('printFecha').textContent = new Date(ventaSeleccionada.fecha_emision)
                            .toLocaleDateString();
                        document.getElementById('printTotal').textContent =
                            `S/ ${parseFloat(ventaSeleccionada.total).toFixed(2)}`;

                        // Campos de contacto
                        document.getElementById('inputEmailEnvio').value = ventaSeleccionada.cliente?.email || '';
                        document.getElementById('inputTelefonoWA').value = ventaSeleccionada.cliente?.telefono || '';

                        // Estado / Validez
                        const printEstado = document.getElementById('printEstado');
                        const esElectronico = (ventaSeleccionada.comprobante || '').includes('ELECTRÓNICA');

                        printEstado.innerHTML = esElectronico ?
                            `<p class="mb-0 fs-11 text-success text-uppercase fw-bold"><i class="ri-checkbox-circle-line me-1"></i> Comprobante validado SUNAT</p>` :
                            `<p class="mb-0 fs-11 text-muted text-uppercase fw-medium"><i class="ri-error-warning-line me-1 text-warning"></i> No tiene validez tributaria</p>`;

                        let modalEl = document.getElementById('modalImpresion');
                        let modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                        modal.show();
                    }
                });
        }

        // Descargar PDF en formato específico
        function descargarPDFFormato(formato) {
            if (!ventaSeleccionada) return;
            // Ajuste de formatos (mapeo de nombres si es necesario)
            const fmt = formato === '50mm' ? '50mm' : (formato === '80mm' ? '80mm' : 'a4');
            const url = `<?php echo e(url('ventas')); ?>/${ventaSeleccionada.id}/pdf/${fmt}`;
            window.open(url, '_blank');
        }

        function enviarPorWhatsApp() {
            if (!ventaSeleccionada) return;

            const telefono = document.getElementById('inputTelefonoWA').value;
            if (!telefono) {
                mostrarToast("Ingrese un número válido", "error");
                return;
            }

            const baseUrl = window.location.origin;
            const linkTicket = `${baseUrl}/ticket/${ventaSeleccionada.codigo_externo}`;

            const mensaje = `¡Hola! Aquí tienes el ticket de tu compra:\n\n` +
                `📄 Ver Ticket: ${linkTicket}`;

            const waUrl = `https://wa.me/51${telefono}?text=${encodeURIComponent(mensaje)}`;
            window.open(waUrl, '_blank');
        }

        function mostrarToast(mensaje, tipo = 'success') {
            const colors = {
                success: "#0ab39c",
                error: "#f06548"
            };
            Toastify({
                text: mensaje,
                duration: 3000,
                gravity: "top",
                position: "right",
                style: {
                    background: colors[tipo]
                }
            }).showToast();
        }

        function copiarLink(link) {
            navigator.clipboard.writeText(link).then(() => {
                Toastify({
                    text: "Enlace copiado al portapapeles",
                    duration: 3000,
                    className: "bg-success"
                }).showToast();
            }).catch(err => {
                const textArea = document.createElement("textarea");
                textArea.value = link;
                document.body.appendChild(textArea);
                textArea.select();
                try {
                    document.execCommand('copy');
                    Toastify({
                        text: "Enlace copiado",
                        duration: 3000,
                        className: "bg-success"
                    }).showToast();
                } catch (e) {
                    console.error('Error al copiar link', e);
                }
                document.body.removeChild(textArea);
            });
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\master\resources\views/ventas/index.blade.php ENDPATH**/ ?>
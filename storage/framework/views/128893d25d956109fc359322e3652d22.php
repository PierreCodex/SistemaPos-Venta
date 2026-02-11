<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('translation.dashboards'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
    <link href="<?php echo e(URL::asset('build/libs/jsvectormap/jsvectormap.min.css')); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(URL::asset('build/libs/swiper/swiper-bundle.min.css')); ?>" rel="stylesheet" type="text/css" />
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col">

            <div class="h-100">
                <div class="row mb-3 pb-1">
                    <div class="col-12">
                        <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                            <div class="flex-grow-1">
                                <h4 class="fs-20 mb-1 text-uppercase fw-bold">Panel de Control: <span
                                        class="text-primary"><?php echo e(Auth::user()->name); ?></span></h4>
                                <p class="text-muted mb-0">
                                    <?php echo e($esAdmin ? 'Aquí tienes un resumen detallado de las operaciones de hoy.' : 'Resumen de tus operaciones de hoy.'); ?>

                                </p>
                            </div>
                            <div class="mt-3 mt-lg-0">
                                <div class="row g-3 mb-0 align-items-center">
                                    <div class="col-sm-auto">
                                        <div class="input-group">
                                            <input type="text" class="form-control border-0 dash-filter-picker shadow"
                                                data-provider="flatpickr" data-date-format="d M, Y"
                                                data-default-date="today">
                                            <div class="input-group-text bg-primary border-primary text-white">
                                                <i class="ri-calendar-2-line"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <a href="<?php echo e(route('ventas.create')); ?>" class="btn btn-soft-success shadow-none"><i
                                                class="ri-add-circle-line align-middle me-1"></i> Nueva Venta</a>
                                    </div>
                                </div>
                            </div>
                        </div><!-- end card header -->
                    </div>
                    <!--end col-->
                </div>
                <!--end row-->

                <div class="row">
                    <div class="col-xl-3 col-md-6">
                        <div class="card card-animate bg-primary">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-bold text-white-50 text-truncate mb-0">
                                            <?php echo e($esAdmin ? 'Ventas Hoy' : 'Mis Ventas Hoy'); ?></p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <h5 class="text-white fs-14 mb-0">
                                            <i class="ri-arrow-right-up-line fs-13 align-middle"></i>
                                            +<?php echo e($estadisticas['cantidad_ventas_hoy']); ?> vtas
                                        </h5>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4 text-white">S/ <span
                                                class="counter-value"
                                                data-target="<?php echo e($estadisticas['ventas_hoy']); ?>">0</span></h4>
                                        <a href="<?php echo e(route('ventas.index')); ?>"
                                            class="text-decoration-underline text-white-50 fs-12">Ver detalles</a>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-white-subtle rounded fs-3">
                                            <i class="bx bx-dollar-circle text-white"></i>
                                        </span>
                                    </div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->

                    <div class="col-xl-3 col-md-6">
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-bold text-muted text-truncate mb-0">
                                            <?php echo e($esAdmin ? 'Ventas este Mes' : 'Mis Ventas del Mes'); ?></p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">S/ <span class="counter-value"
                                                data-target="<?php echo e($estadisticas['ventas_mes']); ?>">0</span></h4>
                                        <span class="badge bg-success-subtle text-success"><i
                                                class="ri-arrow-up-line align-bottom me-1"></i>Ingreso Mensual</span>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-primary-subtle rounded fs-3">
                                            <i class="bx bx-shopping-bag text-primary"></i>
                                        </span>
                                    </div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->

                    <?php if($esAdmin): ?>
                        <div class="col-xl-3 col-md-6">
                            <div class="card card-animate">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <p class="text-uppercase fw-bold text-muted text-truncate mb-0">Alerta Stock
                                                Bajo
                                            </p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-end justify-content-between mt-4">
                                        <div>
                                            <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value"
                                                    data-target="<?php echo e($estadisticas['productos_bajo_stock']); ?>">0</span>
                                                Prod.
                                            </h4>
                                            <a href="<?php echo e(url('inventario/kardex')); ?>"
                                                class="badge bg-danger-subtle text-danger"><i
                                                    class="ri-error-warning-line align-bottom me-1"></i>Requiere pedido</a>
                                        </div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-warning-subtle rounded fs-3">
                                                <i class="bx bx-package text-warning"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div><!-- end card body -->
                            </div><!-- end card -->
                        </div><!-- end col -->
                    <?php endif; ?>

                    <?php if($esAdmin): ?>
                        <div class="col-xl-3 col-md-6">
                            <div class="card card-animate">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <p class="text-uppercase fw-bold text-muted text-truncate mb-0">Clientes Nuevos
                                            </p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-end justify-content-between mt-4">
                                        <div>
                                            <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value"
                                                    data-target="<?php echo e($estadisticas['clientes_nuevos']); ?>">0</span></h4>
                                            <span class="badge bg-info-subtle text-info"><i
                                                    class="ri-user-add-line align-bottom me-1"></i>Este período</span>
                                        </div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-info-subtle rounded fs-3">
                                                <i class="bx bx-user-circle text-info"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div><!-- end card body -->
                            </div><!-- end card -->
                        </div><!-- end col -->
                    <?php endif; ?>
                </div> <!-- end row-->

                <div class="row">
                    <div class="col-xl-8">
                        <div class="card">
                            <div class="card-header align-items-center d-flex border-bottom-0">
                                <h4 class="card-title mb-0 flex-grow-1 text-uppercase fw-bold">Evolución de Ventas (7 días)
                                </h4>
                                <div class="flex-shrink-0">
                                    <div class="dropdown card-header-dropdown">
                                        <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            <span class="text-muted fs-12">Detalles <i
                                                    class="mdi mdi-chevron-down ms-1"></i></span>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" href="<?php echo e(route('ventas.index')); ?>">Ver historial</a>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- end card header -->
                            <div class="card-body p-0 pb-2">
                                <div class="w-100">
                                    <div id="sales_evolution_chart" data-colors='["--vz-success"]' class="apex-charts"
                                        dir="ltr"></div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->

                    <div class="col-xl-4">
                        <div class="card card-height-100">
                            <div class="card-header align-items-center d-flex border-bottom-0">
                                <h4 class="card-title mb-0 flex-grow-1 text-uppercase fw-bold">Métodos Usados</h4>
                            </div><!-- end card header -->
                            <div class="card-body">
                                <div class="px-2 py-0">
                                    <?php
                                        $totalMonto = $metodosPago->sum('total_monto');
                                    ?>
                                    <?php $__currentLoopData = $metodosPago->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $metodo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $porcentaje =
                                                $totalMonto > 0 ? ($metodo->total_monto / $totalMonto) * 100 : 0;
                                            $claseColor = match ($metodo->metodo_pago) {
                                                'EFECTIVO' => 'bg-success',
                                                'YAPE', 'PLIN' => 'bg-info',
                                                'TARJETA' => 'bg-primary',
                                                default => 'bg-warning',
                                            };
                                        ?>
                                        <div class="mb-4">
                                            <p class="mb-1 text-muted fw-medium"><?php echo e($metodo->metodo_pago); ?> <span
                                                    class="float-end text-dark"><?php echo e(number_format($porcentaje, 0)); ?>%</span>
                                            </p>
                                            <div class="progress mt-2" style="height: 7px;">
                                                <div class="progress-bar progress-bar-striped progress-bar-animated <?php echo e($claseColor); ?>"
                                                    role="progressbar" style="width: <?php echo e($porcentaje); ?>%"
                                                    aria-valuenow="<?php echo e($porcentaje); ?>" aria-valuemin="0"
                                                    aria-valuemax="100">
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->
                </div> <!-- end row -->

                <!-- Sección de Evolución ya integrada arriba -->

                <div class="row">
                    <div class="col-xl-7">
                        <div class="card">
                            <div class="card-header align-items-center d-flex border-bottom-0">
                                <h4 class="card-title mb-0 flex-grow-1 text-uppercase fw-bold">Productos más vendidos</h4>
                            </div><!-- end card header -->

                            <div class="card-body">
                                <div class="table-responsive table-card">
                                    <table class="table table-centered table-hover align-middle table-nowrap mb-0">
                                        <thead class="bg-light text-muted">
                                            <tr>
                                                <th scope="col" class="fs-12">PRODUCTO</th>
                                                <th scope="col" class="fs-12">PRECIO</th>
                                                <th scope="col" class="fs-12 text-center">VENDIDO</th>
                                                <th scope="col" class="fs-12">ESTADO STOCK</th>
                                                <th scope="col" class="fs-12">SUBTOTAL</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__currentLoopData = $productosTop; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if($item->producto): ?>
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div
                                                                    class="avatar-xs bg-soft-primary text-primary rounded-circle p-1 me-2 shadow-sm d-flex align-items-center justify-content-center">
                                                                    <i class="ri-shopping-basket-2-line fs-14"></i>
                                                                </div>
                                                                <div>
                                                                    <h5 class="fs-13 my-1 fw-bold text-dark">
                                                                        <?php echo e($item->producto->nombre); ?></h5>
                                                                    <span
                                                                        class="text-muted fs-11"><?php echo e($item->producto->categoria->nombre ?? 'Sin categoría'); ?></span>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="fs-13">S/
                                                            <?php echo e(number_format($item->producto->precio_venta, 2)); ?></td>
                                                        <td class="text-center">
                                                            <span
                                                                class="badge bg-light text-dark border"><?php echo e(number_format($item->total_cantidad, 0)); ?>

                                                                uds.</span>
                                                        </td>
                                                        <td>
                                                            <?php if($item->producto->stock <= $item->producto->stock_minimo): ?>
                                                                <span
                                                                    class="badge bg-danger text-white px-2 shadow-sm">Bajo:
                                                                    <?php echo e($item->producto->stock); ?></span>
                                                            <?php else: ?>
                                                                <span
                                                                    class="badge bg-success text-white px-2 shadow-sm">Stock:
                                                                    <?php echo e($item->producto->stock); ?></span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <h5 class="fs-13 my-1 fw-bold text-primary">S/
                                                                <?php echo e(number_format($item->total_monto, 2)); ?></h5>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-5">
                        <div class="card card-height-100">
                            <div class="card-header align-items-center d-flex border-bottom-0">
                                <h4 class="card-title mb-0 flex-grow-1 text-uppercase fw-bold">Top Clientes</h4>
                            </div><!-- end card header -->

                            <div class="card-body">
                                <div class="table-responsive table-card">
                                    <table class="table table-centered table-hover align-middle table-nowrap mb-0">
                                        <thead class="bg-light text-muted">
                                            <tr>
                                                <th scope="col" class="fs-12">CLIENTE</th>
                                                <th scope="col" class="fs-12 text-center">VTAS</th>
                                                <th scope="col" class="fs-12 text-end">TOTAL</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__currentLoopData = $clientesTop; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ct): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-xs flex-shrink-0 me-3">
                                                                <span
                                                                    class="avatar-title bg-primary text-white rounded-circle fs-12 shadow">
                                                                    <?php echo e(strtoupper(substr($ct->cliente ? $ct->cliente->nombre : ($ct->nombre_cliente_generico ?: 'C'), 0, 1))); ?>

                                                                </span>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <h5 class="fs-13 my-0 fw-bold">
                                                                    <?php echo e($ct->cliente ? $ct->cliente->nombre : ($ct->nombre_cliente_generico ?: 'Cliente General')); ?>

                                                                </h5>
                                                                <span class="text-muted fs-11">
                                                                    <?php echo e($ct->cliente ? $ct->cliente->numero_documento : 'S/D'); ?>

                                                                </span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="fw-medium text-dark"><?php echo e($ct->total_ventas); ?></span>
                                                    </td>
                                                    <td class="text-end text-primary fw-bold">S/
                                                        <?php echo e(number_format($ct->total_gastado, 2)); ?></td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table><!-- end table -->
                                </div>
                            </div> <!-- .card-body-->
                        </div> <!-- .card-->
                    </div> <!-- .col-->
                </div> <!-- end row-->

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card card-height-100">
                            <div class="card-header align-items-center d-flex border-bottom-0">
                                <h4 class="card-title mb-0 flex-grow-1 text-uppercase fw-bold">Ventas Recientes</h4>
                                <div class="flex-shrink-0">
                                    <a href="<?php echo e(route('ventas.index')); ?>"
                                        class="btn btn-soft-primary btn-sm material-shadow-none">
                                        <i class="ri-history-line align-middle me-1"></i> Ver Historial
                                    </a>
                                </div>
                            </div><!-- end card header -->

                            <div class="card-body">
                                <div class="table-responsive table-card">
                                    <table class="table table-centered table-hover align-middle table-nowrap mb-0">
                                        <thead class="bg-light text-muted">
                                            <tr>
                                                <th scope="col" class="fs-12">NRO. VENTA</th>
                                                <th scope="col" class="fs-12">CLIENTE</th>
                                                <th scope="col" class="fs-12">FECHA</th>
                                                <th scope="col" class="fs-12 text-center">ESTADO</th>
                                                <th scope="col" class="fs-12 text-end">TOTAL</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__currentLoopData = $ventasRecientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $venta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td class="fw-bold text-primary">
                                                        #<?php echo e($venta->serie); ?>-<?php echo e(str_pad($venta->numero, 8, '0', STR_PAD_LEFT)); ?>

                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-xs flex-shrink-0 me-2">
                                                                <span
                                                                    class="avatar-title bg-soft-info text-info rounded-circle fs-11 fw-bold">
                                                                    <?php echo e(strtoupper(substr($venta->cliente ? $venta->cliente->nombre : ($venta->nombre_cliente_generico ?: 'C'), 0, 1))); ?>

                                                                </span>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <h5 class="fs-13 my-0 fw-bold">
                                                                    <?php echo e($venta->cliente ? $venta->cliente->nombre : ($venta->nombre_cliente_generico ?: 'Cliente General')); ?>

                                                                </h5>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-muted fs-12">
                                                        <?php echo e($venta->fecha_emision->format('d/m/Y H:i')); ?>

                                                    </td>
                                                    <td class="text-center">
                                                        <?php
                                                            $statusClass = match ($venta->estado) {
                                                                'COMPLETADA' => 'bg-success-subtle text-success',
                                                                'PENDIENTE' => 'bg-warning-subtle text-warning',
                                                                'ANULADA' => 'bg-danger-subtle text-danger',
                                                                default => 'bg-light text-dark',
                                                            };
                                                        ?>
                                                        <span
                                                            class="badge <?php echo e($statusClass); ?> text-uppercase px-2 border-0 shadow-none"><?php echo e($venta->estado == 'COMPLETADA' ? 'PAGADO' : $venta->estado); ?></span>
                                                    </td>
                                                    <td class="text-end">
                                                        <h5 class="fs-13 mb-0 fw-bold text-dark">S/
                                                            <?php echo e(number_format($venta->total, 2)); ?></h5>
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody><!-- end tbody -->
                                    </table><!-- end table -->
                                </div>
                            </div>
                        </div> <!-- .card-->
                    </div> <!-- .col-->
                </div> <!-- end row-->

            </div> <!-- end .h-100-->

        </div> <!-- end col -->

        <div class="col-auto layout-rightside-col">
            <div class="overlay"></div>
            <div class="layout-rightside">
                <div class="card h-100 rounded-0 border-0 shadow-lg">
                    <div class="card-body p-0">
                        <div class="p-4">
                            <h6 class="text-muted mb-4 text-uppercase fw-bold">Acceso Rápido</h6>

                            <div class="d-grid gap-3 mb-5">
                                <a href="<?php echo e(route('ventas.create')); ?>"
                                    class="btn btn-primary py-3 d-flex align-items-center justify-content-center shadow-sm">
                                    <i class="ri-shopping-cart-2-line fs-20 me-2"></i>
                                    <span class="fw-bold">NUEVA VENTA (POS)</span>
                                </a>
                                <?php if($esAdmin): ?>
                                    <a href="<?php echo e(url('inventario/ajustes')); ?>"
                                        class="btn btn-soft-info py-2 d-flex align-items-center justify-content-center">
                                        <i class="ri-list-settings-line fs-18 me-2"></i> Ajustes Stock
                                    </a>
                                    <a href="<?php echo e(route('productos.index')); ?>"
                                        class="btn btn-soft-warning py-2 d-flex align-items-center justify-content-center">
                                        <i class="ri-box-3-line fs-18 me-2"></i> Ver Almacén
                                    </a>
                                <?php endif; ?>
                                <a href="<?php echo e(route('caja.index')); ?>"
                                    class="btn btn-soft-success py-2 d-flex align-items-center justify-content-center">
                                    <i class="ri-safe-2-line fs-18 me-2"></i> Mi Caja
                                </a>
                            </div>

                            <?php if($esAdmin): ?>
                                <div class="card bg-light border-0 shadow-none mb-4">
                                    <div class="card-body p-3">
                                        <p class="text-uppercase fw-bold text-muted fs-11 mb-3">Estado Operativo</p>
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="avatar-xs flex-shrink-0 me-3">
                                                <span
                                                    class="avatar-title bg-danger-subtle text-danger rounded-circle fs-14">
                                                    <i class="ri-error-warning-line"></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="fs-14 mb-0 fw-bold"><?php echo e($estadisticas['productos_bajo_stock']); ?>

                                                </h6>
                                                <p class="text-muted fs-11 mb-0">Productos en stock crítico</p>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-xs flex-shrink-0 me-3">
                                                <span
                                                    class="avatar-title bg-success-subtle text-success rounded-circle fs-14">
                                                    <i class="ri-user-add-line"></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="fs-14 mb-0 fw-bold"><?php echo e($estadisticas['clientes_nuevos']); ?></h6>
                                                <p class="text-muted fs-11 mb-0">Clientes registrados este mes</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="mt-5 text-center">
                                <img src="<?php echo e(URL::asset('build/images/logo-dark.png')); ?>" alt="" height="20"
                                    class="opacity-50 mb-2">
                                <p class="text-muted fs-11 mb-0">Sistema de Gestión POS<br>Master v2.1.0</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end card -->
        </div>
        <!-- end .layout-rightside-->
    </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <!-- apexcharts -->
    <script src="<?php echo e(URL::asset('build/libs/apexcharts/apexcharts.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/jsvectormap/jsvectormap.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/jsvectormap/maps/world-merc.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/swiper/swiper-bundle.min.js')); ?>"></script>
    <!-- dashboard init -->
    <script src="<?php echo e(URL::asset('build/js/pages/dashboard-ecommerce.init.js')); ?>"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var options = {
                series: [{
                    name: 'Ventas Totales',
                    data: <?php echo json_encode($ventasSemana['montos']); ?>

                }],
                chart: {
                    height: 350,
                    type: 'area',
                    toolbar: {
                        show: false
                    },
                    zoom: {
                        enabled: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                colors: ['#0ab39c'], // --vz-success actual color
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        inverseColors: false,
                        opacityFrom: 0.45,
                        opacityTo: 0.05,
                        stops: [20, 100, 100, 100]
                    },
                },
                grid: {
                    borderColor: '#f1f1f1',
                    padding: {
                        bottom: 10
                    }
                },
                xaxis: {
                    categories: <?php echo json_encode($ventasSemana['fechas']); ?>,
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    }
                },
                yaxis: {
                    labels: {
                        formatter: function(value) {
                            return "S/ " + value.toFixed(2);
                        }
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(value) {
                            return "S/ " + value.toFixed(2);
                        }
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#sales_evolution_chart"), options);
            chart.render();
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\master\resources\views/index.blade.php ENDPATH**/ ?>
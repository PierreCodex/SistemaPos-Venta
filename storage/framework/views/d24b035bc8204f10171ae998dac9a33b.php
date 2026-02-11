

<?php $__env->startSection('title'); ?>
    Gestión de Caja
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Gestión de Caja</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="<?php echo e(url('/')); ?>">Inicio</a></li>
                        <li class="breadcrumb-item active">Caja</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ri-check-double-line me-2"></i> <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ri-error-warning-line me-2"></i> <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if(session('warning')): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="ri-alert-line me-2"></i> <?php echo e(session('warning')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    
    <div class="row">
        <div class="col-12">
            <div class="card <?php echo e($cajaAbierta ? 'border-success' : 'border-warning'); ?>">
                <div class="card-header <?php echo e($cajaAbierta ? 'bg-success-subtle' : 'bg-warning-subtle'); ?>">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0">
                            <i class="ri-safe-2-line me-2"></i>
                            Estado de Caja
                        </h5>
                        <?php if($cajaAbierta): ?>
                            <span class="badge bg-success fs-12">
                                <i class="ri-checkbox-circle-line me-1"></i> ABIERTA
                            </span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark fs-12">
                                <i class="ri-lock-line me-1"></i> CERRADA
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <?php if($cajaAbierta): ?>
                        
                        <div class="row g-4">
                            
                            <div class="col-lg-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-primary-subtle rounded-circle">
                                            <i class="ri-user-line text-primary fs-20"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <p class="text-muted mb-1">Abierta por</p>
                                        <h6 class="mb-0"><?php echo e($cajaAbierta->usuario->name ?? 'N/A'); ?></h6>
                                        <small class="text-muted"><?php echo e($cajaAbierta->fecha_apertura->format('d/m/Y H:i')); ?></small>
                                    </div>
                                </div>
                            </div>
                            
                            
                            <div class="col-lg-2 col-6">
                                <div class="text-center p-3 rounded bg-light">
                                    <p class="text-muted text-uppercase fw-semibold mb-1 fs-12">Monto Inicial</p>
                                    <h4 class="mb-0 text-primary">S/ <?php echo e(number_format($resumen['monto_inicial'] ?? 0, 2)); ?></h4>
                                </div>
                            </div>
                            
                            
                            <div class="col-lg-2 col-6">
                                <div class="text-center p-3 rounded bg-success-subtle">
                                    <p class="text-success text-uppercase fw-semibold mb-1 fs-12">Ingresos</p>
                                    <h4 class="mb-0 text-success">S/ <?php echo e(number_format($resumen['total_ingresos'] ?? 0, 2)); ?></h4>
                                </div>
                            </div>
                            
                            
                            <div class="col-lg-2 col-6">
                                <div class="text-center p-3 rounded bg-danger-subtle">
                                    <p class="text-danger text-uppercase fw-semibold mb-1 fs-12">Egresos</p>
                                    <h4 class="mb-0 text-danger">S/ <?php echo e(number_format($resumen['total_egresos'] ?? 0, 2)); ?></h4>
                                </div>
                            </div>
                            
                            
                            <div class="col-lg-2 col-6">
                                <div class="text-center p-3 rounded bg-info-subtle">
                                    <p class="text-info text-uppercase fw-semibold mb-1 fs-12">Monto Actual</p>
                                    <h4 class="mb-0 text-info">S/ <?php echo e(number_format($resumen['monto_esperado'] ?? 0, 2)); ?></h4>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">
                        
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="ri-shopping-cart-2-line text-primary fs-24"></i>
                                    <div>
                                        <span class="text-muted">Ventas realizadas:</span>
                                        <strong class="ms-2"><?php echo e($resumen['cantidad_ventas'] ?? 0); ?></strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="ri-money-dollar-circle-line text-success fs-24"></i>
                                    <div>
                                        <span class="text-muted">Total vendido:</span>
                                        <strong class="ms-2 text-success">S/ <?php echo e(number_format($resumen['total_ventas'] ?? 0, 2)); ?></strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="ri-time-line text-info fs-24"></i>
                                    <div>
                                        <span class="text-muted">Tiempo abierta:</span>
                                        <strong class="ms-2"><?php echo e($resumen['duracion'] ?? 'N/A'); ?></strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                        <div class="d-flex flex-wrap gap-2">
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('caja.movimientos')): ?>
                                <a href="<?php echo e(route('caja.movimiento.form')); ?>" class="btn btn-soft-primary">
                                    <i class="ri-exchange-funds-line me-1"></i> Registrar Movimiento
                                </a>
                            <?php endif; ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('caja.cerrar')): ?>
                                <a href="<?php echo e(route('caja.cierre')); ?>" class="btn btn-warning">
                                    <i class="ri-lock-line me-1"></i> Cerrar Caja
                                </a>
                            <?php endif; ?>
                            <a href="<?php echo e(route('caja.movimientos', $cajaAbierta->id)); ?>" class="btn btn-soft-info">
                                <i class="ri-list-check-2 me-1"></i> Ver Movimientos
                            </a>
                            <a href="<?php echo e(route('caja.pagos-credito')); ?>" class="btn btn-soft-success">
                                <i class="ri-hand-coin-line me-1"></i> Pagos de Crédito
                            </a>
                        </div>
                    <?php else: ?>
                        
                        <div class="text-center py-4">
                            <div class="avatar-lg mx-auto mb-4">
                                <span class="avatar-title bg-warning-subtle rounded-circle">
                                    <i class="ri-safe-2-line text-warning fs-36"></i>
                                </span>
                            </div>
                            <h5 class="mb-2">La caja está cerrada</h5>
                            <p class="text-muted mb-4">Debe abrir la caja para poder realizar ventas y registrar movimientos.</p>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('caja.abrir')): ?>
                                <a href="<?php echo e(route('caja.apertura')); ?>" class="btn btn-success btn-lg">
                                    <i class="ri-lock-unlock-line me-2"></i> Abrir Caja
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">
                        <i class="ri-history-line me-2"></i>Historial de Sesiones
                    </h5>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('caja.reporte')): ?>
                        <a href="<?php echo e(route('caja.reporte')); ?>" class="btn btn-soft-primary btn-sm">
                            <i class="ri-file-chart-line me-1"></i> Ver Reporte
                        </a>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Apertura</th>
                                    <th>Cierre</th>
                                    <th>Usuario</th>
                                    <th class="text-end">M. Inicial</th>
                                    <th class="text-end">Ventas</th>
                                    <th class="text-end">M. Final</th>
                                    <th class="text-center">Diferencia</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $historial; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sesion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><strong><?php echo e($sesion->id); ?></strong></td>
                                        <td>
                                            <small><?php echo e($sesion->fecha_apertura->format('d/m/Y')); ?></small><br>
                                            <small class="text-muted"><?php echo e($sesion->fecha_apertura->format('H:i')); ?></small>
                                        </td>
                                        <td>
                                            <?php if($sesion->fecha_cierre): ?>
                                                <small><?php echo e($sesion->fecha_cierre->format('d/m/Y')); ?></small><br>
                                                <small class="text-muted"><?php echo e($sesion->fecha_cierre->format('H:i')); ?></small>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e($sesion->usuario->name ?? 'N/A'); ?></td>
                                        <td class="text-end">S/ <?php echo e(number_format($sesion->monto_inicial, 2)); ?></td>
                                        <td class="text-end text-success fw-semibold">S/ <?php echo e(number_format($sesion->total_ventas, 2)); ?></td>
                                        <td class="text-end">
                                            <?php if($sesion->monto_final !== null): ?>
                                                S/ <?php echo e(number_format($sesion->monto_final, 2)); ?>

                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center"><?php echo $sesion->badge_diferencia; ?></td>
                                        <td class="text-center"><?php echo $sesion->badge_estado; ?></td>
                                        <td class="text-center">
                                            <a href="<?php echo e(route('caja.show', $sesion->id)); ?>" class="btn btn-soft-info btn-sm" title="Ver Detalle">
                                                <i class="ri-eye-line"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="10" class="text-center text-muted py-4">
                                            <i class="ri-inbox-line fs-24 d-block mb-2"></i>
                                            No hay sesiones de caja registradas
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\master\resources\views/caja/index.blade.php ENDPATH**/ ?>


<?php $__env->startSection('title'); ?>
    Movimientos de Caja
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Movimientos - Sesión #<?php echo e($cajaSesion->id); ?></h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="<?php echo e(url('/')); ?>">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo e(route('caja.index')); ?>">Caja</a></li>
                        <li class="breadcrumb-item active">Movimientos</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row mb-3">
        <div class="col-12">
            <div class="card bg-light border-0">
                <div class="card-body py-3">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <?php echo $cajaSesion->badge_estado; ?>

                        </div>
                        <div class="col">
                            <span class="text-muted">Apertura:</span>
                            <strong><?php echo e($cajaSesion->fecha_apertura->format('d/m/Y H:i')); ?></strong>
                            <span class="mx-2">|</span>
                            <span class="text-muted">Usuario:</span>
                            <strong><?php echo e($cajaSesion->usuario->name ?? 'N/A'); ?></strong>
                        </div>
                        <div class="col-auto">
                            <a href="<?php echo e(route('caja.show', $cajaSesion->id)); ?>" class="btn btn-soft-primary btn-sm">
                                <i class="ri-eye-line me-1"></i> Ver Sesión Completa
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-exchange-funds-line me-2"></i>Lista de Movimientos
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tablaMovimientos" class="table table-hover align-middle" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha/Hora</th>
                                    <th>Tipo</th>
                                    <th>Concepto</th>
                                    <th>Descripción</th>
                                    <th>Usuario</th>
                                    <th class="text-end">Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $movimientos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movimiento): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($movimiento->id); ?></td>
                                        <td><?php echo e($movimiento->created_at->format('d/m/Y H:i:s')); ?></td>
                                        <td><?php echo $movimiento->badge_tipo; ?></td>
                                        <td><?php echo e($movimiento->concepto_texto); ?></td>
                                        <td>
                                            <span class="text-truncate d-inline-block" style="max-width: 250px;" title="<?php echo e($movimiento->descripcion); ?>">
                                                <?php echo e($movimiento->descripcion); ?>

                                            </span>
                                        </td>
                                        <td><?php echo e($movimiento->usuario->name ?? 'Sistema'); ?></td>
                                        <td class="text-end"><?php echo $movimiento->monto_formateado; ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>

                    
                    <div class="d-flex justify-content-center mt-3">
                        <?php echo e($movimientos->links()); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row">
        <div class="col-12">
            <a href="<?php echo e(route('caja.index')); ?>" class="btn btn-secondary">
                <i class="ri-arrow-left-line me-1"></i> Volver al Panel de Caja
            </a>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\master\resources\views/caja/movimientos.blade.php ENDPATH**/ ?>
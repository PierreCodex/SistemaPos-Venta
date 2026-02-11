



<div class="items-header">
    <div class="header-prod">PRODUCTO</div>
    <div class="header-cant">CANT</div>
    <div class="header-um">U.M</div>
    <div class="header-pu">P.U</div>
    <div class="header-imp">IMP</div>
</div>


<div class="items-section">
    <?php $__empty_1 = true; $__currentLoopData = $detalles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $detalle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="item">
            <div class="item-prod"><?php echo e(strtoupper($detalle['descripcion'] ?? '')); ?></div>
            <div class="item-cant"><?php echo e(number_format($detalle['cantidad'] ?? 1, 0)); ?></div>
            <div class="item-um"><?php echo e($detalle['unidad'] ?? 'NIU'); ?></div>
            <div class="item-pu"><?php echo e(number_format($detalle['precio_unitario'] ?? 0, 2)); ?></div>
            <div class="item-imp"><?php echo e(number_format($detalle['total'] ?? 0, 2)); ?></div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="item">
            <div style="width: 100%; text-align: center;">Sin items</div>
        </div>
    <?php endif; ?>
</div>
<?php /**PATH C:\xampp\htdocs\master\resources\views/pdf/components/items-table-ticket-exact.blade.php ENDPATH**/ ?>
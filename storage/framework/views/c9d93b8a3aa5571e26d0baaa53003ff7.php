

<?php
    $maxFilas = in_array($format, ['a5', 'A5']) ? 8 : 15;
    $contador = count($detalles);
?>

<?php if(in_array($format, ['a4', 'A4', 'a5', 'A5'])): ?>
    
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%">Nº</th>
                <th style="width: 45%">PRODUCTO</th>
                <th style="width: 10%">CANT</th>
                <th style="width: 10%">U.M</th>
                <th style="width: 15%">P.U</th>
                <th style="width: 15%">IMP</th>
            </tr>
        </thead>
        <tbody>
            
            <?php $__currentLoopData = $detalles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $detalle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="text-center"><?php echo e($index + 1); ?></td>
                    <td><?php echo e(strtoupper(($detalle['codigo'] ? '[' . $detalle['codigo'] . '] ' : '') . ($detalle['descripcion'] ?? ''))); ?>

                    </td>
                    <td class="text-center"><?php echo e(number_format($detalle['cantidad'] ?? 0, 2)); ?></td>
                    <td class="text-center"><?php echo e($detalle['unidad'] ?? 'NIU'); ?></td>
                    <td class="text-right"><?php echo e(number_format($detalle['precio_unitario'] ?? 0, 2)); ?></td>
                    <td class="text-right"><?php echo e(number_format($detalle['total'] ?? 0, 2)); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            
            <?php for($i = $contador; $i < $maxFilas; $i++): ?>
                <tr>
                    <td>&nbsp;</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            <?php endfor; ?>
        </tbody>
    </table>
<?php else: ?>
    
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 40%">PRODUCTO</th>
                <th style="width: 10%">CANT</th>
                <th style="width: 10%">U.M</th>
                <th style="width: 20%">P.U</th>
                <th style="width: 20%">IMP</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $detalles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detalle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="text-left"><?php echo e(strtoupper($detalle['descripcion'] ?? '')); ?></td>
                    <td class="text-center"><?php echo e(number_format($detalle['cantidad'] ?? 0, 2)); ?></td>
                    <td class="text-center"><?php echo e($detalle['unidad'] ?? 'NIU'); ?></td>
                    <td class="text-right"><?php echo e(number_format($detalle['precio_unitario'] ?? 0, 2)); ?></td>
                    <td class="text-right"><?php echo e(number_format($detalle['total'] ?? 0, 2)); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\master\resources\views/pdf/components/items-table.blade.php ENDPATH**/ ?>



<table class="items-table">
    <thead>
        <tr style="border-top: 1px solid #000; border-bottom: 1px solid #000;">
            <th style="width: 33%; text-align: left; font-size: 6.5px; padding: 2px 0;">PRODUCTO</th>
            <th style="width: 11%; text-align: center; font-size: 6.5px; padding: 2px 0;">CANT</th>
            <th style="width: 11%; text-align: center; font-size: 6.5px; padding: 2px 0;">U.M</th>
            <th style="width: 20%; text-align: right; font-size: 6.5px; padding: 2px 0;">P.U</th>
            <th style="width: 25%; text-align: right; font-size: 6.5px; padding: 2px 0;">IMP</th>
        </tr>
    </thead>
    <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $detalles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detalle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr style="border-bottom: 0.1pt dashed #eee;">
                <td
                    style="font-size: 6.5px; padding: 2px 0; text-align: left; vertical-align: top; word-wrap: break-word;">
                    <?php echo e(strtoupper($detalle['descripcion'] ?? 'PRODUCTO')); ?>

                </td>
                <td style="font-size: 6.5px; padding: 2px 0; text-align: center; vertical-align: top;">
                    <?php if(in_array($detalle['unidad'] ?? 'NIU', ['KG', 'LTR'])): ?>
                        <?php echo e(number_format($detalle['cantidad'] ?? 0, 3)); ?>

                    <?php else: ?>
                        <?php echo e(number_format($detalle['cantidad'] ?? 0, 0)); ?>

                    <?php endif; ?>
                </td>
                <td style="font-size: 6.5px; padding: 2px 0; text-align: center; vertical-align: top;">
                    <?php echo e(strtoupper(substr($detalle['unidad'] ?? 'NIU', 0, 3))); ?>

                </td>
                <td style="font-size: 6.5px; padding: 2px 0; text-align: right; vertical-align: top;">
                    <?php echo e(number_format($detalle['precio_unitario'] ?? 0, 2)); ?>

                </td>
                <td style="font-size: 6.5px; padding: 2px 0; text-align: right; vertical-align: top;">
                    <?php echo e(number_format($detalle['subtotal'] ?? 0, 2)); ?>

                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="5" style="text-align: center; font-size: 7px; padding: 5px;">Sin items</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
<div style="border-top: 1px solid #000; margin-top: 0;"></div>
<?php /**PATH C:\xampp\htdocs\master\resources\views/pdf/components/items-table-ticket-exact.blade.php ENDPATH**/ ?>
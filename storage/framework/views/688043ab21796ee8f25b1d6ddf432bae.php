


<?php if(in_array($format, ['a4', 'A4', 'a5', 'A5'])): ?>
    <table class="totals-table">
        <tr>
            <td rowspan="7" style="width: 60%;">
                <div class="qr-info-container">
                    <div class="qr-section">
                        <?php if(isset($qr_code) && $qr_code): ?>
                            <img src="<?php echo e($qr_code); ?>" alt="Código QR">
                        <?php endif; ?>
                    </div>
                    <div class="info-footer">
                        <b>FECHA EMISIÓN:</b> <?php echo e($fecha_emision); ?><br>
                        <b>CONDICIÓN DE PAGO:</b> <?php echo e($document->forma_pago_tipo ?? 'CONTADO'); ?><br>
                        <?php if(!empty($document->observaciones)): ?>
                            <b>OBSERVACIONES:</b> <?php echo e($document->observaciones); ?><br>
                        <?php endif; ?>
                        <?php if(!empty($document->leyendas)): ?>
                            <b>LEYENDAS:</b><br>
                            <?php
                                $leyendas = is_array($document->leyendas) ? $document->leyendas : json_decode($document->leyendas, true);
                                $leyendas = $leyendas ?? [];
                            ?>
                            <?php $__currentLoopData = $leyendas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $leyenda): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                • <?php echo e($leyenda['value'] ?? ''); ?><br>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                        <?php if($hash): ?>
                            <b>HASH:</b> <?php echo e(substr($hash, 0, 20)); ?>...<br>
                        <?php endif; ?>
                    </div>
                </div>
            </td>
            <td class="label">Total Ope. Gravadas</td>
            <td><?php echo e($totales['moneda']); ?> <?php echo e($totales['subtotal_formatted']); ?></td>
        </tr>
        <tr>
            <td class="label">Total Ope. Inafectadas</td>
            <td><?php echo e($totales['moneda']); ?> <?php echo e(number_format($document->mto_oper_inafectas ?? 0, 2)); ?></td>
        </tr>
        <tr>
            <td class="label">Total Ope. Exoneradas</td>
            <td><?php echo e($totales['moneda']); ?> <?php echo e(number_format($document->mto_oper_exoneradas ?? 0, 2)); ?></td>
        </tr>
        <tr>
            <td class="label">Total Descuentos</td>
            <td><?php echo e($totales['moneda']); ?> 0.00</td>
        </tr>
        <tr>
            <td class="label">Total IGV</td>
            <td><?php echo e($totales['moneda']); ?> <?php echo e($totales['igv_formatted']); ?></td>
        </tr>
        <tr>
            <td class="label">Total ISC</td>
            <td><?php echo e($totales['moneda']); ?> <?php echo e(number_format($document->mto_isc ?? 0, 2)); ?></td>
        </tr>
        <tr>
            <td class="label resaltado">TOTAL A PAGAR</td>
            <td class="resaltado"><?php echo e($totales['moneda']); ?> <?php echo e($totales['total_formatted']); ?></td>
        </tr>
    </table>
<?php endif; ?><?php /**PATH C:\xampp\htdocs\master\resources\views/pdf/components/totals-original.blade.php ENDPATH**/ ?>
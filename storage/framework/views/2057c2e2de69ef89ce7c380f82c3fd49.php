


<div class="payment-info" style="margin-top: 5pt; padding-top: 5pt; border-top: 1px dashed #000; font-size: 8px;">
    <div style="display: flex; justify-content: space-between;">
        <span>Met. Pago:</span>
        <span><?php echo e(strtoupper($metodo_pago ?? 'EFECTIVO')); ?></span>
    </div>
    <div style="display: flex; justify-content: space-between;">
        <span>Recibido:</span>
        <span><?php echo e(number_format($monto_recibido ?? 0, 2)); ?></span>
    </div>
    <div style="display: flex; justify-content: space-between;">
        <span>Vuelto:</span>
        <span><?php echo e(number_format($vuelto ?? 0, 2)); ?></span>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\master\resources\views/pdf/components/payment-info-ticket.blade.php ENDPATH**/ ?>
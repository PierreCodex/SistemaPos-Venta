


<div class="totals-section">
    
    <div class="total-line">
        <span class="total-text">TOTAL GRAVADO</span>
        <span class="total-dots">........................</span>
        <span class="total-value">(S/) <?php echo e($totales['subtotal_formatted'] ?? '16.95'); ?></span>
    </div>
    
    
    <div class="total-line">
        <span class="total-text">I.G.V</span>
        <span class="total-dots">..............................</span>
        <span class="total-value">(S/) <?php echo e($totales['igv_formatted'] ?? '3.05'); ?></span>
    </div>
    
    
    <div class="total-line total-final">
        <span class="total-text">TOTAL</span>
        <span class="total-dots">.................................</span>
        <span class="total-value">(S/) <?php echo e($totales['total_formatted'] ?? '20.00'); ?></span>
    </div>
</div>


<div class="total-letras">
    SON: <?php echo e(strtoupper($total_en_letras ?? 'VEINTE CON 00/100 SOLES')); ?>

</div>


<div class="payment-info">
    <div><strong>FORMA DE PAGO:</strong> <?php echo e($document->forma_pago_tipo ?? 'EFECTIVO'); ?></div>
    <div><strong>COND.VENTA:</strong> <?php echo e($document->condicion_venta ?? 'CONTADO'); ?></div>
</div>


<?php if(!empty($document->observaciones)): ?>
    <div class="payment-info">
        <div><strong>Observaciones:</strong></div>
        <div><?php echo e($document->observaciones); ?></div>
    </div>
<?php endif; ?><?php /**PATH C:\xampp\htdocs\master\resources\views/pdf/components/totals-ticket-exact.blade.php ENDPATH**/ ?>
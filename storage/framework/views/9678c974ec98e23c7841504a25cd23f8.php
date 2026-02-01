


<div class="totals-section">
    
    <div class="total-line">
        <span class="total-text">OP. GRAVADA</span>
        <span class="total-dots">........................</span>
        <span class="total-value">(S/) <?php echo e(number_format($totales['subtotal'] ?? 0, 2)); ?></span>
    </div>

    
    <div class="total-line">
        <span class="total-text">I.G.V (18%)</span>
        <span class="total-dots">........................</span>
        <span class="total-value">(S/) <?php echo e(number_format($totales['igv'] ?? 0, 2)); ?></span>
    </div>

    
    <div class="total-line total-final">
        <span class="total-text">TOTAL</span>
        <span class="total-dots">.................................</span>
        <span class="total-value">(S/) <?php echo e(number_format($totales['total'] ?? 0, 2)); ?></span>
    </div>
</div>


<div class="total-letras">
    SON: <?php echo e(strtoupper($total_en_letras ?? '')); ?>

</div>


<?php if(!empty($document['observaciones'] ?? '')): ?>
    <div class="payment-info" style="border-top: none;">
        <div><strong>Observaciones:</strong></div>
        <div><?php echo e($document['observaciones']); ?></div>
    </div>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\master\resources\views/pdf/components/totals-ticket-exact.blade.php ENDPATH**/ ?>
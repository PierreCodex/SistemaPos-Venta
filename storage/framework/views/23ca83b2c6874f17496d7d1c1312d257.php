


<?php if(in_array($format, ['a4', 'A4', 'a5', 'A5'])): ?>
    <table class="en-letras">
        <tr>
            <td>SON: <?php echo e(strtoupper($total_en_letras)); ?> <?php echo e(strtoupper($totales['moneda_nombre'] ?? 'SOLES')); ?></td>
        </tr>
    </table>
<?php endif; ?><?php /**PATH C:\xampp\htdocs\master\resources\views/pdf/components/total-letras.blade.php ENDPATH**/ ?>
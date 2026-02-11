



<?php if(isset($qr_code) && !empty($qr_code)): ?>
    <div class="qr-section">
        <div class="qr-code">
            <img src="<?php echo e($qr_code); ?>" alt="QR Code">
        </div>
    </div>
<?php endif; ?>


<div class="footer-text">
    <?php if(isset($qr_code) && !empty($qr_code)): ?>
        Representación impresa de la <?php echo e(strtoupper($tipo_documento_nombre ?? 'BOLETA DE VENTA ELECTRONICA')); ?>.<br>
        Puede verificarla en www.sunat.gob.pe
    <?php else: ?>
        Este documento es una <?php echo e(strtoupper($tipo_documento_nombre ?? 'NOTA DE VENTA')); ?>.<br>
        No tiene valor tributario.
    <?php endif; ?>
</div>


<?php if(isset($hash) && !empty($hash)): ?>
    <div class="footer-auth">
        <?php echo e($hash); ?>

    </div>
<?php endif; ?>


<div class="footer-url">
    www.nubefact.com
</div>


<div class="powered-by">
    Powered by NUBEFACT
</div>
<?php /**PATH C:\xampp\htdocs\master\resources\views/pdf/components/footer-ticket-exact.blade.php ENDPATH**/ ?>
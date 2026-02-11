


<?php if(isset($qr_code) && $qr_code): ?>
    <div class="qr-section">
        <div class="qr-code">
            <img src="<?php echo e($qr_code); ?>" 
                 alt="Código QR" 
                 style="width: <?php echo e($format === 'a4' ? '80px' : '60px'); ?>; height: <?php echo e($format === 'a4' ? '80px' : '60px'); ?>;">
        </div>
        <div class="qr-info">
            Representación impresa del comprobante electrónico
        </div>
    </div>
<?php endif; ?>

<?php if(isset($hash) || true): ?>
    <div class="footer">
        <div>Autorizado mediante Resolución de Superintendencia Nº 097-2012/SUNAT</div>
        <div>Representación impresa del Comprobante de Pago Electrónico</div>
        
        <?php if(isset($hash) && $hash): ?>
            <div class="hash-section">
                <strong>HASH CDR:</strong> <?php echo e($hash); ?>

            </div>
        <?php endif; ?>
        
        <div class="hash-section">
            Consulte su comprobante en: <?php echo e(config('app.url', 'https://mi-empresa.com')); ?>

        </div>
    </div>
<?php endif; ?><?php /**PATH C:\xampp\htdocs\master\resources\views/pdf/components/qr-footer.blade.php ENDPATH**/ ?>
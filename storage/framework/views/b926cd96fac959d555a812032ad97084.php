


<?php if(in_array($format, ['a4', 'A4', 'a5', 'A5'])): ?>
    
    <div class="client-info">
        <div>
            <p>
                <b><?php echo e($client['tipo_documento'] == '6' ? 'RUC' : 'DNI'); ?>:</b> <?php echo e($client['numero_documento'] ?? 'N/A'); ?><br>
                <b>CLIENTE:</b> <?php echo e($client['razon_social'] ?? 'CLIENTE'); ?><br>
                <?php if(isset($client['direccion']) && $client['direccion']): ?>
                    <b>DIRECCIÓN:</b> <?php echo e($client['direccion']); ?>

                <?php endif; ?>
            </p>
        </div>
        <div>
            <p>
                <b>FECHA EMISIÓN:</b> <?php echo e($fecha_emision); ?><br>
                <b>FECHA VENCIMIENTO:</b> <?php echo e($fecha_vencimiento ?? '-'); ?><br>
                <b>MONEDA:</b> <?php echo e($totales['moneda_nombre'] ?? 'SOLES'); ?>

            </p>
        </div>
    </div>
<?php else: ?>
    
    <div class="client-section">
        <div class="client-row">
            <span class="client-label">CLIENTE:</span> <?php echo e(strtoupper($client['razon_social'] ?? $client['nombre'] ?? 'CLIENTE')); ?>

        </div>
        
        <?php if(isset($client['numero_documento'])): ?>
            <div class="client-row">
                <span class="client-label"><?php echo e($client['tipo_documento'] == '6' ? 'RUC' : ($client['tipo_documento'] == '1' ? 'DNI' : 'DOC')); ?>:</span> <?php echo e($client['numero_documento']); ?>

            </div>
        <?php endif; ?>
        
        <?php if(isset($client['direccion']) && $client['direccion']): ?>
            <div class="client-row break-word">
                <span class="client-label">DIR:</span> <?php echo e($client['direccion']); ?>

            </div>
        <?php endif; ?>
        
        <?php if(isset($fecha_emision)): ?>
            <div class="client-row">
                <span class="client-label">FECHA:</span> <?php echo e($fecha_emision); ?>

            </div>
        <?php endif; ?>
    </div>
<?php endif; ?><?php /**PATH C:\xampp\htdocs\master\resources\views/pdf/components/client-info.blade.php ENDPATH**/ ?>
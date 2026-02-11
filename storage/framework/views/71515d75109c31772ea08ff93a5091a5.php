


<div class="client-section">
    
    <div class="client-name"><?php echo e(strtoupper($client['razon_social'] ?? 'CAMILO SANCHEZ')); ?></div>

    
    <div class="client-separator">---</div>

    
    <div class="client-details">
        <?php if(
            !empty($client['numero_documento']) &&
                $client['numero_documento'] !== '00000000' &&
                $client['numero_documento'] !== 'N/A'): ?>
            <?php echo e(($client['tipo_documento'] ?? '1') == '6' ? 'RUC' : 'DNI'); ?>: <?php echo e($client['numero_documento']); ?>

        <?php else: ?>
            VARIOS
        <?php endif; ?>
    </div>

    
    <div class="client-details">
        FECHA: <?php echo e($fecha_emision ?? '06/03/2024'); ?> HORA: <?php echo e(now()->format('H:i:s A')); ?>

    </div>
</div>
<?php /**PATH C:\xampp\htdocs\master\resources\views/pdf/components/client-info-ticket-exact.blade.php ENDPATH**/ ?>
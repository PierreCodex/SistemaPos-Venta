


<div class="client-section">
    
    <div class="client-name"><?php echo e(strtoupper($client['nombre'] ?? 'CLIENTE GENERAL')); ?></div>

    
    <div class="client-separator">---</div>

    
    <div class="client-details"><?php echo e($client['tipo_documento'] ?? 'DNI'); ?> <?php echo e($client['numero_documento'] ?? '00000000'); ?>

    </div>

    
    <div class="client-details">
        FECHA: <?php echo e($fecha_emision ?? '06/03/2024'); ?> HORA: <?php echo e(now()->format('H:i:s A')); ?>

    </div>
</div>
<?php /**PATH C:\xampp\htdocs\master\resources\views/pdf/components/client-info-ticket-exact.blade.php ENDPATH**/ ?>
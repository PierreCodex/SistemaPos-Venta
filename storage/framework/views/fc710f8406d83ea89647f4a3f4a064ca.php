


<?php
    $logoPath = public_path('img/logo-ticket.png'); // Ruta local en master
?>

<div class="header">
    
    <?php if(file_exists($logoPath)): ?>
        <div class="logo-section-ticket">
            <img src="data:image/png;base64,<?php echo e(base64_encode(file_get_contents($logoPath))); ?>" alt="Logo"
                class="logo-img-ticket">
        </div>
    <?php endif; ?>

    
    <div class="company-name"><?php echo e(strtoupper($company['razon_social'] ?? '')); ?></div>

    
    <div class="company-ruc">RUC: <?php echo e($company['ruc'] ?? ''); ?></div>

    
    <div class="company-details">
        <?php echo e($company['direccion'] ?? ''); ?><br>
        <?php echo e($company['distrito'] ?? ''); ?> <?php echo e($company['provincia'] ?? ''); ?><br>
        <?php if(!empty($company['telefono'])): ?>
            Tel: <?php echo e($company['telefono']); ?><br>
        <?php endif; ?>
        <?php if(!empty($company['email'])): ?>
            Correo: <?php echo e($company['email']); ?><br>
        <?php endif; ?>
        <?php if(!empty($company['website'])): ?>
            Web: <?php echo e($company['website']); ?>

        <?php endif; ?>
    </div>

    
    <div class="document-title"><?php echo e(strtoupper($tipo_documento_nombre ?? 'COMPROBANTE')); ?></div>

    
    <div class="document-number"><?php echo e($document['serie'] ?? ''); ?> -
        <?php echo e(str_pad($document['numero'] ?? ($document['correlativo'] ?? ''), 8, '0', STR_PAD_LEFT)); ?></div>
</div>
<?php /**PATH C:\xampp\htdocs\master\resources\views/pdf/components/header-ticket-exact.blade.php ENDPATH**/ ?>
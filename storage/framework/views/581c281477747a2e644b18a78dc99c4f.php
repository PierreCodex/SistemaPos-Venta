


<?php
    // Unificamos la carga de la imagen en Base64 para todos los formatos y evitar problemas de rutas con dompdf.
    $logoPath = public_path('logo_factura.png');

?>

<?php if(in_array($format, ['a4', 'A4', 'a5', 'A5'])): ?>
    
    <div class="header">
        <div class="logo-section">
            <img src="data:image/png;base64,<?php echo e(base64_encode(file_get_contents($logoPath))); ?>" alt="Logo Empresa" class="logo-img">
        </div>
        
        <div class="company-section">
            <div class="company-name"><?php echo e(strtoupper($company->razon_social ?? 'EMPRESA')); ?></div>
            <div class="company-details">
                <?php if($company->direccion): ?>
                    <?php echo e($company->direccion); ?><br>
                <?php endif; ?>
                <?php if($company->distrito || $company->provincia || $company->departamento): ?>
                    <?php echo e($company->distrito ? $company->distrito . ', ' : ''); ?><?php echo e($company->provincia ? $company->provincia . ', ' : ''); ?><?php echo e($company->departamento); ?><br>
                <?php endif; ?>
                <?php if($company->telefono): ?>
                    TELÉFONO: <?php echo e($company->telefono); ?><br>
                <?php endif; ?>
                <?php if($company->email): ?>
                    EMAIL: <?php echo e($company->email); ?><br>
                <?php endif; ?>
                <?php if($company->web): ?>
                    WEB: <?php echo e($company->web); ?>

                <?php endif; ?>
            </div>
        </div>
        
        <div class="document-section">
            <div class="factura-box">
                <p><b>RUC <?php echo e($company->ruc ?? 'N/A'); ?></b></p>
                <p><b><?php echo e(strtoupper($tipo_documento_nombre ?? 'FACTURA ELECTRÓNICA')); ?></b></p>
                <p><b><?php echo e($document->serie); ?>-<?php echo e(str_pad($document->correlativo, 6, '0', STR_PAD_LEFT)); ?></b></p>
            </div>
        </div>
    </div>
<?php else: ?>
    
    <div class="header">
        <div class="logo-section-ticket">
          
            <img src="data:image/jpg;base64,<?php echo e(base64_encode(file_get_contents($logoPath))); ?>" alt="Logo Empresa" class="logo-img-ticket">
          
        </div>
        <div class="company-name"><?php echo e(strtoupper($company->razon_social ?? 'EMPRESA')); ?></div>
        <div class="company-details">
            <?php if($company->nombre_comercial): ?>
                <?php echo e($company->nombre_comercial); ?><br>
            <?php endif; ?>
            RUC: <?php echo e($company->ruc ?? ''); ?><br>
            <?php echo e($company->direccion ?? ''); ?><br>
            <?php if($company->telefono): ?>
                Tel: <?php echo e($company->telefono); ?><br>
            <?php endif; ?>
            <?php if($company->email): ?>
                Email: <?php echo e($company->email); ?>

            <?php endif; ?>
        </div>
        
        <div class="document-info">
            <div><?php echo e(strtoupper($tipo_documento_nombre)); ?></div>
            <div><?php echo e($document->numero_completo); ?></div>
            <div><?php echo e($fecha_emision); ?></div>
        </div>
    </div>
<?php endif; ?><?php /**PATH C:\xampp\htdocs\master\resources\views/pdf/components/header.blade.php ENDPATH**/ ?>
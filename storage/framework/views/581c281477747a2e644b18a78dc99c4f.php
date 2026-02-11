


<?php
    // Asegurar que $company sea un objeto para facilitar el acceso en la vista
    $companyObject = is_array($company) ? (object) $company : $company;

    // Obtener el logo desde los datos de la empresa
    $logo = data_get($company, 'logo');
    $logoPath = null;

    if ($logo && file_exists(storage_path('app/public/' . $logo))) {
        $logoPath = storage_path('app/public/' . $logo);
    } else {
        $logoPath = public_path('logo_factura.png');
    }

    // Verificamos si realmente podemos leer el archivo para evitar errores 500
    $logoBase64 = null;
    if (file_exists($logoPath)) {
        try {
            $logoBase64 = base64_encode(file_get_contents($logoPath));
        } catch (\Exception $e) {
            $logoBase64 = null;
        }
    }

    // Normalizar campos que pueden variar entre servicios
    $razonSocial = data_get($company, 'razon_social', 'Mi Empresa');
    $direccion = data_get($company, 'direccion');
    $ruc = data_get($company, 'ruc');
    $telefono = data_get($company, 'telefono');
    $email = data_get($company, 'email');
    $web = data_get($company, 'website') ?? data_get($company, 'web');
    $distrito = data_get($company, 'distrito');
    $provincia = data_get($company, 'provincia');
    $departamento = data_get($company, 'departamento');
    $nombreComercial = data_get($company, 'nombre_comercial');
?>

<?php if(in_array($format, ['a4', 'A4', 'a5', 'A5'])): ?>
    
    <div class="header">
        <div class="logo-section">
            <?php if($logoBase64): ?>
                <img src="data:image/png;base64,<?php echo e($logoBase64); ?>" alt="Logo Empresa" class="logo-img">
            <?php endif; ?>
        </div>

        <div class="company-section">
            <div class="company-name"><?php echo e(strtoupper($razonSocial)); ?></div>
            <div class="company-details">
                <?php if($direccion): ?>
                    <?php echo e($direccion); ?><br>
                <?php endif; ?>
                <?php if($distrito || $provincia || $departamento): ?>
                    <?php echo e($distrito ? $distrito . ', ' : ''); ?><?php echo e($provincia ? $provincia . ', ' : ''); ?><?php echo e($departamento); ?><br>
                <?php endif; ?>
                <?php if($telefono): ?>
                    TELÉFONO: <?php echo e($telefono); ?><br>
                <?php endif; ?>
                <?php if($email): ?>
                    EMAIL: <?php echo e($email); ?><br>
                <?php endif; ?>
                <?php if($web): ?>
                    WEB: <?php echo e($web); ?>

                <?php endif; ?>
            </div>
        </div>

        <div class="document-section">
            <div class="factura-box">
                <p><b>RUC <?php echo e($ruc ?? 'N/A'); ?></b></p>
                <p><b><?php echo e(strtoupper($tipo_documento_nombre ?? 'FACTURA ELECTRÓNICA')); ?></b></p>
                <p><b><?php echo e($document->serie); ?>-<?php echo e(str_pad($document->correlativo, 6, '0', STR_PAD_LEFT)); ?></b></p>
            </div>
        </div>
    </div>
<?php else: ?>
    
    <div class="header">
        <div class="logo-section-ticket">
            <?php if($logoBase64): ?>
                <img src="data:image/jpg;base64,<?php echo e($logoBase64); ?>" alt="Logo Empresa" class="logo-img-ticket">
            <?php endif; ?>
        </div>
        <div class="company-name"><?php echo e(strtoupper($razonSocial)); ?></div>
        <div class="company-details">
            <?php if($nombreComercial): ?>
                <?php echo e($nombreComercial); ?><br>
            <?php endif; ?>
            RUC: <?php echo e($ruc ?? ''); ?><br>
            <?php echo e($direccion ?? ''); ?><br>
            <?php if($telefono): ?>
                Tel: <?php echo e($telefono); ?><br>
            <?php endif; ?>
            <?php if($email): ?>
                Email: <?php echo e($email); ?>

            <?php endif; ?>
        </div>

        <div class="document-info">
            <div><?php echo e(strtoupper($tipo_documento_nombre)); ?></div>
            <div><?php echo e($document->numero_completo); ?></div>
            <div><?php echo e($fecha_emision); ?></div>
        </div>
    </div>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\master\resources\views/pdf/components/header.blade.php ENDPATH**/ ?>
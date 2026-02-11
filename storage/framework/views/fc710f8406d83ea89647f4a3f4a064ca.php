


<?php
    // Obtener el logo desde los datos de la empresa
    $logo = data_get($company, 'logo');
    $logoPath = null;
    $logoBase64 = null;

    if ($logo && file_exists(storage_path('app/public/' . $logo))) {
        $logoPath = storage_path('app/public/' . $logo);
    } else {
        $logoPath = public_path('logo_factura.png');
    }

    if (file_exists($logoPath)) {
        try {
            $logoBase64 = base64_encode(file_get_contents($logoPath));
        } catch (\Exception $e) {
            $logoBase64 = null;
        }
    }

    // Normalizar datos
    $razonSocial = data_get($company, 'razon_social', 'EMPRESA DEMO SAC');
    $ruc = data_get($company, 'ruc', '20100100100');
    $direccion = data_get($company, 'direccion', 'CALLE LAS NORMAS 123');
    $distrito = data_get($company, 'distrito', 'CALLAO');
    $email = data_get($company, 'email', 'Administrador@facturas.net');
    $web = data_get($company, 'website') ?? data_get($company, 'web', 'www.facturas.net');
?>

<div class="header">
    
    <?php if($logoBase64): ?>
        <div class="logo-section-ticket">
            <img src="data:image/png;base64,<?php echo e($logoBase64); ?>" alt="Logo" class="logo-img-ticket">
        </div>
    <?php endif; ?>

    
    <div class="company-name"><?php echo e(strtoupper($razonSocial)); ?></div>

    
    <div class="company-ruc">RUC: <?php echo e($ruc); ?></div>

    
    <div class="company-details">
        <?php echo e($direccion); ?><br>
        <?php echo e($distrito); ?><br>
        Correo: <?php echo e($email); ?><br>
        Web: <?php echo e($web); ?>

    </div>

    
    <div class="document-title"><?php echo e(strtoupper($tipo_documento_nombre ?? 'BOLETA DE VENTA ELECTRONICA')); ?></div>

    
    <div class="document-number"><?php echo e($document->serie ?? 'B002'); ?> -
        <?php echo e(str_pad($document->numero ?? ($document->correlativo ?? '0'), 8, '0', STR_PAD_LEFT)); ?></div>
</div>
<?php /**PATH C:\xampp\htdocs\master\resources\views/pdf/components/header-ticket-exact.blade.php ENDPATH**/ ?>
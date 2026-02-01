<?php $__env->startSection('content'); ?>
    
    <?php echo $__env->make('pdf.components.header', [
        'company' => $company, 
        'document' => $document, 
        'tipo_documento_nombre' => $tipo_documento_nombre,
        'fecha_emision' => $fecha_emision,
        'format' => 'a4'
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <?php echo $__env->make('pdf.components.client-info', [
        'client' => $client,
        'format' => 'a4',
        'fecha_emision' => $fecha_emision,
        'fecha_vencimiento' => $fecha_vencimiento ?? null,
        'totales' => $totales ?? []
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <?php echo $__env->make('pdf.components.items-table', [
        'detalles' => $detalles,
        'format' => 'a4'
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <?php echo $__env->make('pdf.components.total-letras', [
        'total_en_letras' => $total_en_letras ?? '',
        'totales' => $totales ?? [],
        'format' => 'a4'
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <?php echo $__env->make('pdf.components.totals-original', [
        'document' => $document,
        'format' => 'a4',
        'qr_code' => $qr_code ?? null,
        'hash' => $hash ?? null,
        'fecha_emision' => $fecha_emision,
        'total_en_letras' => $total_en_letras ?? '',
        'totales' => $totales ?? []
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <?php echo $__env->make('pdf.components.qr-footer', [
        'qr_code' => null,
        'hash' => $hash ?? null,
        'format' => 'a4'
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('pdf.layouts.a4', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\master\resources\views/pdf/a4/boleta.blade.php ENDPATH**/ ?>
<?php $__env->startSection('content'); ?>
    
    <?php echo $__env->make('pdf.components.header-ticket-exact', [
        'company' => $company, 
        'document' => $document, 
        'tipo_documento_nombre' => $tipo_documento_nombre
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <?php echo $__env->make('pdf.components.client-info-ticket-exact', [
        'client' => $client,
        'fecha_emision' => $fecha_emision
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <?php echo $__env->make('pdf.components.items-table-ticket-exact', [
        'detalles' => $detalles
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <?php echo $__env->make('pdf.components.totals-ticket-exact', [
        'document' => $document,
        'totales' => $totales ?? [],
        'total_en_letras' => $total_en_letras ?? ''
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <?php echo $__env->make('pdf.components.footer-ticket-exact', [
        'document' => $document,
        'qr_code' => $qr_code ?? null,
        'hash' => $hash ?? null,
        'tipo_documento_nombre' => $tipo_documento_nombre ?? 'BOLETA ELECTRÓNICA'
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('pdf.layouts.80mm', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\master\resources\views/pdf/80mm/sale-note.blade.php ENDPATH**/ ?>
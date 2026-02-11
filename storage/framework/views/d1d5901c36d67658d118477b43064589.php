<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket #<?php echo e($venta->comprobante_completo); ?> | <?php echo e($empresa->nombre_comercial); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.2.0/remixicon.min.css">
    <style>
        :root {
            --primary: #405189;
            --success: #0ab39c;
            --text-main: #343a40;
            --text-muted: #878a99;
            --bg-body: #f3f3f9;
        }

        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: var(--bg-body);
            color: var(--text-main);
            display: flex;
            justify-content: center;
        }

        .container {
            width: 100%;
            max-width: 450px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .header {
            padding: 30px 20px;
            text-align: center;
            border-bottom: 2px dashed #eee;
            position: relative;
        }

        .empresa-logo {
            max-width: 120px;
            margin-bottom: 15px;
        }

        .empresa-nombre {
            font-size: 20px;
            font-weight: 700;
            margin: 0 0 5px 0;
            color: var(--primary);
            text-transform: uppercase;
        }

        .empresa-info {
            font-size: 13px;
            color: var(--text-muted);
            margin: 0;
            line-height: 1.5;
        }

        .ticket-info {
            padding: 20px;
            background: #f8f9fa;
            text-align: center;
        }

        .ticket-num {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .ticket-fecha {
            font-size: 14px;
            color: var(--text-muted);
        }

        .section-title {
            padding: 15px 20px 5px;
            font-size: 12px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .content-box {
            padding: 10px 20px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .info-label {
            color: var(--text-muted);
        }

        .info-value {
            font-weight: 600;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .items-table th {
            text-align: left;
            font-size: 12px;
            padding: 10px 5px;
            border-bottom: 1px solid #eee;
            color: var(--text-muted);
        }

        .items-table td {
            padding: 12px 5px;
            font-size: 14px;
            border-bottom: 1px solid #f8f9fa;
        }

        .item-nombre {
            font-weight: 600;
            display: block;
        }

        .item-detalle {
            font-size: 12px;
            color: var(--text-muted);
        }

        .totals {
            padding: 20px;
            background-color: #fafafa;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .total-row.big {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 2px solid #eee;
            font-size: 20px;
            font-weight: 700;
            color: var(--primary);
        }

        .footer {
            padding: 30px 20px;
            text-align: center;
            font-size: 13px;
            color: var(--text-muted);
        }

        .footer i {
            font-size: 24px;
            color: var(--success);
            margin-bottom: 10px;
            display: block;
        }

        .btn-whatsapp {
            display: inline-flex;
            align-items: center;
            background-color: #25D366;
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            border-radius: 50px;
            font-weight: 600;
            margin-top: 20px;
            box-shadow: 0 4px 10px rgba(37, 211, 102, 0.3);
            transition: transform 0.2s;
        }

        .btn-whatsapp:active {
            transform: scale(0.95);
        }

        .btn-whatsapp i {
            margin-right: 8px;
            font-size: 18px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            margin-top: 10px;
        }

        .status-paid {
            background: #daf4f0;
            color: #0ab39c;
        }

        .status-pending {
            background: #fef4e4;
            color: #f7b84b;
        }

        .status-canceled {
            background: #fde8e4;
            color: #f06548;
        }

        .d-flex {
            display: flex;
        }

        .justify-content-center {
            justify-content: center;
        }

        .gap-2 {
            gap: 8px;
        }

        @media print {
            .btn-whatsapp {
                display: none;
            }

            body {
                padding: 0;
                background: #fff;
            }

            .container {
                box-shadow: none;
                border: none;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 0;
                background: #fff;
            }

            .container {
                box-shadow: none;
                border: none;
                border-radius: 0;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <?php if($empresa && $empresa->logo): ?>
                <img src="<?php echo e(asset('storage/' . $empresa->logo)); ?>" alt="Logo" class="empresa-logo">
            <?php endif; ?>
            <h1 class="empresa-nombre"><?php echo e($empresa->nombre); ?></h1>
            <p class="empresa-info">
                RUC: <?php echo e($empresa->ruc); ?><br>
                <?php echo e($empresa->direccion); ?><br>
                Tel: <?php echo e($empresa->telefono); ?>

            </p>
        </div>

        <div class="ticket-info">
            <div class="ticket-num"><?php echo e(strtoupper($venta->comprobante)); ?> <?php echo e($venta->comprobante_completo); ?></div>
            <div class="ticket-fecha"><?php echo e($venta->fecha_emision->format('d/m/Y h:i A')); ?></div>

            <?php
                $statusClass = match ($venta->estado) {
                    'COMPLETADA' => 'status-paid',
                    'PENDIENTE' => 'status-pending',
                    'ANULADA' => 'status-canceled',
                    default => '',
                };
            ?>
            <div class="status-badge <?php echo e($statusClass); ?>">
                <?php echo e($venta->estado); ?>

            </div>
        </div>

        <div class="section-title">Datos del Cliente</div>
        <div class="content-box">
            <div class="info-row">
                <span class="info-label">Cliente:</span>
                <span class="info-value"><?php echo e($venta->nombre_cliente); ?></span>
            </div>
            <?php if($venta->cliente): ?>
                <div class="info-row">
                    <span class="info-label"><?php echo e($venta->cliente->tipo_documento); ?>:</span>
                    <span class="info-value"><?php echo e($venta->cliente->numero_documento); ?></span>
                </div>
            <?php endif; ?>
        </div>

        <div class="section-title">Detalle de Productos</div>
        <div class="content-box">
            <table class="items-table">
                <thead>
                    <tr>
                        <th>PRODUCTO</th>
                        <th style="text-align: center;">CANT</th>
                        <th style="text-align: right;">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $venta->detalles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detalle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <span class="item-nombre"><?php echo e($detalle->producto->nombre); ?></span>
                                <span class="item-detalle">S/ <?php echo e(number_format($detalle->precio_unitario, 2)); ?></span>
                            </td>
                            <td style="text-align: center;"><?php echo e(number_format($detalle->cantidad, 2)); ?></td>
                            <td style="text-align: right; font-weight: 600;">S/
                                <?php echo e(number_format($detalle->subtotal, 2)); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        <div class="totals">
            <div class="total-row">
                <span class="info-label">Subtotal:</span>
                <span class="info-value">S/ <?php echo e(number_format($venta->subtotal, 2)); ?></span>
            </div>
            <div class="total-row">
                <span class="info-label">IGV (<?php echo e(number_format($venta->igv_porcentaje, 0)); ?>%):</span>
                <span class="info-value">S/ <?php echo e(number_format($venta->igv_monto, 2)); ?></span>
            </div>
            <?php if($venta->descuento > 0): ?>
                <div class="total-row" style="color: #f06548;">
                    <span class="info-label">Descuento:</span>
                    <span class="info-value">- S/ <?php echo e(number_format($venta->descuento, 2)); ?></span>
                </div>
            <?php endif; ?>
            <div class="total-row big">
                <span>TOTAL</span>
                <span>S/ <?php echo e(number_format($venta->total, 2)); ?></span>
            </div>

            <div class="total-row" style="margin-top: 15px;">
                <span class="info-label">Método de Pago:</span>
                <span class="info-value text-uppercase"><?php echo e($venta->metodo_pago); ?></span>
            </div>

            <?php if($venta->es_credito): ?>
                <div class="total-row">
                    <span class="info-label">Saldo Pendiente:</span>
                    <span class="info-value text-danger">S/ <?php echo e(number_format($venta->saldo_pendiente, 2)); ?></span>
                </div>
            <?php endif; ?>
        </div>

        <div class="footer">
            <i class="ri-checkbox-circle-line"></i>
            <strong>¡Gracias por su compra!</strong>
            <p>Vuelva pronto a <?php echo e($empresa->nombre_comercial); ?></p>

            <?php if($venta->comprobanteElectronico): ?>
                <p style="font-size: 11px; margin-top: 10px;">
                    Representación impresa de la <?php echo e($venta->comprobanteElectronico->tipo_comprobante_nombre); ?>

                    electrónica.
                    Para descargar su comprobante oficial, ingrese a nuestro portal.
                </p>
            <?php endif; ?>

            <div class="d-flex gap-2 justify-content-center" style="margin-top: 20px;">
                <button onclick="window.print()" class="btn-whatsapp" style="background: #343a40;">
                    <i class="ri-printer-line"
                        style="color: white; font-size: 18px; display: inline-block; margin-bottom: 0;"></i> Imprimir
                    Ticket
                </button>
                <a href="https://api.whatsapp.com/send?text=Hola! Aquí tienes mi ticket de compra: <?php echo e(request()->fullUrl()); ?>"
                    target="_blank" class="btn-whatsapp">
                    <i class="ri-whatsapp-line"
                        style="color: white; font-size: 18px; display: inline-block; margin-bottom: 0;"></i> Compartir
                </a>
            </div>
        </div>
    </div>
</body>

</html>
<?php /**PATH C:\xampp\htdocs\master\resources\views/tickets/publico.blade.php ENDPATH**/ ?>
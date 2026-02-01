<?php $__env->startSection('format-styles'); ?>
    <style>
        /* ================= BASE ================= */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica', Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 20px 0;
            margin: 0;
        }

        .ticket-container {
            width: 135pt;
            /* Reducido ligeramente para margen de seguridad */
            background-color: white;
            margin: 0 auto;
            padding: 0;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .ticket {
            width: 100%;
            padding: 0;
            /* Eliminado padding para usar el ancho real */
            text-align: center;
            margin: 0 auto;
        }

        /* ================= HEADER ================= */
        .header {
            text-align: center;
            margin-bottom: 2px;
            padding-bottom: 2px;
            border-bottom: 1px dashed #ccc;
        }

        .logo-section-ticket {
            text-align: center;
            margin-bottom: 1px;
        }

        .logo-img-ticket {
            width: 75px;
            height: 31px;
            object-fit: contain;
            display: block;
            margin: 0 auto 1px;
            padding: 1px;
        }

        .company-name {
            font-size: 9px;
            font-weight: bold;
            margin-bottom: 1px;
            text-transform: uppercase;
            color: #000;
        }

        .company-ruc {
            font-size: 8px;
            font-weight: bold;
            margin-bottom: 1px;
        }

        .company-details {
            font-size: 8px;
            line-height: 1.1;
            margin-bottom: 2px;
        }

        /* ================= DOCUMENT TITLE ================= */
        .document-title {
            font-size: 8px;
            font-weight: bold;
            text-align: center;
            margin: 3px 0;
            text-transform: uppercase;
            padding: 2px 0;
            border-top: 1px dashed #ccc;
            border-bottom: 1px dashed #ccc;
        }

        .document-number {
            font-size: 8px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 3px;
        }

        /* ================= CLIENT INFO ================= */
        .client-section {
            margin: 3px 0;
            font-size: 8px;
            padding: 2px 0;
            border-bottom: 1px dashed #ccc;
        }

        .client-name {
            font-weight: bold;
            font-size: 8px;
            text-align: center;
            margin-bottom: 1px;
        }

        .client-separator {
            text-align: center;
            margin: 1px 0;
            font-size: 8px;
        }

        .client-details {
            font-size: 8px;
            margin-bottom: 2px;
            text-align: center;
        }

        /* ================= ITEMS TABLE ================= */
        .items-section {
            margin: 2px 0;
            width: 100%;
        }

        table.items-table {
            width: 100%;
            border-collapse: collapse;
        }

        table.items-table th,
        table.items-table td {
            font-size: 6.5px;
            padding: 1px 0;
            vertical-align: top;
        }




        /* ================= TOTALS ================= */
        .totals-section {
            margin: 2px 0;
            font-size: 8px;
            border-top: 1px solid #000;
            padding-top: 1px;
        }

        .total-line {
            display: block;
            width: 100%;
            margin-bottom: 1px;
            font-weight: bold;
            font-size: 8px;
            line-height: 1.2;
            position: relative;
        }

        .total-text {
            display: inline-block;
            float: left;
            font-weight: bold;
        }

        .total-value {
            display: inline-block;
            float: right;
            font-weight: bold;
        }

        .total-dots {
            display: inline-block;
            float: left;
            font-weight: normal;
            letter-spacing: 0.3px;
            overflow: hidden;
            margin: 0 1px;
        }

        .total-final {
            border-top: 1px solid #000;
            padding-top: 1px;
            margin-top: 1px;
            font-size: 8px;
        }

        .total-final .total-text,
        .total-final .total-value {
            font-size: 8px;
            font-weight: bold;
        }

        /* Clear floats */
        .total-line::after {
            content: "";
            display: table;
            clear: both;
        }

        .total-letras {
            font-size: 8px;
            font-weight: bold;
            margin: 2px 0;
            text-align: left;
        }

        /* ================= PAYMENT INFO ================= */
        .payment-info {
            font-size: 8px;
            margin: 2px 0;
            text-align: left;
            padding: 2px 0;
            border-top: 1px dashed #ccc;
            border-bottom: 1px dashed #ccc;
        }

        .payment-info div {
            margin-bottom: 1px;
        }

        /* ================= QR AND FOOTER ================= */
        .qr-section {
            text-align: center;
            margin: 3px 0;
            padding: 3px 0;
            border-bottom: 1px dashed #ccc;
        }

        .qr-code img {
            width: 60px;
            height: 60px;
            margin: 2px 0;
        }

        .footer-text {
            font-size: 8px;
            text-align: center;
            line-height: 1.1;
            margin: 1px 0;
        }

        .footer-url {
            font-size: 8px;
            text-align: center;
            font-weight: bold;
            margin: 1px 0;
        }

        .footer-auth {
            font-size: 6px;
            text-align: center;
            margin: 1px 0;
            word-wrap: break-word;
            word-break: break-all;
            padding: 0 5px;
        }

        .powered-by {
            font-size: 7px;
            text-align: center;
            margin-top: 1px;
            color: #888;
        }

        /* ================= UTILITIES ================= */
        .text-bold {
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        /* ================= PRINT STYLES ================= */
        @media print {
            body {
                background: none;
                margin: 0;
                padding: 0;
            }

            .ticket-container {
                box-shadow: none;
                border-radius: 0;
                width: 50mm;
            }

            .ticket {
                width: 50mm;
                padding: 0;
                margin: 0;
            }

            .no-print {
                display: none;
            }
        }

        /* ================= ACTION BUTTONS ================= */
        .actions {
            text-align: center;
            margin-top: 10px;
        }

        .btn {
            background-color: #4CAF50;
            border: none;
            color: white;
            padding: 5px 10px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 12px;
            margin: 2px 1px;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #45a049;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('body-content'); ?>
    <div class="ticket-container">
        <div class="ticket">
            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('pdf.layouts.base', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\master\resources\views/pdf/layouts/50mm.blade.php ENDPATH**/ ?>
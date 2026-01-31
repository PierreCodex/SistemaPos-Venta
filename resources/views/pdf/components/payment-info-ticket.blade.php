{{-- PDF Ticket Payment Info Component --}}
{{-- Props: $metodo_pago, $monto_recibido, $vuelto --}}

<div class="payment-info" style="margin-top: 5pt; padding-top: 5pt; border-top: 1px dashed #000; font-size: 8px;">
    <div style="display: flex; justify-content: space-between;">
        <span>Met. Pago:</span>
        <span>{{ strtoupper($metodo_pago ?? 'EFECTIVO') }}</span>
    </div>
    <div style="display: flex; justify-content: space-between;">
        <span>Recibido:</span>
        <span>{{ number_format($monto_recibido ?? 0, 2) }}</span>
    </div>
    <div style="display: flex; justify-content: space-between;">
        <span>Vuelto:</span>
        <span>{{ number_format($vuelto ?? 0, 2) }}</span>
    </div>
</div>

{{-- PDF Ticket Payment Info Component --}}
{{-- Props: $document --}}
<div class="payment-info">
    <div><strong>FORMA DE PAGO:</strong> {{ $document->forma_pago_tipo ?? 'EFECTIVO' }}</div>
    <div><strong>COND.VENTA:</strong> {{ $document->condicion_venta ?? 'CONTADO' }}</div>
    @if (!empty($document->observaciones))
        <div><strong>OBSERVACIONES:</strong> {{ $document->observaciones }}</div>
    @endif
</div>

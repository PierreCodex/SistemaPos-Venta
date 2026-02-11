{{-- PDF Ticket Items Component (Exact Design Match) --}}
{{-- Props: $detalles --}}

{{-- Items Header --}}
<div class="items-header">
    <div class="header-prod">PRODUCTO</div>
    <div class="header-cant">CANT</div>
    <div class="header-um">U.M</div>
    <div class="header-pu">P.U</div>
    <div class="header-imp">IMP</div>
</div>

{{-- Items List --}}
<div class="items-section">
    @forelse($detalles as $index => $detalle)
        <div class="item">
            <div class="item-prod">{{ strtoupper($detalle['descripcion'] ?? '') }}</div>
            <div class="item-cant">{{ number_format($detalle['cantidad'] ?? 1, 0) }}</div>
            <div class="item-um">{{ $detalle['unidad'] ?? 'NIU' }}</div>
            <div class="item-pu">{{ number_format($detalle['precio_unitario'] ?? 0, 2) }}</div>
            <div class="item-imp">{{ number_format($detalle['total'] ?? 0, 2) }}</div>
        </div>
    @empty
        <div class="item">
            <div style="width: 100%; text-align: center;">Sin items</div>
        </div>
    @endforelse
</div>

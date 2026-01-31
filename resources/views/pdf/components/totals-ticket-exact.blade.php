{{-- PDF Ticket Totals Component (Exact Design Match) --}}
{{-- Props: $document, $totales, $total_en_letras --}}

<div class="totals-section">
    {{-- Subtotal --}}
    <div class="total-line">
        <span class="total-text">OP. GRAVADA</span>
        <span class="total-dots">........................</span>
        <span class="total-value">(S/) {{ number_format($totales['subtotal'] ?? 0, 2) }}</span>
    </div>

    {{-- IGV --}}
    <div class="total-line">
        <span class="total-text">I.G.V (18%)</span>
        <span class="total-dots">........................</span>
        <span class="total-value">(S/) {{ number_format($totales['igv'] ?? 0, 2) }}</span>
    </div>

    {{-- Total Final --}}
    <div class="total-line total-final">
        <span class="total-text">TOTAL</span>
        <span class="total-dots">.................................</span>
        <span class="total-value">(S/) {{ number_format($totales['total'] ?? 0, 2) }}</span>
    </div>
</div>

{{-- Total en Letras --}}
<div class="total-letras">
    SON: {{ strtoupper($total_en_letras ?? '') }}
</div>

{{-- Payment Info --}}
@include('pdf.components.payment-info-ticket')

{{-- Observations --}}
@if (!empty($document['observaciones'] ?? ''))
    <div class="payment-info" style="border-top: none;">
        <div><strong>Observaciones:</strong></div>
        <div>{{ $document['observaciones'] }}</div>
    </div>
@endif

{{-- PDF Ticket Items Component (Compact Single Row) --}}
{{-- Props: $detalles --}}

<table class="items-table">
    <thead>
        <tr style="border-top: 1px solid #000; border-bottom: 1px solid #000;">
            <th style="width: 33%; text-align: left; font-size: 6.5px; padding: 2px 0;">PRODUCTO</th>
            <th style="width: 11%; text-align: center; font-size: 6.5px; padding: 2px 0;">CANT</th>
            <th style="width: 11%; text-align: center; font-size: 6.5px; padding: 2px 0;">U.M</th>
            <th style="width: 20%; text-align: right; font-size: 6.5px; padding: 2px 0;">P.U</th>
            <th style="width: 25%; text-align: right; font-size: 6.5px; padding: 2px 0;">IMP</th>
        </tr>
    </thead>
    <tbody>
        @forelse($detalles as $detalle)
            <tr style="border-bottom: 0.1pt dashed #eee;">
                <td
                    style="font-size: 6.5px; padding: 2px 0; text-align: left; vertical-align: top; word-wrap: break-word;">
                    {{ strtoupper($detalle['descripcion'] ?? 'PRODUCTO') }}
                </td>
                <td style="font-size: 6.5px; padding: 2px 0; text-align: center; vertical-align: top;">
                    @if (in_array($detalle['unidad'] ?? 'NIU', ['KG', 'LTR']))
                        {{ number_format($detalle['cantidad'] ?? 0, 3) }}
                    @else
                        {{ number_format($detalle['cantidad'] ?? 0, 0) }}
                    @endif
                </td>
                <td style="font-size: 6.5px; padding: 2px 0; text-align: center; vertical-align: top;">
                    {{ strtoupper(substr($detalle['unidad'] ?? 'NIU', 0, 3)) }}
                </td>
                <td style="font-size: 6.5px; padding: 2px 0; text-align: right; vertical-align: top;">
                    {{ number_format($detalle['precio_unitario'] ?? 0, 2) }}
                </td>
                <td style="font-size: 6.5px; padding: 2px 0; text-align: right; vertical-align: top;">
                    {{ number_format($detalle['subtotal'] ?? 0, 2) }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" style="text-align: center; font-size: 7px; padding: 5px;">Sin items</td>
            </tr>
        @endforelse
    </tbody>
</table>
<div style="border-top: 1px solid #000; margin-top: 0;"></div>

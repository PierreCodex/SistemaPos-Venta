{{-- PDF Items Table Component --}}
{{-- Props: $detalles, $format --}}
@php
    $maxFilas = in_array($format, ['a5', 'A5']) ? 8 : 15;
    $contador = count($detalles);
@endphp

@if (in_array($format, ['a4', 'A4', 'a5', 'A5']))
    {{-- A4/A5 Items Table --}}
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%">Nº</th>
                <th style="width: 45%">PRODUCTO</th>
                <th style="width: 10%">CANT</th>
                <th style="width: 10%">U.M</th>
                <th style="width: 15%">P.U</th>
                <th style="width: 15%">IMP</th>
            </tr>
        </thead>
        <tbody>
            {{-- Items reales --}}
            @foreach ($detalles as $index => $detalle)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ strtoupper(($detalle['codigo'] ? '[' . $detalle['codigo'] . '] ' : '') . ($detalle['descripcion'] ?? '')) }}
                    </td>
                    <td class="text-center">{{ number_format($detalle['cantidad'] ?? 0, 2) }}</td>
                    <td class="text-center">{{ $detalle['unidad'] ?? 'NIU' }}</td>
                    <td class="text-right">{{ number_format($detalle['precio_unitario'] ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($detalle['total'] ?? 0, 2) }}</td>
                </tr>
            @endforeach

            {{-- Filas vacías --}}
            @for ($i = $contador; $i < $maxFilas; $i++)
                <tr>
                    <td>&nbsp;</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endfor
        </tbody>
    </table>
@else
    {{-- Ticket Items Table Fallback --}}
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 40%">PRODUCTO</th>
                <th style="width: 10%">CANT</th>
                <th style="width: 10%">U.M</th>
                <th style="width: 20%">P.U</th>
                <th style="width: 20%">IMP</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($detalles as $detalle)
                <tr>
                    <td class="text-left">{{ strtoupper($detalle['descripcion'] ?? '') }}</td>
                    <td class="text-center">{{ number_format($detalle['cantidad'] ?? 0, 2) }}</td>
                    <td class="text-center">{{ $detalle['unidad'] ?? 'NIU' }}</td>
                    <td class="text-right">{{ number_format($detalle['precio_unitario'] ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($detalle['total'] ?? 0, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

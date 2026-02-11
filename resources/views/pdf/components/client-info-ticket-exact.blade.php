{{-- PDF Ticket Client Info Component (Exact Design Match) --}}
{{-- Props: $client, $fecha_emision --}}

<div class="client-section">
    {{-- Client Name --}}
    <div class="client-name">{{ strtoupper($client['razon_social'] ?? 'CAMILO SANCHEZ') }}</div>

    {{-- Separator --}}
    <div class="client-separator">---</div>

    {{-- Document Number --}}
    <div class="client-details">
        @if (
            !empty($client['numero_documento']) &&
                $client['numero_documento'] !== '00000000' &&
                $client['numero_documento'] !== 'N/A')
            {{ ($client['tipo_documento'] ?? '1') == '6' ? 'RUC' : 'DNI' }}: {{ $client['numero_documento'] }}
        @else
            VARIOS
        @endif
    </div>

    {{-- Date and Time --}}
    <div class="client-details">
        FECHA: {{ $fecha_emision ?? '06/03/2024' }} HORA: {{ now()->format('H:i:s A') }}
    </div>
</div>

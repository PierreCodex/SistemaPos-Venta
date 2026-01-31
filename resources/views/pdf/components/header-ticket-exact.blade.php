{{-- PDF Ticket Header Component (Exact Design Match) --}}
{{-- Props: $company, $document, $tipo_documento_nombre --}}

@php
    $logoPath = public_path('img/logo-ticket.png'); // Ruta local en master
@endphp

<div class="header">
    {{-- Logo --}}
    @if (file_exists($logoPath))
        <div class="logo-section-ticket">
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents($logoPath)) }}" alt="Logo"
                class="logo-img-ticket">
        </div>
    @endif

    {{-- Company Name --}}
    <div class="company-name">{{ strtoupper($company['razon_social'] ?? '') }}</div>

    {{-- RUC --}}
    <div class="company-ruc">RUC: {{ $company['ruc'] ?? '' }}</div>

    {{-- Company Details --}}
    <div class="company-details">
        {{ $company['direccion'] ?? '' }}<br>
        {{ $company['distrito'] ?? '' }} {{ $company['provincia'] ?? '' }}<br>
        @if (!empty($company['telefono']))
            Tel: {{ $company['telefono'] }}<br>
        @endif
        @if (!empty($company['email']))
            Correo: {{ $company['email'] }}<br>
        @endif
        @if (!empty($company['website']))
            Web: {{ $company['website'] }}
        @endif
    </div>

    {{-- Document Title --}}
    <div class="document-title">{{ strtoupper($tipo_documento_nombre ?? 'COMPROBANTE') }}</div>

    {{-- Document Number --}}
    <div class="document-number">{{ $document['serie'] ?? '' }} -
        {{ str_pad($document['numero'] ?? ($document['correlativo'] ?? ''), 8, '0', STR_PAD_LEFT) }}</div>
</div>

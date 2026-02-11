{{-- PDF Ticket Header Component (Exact Design Match) --}}
{{-- Props: $company, $document, $tipo_documento_nombre --}}

@php
    // Obtener el logo desde los datos de la empresa
    $logo = data_get($company, 'logo');
    $logoPath = null;
    $logoBase64 = null;

    if ($logo && file_exists(storage_path('app/public/' . $logo))) {
        $logoPath = storage_path('app/public/' . $logo);
    } else {
        $logoPath = public_path('logo_factura.png');
    }

    if (file_exists($logoPath)) {
        try {
            $logoBase64 = base64_encode(file_get_contents($logoPath));
        } catch (\Exception $e) {
            $logoBase64 = null;
        }
    }

    // Normalizar datos
    $razonSocial = data_get($company, 'razon_social', 'EMPRESA DEMO SAC');
    $ruc = data_get($company, 'ruc', '20100100100');
    $direccion = data_get($company, 'direccion', 'CALLE LAS NORMAS 123');
    $distrito = data_get($company, 'distrito', 'CALLAO');
    $email = data_get($company, 'email', 'Administrador@facturas.net');
    $web = data_get($company, 'website') ?? data_get($company, 'web', 'www.facturas.net');
@endphp

<div class="header">
    {{-- Logo --}}
    @if ($logoBase64)
        <div class="logo-section-ticket">
            <img src="data:image/png;base64,{{ $logoBase64 }}" alt="Logo" class="logo-img-ticket">
        </div>
    @endif

    {{-- Company Name --}}
    <div class="company-name">{{ strtoupper($razonSocial) }}</div>

    {{-- RUC --}}
    <div class="company-ruc">RUC: {{ $ruc }}</div>

    {{-- Company Details --}}
    <div class="company-details">
        {{ $direccion }}<br>
        {{ $distrito }}<br>
        Correo: {{ $email }}<br>
        Web: {{ $web }}
    </div>

    {{-- Document Title --}}
    <div class="document-title">{{ strtoupper($tipo_documento_nombre ?? 'BOLETA DE VENTA ELECTRONICA') }}</div>

    {{-- Document Number --}}
    <div class="document-number">{{ $document->serie ?? 'B002' }} -
        {{ str_pad($document->numero ?? ($document->correlativo ?? '0'), 8, '0', STR_PAD_LEFT) }}</div>
</div>

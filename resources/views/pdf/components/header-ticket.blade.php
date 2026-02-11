{{-- PDF Ticket Header Component (Original Style) --}}
{{-- Props: $company, $document, $tipo_documento_nombre, $format --}}

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
    $razonSocial = data_get($company, 'razon_social', 'EMPRESA');
    $nombreComercial = data_get($company, 'nombre_comercial');
    $direccion = data_get($company, 'direccion', 'DIRECCIÓN');
    $ruc = data_get($company, 'ruc');
    $telefono = data_get($company, 'telefono');
    $email = data_get($company, 'email');
    $distrito = data_get($company, 'distrito');
    $provincia = data_get($company, 'provincia');
@endphp

<div class="header">
    {{-- Logo --}}
    @if ($logoBase64)
        <div class="logo-section-ticket">
            <img src="data:image/png;base64,{{ $logoBase64 }}" alt="Logo Empresa" class="logo-img-ticket">
        </div>
    @endif

    {{-- Company Info --}}
    <div class="company-name">{{ strtoupper($razonSocial) }}</div>

    <div class="company-details">
        @if ($nombreComercial && $nombreComercial != $razonSocial)
            {{ $nombreComercial }}<br>
        @endif

        {{ $direccion }}<br>

        @if ($distrito || $provincia)
            {{ $distrito }}{{ $provincia ? ', ' . $provincia : '' }}<br>
        @endif

        @if ($telefono)
            Tel: {{ $telefono }}<br>
        @endif

        @if ($email)
            {{ strtoupper($email) }}
        @endif
    </div>

    {{-- Document Info --}}
    <div class="document-info">
        <div>{{ strtoupper($tipo_documento_nombre) }}</div>
        <div>{{ $document->serie }}-{{ str_pad($document->correlativo, 6, '0', STR_PAD_LEFT) }}</div>
        <div>RUC: {{ $company->ruc }}</div>
    </div>
</div>

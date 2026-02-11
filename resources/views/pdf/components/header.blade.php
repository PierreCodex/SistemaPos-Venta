{{-- PDF Header Component --}}
{{-- Props: $company, $document, $tipo_documento_nombre, $fecha_emision, $format --}}

@php
    // Asegurar que $company sea un objeto para facilitar el acceso en la vista
    $companyObject = is_array($company) ? (object) $company : $company;

    // Obtener el logo desde los datos de la empresa
    $logo = data_get($company, 'logo');
    $logoPath = null;

    if ($logo && file_exists(storage_path('app/public/' . $logo))) {
        $logoPath = storage_path('app/public/' . $logo);
    } else {
        $logoPath = public_path('logo_factura.png');
    }

    // Verificamos si realmente podemos leer el archivo para evitar errores 500
    $logoBase64 = null;
    if (file_exists($logoPath)) {
        try {
            $logoBase64 = base64_encode(file_get_contents($logoPath));
        } catch (\Exception $e) {
            $logoBase64 = null;
        }
    }

    // Normalizar campos que pueden variar entre servicios
    $razonSocial = data_get($company, 'razon_social', 'Mi Empresa');
    $direccion = data_get($company, 'direccion');
    $ruc = data_get($company, 'ruc');
    $telefono = data_get($company, 'telefono');
    $email = data_get($company, 'email');
    $web = data_get($company, 'website') ?? data_get($company, 'web');
    $distrito = data_get($company, 'distrito');
    $provincia = data_get($company, 'provincia');
    $departamento = data_get($company, 'departamento');
    $nombreComercial = data_get($company, 'nombre_comercial');
@endphp

@if (in_array($format, ['a4', 'A4', 'a5', 'A5']))
    {{-- A4/A5 Header --}}
    <div class="header">
        <div class="logo-section">
            @if ($logoBase64)
                <img src="data:image/png;base64,{{ $logoBase64 }}" alt="Logo Empresa" class="logo-img">
            @endif
        </div>

        <div class="company-section">
            <div class="company-name">{{ strtoupper($razonSocial) }}</div>
            <div class="company-details">
                @if ($direccion)
                    {{ $direccion }}<br>
                @endif
                @if ($distrito || $provincia || $departamento)
                    {{ $distrito ? $distrito . ', ' : '' }}{{ $provincia ? $provincia . ', ' : '' }}{{ $departamento }}<br>
                @endif
                @if ($telefono)
                    TELÉFONO: {{ $telefono }}<br>
                @endif
                @if ($email)
                    EMAIL: {{ $email }}<br>
                @endif
                @if ($web)
                    WEB: {{ $web }}
                @endif
            </div>
        </div>

        <div class="document-section">
            <div class="factura-box">
                <p><b>RUC {{ $ruc ?? 'N/A' }}</b></p>
                <p><b>{{ strtoupper($tipo_documento_nombre ?? 'FACTURA ELECTRÓNICA') }}</b></p>
                <p><b>{{ $document->serie }}-{{ str_pad($document->correlativo, 6, '0', STR_PAD_LEFT) }}</b></p>
            </div>
        </div>
    </div>
@else
    {{-- Ticket Header (50mm, 80mm, ticket) --}}
    <div class="header">
        <div class="logo-section-ticket">
            @if ($logoBase64)
                <img src="data:image/jpg;base64,{{ $logoBase64 }}" alt="Logo Empresa" class="logo-img-ticket">
            @endif
        </div>
        <div class="company-name">{{ strtoupper($razonSocial) }}</div>
        <div class="company-details">
            @if ($nombreComercial)
                {{ $nombreComercial }}<br>
            @endif
            RUC: {{ $ruc ?? '' }}<br>
            {{ $direccion ?? '' }}<br>
            @if ($telefono)
                Tel: {{ $telefono }}<br>
            @endif
            @if ($email)
                Email: {{ $email }}
            @endif
        </div>

        <div class="document-info">
            <div>{{ strtoupper($tipo_documento_nombre) }}</div>
            <div>{{ $document->numero_completo }}</div>
            <div>{{ $fecha_emision }}</div>
        </div>
    </div>
@endif

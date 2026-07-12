@extends('layouts.master')

@section('title')
    APIs - Documentación Interactiva
@endsection

@section('css')
    <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5/swagger-ui.css">
    <style>
        .swagger-ui .topbar {
            display: none;
        }
        .swagger-ui .info {
            margin-top: 20px;
        }
        #swagger-ui {
            min-height: calc(100vh - 250px);
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0"><i class="ri-plug-line me-2"></i>APIs - Documentación Interactiva</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">APIs</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-book-open-line me-1"></i> Probar endpoints
                    </h5>
                    <p class="text-muted mb-0 fs-12">
                        Usa el botón <strong>Authorize</strong> para ingresar tu token Sanctum:
                        <code>Bearer TU_TOKEN</code>.
                    </p>
                </div>
                <div class="card-body p-0">
                    <div id="swagger-ui"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-bundle.js"></script>
    <script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-standalone-preset.js"></script>
    <script>
        window.onload = function() {
            window.ui = SwaggerUIBundle({
                url: "{{ route('apis.swagger') }}",
                dom_id: '#swagger-ui',
                deepLinking: true,
                presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIStandalonePreset
                ],
                plugins: [
                    SwaggerUIBundle.plugins.DownloadUrl
                ],
                layout: "StandaloneLayout",
                requestInterceptor: function(request) {
                    // Asegura que las peticiones desde Swagger vayan con el path public si aplica
                    return request;
                }
            });
        };
    </script>
@endsection

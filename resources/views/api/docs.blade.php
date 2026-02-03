@extends('layouts.master')

@section('title')
    API Documentation
@endsection

@section('css')
    <style>
        :root {
            --api-bg: #0f172a;
            --api-card: rgba(30, 41, 59, 0.7);
            --api-accent: #38bdf8;
            --api-text: #e2e8f0;
            --api-method-get: #10b981;
            --api-method-post: #3b82f6;
        }

        .docs-container {
            padding: 2rem;
            color: var(--api-text);
            background: radial-gradient(circle at top right, #1e293b, #0f172a);
            min-height: 100vh;
            border-radius: 15px;
        }

        .glass-card {
            background: var(--api-card);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
            transition: transform 0.3s ease;
        }

        .glass-card:hover {
            transform: translateY(-5px);
            border-color: var(--api-accent);
        }

        .method-badge {
            font-weight: bold;
            padding: 0.2rem 0.6rem;
            border-radius: 0.4rem;
            text-transform: uppercase;
            font-size: 0.8rem;
            margin-right: 1rem;
        }

        .bg-get {
            background: rgba(16, 185, 129, 0.2);
            color: var(--api-method-get);
            border: 1px solid var(--api-method-get);
        }

        .bg-post {
            background: rgba(59, 130, 246, 0.2);
            color: var(--api-method-post);
            border: 1px solid var(--api-method-post);
        }

        .endpoint-url {
            font-family: 'Fira Code', monospace;
            color: var(--api-accent);
            background: rgba(0, 0, 0, 0.3);
            padding: 0.4rem 0.8rem;
            border-radius: 0.5rem;
        }

        code {
            background: #000;
            color: #f8fafc;
            padding: 1rem;
            display: block;
            border-radius: 0.5rem;
            overflow-x: auto;
            border: 1px solid #334155;
        }

        .header-section {
            margin-bottom: 3rem;
            text-align: center;
        }

        .header-section h1 {
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(to right, #38bdf8, #818cf8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
@endsection

@section('content')
    <div class="docs-container">
        <div class="header-section">
            <h1>🚀 POS System API Docs</h1>
            <p class="text-muted">Documentación técnica para integración con sistemas externos (n8n, Apps Móviles, etc).</p>
        </div>

        <div class="glass-card">
            <h3>🔑 Autenticación</h3>
            <p>Todas las peticiones deben incluir un <strong>Bearer Token</strong> en la cabecera
                <code>Authorization</code>.
            </p>
            <p>Puedes generar y gestionar tus claves desde el <a href="{{ route('api-tokens.index') }}"
                    class="text-info font-weight-bold">Gestor de Tokens de API</a>.</p>
            <code>Authorization: Bearer TU_TOKEN_AQUÍ<br>Accept: application/json</code>
        </div>

        <!-- PRODUCTOS -->
        <h2 class="mb-4">📦 Productos</h2>

        <div class="glass-card">
            <div class="d-flex align-items-center mb-3">
                <span class="method-badge bg-get">GET</span>
                <span class="endpoint-url">/api/productos</span>
            </div>
            <p>Obtiene una lista paginada de todos los productos activos.</p>
            <h5>Respuesta (Ejemplo):</h5>
            <code>
                {
                "data": [
                {
                "id": 1,
                "codigo": "P001",
                "nombre": "Producto Ejemplo",
                "existencias": { "actual": 10, ... },
                "precios": { "venta": 25.0, ... }
                }
                ],
                "meta": { "total": 150, "current_page": 1 }
                }
            </code>
        </div>

        <div class="glass-card">
            <div class="d-flex align-items-center mb-3">
                <span class="method-badge bg-get">GET</span>
                <span class="endpoint-url">/api/productos?buscar={query}</span>
            </div>
            <p><strong>Búsqueda avanzada:</strong> Filtra productos por nombre, código o código de barras. Ideal para
                Chatbots de WhatsApp.</p>
            <p class="text-muted small">Ejemplo: <code>/api/productos?buscar=martillo</code></p>
        </div>

        <div class="glass-card">
            <div class="d-flex align-items-center mb-3">
                <span class="method-badge bg-get">GET</span>
                <span class="endpoint-url">/api/productos/{id}</span>
            </div>
            <p>Obtiene el detalle completo de un producto específico mediante su ID.</p>
        </div>

    </div>
@endsection

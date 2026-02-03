@extends('layouts.master')

@section('title')
    Gestor de Keys API
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Gestión de Tokens de API</h4>
                </div>
            </div>
        </div>

        <!-- Alerta de Nuevo Token (Solo se muestra una vez) -->
        @if (session('new_token'))
            <div class="row">
                <div class="col-12">
                    <div class="card border-success shadow">
                        <div class="card-header bg-success text-white">
                            <h5 class="card-title mb-0 text-white"><i class="ri-broadcast-line align-middle"></i> ¡Token
                                Generado Exitosamente!</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-danger font-weight-bold">⚠️ IMPORTANTE: Copia este token ahora. No podrás volver
                                a verlo por razones de seguridad.</p>
                            <div class="input-group">
                                <input type="text" id="plainToken" class="form-control form-control-lg bg-light"
                                    value="{{ session('new_token') }}" readonly>
                                <button class="btn btn-primary" onclick="copyToken()">Copiar Token</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="row">
            <!-- Formulario Crear Token -->
            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Generar Nueva Key</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('api-tokens.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Nombre de la Integración (ej: n8n, App Móvil)</label>
                                <input type="text" name="token_name" class="form-control"
                                    placeholder="Nombre descriptivo" required>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Crear Token</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Lista de Tokens -->
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Tus Tokens Activos</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table align-middle table-nowrap mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Último Uso</th>
                                        <th>Creado</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($tokens as $token)
                                        <tr>
                                            <td class="fw-medium">{{ $token->name }}</td>
                                            <td>{{ $token->last_used_at ? $token->last_used_at->diffForHumans() : 'Nunca usado' }}
                                            </td>
                                            <td>{{ $token->created_at->format('d/m/Y') }}</td>
                                            <td>
                                                <form action="{{ route('api-tokens.destroy', $token->id) }}" method="POST"
                                                    onsubmit="return confirm('¿Estás seguro de revocar este token?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="btn btn-sm btn-soft-danger">Eliminar</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No tienes tokens activos.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function copyToken() {
            var copyText = document.getElementById("plainToken");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(copyText.value);

            // Notificación básica
            alert("¡Token copiado al portapapeles!");
        }
    </script>
@endsection

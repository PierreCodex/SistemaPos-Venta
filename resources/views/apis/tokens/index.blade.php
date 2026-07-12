@extends('layouts.master')

@section('title')
    Tokens de API
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0"><i class="ri-key-2-line me-2"></i>Tokens de API</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('apis.index') }}">APIs</a></li>
                        <li class="breadcrumb-item active">Tokens</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    {{-- Token recién creado --}}
    @if (session('tokenNuevo'))
        <div class="row">
            <div class="col-12">
                <div class="alert alert-success border-2 alert-dismissible fade show" role="alert">
                    <div class="d-flex align-items-start">
                        <i class="ri-checkbox-circle-line fs-4 me-2"></i>
                        <div class="flex-grow-1">
                            <h6 class="alert-heading">Token "{{ session('nombreToken') }}" creado</h6>
                            <p class="mb-2">Guárdalo ahora. Por seguridad no se volverá a mostrar:</p>
                            <div class="input-group">
                                <input type="text" class="form-control font-monospace bg-white"
                                    value="{{ session('tokenNuevo') }}" id="tokenNuevo" readonly>
                                <button class="btn btn-outline-secondary" type="button" onclick="copiarToken()">
                                    <i class="ri-file-copy-line"></i> Copiar
                                </button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            </div>
        </div>
    @endif

    {{-- Mensajes --}}
    @if (session('success') && !session('tokenNuevo'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ri-checkbox-circle-line me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">
                        <i class="ri-shield-keyhole-line me-1"></i> Mis tokens Sanctum
                    </h5>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevoToken">
                        <i class="ri-add-line"></i> Nuevo token
                    </button>
                </div>
                <div class="card-body">
                    @if ($tokens->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="ri-key-line fs-2 d-block mb-2"></i>
                            <p class="mb-0">No tienes tokens de API creados.</p>
                            <small>Crea uno para usar las APIs desde aplicaciones externas.</small>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Habilidades</th>
                                        <th>Creado</th>
                                        <th>Último uso</th>
                                        <th class="text-center" style="width: 120px;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tokens as $token)
                                        <tr>
                                            <td>
                                                <strong>{{ $token->name }}</strong>
                                                <br>
                                                <small class="text-muted">ID: {{ $token->id }}</small>
                                            </td>
                                            <td>
                                                @if (empty($token->abilities) || $token->abilities === ['*'])
                                                    <span class="badge bg-info">Sin restricción de abilities</span>
                                                @else
                                                    @foreach ($token->abilities as $ability)
                                                        <span class="badge bg-secondary me-1">{{ $ability }}</span>
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td>{{ $token->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                @if ($token->last_used_at)
                                                    {{ $token->last_used_at->format('d/m/Y H:i') }}
                                                @else
                                                    <span class="text-muted">Nunca</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <form action="{{ route('apis.tokens.destroy', $token->id) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('¿Revocar este token? Las integraciones que lo usen dejarán de funcionar.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-soft-danger">
                                                        <i class="ri-delete-bin-line"></i> Revocar
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Nuevo Token --}}
    <div class="modal fade" id="modalNuevoToken" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white"><i class="ri-key-2-line me-1"></i> Crear token de API</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('apis.tokens.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nombre del token <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name"
                                placeholder="Ej: VigilanteIA, App móvil, Integración Shopify" required>
                            <small class="text-muted">Usa un nombre descriptivo para identificar dónde se usa el
                                token.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Habilidades (opcional)</label>
                            <p class="text-muted fs-12 mb-2">
                                Si no seleccionas ninguna, el token heredará todos tus permisos de API. Los middleware
                                de permisos seguirán controlando el acceso real.
                            </p>

                            @if ($habilidadesDisponibles->isEmpty())
                                <div class="alert alert-warning mb-0">
                                    <i class="ri-alert-line me-1"></i> No tienes permisos de API asignados. Solicita al
                                    administrador que te asigne los permisos necesarios en <a
                                        href="{{ route('roles.index') }}">Roles y Permisos</a>.
                                </div>
                            @else
                                <div class="row">
                                    @foreach ($habilidadesDisponibles as $habilidad)
                                        <div class="col-md-6">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox"
                                                    name="habilidades[]" value="{{ $habilidad }}"
                                                    id="hab_{{ $loop->index }}">
                                                <label class="form-check-label"
                                                    for="hab_{{ $loop->index }}">
                                                    <code>{{ $habilidad }}</code>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line me-1"></i> Crear token
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function copiarToken() {
            const input = document.getElementById('tokenNuevo');
            input.select();
            input.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(input.value).then(() => {
                mostrarToast('Token copiado al portapapeles', 'success');
            }).catch(() => {
                mostrarToast('No se pudo copiar automáticamente', 'warning');
            });
        }

        function mostrarToast(mensaje, tipo = 'success') {
            if (typeof Toastify !== 'undefined') {
                const colors = {
                    success: "linear-gradient(to right, #0ab39c, #0ab39c)",
                    warning: "linear-gradient(to right, #f7b84b, #f7b84b)"
                };
                Toastify({
                    text: mensaje,
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    style: {
                        background: colors[tipo] || colors.success
                    }
                }).showToast();
            }
        }
    </script>
@endsection

@extends('layouts.master')

@section('title')
    Categorías Globales
@endsection

@section('css')
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet"
        type="text/css" />
    <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Categorías Globales</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Catálogo</a></li>
                        <li class="breadcrumb-item active">Categorías Globales</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    {{-- Los mensajes de éxito/error ahora se muestran con Toastify --}}

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">Lista de Categorías Globales</h5>
                    <div class="d-flex flex-shrink-0 gap-2">
                        <button type="button" id="btnExportarPDF" class="btn btn-soft-danger waves-effect waves-light">
                            <i class="las la-file-pdf fs-3"></i><span>PDF</span>
                        </button>
                        <button type="button" id="btnExportarExcel" class="btn btn-soft-success waves-effect waves-light">
                            <i class="las la-file-excel fs-3"></i><span>Excel</span>
                        </button>
                        @can('categorias.crear')
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#modalCategoriaGlobal" onclick="limpiarFormulario()">
                                <i class="ri-add-line align-bottom me-1"></i> Nueva Categoría Global
                            </button>
                        @endcan
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle mb-0" id="tablaCategoriasGlobales">
                            <thead class="table-light">
                                <tr>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th style="width: 100px;">Estado</th>
                                    <th class="no-exportar" style="width: 150px;">Acciones</th>
                                    <th class="no-exportar" style="width: 80px;">On/Off</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categoriasGlobales as $categoria)
                                    <tr data-id="{{ $categoria->id }}">
                                        <td><strong>{{ $categoria->nombre }}</strong></td>
                                        <td>{{ Str::limit($categoria->descripcion, 50) ?? '-' }}</td>
                                        <td id="estado-badge-{{ $categoria->id }}">
                                            @if ($categoria->estado)
                                                <span class="badge bg-success">Activo</span>
                                            @else
                                                <span class="badge bg-danger">Inactivo</span>
                                            @endif
                                        </td>
                                        <td class="no-exportar">
                                            <div class="d-flex gap-1">
                                                @can('categorias.editar')
                                                    <button type="button" class="btn btn-sm btn-warning"
                                                        onclick="editarCategoria({{ $categoria->id }})" title="Editar">
                                                        <i class="ri-pencil-line"></i>
                                                    </button>
                                                @endcan
                                                @can('categorias.eliminar')
                                                    <button type="button" class="btn btn-sm btn-danger"
                                                        onclick="eliminarCategoria({{ $categoria->id }}, '{{ $categoria->nombre }}')"
                                                        title="Eliminar">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                @endcan
                                            </div>
                                        </td>
                                        <td class="no-exportar">
                                            @can('categorias.editar')
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="toggle-estado-{{ $categoria->id }}"
                                                        {{ $categoria->estado ? 'checked' : '' }}
                                                        onchange="toggleEstado({{ $categoria->id }})">
                                                </div>
                                            @else
                                                <span
                                                    class="badge {{ $categoria->estado ? 'bg-success' : 'bg-danger' }}">{{ $categoria->estado ? 'On' : 'Off' }}</span>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="ri-folder-line fs-1 d-block mb-2"></i>
                                            No hay categorías globales registradas
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Crear/Editar --}}
    <div class="modal fade" id="modalCategoriaGlobal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Nueva Categoría Global</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formCategoriaGlobal" method="POST" action="{{ route('categorias-globales.store') }}">
                    @csrf
                    <input type="hidden" id="formMethod" name="_method" value="POST">

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nombre') is-invalid @enderror"
                                id="nombre" name="nombre" value="{{ old('nombre') }}"
                                placeholder="Ej: Alimentos, Electrónicos" required>
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion"
                                rows="3" placeholder="Descripción opcional...">{{ old('descripcion') }}</textarea>
                        </div>

                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="estado" name="estado"
                                value="1" {{ old('estado', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="estado">Estado Activo</label>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" id="btnGuardar" class="btn btn-primary">
                            <span id="btnGuardarTexto">
                                <i class="ri-save-line me-1"></i> Guardar
                            </span>
                            <span id="btnGuardarSpinner" class="d-none">
                                <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                Guardando...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Eliminar --}}
    <div class="modal fade" id="modalEliminar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <i class="ri-error-warning-line text-danger" style="font-size: 4rem;"></i>
                    <p class="mt-3">¿Eliminar la categoría global:</p>
                    <p class="fw-bold fs-5" id="nombreEliminar"></p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form id="formEliminar" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="ri-delete-bin-line me-1"></i> Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

    {{-- Configuración pasada a JavaScript --}}
    <script>
        window.CATEGORIAS_GLOBALES_CONFIG = {
            categorias: @json($categoriasGlobales),
            routes: {
                store: "{{ route('categorias-globales.store') }}"
            },
            csrfToken: "{{ csrf_token() }}"
        };
    </script>

    {{-- Script del módulo --}}
    <script src="{{ URL::asset('js/modules/categorias-globales/index.js') }}"></script>
@endsection

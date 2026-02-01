@extends('layouts.master')

@section('title')
    Marcas
@endsection

@section('css')
    {{-- DataTables CSS --}}
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet"
        type="text/css" />
    <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Marcas</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Catálogo</a></li>
                        <li class="breadcrumb-item active">Marcas</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex align-items-center flex-wrap gap-2">
                    <h5 class="card-title mb-0 flex-grow-1 text-uppercase fw-bold">Listado de Marcas</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" id="btnExportarPDF"
                            class="btn btn-soft-danger waves-effect waves-light shadow-none d-flex align-items-center">
                            <i class="ri-file-pdf-line fs-18"></i> <span
                                class="d-none d-sm-inline ms-1 text-uppercase">PDF</span>
                        </button>
                        <button type="button" id="btnExportarExcel"
                            class="btn btn-soft-success waves-effect waves-light shadow-none d-flex align-items-center">
                            <i class="ri-file-excel-line fs-18"></i> <span
                                class="d-none d-sm-inline ms-1 text-uppercase">Excel</span>
                        </button>
                        @can('marcas.crear')
                            <button type="button" class="btn btn-primary d-flex align-items-center shadow-sm"
                                data-bs-toggle="modal" data-bs-target="#modalMarca" onclick="limpiarFormulario()">
                                <i class="ri-add-line fs-18 me-1"></i> <span class="d-none d-md-inline text-uppercase">Nueva
                                    Marca</span>
                                <span class="d-inline d-md-none text-uppercase">Nuevo</span>
                            </button>
                        @endcan
                    </div>
                </div>


                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tablaMarcas" class="table nowrap align-middle mb-0" style="width:100%">
                            <thead class="table-light text-muted">
                                <tr class="text-uppercase fs-12">
                                    <th>Codigo</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th style="width: 100px;">Estado</th>
                                    <th class="no-exportar" style="width: 150px;">Acciones</th>
                                    <th class="no-exportar" style="width: 50px;">On/Off</th>
                                </tr>
                            </thead>
                            <tbody>

                                @forelse($marcas as $marca)
                                    <tr data-id="{{ $marca->id }}">
                                        <td><strong>{{ $marca->codigo }}</strong></td>
                                        <td>
                                            <h5><span class="badge bg-primary">{{ $marca->nombre ?? '-' }}</span></h5>
                                        </td>
                                        <td>
                                            @if ($marca->descripcion)
                                                <i
                                                    class="bx bx-comment-dots me-1"></i>{{ Str::limit($marca->descripcion, 40) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td id="estado-badge-{{ $marca->id }}">
                                            @if ($marca->estado)
                                                <span class="badge bg-success">Activo</span>
                                            @else
                                                <span class="badge bg-danger">Inactivo</span>
                                            @endif
                                        </td>
                                        <td class="no-exportar">
                                            <div class="d-flex gap-1">
                                                <button type="button" class="btn btn-sm btn-info"
                                                    onclick="verMarca({{ $marca->id }})" title="Ver">
                                                    <i class="ri-eye-line"></i>
                                                </button>
                                                @can('marcas.editar')
                                                    <button type="button" class="btn btn-sm btn-warning"
                                                        onclick="editarMarca({{ $marca->id }})" title="Editar">
                                                        <i class="ri-pencil-line"></i>
                                                    </button>
                                                @endcan
                                                @can('marcas.eliminar')
                                                    <button type="button" class="btn btn-sm btn-danger"
                                                        onclick="eliminarMarca({{ $marca->id }}, '{{ $marca->nombre }}')"
                                                        title="Eliminar">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                @endcan
                                            </div>
                                        </td>
                                        <td class="no-exportar">
                                            @can('marcas.editar')
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="toggle-estado-{{ $marca->id }}"
                                                        {{ $marca->estado ? 'checked' : '' }}
                                                        onchange="toggleEstado({{ $marca->id }})">
                                                </div>
                                            @else
                                                <span
                                                    class="badge {{ $marca->estado ? 'bg-success' : 'bg-danger' }}">{{ $marca->estado ? 'On' : 'Off' }}</span>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>


    {{-- Modal Crear/Editar --}}
    <div class="modal fade" id="modalMarca" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Nueva Marca</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formMarca" method="POST" action="{{ route('marcas.store') }}">
                    @csrf
                    <input type="hidden" id="formMethod" name="_method" value="POST">

                    <div class="modal-body">

                        <div class="mb-3">
                            <label for="codigo" class="form-label">Código <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control @error('codigo') is-invalid @enderror"
                                    id="codigo" name="codigo" value="{{ old('codigo') }}"
                                    placeholder="Ej: MRC-A1B2C3" required>
                                <button type="button" class="btn btn-outline-secondary" onclick="generarCodigo()"
                                    title="Generar código aleatorio">
                                    <i class=" bx bxs-magic-wand"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nombre') is-invalid @enderror"
                                id="nombre" name="nombre" value="{{ old('nombre') }}"
                                placeholder="Ej: Lácteos, Laptops" required>
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion"
                                rows="3" placeholder="Descripción opcional...">{{ old('descripcion') }}</textarea>
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

    {{-- Modal Ver --}}
    <div class="modal fade" id="modalVer" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalle de Marca</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table id="model-datatables" class="table nowrap align-middle" style="width:100%">
                        <tr>
                            <th>ID:</th>
                            <td id="verID"></td>
                        </tr>
                        <tr>
                            <th>Codigo:</th>
                            <td id="verCodigo" class="fw-bold"></td>
                        </tr>
                        <tr>
                            <th>Nombre:</th>
                            <td id="verNombre" class="fw-bold"></td>
                        </tr>
                        <tr>
                            <th>Descripción:</th>
                            <td id="verDescripcion"></td>
                        </tr>
                        <tr>
                            <th>Estado:</th>
                            <td id="verEstado"></td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
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
                    <p class="mt-3">¿Eliminar la marca:</p>
                    <p class="fw-bold fs-5" id="nombreEliminar"></p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form id="formEliminar" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    {{-- jQuery (requerido por DataTables) --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    {{-- DataTables JS --}}
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
        window.MARCAS_CONFIG = {
            marcas: @json($marcas),
            routes: {
                store: "{{ route('marcas.store') }}"
            },
            csrfToken: "{{ csrf_token() }}"
        };
    </script>

    {{-- Script del módulo --}}
    <script src="{{ URL::asset('js/modules/marcas/index.js') }}"></script>
@endsection

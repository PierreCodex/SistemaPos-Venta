@extends('layouts.master')

@section('title')
    Proveedores
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
                <h4 class="mb-sm-0">Proveedores</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Directorio</a></li>
                        <li class="breadcrumb-item active">Proveedores</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">Listado de Proveedores</h5>
                    <div class="d-flex flex-shrink-0 gap-2">
                        <button type="button" id="btnExportarPDF" class="btn btn-soft-danger waves-effect waves-light">
                            <i class="las la-file-pdf fs-3"></i><span>PDF</span>
                        </button>
                        <button type="button" id="btnExportarExcel" class="btn btn-soft-success waves-effect waves-light">
                            <i class="las la-file-excel fs-3"></i><span>Excel</span>
                        </button>
                        @can('proveedores.crear')
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#modalProveedor" onclick="limpiarFormulario()">
                                <i class="ri-add-line me-1"></i> Nuevo Proveedor
                            </button>
                        @endcan
                    </div>
                </div>

                <div class="card-body">
                    <table id="tablaProveedores" class="table nowrap align-middle" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th>Documento</th>
                                <th>Nombre</th>
                                <th>Teléfono</th>
                                <th>Email</th>
                                <th style="width: 100px;">Estado</th>
                                <th class="no-exportar" style="width: 150px;">Acciones</th>
                                <th class="no-exportar" style="width: 50px;">On/Off</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($proveedores as $proveedor)
                                <tr data-id="{{ $proveedor->id }}">
                                    <td>
                                        <span class="badge bg-secondary">{{ $proveedor->tipo_documento }}</span>
                                        <strong>{{ $proveedor->documento }}</strong>
                                    </td>
                                    <td><strong>{{ $proveedor->nombre }}</strong></td>
                                    <td>{{ $proveedor->telefono ?? '-' }}</td>
                                    <td>{{ $proveedor->email ?? '-' }}</td>
                                    <td id="estado-badge-{{ $proveedor->id }}">
                                        @if ($proveedor->estado)
                                            <span class="badge bg-success">Activo</span>
                                        @else
                                            <span class="badge bg-danger">Inactivo</span>
                                        @endif
                                    </td>
                                    <td class="no-exportar">
                                        <div class="d-flex gap-1">
                                            <button type="button" class="btn btn-sm btn-info"
                                                onclick="verProveedor({{ $proveedor->id }})" title="Ver">
                                                <i class="ri-eye-line"></i>
                                            </button>
                                            @can('proveedores.editar')
                                                <button type="button" class="btn btn-sm btn-warning"
                                                    onclick="editarProveedor({{ $proveedor->id }})" title="Editar">
                                                    <i class="ri-pencil-line"></i>
                                                </button>
                                            @endcan
                                            @can('proveedores.eliminar')
                                                <button type="button" class="btn btn-sm btn-danger"
                                                    onclick="eliminarProveedor({{ $proveedor->id }}, '{{ $proveedor->nombre }}')"
                                                    title="Eliminar">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            @endcan
                                        </div>
                                    </td>
                                    <td class="no-exportar">
                                        @can('proveedores.editar')
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox"
                                                    id="toggle-estado-{{ $proveedor->id }}"
                                                    {{ $proveedor->estado ? 'checked' : '' }}
                                                    onchange="toggleEstado({{ $proveedor->id }})">
                                            </div>
                                        @else
                                            <span
                                                class="badge {{ $proveedor->estado ? 'bg-success' : 'bg-danger' }}">{{ $proveedor->estado ? 'On' : 'Off' }}</span>
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

    {{-- Modal Crear/Editar --}}
    <div class="modal fade" id="modalProveedor" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Nuevo Proveedor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formProveedor" method="POST" action="{{ route('proveedores.store') }}">
                    @csrf
                    <input type="hidden" id="formMethod" name="_method" value="POST">

                    <div class="modal-body">
                        {{-- Documento --}}
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="tipo_documento" class="form-label">Tipo Doc. <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="tipo_documento" name="tipo_documento" required>
                                    <option value="RUC">RUC</option>
                                    <option value="DNI">DNI</option>
                                    <option value="CE">CE</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label for="documento" class="form-label">Número <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="documento" name="documento"
                                    placeholder="Ej: 20123456789" required maxlength="20">
                            </div>
                        </div>

                        {{-- Nombre --}}
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Razón Social / Nombre <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombre" name="nombre"
                                placeholder="Ej: Distribuidora ABC S.A.C." required maxlength="200">
                        </div>

                        {{-- Contacto --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="text" class="form-control" id="telefono" name="telefono"
                                    placeholder="Ej: 987654321" maxlength="20">
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="Ej: proveedor@email.com" maxlength="100">
                            </div>
                        </div>

                        {{-- Dirección --}}
                        <div class="mb-3">
                            <label for="direccion" class="form-label">Dirección</label>
                            <textarea class="form-control" id="direccion" name="direccion" rows="2"
                                placeholder="Dirección del proveedor..." maxlength="500"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" id="btnGuardar" class="btn btn-primary">
                            <span id="btnGuardarTexto"><i class="ri-save-line me-1"></i> Guardar</span>
                            <span id="btnGuardarSpinner" class="d-none">
                                <span class="spinner-border spinner-border-sm me-1"></span> Guardando...
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
                <div class="modal-header border-bottom pb-4">
                    <h5 class="modal-title"><i class="ri-user-search-line me-2"></i>Detalle del Proveedor</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-borderless">
                        <tr>
                            <th class="text-muted" style="width: 35%;">ID:</th>
                            <td id="verID" class="fw-bold"></td>
                        </tr>
                        <tr>
                            <th class="text-muted">Documento:</th>
                            <td id="verDocumento"></td>
                        </tr>
                        <tr>
                            <th class="text-muted">Nombre:</th>
                            <td id="verNombre" class="fw-bold"></td>
                        </tr>
                        <tr>
                            <th class="text-muted">Teléfono:</th>
                            <td id="verTelefono"></td>
                        </tr>
                        <tr>
                            <th class="text-muted">Email:</th>
                            <td id="verEmail"></td>
                        </tr>
                        <tr>
                            <th class="text-muted">Dirección:</th>
                            <td id="verDireccion"></td>
                        </tr>
                        <tr>
                            <th class="text-muted">Estado:</th>
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
                    <p class="mt-3">¿Eliminar el proveedor:</p>
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

    <script>
        window.PROVEEDORES_CONFIG = {
            proveedores: @json($proveedores),
            routes: {
                store: "{{ route('proveedores.store') }}"
            },
            csrfToken: "{{ csrf_token() }}"
        };
    </script>

    <script src="{{ URL::asset('js/modules/proveedores/index.js') }}"></script>
@endsection

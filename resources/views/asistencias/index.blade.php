@extends('layouts.master')
@section('title')
    Registro de Asistencias
@endsection
@section('css')
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet"
        type="text/css" />
    <link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Control de Asistencias</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Recursos Humanos</a></li>
                        <li class="breadcrumb-item active">Asistencias</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('asistencias.index') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-xxl-3 col-sm-4">
                                <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                                <input type="date" class="form-control" name="fecha_inicio"
                                    value="{{ $filtros['fecha_inicio'] }}">
                            </div>
                            <div class="col-xxl-3 col-sm-4">
                                <label for="fecha_fin" class="form-label">Fecha Fin</label>
                                <input type="date" class="form-control" name="fecha_fin"
                                    value="{{ $filtros['fecha_fin'] }}">
                            </div>
                            @if ($esAdmin)
                                <div class="col-xxl-3 col-sm-4">
                                    <label for="user_id" class="form-label">Empleado</label>
                                    <select class="form-control select2" name="user_id">
                                        <option value="">Todos los empleados</option>
                                        @foreach ($usuarios as $usuario)
                                            <option value="{{ $usuario->id }}"
                                                {{ $filtros['user_id'] == $usuario->id ? 'selected' : '' }}>
                                                {{ $usuario->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <div class="col-xxl-3 col-sm-12 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100"><i
                                        class="ri-equalizer-fill me-1 align-bottom"></i> Filtrar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">Registros de Asistencias</h5>
                    @if ($puedeRegistrar)
                        <div class="flex-shrink-0">
                            <button class="btn btn-success" onclick="abrirModalAsistencia()">
                                <i class="ri-fingerprint-line align-bottom me-1"></i> Registrar Asistencia
                            </button>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    <table id="tablaAsistencias"
                        class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
                        <thead>
                            <tr>
                                <th>Empleado</th>
                                <th>Fecha</th>
                                <th>Entrada</th>
                                <th>Salida</th>
                                <th>Tardanza</th>
                                <th>Extras</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($asistencias as $asistencia)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <img src="{{ $asistencia->usuario->avatar ? URL::asset('images/' . $asistencia->usuario->avatar) : URL::asset('build/images/users/avatar-1.jpg') }}"
                                                    alt="" class="avatar-xs rounded-circle" />
                                            </div>
                                            <div class="flex-grow-1 ms-2">
                                                {{ $asistencia->usuario->name }}
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($asistencia->fecha)->format('d/m/Y') }}</td>
                                    <td>{{ $asistencia->hora_entrada ? \Carbon\Carbon::parse($asistencia->hora_entrada)->format('h:i A') : '-' }}
                                    </td>
                                    <td>{{ $asistencia->hora_salida ? \Carbon\Carbon::parse($asistencia->hora_salida)->format('h:i A') : '-' }}
                                    </td>
                                    <td>
                                        @if ($asistencia->minutos_tardanza > 0)
                                            <span class="text-danger fw-medium">{{ $asistencia->minutos_tardanza }}
                                                min</span>
                                        @else
                                            <span class="text-success">0 min</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($asistencia->minutos_extra > 0)
                                            <span class="text-primary fw-medium">{{ $asistencia->minutos_extra }}
                                                min</span>
                                        @else
                                            <span>0 min</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $estadoClass = match ($asistencia->estado) {
                                                'PRESENTE' => 'bg-success',
                                                'TARDANZA' => 'bg-warning',
                                                'FALTA' => 'bg-danger',
                                                default => 'bg-secondary',
                                            };
                                            $estadoLabel = match ($asistencia->estado) {
                                                'PRESENTE' => 'Asistio',
                                                'TARDANZA' => 'Tardanza',
                                                'FALTA' => 'Falto',
                                                default => $asistencia->estado,
                                            };
                                        @endphp
                                        <span class="badge {{ $estadoClass }}">{{ $estadoLabel }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('asistencias.show', $asistencia->user_id) }}"
                                                class="btn btn-soft-info btn-sm" title="Ver Calendario">
                                                <i class="ri-calendar-line"></i>
                                            </a>
                                            @if ($esAdmin)
                                                <button class="btn btn-soft-danger btn-sm"
                                                    onclick="eliminarAsistencia({{ $asistencia->id }})">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Registro Asistencia -->
    <div class="modal fade" id="modalAsistencia" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-soft-info p-3">
                    <h5 class="modal-title">
                        <i class="ri-user-add-line align-middle me-1"></i> Registrar Asistencia
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formAsistencia">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-4">
                            <!-- Empleado -->
                            <div class="col-lg-6">
                                <label for="reg_user_id" class="form-label fw-bold">Empleado <span
                                        class="text-danger">*</span></label>
                                @if ($esAdmin)
                                    <select name="user_id" id="reg_user_id" class="form-control select2-modal" required>
                                        <option value="">Seleccione una opcion...</option>
                                        @foreach ($usuarios as $usuario)
                                            <option value="{{ $usuario->id }}"
                                                {{ auth()->id() == $usuario->id ? 'selected' : '' }}>
                                                {{ $usuario->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                                    <input type="text" class="form-control" value="{{ auth()->user()->name }}"
                                        readonly disabled>
                                @endif
                            </div>

                            <!-- Estado -->
                            <div class="col-lg-6">
                                <label for="reg_estado" class="form-label fw-bold">Estado de Asistencia <span
                                        class="text-danger">*</span></label>
                                <select name="estado" id="reg_estado" class="form-control select2-modal" required>
                                    <option value="">Seleccione una opcion...</option>
                                    <option value="PRESENTE">Asistio</option>
                                    <option value="FALTA">Falto</option>
                                </select>
                            </div>

                            <!-- Fecha -->
                            <div class="col-lg-6">
                                <label for="reg_fecha" class="form-label fw-bold">Fecha <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                                    <input type="date" name="fecha" id="reg_fecha" class="form-control"
                                        value="{{ date('Y-m-d') }}" required {{ !$esAdmin ? 'readonly' : '' }}>
                                </div>
                            </div>

                            <!-- Hora Entrada -->
                            <div class="col-lg-6">
                                <label for="reg_hora_entrada" class="form-label fw-bold">Hora de Entrada <span
                                        class="text-danger">*</span>
                                    @if (!$esAdmin)
                                        <span class="badge badge-soft-primary ms-2" id="time-badge">Tiempo Real</span>
                                    @endif
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-time-line"></i></span>
                                    <input type="time" name="hora_entrada" id="reg_hora_entrada" class="form-control"
                                        value="{{ date('H:i') }}" required {{ !$esAdmin ? 'readonly' : '' }}>
                                </div>
                            </div>

                            <!-- Observaciones -->
                            <div class="col-lg-12">
                                <label for="reg_observaciones" class="form-label fw-bold">Observaciones (Opcional)</label>
                                <textarea name="observaciones" id="reg_observaciones" class="form-control" rows="3"
                                    placeholder="Observaciones adicionales"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light p-3 d-flex justify-content-center">
                        <button type="submit" class="btn btn-primary d-flex align-items-center px-4 py-2"
                            style="background-color: #a855f7; border: none;">
                            <i class="ri-save-line me-2"></i> Guardar Asistencia
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#tablaAsistencias').DataTable({
                responsive: true,
                order: [
                    [1, 'desc']
                ]
            });

            $('.select2-modal').select2({
                dropdownParent: $('#modalAsistencia')
            });

            // Update clock
            setInterval(() => {
                let now = new Date();
                $('#reloj').text(now.toLocaleTimeString());

                @if (!$esAdmin)
                    // Si no es admin, mantener la hora del modal sincronizada con el servidor/cliente
                    let hours = String(now.getHours()).padStart(2, '0');
                    let minutes = String(now.getMinutes()).padStart(2, '0');
                    $('#reg_hora_entrada').val(hours + ':' + minutes);
                @endif
            }, 1000);

            $('#formAsistencia').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: `{{ route('asistencias.store') }}`,
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Éxito', response.message, 'success').then(() => location
                                .reload());
                        }
                    },
                    error: function(xhr) {
                        let msg = xhr.responseJSON?.message || 'Error en el servidor';
                        Swal.fire('Error', msg, 'error');
                    }
                });
            });
        });

        function abrirModalAsistencia() {
            $('#modalAsistencia').modal('show');
        }

        function eliminarAsistencia(id) {
            Swal.fire({
                title: '¿Eliminar registro?',
                text: "Esta acción no se puede deshacer",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ url('asistencias') }}/${id}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            location.reload();
                        }
                    });
                }
            });
        }
    </script>
@endsection

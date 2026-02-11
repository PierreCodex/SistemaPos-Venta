@extends('layouts.master')
@section('title')
    Gestión de Horarios
@endsection
@section('css')
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet"
        type="text/css" />
    <link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .day-checkbox {
            display: none;
        }

        .day-label {
            cursor: pointer;
            padding: 8px 12px;
            border: 1px solid #e9ebec;
            border-radius: 5px;
            margin-right: 5px;
            margin-bottom: 5px;
            transition: all 0.2s;
            display: inline-block;
        }

        .day-checkbox:checked+.day-label {
            background-color: var(--vz-primary);
            color: white;
            border-color: var(--vz-primary);
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Gestión de Horarios</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Recursos Humanos</a></li>
                        <li class="breadcrumb-item active">Horarios</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">Lista de Horarios y Turnos</h5>
                    <div class="flex-shrink-0">
                        @can('horarios.crear')
                            <button class="btn btn-primary add-btn" data-bs-toggle="modal" data-bs-target="#modalHorario">
                                <i class="ri-add-line align-bottom me-1"></i> Nuevo Horario
                            </button>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <table id="tablaHorarios" class="table table-bordered dt-responsive nowrap table-striped align-middle"
                        style="width:100%">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Horario</th>
                                <th>Días</th>
                                <th>Empleados</th>
                                <th>Sueldo Base</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($horarios as $horario)
                                <tr>
                                    <td><span class="badge badge-soft-primary fs-12">{{ $horario->codigo }}</span></td>
                                    <td>{{ $horario->nombre }}</td>
                                    <td>
                                        <div class="text-primary fw-medium">
                                            {{ \Carbon\Carbon::parse($horario->hora_inicio)->format('h:i A') }}</div>
                                        <div class="text-muted small">hasta
                                            {{ \Carbon\Carbon::parse($horario->hora_fin)->format('h:i A') }}</div>
                                    </td>
                                    <td>
                                        @foreach ($horario->dias_laborales as $dia)
                                            <span class="badge bg-light text-dark">{{ substr($dia, 0, 2) }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill bg-info">{{ $horario->usuarios_count }}
                                            asignados</span>
                                    </td>
                                    <td>${{ number_format($horario->sueldo_base, 2) }}</td>
                                    <td>
                                        @if ($horario->activo)
                                            <span class="badge bg-success">Activo</span>
                                        @else
                                            <span class="badge bg-danger">Inactivo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown d-inline-block">
                                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-more-fill align-middle"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                @can('horarios.asignar')
                                                    <li><a class="dropdown-item edit-item-btn" href="javascript:void(0);"
                                                            onclick="abrirModalAsignar({{ $horario->id }}, '{{ $horario->nombre }}')"><i
                                                                class="ri-user-add-line align-bottom me-2 text-muted"></i>
                                                            Asignar Personal</a></li>
                                                @endcan
                                                @can('horarios.editar')
                                                    <li><a class="dropdown-item edit-item-btn" href="javascript:void(0);"
                                                            onclick="editarHorario({{ json_encode($horario) }})"><i
                                                                class="ri-pencil-fill align-bottom me-2 text-muted"></i>
                                                            Editar</a></li>
                                                    <li><a class="dropdown-item" href="javascript:void(0);"
                                                            onclick="toggleEstado({{ $horario->id }})"><i
                                                                class="ri-refresh-line align-bottom me-2 text-muted"></i>
                                                            {{ $horario->activo ? 'Desactivar' : 'Activar' }}</a></li>
                                                @endcan
                                                @can('horarios.eliminar')
                                                    <li>
                                                        <a class="dropdown-item remove-item-btn" href="javascript:void(0);"
                                                            onclick="eliminarHorario({{ $horario->id }})">
                                                            <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i>
                                                            Eliminar
                                                        </a>
                                                    </li>
                                                @endcan
                                            </ul>
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

    <!-- Modal Horario (Crear/Editar) -->
    <div class="modal fade" id="modalHorario" tabindex="-1" aria-labelledby="modalHorarioLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalHorarioLabel">Nuevo Horario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formHorario">
                    @csrf
                    <input type="hidden" name="id" id="horario_id">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-lg-12">
                                <label for="nombre" class="form-label">Nombre del Turno <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="nombre" id="nombre" class="form-control"
                                    placeholder="Ej: Turno Mañana" required>
                            </div>
                            <div class="col-lg-6">
                                <label for="hora_inicio" class="form-label">Hora de Inicio <span
                                        class="text-danger">*</span></label>
                                <input type="time" name="hora_inicio" id="hora_inicio" class="form-control" required>
                            </div>
                            <div class="col-lg-6">
                                <label for="hora_fin" class="form-label">Hora de Fin <span
                                        class="text-danger">*</span></label>
                                <input type="time" name="hora_fin" id="hora_fin" class="form-control" required>
                            </div>
                            <div class="col-lg-6">
                                <label for="hora_inicio_refrigerio" class="form-label">Inicio Refrigerio (Op)</label>
                                <input type="time" name="hora_inicio_refrigerio" id="hora_inicio_refrigerio"
                                    class="form-control">
                            </div>
                            <div class="col-lg-6">
                                <label for="hora_fin_refrigerio" class="form-label">Fin Refrigerio (Op)</label>
                                <input type="time" name="hora_fin_refrigerio" id="hora_fin_refrigerio"
                                    class="form-control">
                            </div>
                            <div class="col-lg-12">
                                <label class="form-label">Días Laborales <span class="text-danger">*</span></label>
                                <div class="d-flex flex-wrap">
                                    @foreach (['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'] as $dia)
                                        <input type="checkbox" name="dias_laborales[]" value="{{ $dia }}"
                                            id="dia_{{ $dia }}" class="day-checkbox">
                                        <label for="dia_{{ $dia }}"
                                            class="day-label">{{ $dia }}</label>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <label for="tolerancia_minutos" class="form-label">Tolerancia (min)</label>
                                <input type="number" name="tolerancia_minutos" id="tolerancia_minutos"
                                    class="form-control" value="0" required>
                            </div>
                            <div class="col-lg-4">
                                <label for="sueldo_base" class="form-label">Sueldo Base ($)</label>
                                <input type="number" step="0.01" name="sueldo_base" id="sueldo_base"
                                    class="form-control" value="0.00" required>
                            </div>
                            <div class="col-lg-4">
                                <label for="pago_hora_extra" class="form-label">Pago Hora Extra</label>
                                <input type="number" step="0.01" name="pago_hora_extra" id="pago_hora_extra"
                                    class="form-control" value="0.00" required>
                            </div>
                            <div class="col-lg-6">
                                <label for="descuento_falta" class="form-label">
                                    Descuento por Falta
                                    <i class="ri-information-line text-info fs-16 align-middle" data-bs-toggle="tooltip"
                                        data-bs-placement="top"
                                        title="El sistema calculará el descuento por faltas automáticamente: (Sueldo Base / Días del Mes) × Nº de Faltas."></i>
                                </label>
                                <input type="number" step="0.01" name="descuento_falta" id="descuento_falta"
                                    class="form-control" value="0.00" required>
                            </div>
                            <div class="col-lg-6">
                                <label for="descuento_minuto" class="form-label">Descuento x Minuto de Tardanza</label>
                                <input type="number" step="0.01" name="descuento_minuto" id="descuento_minuto"
                                    class="form-control" value="0.00" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar Horario</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Asignar Usuarios -->
    <div class="modal fade" id="modalAsignar" tabindex="-1" aria-labelledby="modalAsignarLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAsignarLabel">Asignar Personal al Horario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formAsignar">
                    @csrf
                    <input type="hidden" name="horario_id" id="asignar_horario_id">
                    <div class="modal-body">
                        <h6 id="asignar_nombre_horario" class="mb-3 text-primary"></h6>
                        <div class="col-lg-12">
                            <label for="user_ids" class="form-label">Seleccionar Empleados</label>
                            <select name="user_ids[]" id="user_ids" class="form-control select2" multiple="multiple"
                                style="width: 100%">
                                @foreach ($usuariosDisponibles as $usuario)
                                    <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Actualizar Asignaciones</button>
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
            $('#tablaHorarios').DataTable({
                responsive: true
            });

            $('.select2').select2({
                dropdownParent: $('#modalAsignar')
            });

            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })

            // Reset modal on close
            $('#modalHorario').on('hidden.bs.modal', function() {
                $('#formHorario')[0].reset();
                $('#horario_id').val('');
                $('#modalHorarioLabel').text('Nuevo Horario');
            });

            // Form Submit (Crear/Editar)
            $('#formHorario').submit(function(e) {
                e.preventDefault();
                let id = $('#horario_id').val();
                let url = id ? `{{ url('horarios') }}/${id}` : `{{ route('horarios.store') }}`;
                let method = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    type: method,
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Éxito', response.message, 'success').then(() => location
                                .reload());
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        let msg = xhr.responseJSON?.message || 'Error en el servidor';
                        Swal.fire('Error', msg, 'error');
                    }
                });
            });

            // Form Asignar
            $('#formAsignar').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: `{{ route('horarios.asignar-usuarios') }}`,
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Éxito', response.message, 'success').then(() => location
                                .reload());
                        }
                    }
                });
            });
        });

        function editarHorario(horario) {
            $('#modalHorarioLabel').text('Editar Horario');
            $('#horario_id').val(horario.id);
            $('#nombre').val(horario.nombre);
            $('#hora_inicio').val(horario.hora_inicio);
            $('#hora_fin').val(horario.hora_fin);
            $('#hora_inicio_refrigerio').val(horario.hora_inicio_refrigerio);
            $('#hora_fin_refrigerio').val(horario.hora_fin_refrigerio);
            $('#tolerancia_minutos').val(horario.tolerancia_minutos);
            $('#sueldo_base').val(horario.sueldo_base);
            $('#pago_hora_extra').val(horario.pago_hora_extra);
            $('#descuento_falta').val(horario.descuento_falta);
            $('#descuento_minuto').val(horario.descuento_minuto);

            // Check days
            $('.day-checkbox').prop('checked', false);
            if (horario.dias_laborales) {
                horario.dias_laborales.forEach(dia => {
                    $(`#dia_${dia}`).prop('checked', true);
                });
            }

            $('#modalHorario').modal('show');
        }

        function toggleEstado(id) {
            $.ajax({
                url: `{{ url('horarios') }}/${id}/toggle-estado`,
                type: 'PATCH',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    }
                }
            });
        }

        function abrirModalAsignar(id, nombre) {
            $('#asignar_horario_id').val(id);
            $('#asignar_nombre_horario').text(nombre);

            // Cargar usuarios ya asignados
            $.get(`{{ url('horarios') }}/${id}/usuarios`, function(data) {
                let ids = data.map(u => u.id);
                $('#user_ids').val(ids).trigger('change');
                $('#modalAsignar').modal('show');
            });
        }

        function eliminarHorario(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "No podrás revertir esto",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ url('horarios') }}/${id}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Eliminado', response.message, 'success').then(() => location
                                    .reload());
                            }
                        }
                    });
                }
            });
        }
    </script>
@endsection

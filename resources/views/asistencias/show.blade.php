@extends('layouts.master')
@section('title')
    Calendario de Asistencias - {{ $usuario->name }}
@endsection
@section('css')
    <link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .fc-event {
            cursor: pointer;
            border-radius: 6px !important;
            padding: 2px 4px !important;
        }

        .fc .fc-daygrid-day.fc-day-today {
            background-color: rgba(var(--vz-primary-rgb), 0.08) !important;
        }

        .fc .fc-button-primary {
            background-color: var(--vz-primary);
            border-color: var(--vz-primary);
        }

        .fc .fc-button-primary:hover {
            background-color: var(--vz-primary);
            opacity: 0.85;
        }

        .fc .fc-button-primary:not(:disabled).fc-button-active,
        .fc .fc-button-primary:not(:disabled):active {
            background-color: var(--vz-primary);
            border-color: var(--vz-primary);
        }

        /* Botones de estado progresivo */
        .estado-btn {
            position: relative;
            padding: 10px 18px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
            min-width: 120px;
        }

        .estado-btn.completado {
            opacity: 0.75;
        }

        .estado-btn.completado::after {
            content: '✓';
            position: absolute;
            top: -6px;
            right: -6px;
            background: #28a745;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .estado-btn:disabled:not(.completado) {
            opacity: 0.4;
        }

        .progress-connector {
            width: 40px;
            height: 2px;
            background: #e9ecef;
        }

        .progress-connector.active {
            background: #28a745;
        }

        /* Custom event content in calendar */
        .fc-event-custom {
            font-size: 11px;
            line-height: 1.4;
        }

        .fc-event-custom .event-title {
            font-weight: 600;
            font-size: 12px;
        }

        .fc-event-custom .event-detail {
            font-size: 10px;
            opacity: 0.9;
        }

        .fc .fc-daygrid-event {
            white-space: normal !important;
        }

        /* Mini stats */
        .mini-stat {
            text-align: center;
            padding: 12px 8px;
        }

        .mini-stat .stat-value {
            font-size: 1.4rem;
            font-weight: 600;
        }

        .mini-stat .stat-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #878a99;
            margin-top: 2px;
        }
    </style>
@endsection

@section('content')
    @php
        $horario = $usuario->horarioActual();
        $tieneRefrigerio =
            $horario && !empty($horario->hora_inicio_refrigerio) && !empty($horario->hora_fin_refrigerio);
    @endphp

    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Calendario de Asistencia</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('asistencias.index') }}">Asistencias</a></li>
                        <li class="breadcrumb-item active">Calendario</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Info del empleado + Estadísticas en una sola fila -->
    <div class="row mb-3">
        <!-- Info del empleado -->
        <div class="col-lg-5">
            <div class="card mb-0 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <img src="{{ $usuario->avatar ? URL::asset('images/' . $usuario->avatar) : URL::asset('build/images/users/avatar-1.jpg') }}"
                            alt="" class="avatar-sm rounded-circle shadow" />
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h5 class="mb-1">{{ $usuario->name }}</h5>
                        <p class="text-muted mb-0">
                            <span class="badge badge-soft-primary">{{ $horario->nombre ?? 'Sin horario' }}</span>
                            @if ($horario)
                                <span class="ms-1 text-muted small">
                                    {{ \Carbon\Carbon::parse($horario->hora_inicio)->format('h:i A') }} -
                                    {{ \Carbon\Carbon::parse($horario->hora_fin)->format('h:i A') }}
                                </span>
                                @if ($tieneRefrigerio)
                                    <br>
                                    <span class="badge badge-soft-warning mt-1">
                                        <i class="ri-cup-line me-1"></i>
                                        {{ \Carbon\Carbon::parse($horario->hora_inicio_refrigerio)->format('h:i A') }} -
                                        {{ \Carbon\Carbon::parse($horario->hora_fin_refrigerio)->format('h:i A') }}
                                    </span>
                                @endif
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mini estadísticas horizontales -->
        <div class="col-lg-7">
            <div class="row g-2 h-100">
                <div class="col-3">
                    <div class="card mb-0 h-100 border-start border-success border-3">
                        <div class="card-body mini-stat">
                            <div class="avatar-xs mx-auto mb-1">
                                <span class="avatar-title bg-success-subtle rounded fs-5">
                                    <i class="ri-check-double-line text-success"></i>
                                </span>
                            </div>
                            <div class="stat-value text-success" id="stat-asistencias">0</div>
                            <div class="stat-label">Asistencias</div>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card mb-0 h-100 border-start border-warning border-3">
                        <div class="card-body mini-stat">
                            <div class="avatar-xs mx-auto mb-1">
                                <span class="avatar-title bg-warning-subtle rounded fs-5">
                                    <i class="ri-time-line text-warning"></i>
                                </span>
                            </div>
                            <div class="stat-value text-warning" id="stat-tardanza">0</div>
                            <div class="stat-label">Tardanza (min)</div>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card mb-0 h-100 border-start border-danger border-3">
                        <div class="card-body mini-stat">
                            <div class="avatar-xs mx-auto mb-1">
                                <span class="avatar-title bg-danger-subtle rounded fs-5">
                                    <i class="ri-close-circle-line text-danger"></i>
                                </span>
                            </div>
                            <div class="stat-value text-danger" id="stat-faltas">0</div>
                            <div class="stat-label">Faltas</div>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card mb-0 h-100 border-start border-info border-3">
                        <div class="card-body mini-stat">
                            <div class="avatar-xs mx-auto mb-1">
                                <span class="avatar-title bg-info-subtle rounded fs-5">
                                    <i class="ri-timer-line text-info"></i>
                                </span>
                            </div>
                            <div class="stat-value text-info" id="stat-extras">0</div>
                            <div class="stat-label">H. Extra (min)</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Botones de los 4 estados del día de hoy -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                        <h6 class="mb-0 text-muted">
                            <i class="ri-calendar-check-line me-1"></i>
                            Hoy {{ \Carbon\Carbon::today()->format('d/m/Y') }}
                            <span id="reloj" class="ms-1 fw-normal"></span>
                        </h6>

                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            {{-- 1. ENTRADA --}}
                            <button id="btnEntrada"
                                class="btn btn-sm estado-btn {{ $asistenciaHoy ? 'btn-soft-success completado' : 'btn-success' }}"
                                onclick="registrarEntrada()" {{ $asistenciaHoy ? 'disabled' : '' }}>
                                <i class="ri-login-box-line me-1"></i> Entrada
                                @if ($asistenciaHoy && $asistenciaHoy->hora_entrada)
                                    <small
                                        class="d-block">{{ \Carbon\Carbon::parse($asistenciaHoy->hora_entrada)->format('h:i A') }}</small>
                                @endif
                            </button>

                            <div class="progress-connector {{ $asistenciaHoy ? 'active' : '' }}"></div>

                            {{-- 2. INICIO REFRIGERIO --}}
                            @if ($tieneRefrigerio)
                                <button id="btnInicioRef"
                                    class="btn btn-sm estado-btn {{ $asistenciaHoy && $asistenciaHoy->hora_inicio_refrigerio ? 'btn-soft-warning completado' : 'btn-warning' }}"
                                    onclick="registrarEvento('refrigerio_inicio')"
                                    {{ !$asistenciaHoy || $asistenciaHoy->hora_inicio_refrigerio ? 'disabled' : '' }}>
                                    <i class="ri-rest-time-line me-1"></i> Inicio Ref.
                                    @if ($asistenciaHoy && $asistenciaHoy->hora_inicio_refrigerio)
                                        <small
                                            class="d-block">{{ \Carbon\Carbon::parse($asistenciaHoy->hora_inicio_refrigerio)->format('h:i A') }}</small>
                                    @endif
                                </button>

                                <div
                                    class="progress-connector {{ $asistenciaHoy && $asistenciaHoy->hora_inicio_refrigerio ? 'active' : '' }}">
                                </div>

                                {{-- 3. FIN REFRIGERIO --}}
                                <button id="btnFinRef"
                                    class="btn btn-sm estado-btn {{ $asistenciaHoy && $asistenciaHoy->hora_fin_refrigerio ? 'btn-soft-info completado' : 'btn-info' }}"
                                    onclick="registrarEvento('refrigerio_fin')"
                                    {{ !$asistenciaHoy || !$asistenciaHoy->hora_inicio_refrigerio || $asistenciaHoy->hora_fin_refrigerio ? 'disabled' : '' }}>
                                    <i class="ri-cup-line me-1"></i> Fin Ref.
                                    @if ($asistenciaHoy && $asistenciaHoy->hora_fin_refrigerio)
                                        <small
                                            class="d-block">{{ \Carbon\Carbon::parse($asistenciaHoy->hora_fin_refrigerio)->format('h:i A') }}</small>
                                    @endif
                                </button>

                                <div
                                    class="progress-connector {{ $asistenciaHoy && $asistenciaHoy->hora_fin_refrigerio ? 'active' : '' }}">
                                </div>
                            @else
                                <span class="badge badge-soft-secondary py-2 px-3">Sin refrigerio</span>
                                <div class="progress-connector {{ $asistenciaHoy ? 'active' : '' }}"></div>
                            @endif

                            {{-- 4. SALIDA --}}
                            @php
                                $salidaDisabled = !$asistenciaHoy || $asistenciaHoy->hora_salida;
                                if ($tieneRefrigerio && $asistenciaHoy && !$asistenciaHoy->hora_fin_refrigerio) {
                                    $salidaDisabled = true;
                                }
                            @endphp
                            <button id="btnSalida"
                                class="btn btn-sm estado-btn {{ $asistenciaHoy && $asistenciaHoy->hora_salida ? 'btn-soft-danger completado' : 'btn-danger' }}"
                                onclick="confirmarSalida()" {{ $salidaDisabled ? 'disabled' : '' }}>
                                <i class="ri-logout-box-line me-1"></i> Salida
                                @if ($asistenciaHoy && $asistenciaHoy->hora_salida)
                                    <small
                                        class="d-block">{{ \Carbon\Carbon::parse($asistenciaHoy->hora_salida)->format('h:i A') }}</small>
                                @endif
                            </button>
                        </div>

                        @if ($asistenciaHoy && $asistenciaHoy->hora_salida)
                            <span class="badge bg-success-subtle text-success fs-12 py-2 px-3">
                                <i class="ri-check-double-line me-1"></i> Jornada completada
                            </span>
                        @else
                            <span></span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendario a ancho completo -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ URL::asset('build/libs/fullcalendar/index.global.min.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>

    <script>
        // Reloj en tiempo real
        setInterval(() => {
            let now = new Date();
            let el = document.getElementById('reloj');
            if (el) el.textContent = now.toLocaleTimeString('es-PE');
        }, 1000);

        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                locale: 'es',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth'
                },
                initialView: 'dayGridMonth',
                height: 'auto',
                dayMaxEvents: 2,
                eventContent: function(arg) {
                    let props = arg.event.extendedProps;
                    let html = '<div class="fc-event-custom p-1">';

                    // Estado principal
                    html += '<div class="event-title">' + arg.event.title + '</div>';

                    // Hora de entrada
                    if (props.entrada) {
                        html += '<div class="event-detail">🕐 ' + formatTime(props.entrada) + '</div>';
                    }

                    // Refrigerio
                    if (props.tiene_refrigerio && props.inicio_refrigerio) {
                        html += '<div class="event-detail">☕ ' + formatTime(props.inicio_refrigerio);
                        if (props.fin_refrigerio) {
                            html += ' - ' + formatTime(props.fin_refrigerio);
                        }
                        html += '</div>';
                    }

                    // Hora de salida
                    if (props.salida) {
                        html += '<div class="event-detail">🚪 ' + formatTime(props.salida) + '</div>';
                    }

                    // Tardanza
                    if (props.tardanza > 0) {
                        html += '<div class="event-detail" style="color:#ffe0e0;">⏰ ' + props.tardanza +
                            ' min</div>';
                    }

                    html += '</div>';
                    return {
                        html: html
                    };
                },
                events: function(info, successCallback, failureCallback) {
                    let month = info.start.getMonth() + 1;
                    let year = info.start.getFullYear();
                    fetch(
                            `{{ route('asistencias.calendario-data', $usuario->id) }}?month=${month}&year=${year}`
                        )
                        .then(response => response.json())
                        .then(data => {
                            successCallback(data);
                            actualizarEstadisticas(data);
                        })
                        .catch(err => {
                            console.error('Error cargando calendario:', err);
                            failureCallback(err);
                        });
                },
                eventClick: function(info) {
                    mostrarDetalleAsistencia(info.event);
                }
            });
            calendar.render();
        });

        function formatTime(timeStr) {
            if (!timeStr) return '-';
            try {
                let parts = timeStr.split(':');
                let hours = parseInt(parts[0]);
                let mins = parts[1];
                let ampm = hours >= 12 ? 'PM' : 'AM';
                hours = hours % 12 || 12;
                return hours + ':' + mins + ' ' + ampm;
            } catch (e) {
                return timeStr;
            }
        }

        function registrarEntrada() {
            $.post(`{{ route('asistencias.store') }}`, {
                _token: '{{ csrf_token() }}',
                user_id: '{{ $usuario->id }}'
            }).done(function(response) {
                Swal.fire('Éxito', response.message || 'Entrada registrada', 'success').then(() =>
                    location.reload());
            }).fail(function(xhr) {
                Swal.fire('Error', xhr.responseJSON?.message || 'Error en el servidor', 'error');
            });
        }

        function registrarEvento(tipo) {
            $.post(`{{ route('asistencias.evento') }}`, {
                _token: '{{ csrf_token() }}',
                tipo: tipo,
                asistencia_id: '{{ $asistenciaHoy->id ?? '' }}'
            }).done(function(response) {
                Swal.fire('Éxito', response.message || 'Evento registrado', 'success').then(() =>
                    location.reload());
            }).fail(function(xhr) {
                Swal.fire('Error', xhr.responseJSON?.message || 'Error en el servidor', 'error');
            });
        }

        function confirmarSalida() {
            Swal.fire({
                title: '¿Confirmar Salida?',
                text: 'Se registrará tu hora de salida. Esta acción no se puede deshacer.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="ri-logout-box-line me-1"></i> Sí, registrar salida',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    registrarEvento('salida');
                }
            });
        }

        function mostrarDetalleAsistencia(event) {
            let props = event.extendedProps;
            let estadoLabel = props.estado === 'PRESENTE' ? 'Asistio' : (props.estado === 'FALTA' ? 'Falto' : (
                props.estado === 'TARDANZA' ? 'Tardanza' : props.estado));
            let badgeClass = props.estado === 'PRESENTE' ? 'bg-success' : (props.estado === 'FALTA' ?
                'bg-danger' : 'bg-warning');

            let html = `
                <div class="text-start">
                    <div class="mb-3 text-center">
                        <span class="badge ${badgeClass} fs-13 py-2 px-3">${estadoLabel}</span>
                    </div>
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="fw-medium" style="width:40%"><i class="ri-login-box-line me-1 text-success"></i> Entrada</td>
                            <td>${props.entrada ? formatTime(props.entrada) : '<span class="text-muted">-</span>'}</td>
                        </tr>`;

            if (props.tiene_refrigerio) {
                html += `
                        <tr>
                            <td class="fw-medium"><i class="ri-rest-time-line me-1 text-warning"></i> Inicio Ref.</td>
                            <td>${props.inicio_refrigerio ? formatTime(props.inicio_refrigerio) : '<span class="text-muted">Pendiente</span>'}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium"><i class="ri-cup-line me-1 text-info"></i> Fin Ref.</td>
                            <td>${props.fin_refrigerio ? formatTime(props.fin_refrigerio) : '<span class="text-muted">Pendiente</span>'}</td>
                        </tr>`;
            }

            html += `
                        <tr>
                            <td class="fw-medium"><i class="ri-logout-box-line me-1 text-danger"></i> Salida</td>
                            <td>${props.salida ? formatTime(props.salida) : '<span class="text-muted">Pendiente</span>'}</td>
                        </tr>`;

            if (props.tardanza > 0) {
                html += `
                        <tr>
                            <td class="fw-medium"><i class="ri-time-line me-1 text-danger"></i> Tardanza</td>
                            <td><span class="text-danger fw-bold">${props.tardanza} min</span></td>
                        </tr>`;
            }

            if (props.minutos_extra > 0) {
                html += `
                        <tr>
                            <td class="fw-medium"><i class="ri-timer-line me-1 text-info"></i> Extras</td>
                            <td><span class="text-info fw-bold">${props.minutos_extra} min</span></td>
                        </tr>`;
            }

            if (props.observaciones) {
                html += `
                        <tr>
                            <td class="fw-medium"><i class="ri-file-text-line me-1"></i> Obs.</td>
                            <td>${props.observaciones}</td>
                        </tr>`;
            }

            html += `</table></div>`;

            Swal.fire({
                title: 'Detalle de Asistencia',
                html: html,
                width: 420,
                showCloseButton: true,
                showConfirmButton: false,
            });
        }

        function actualizarEstadisticas(eventos) {
            let tardanza = 0,
                faltas = 0,
                asistencias = 0,
                extras = 0;
            eventos.forEach(e => {
                let p = e.extendedProps || {};
                tardanza += parseInt(p.tardanza || 0);
                extras += parseInt(p.minutos_extra || 0);
                if (p.estado === 'FALTA') faltas++;
                if (p.estado === 'PRESENTE' || p.estado === 'TARDANZA') asistencias++;
            });
            document.getElementById('stat-tardanza').textContent = tardanza;
            document.getElementById('stat-faltas').textContent = faltas;
            document.getElementById('stat-asistencias').textContent = asistencias;
            document.getElementById('stat-extras').textContent = extras;
        }
    </script>
@endsection

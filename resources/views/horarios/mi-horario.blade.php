@extends('layouts.master')
@section('title')
    Mi Horario
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Mi Horario</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Mi Horario</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-xxl-6 col-lg-8">
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-header bg-primary py-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-md">
                                <span class="avatar-title bg-white-50 text-white rounded-circle fs-24">
                                    <i class="ri-calendar-todo-line"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h4 class="card-title mb-0 text-white">Horario de Trabajo Personal</h4>
                            <p class="text-white-50 mb-0">Información detallada de tu turno asignado</p>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if ($horario)
                        <div class="p-4 border-bottom bg-light-subtle">
                            <div class="row align-items-center">
                                <div class="col-sm-auto">
                                    <div class="avatar-sm">
                                        <div class="avatar-title bg-primary-subtle text-primary rounded-circle fs-20">
                                            <i class="ri-time-line"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <h5 class="fs-16 mb-1">{{ $horario->nombre }}</h5>
                                    <p class="text-muted mb-0">Código: <span
                                            class="fw-medium text-dark">{{ $horario->codigo }}</span></p>
                                </div>
                                <div class="col-sm-auto mt-3 mt-sm-0">
                                    <span class="badge bg-success-subtle text-success fs-12 px-3 py-2">
                                        <i class="ri-checkbox-circle-line me-1"></i> Turno Vigente
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="p-4">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="p-3 border rounded border-dashed">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="ri-login-box-line fs-20 text-success me-2"></i>
                                            <h6 class="mb-0 fw-semibold">Entrada Principal</h6>
                                        </div>
                                        <h3 class="mb-1 text-center font-monospace">
                                            {{ \Carbon\Carbon::parse($horario->hora_inicio)->format('h:i A') }}
                                        </h3>
                                        <p class="text-muted mb-0 text-center small">
                                            <i class="ri-information-line me-1"></i> Tolerancia:
                                            {{ $horario->tolerancia_minutos }} min
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 border rounded border-dashed">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="ri-logout-box-line fs-20 text-danger me-2"></i>
                                            <h6 class="mb-0 fw-semibold">Salida Principal</h6>
                                        </div>
                                        <h3 class="mb-1 text-center font-monospace">
                                            {{ \Carbon\Carbon::parse($horario->hora_fin)->format('h:i A') }}
                                        </h3>
                                        <p class="text-muted mb-0 text-center small">Finalización de jornada</p>
                                    </div>
                                </div>

                                @if ($horario->hora_inicio_refrigerio)
                                    <div class="col-12">
                                        <div class="p-3 border rounded border-dashed bg-light-subtle">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="ri-restaurant-line fs-20 text-warning me-2"></i>
                                                <h6 class="mb-0 fw-semibold">Tiempo de Refrigerio / Almuerzo</h6>
                                            </div>
                                            <div class="d-flex justify-content-center align-items-center gap-4 py-2">
                                                <div class="text-center">
                                                    <small class="text-muted d-block">Inicio</small>
                                                    <span
                                                        class="fs-18 fw-medium">{{ \Carbon\Carbon::parse($horario->hora_inicio_refrigerio)->format('h:i A') }}</span>
                                                </div>
                                                <div class="fs-24 text-muted border-start h-100"></div>
                                                <div class="text-center">
                                                    <small class="text-muted d-block">Retorno</small>
                                                    <span
                                                        class="fs-18 fw-medium">{{ \Carbon\Carbon::parse($horario->hora_fin_refrigerio)->format('h:i A') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="col-12">
                                    <h6 class="mb-3 text-uppercase fs-12 text-muted">Días Laborales</h6>
                                    <div class="d-flex flex-wrap gap-2">
                                        @php
                                            $todosLosDias = [
                                                'Lunes',
                                                'Martes',
                                                'Miércoles',
                                                'Jueves',
                                                'Viernes',
                                                'Sábado',
                                                'Domingo',
                                            ];
                                        @endphp
                                        @foreach ($todosLosDias as $dia)
                                            @php
                                                $esLaboral = in_array($dia, $horario->dias_laborales);
                                            @endphp
                                            <div
                                                class="flex-grow-1 text-center p-2 rounded {{ $esLaboral ? 'bg-primary text-white' : 'bg-light text-muted' }}">
                                                <span class="d-block fw-bold">{{ substr($dia, 0, 3) }}</span>
                                                <small
                                                    class="{{ $esLaboral ? 'text-white-50' : 'text-muted' }}">{{ $esLaboral ? 'Trabaja' : 'Libre' }}</small>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer bg-light p-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="flex-shrink-0">
                                    <i class="ri-error-warning-line fs-24 text-info"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <p class="text-muted mb-0 fs-13">
                                        Recuerda marcar tu asistencia diariamente desde la sección de <strong>Mi
                                            Asistencia</strong> para evitar descuentos.
                                        Tu sueldo base actual vinculado a este horario es de <span
                                            class="fw-bold text-warning">S/.{{ number_format($horario->sueldo_base, 2) }}</span>.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="p-5 text-center">
                            <div class="avatar-lg mx-auto mb-4">
                                <div class="avatar-title bg-light text-muted rounded-circle fs-32">
                                    <i class="ri-emotion-sad-line"></i>
                                </div>
                            </div>
                            <h5>Sin Horario Asignado</h5>
                            <p class="text-muted">No tienes un horario de trabajo asignado actualmente. Por favor, contacta
                                con tu administrador.</p>
                            <a href="{{ url('/') }}" class="btn btn-primary mt-3">Volver al Inicio</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

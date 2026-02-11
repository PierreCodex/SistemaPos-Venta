@extends('layouts.master')

@section('title')
    Kardex - {{ $producto->nombre }}
@endsection

@section('css')
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
    <style>
        .movimiento-entrada {
            color: #0ab39c;
            font-weight: bold;
        }

        .movimiento-salida {
            color: #f06548;
            font-weight: bold;
        }

        .timeline-item {
            position: relative;
            padding-left: 30px;
            padding-bottom: 20px;
            border-left: 2px solid #e9ebec;
        }

        .timeline-item:last-child {
            border-left: none;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -6px;
            top: 0;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #405189;
        }

        .timeline-item.entrada::before {
            background: #0ab39c;
        }

        .timeline-item.salida::before {
            background: #f06548;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Kardex del Producto</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('inventario.kardex.index') }}">Kardex</a></li>
                        <li class="breadcrumb-item active">{{ $producto->codigo }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Información del Producto -->
    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body text-center">
                    @if ($producto->imagen)
                        <img src="{{ asset('storage/' . $producto->imagen) }}" alt="{{ $producto->nombre }}"
                            class="img-fluid rounded mb-3" style="max-height: 150px;">
                    @else
                        <div class="avatar-xl mx-auto mb-3">
                            <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-1">
                                <i class="ri-box-3-line"></i>
                            </span>
                        </div>
                    @endif

                    <h5 class="mb-1">{{ $producto->nombre }}</h5>
                    <p class="text-muted mb-3">
                        <span class="badge bg-light text-primary">{{ $producto->codigo }}</span>
                        @if ($producto->codigo_barras)
                            <br><small class="text-muted">{{ $producto->codigo_barras }}</small>
                        @endif
                    </p>

                    <div class="d-flex justify-content-center gap-4 mb-3">
                        <div class="text-center">
                            <h3
                                class="mb-1 {{ $producto->stock <= $producto->stock_minimo ? 'text-danger' : 'text-success' }}">
                                {{ number_format($producto->stock, 2) }}
                            </h3>
                            <span class="text-muted">Stock Actual</span>
                        </div>
                        <div class="text-center">
                            <h3 class="mb-1">{{ number_format($producto->stock_minimo, 2) }}</h3>
                            <span class="text-muted">Stock Mínimo</span>
                        </div>
                    </div>

                    <div class="text-start">
                        <p class="mb-1"><strong>Categoría:</strong> {{ $producto->categoria->nombre ?? 'N/A' }}</p>
                        <p class="mb-1"><strong>Marca:</strong> {{ $producto->marca->nombre ?? 'N/A' }}</p>
                        <p class="mb-1"><strong>Unidad:</strong> {{ $producto->unidad->nombre ?? 'UND' }}</p>
                        <p class="mb-1"><strong>P. Venta:</strong> S/ {{ number_format($producto->precio_venta, 2) }}</p>
                        <p class="mb-0"><strong>P. Compra:</strong> S/ {{ number_format($producto->precio_compra, 2) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Filtros de fecha -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="ri-filter-3-line me-2"></i>Filtrar por Fecha</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('inventario.kardex.producto', $producto->id) }}">
                        <div class="mb-2">
                            <label class="form-label fw-semibold text-muted text-uppercase fs-11">Desde</label>
                            <div class="input-group">
                                <input type="text" name="fecha_desde" class="form-control border-light bg-light"
                                    data-provider="flatpickr" data-date-format="Y-m-d" value="{{ $fechaDesde ?? '' }}">
                                <span class="input-group-text border-light bg-light"><i
                                        class="ri-calendar-event-line"></i></span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted text-uppercase fs-11">Hasta</label>
                            <div class="input-group">
                                <input type="text" name="fecha_hasta" class="form-control border-light bg-light"
                                    data-provider="flatpickr" data-date-format="Y-m-d" value="{{ $fechaHasta ?? '' }}">
                                <span class="input-group-text border-light bg-light"><i
                                        class="ri-calendar-event-line"></i></span>
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-filter-3-line me-1"></i>Filtrar
                            </button>
                            <a href="{{ route('inventario.kardex.producto', $producto->id) }}" class="btn btn-light">
                                <i class="ri-refresh-line me-1"></i>Limpiar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">
                        <i class="ri-history-line me-2"></i>Historial de Movimientos
                    </h5>
                    <div class="d-flex gap-2">
                        <button type="button" id="btnExportarExcel" class="btn btn-soft-success btn-sm">
                            <i class="las la-file-excel"></i> Excel
                        </button>
                        <button type="button" id="btnExportarPDF" class="btn btn-soft-danger btn-sm">
                            <i class="las la-file-pdf"></i> PDF
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    @if ($movimientos->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="ri-history-line fs-1"></i>
                            <p class="mt-2">No hay movimientos registrados para este producto</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table id="tablaKardexProducto" class="table align-middle" style="width:100%">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Tipo Movimiento</th>
                                        <th class="text-end">Cantidad</th>
                                        <th class="text-end">Stock Anterior</th>
                                        <th class="text-end">Stock Resultante</th>
                                        <th>Referencia</th>
                                        <th>Usuario</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($movimientos as $mov)
                                        <tr>
                                            <td>{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                @php $info = $tiposMovimiento[$mov->tipo_movimiento] ?? ['label' => $mov->tipo_movimiento, 'color' => 'secondary', 'icon' => 'ri-question-line']; @endphp
                                                <span
                                                    class="badge bg-{{ $info['color'] }}-subtle text-{{ $info['color'] }}">
                                                    <i class="{{ $info['icon'] }} me-1"></i>{{ $info['label'] }}
                                                </span>
                                            </td>
                                            <td
                                                class="text-end fw-bold {{ $mov->cantidad >= 0 ? 'movimiento-entrada' : 'movimiento-salida' }}">
                                                {{ $mov->cantidad >= 0 ? '+' : '' }}{{ number_format($mov->cantidad, 3) }}
                                            </td>
                                            <td class="text-end">{{ number_format($mov->stock_anterior, 3) }}</td>
                                            <td class="text-end fw-semibold">
                                                {{ number_format($mov->stock_resultante, 3) }}</td>
                                            <td>
                                                @if ($mov->referencia_tipo && $mov->referencia_id)
                                                    <small class="text-muted">{{ ucfirst($mov->referencia_tipo) }}
                                                        #{{ $mov->referencia_id }}</small>
                                                @elseif($mov->observaciones)
                                                    <small class="text-muted" title="{{ $mov->observaciones }}">
                                                        {{ Str::limit($mov->observaciones, 30) }}
                                                    </small>
                                                @else
                                                    <small class="text-muted">-</small>
                                                @endif
                                            </td>
                                            <td>{{ $mov->user->name ?? 'Sistema' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Resumen -->
                        <div class="row mt-4">
                            <div class="col-md-4">
                                <div class="border rounded p-3 text-center">
                                    <h4 class="text-success mb-1">
                                        +{{ number_format($movimientos->where('cantidad', '>', 0)->sum('cantidad'), 2) }}
                                    </h4>
                                    <span class="text-muted">Total Entradas</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-3 text-center">
                                    <h4 class="text-danger mb-1">
                                        {{ number_format($movimientos->where('cantidad', '<', 0)->sum('cantidad'), 2) }}
                                    </h4>
                                    <span class="text-muted">Total Salidas</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-3 text-center bg-light">
                                    <h4 class="text-primary mb-1">{{ $movimientos->count() }}</h4>
                                    <span class="text-muted">Total Movimientos</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

    <script>
        // Inicializar DataTable
        let tabla = $('#tablaKardexProducto').DataTable({
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'excelHtml5',
                    className: 'd-none',
                    title: 'Kardex - {{ $producto->nombre }} ({{ $producto->codigo }})'
                },
                {
                    extend: 'pdfHtml5',
                    className: 'd-none',
                    title: 'Kardex - {{ $producto->nombre }} ({{ $producto->codigo }})'
                }
            ],
            paging: false,
            info: false,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
            },
            order: [
                [0, 'desc']
            ]
        });

        // Botones de exportación
        $('#btnExportarExcel').click(() => tabla.button('.buttons-excel').trigger());
        $('#btnExportarPDF').click(() => tabla.button('.buttons-pdf').trigger());
    </script>
@endsection

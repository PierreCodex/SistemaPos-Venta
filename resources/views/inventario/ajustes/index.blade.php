@extends('layouts.master')

@section('title')
    Ajustes de Inventario
@endsection

@section('css')
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet"
        type="text/css" />
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Ajustes de Inventario</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Inventario</a></li>
                        <li class="breadcrumb-item active">Ajustes</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Alerta de Stock Bajo -->
        @if ($productosStockBajo->count() > 0)
            <div class="col-12">
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="ri-alert-line me-2"></i>
                    <strong>¡Atención!</strong> Hay <span class="badge bg-danger">{{ $productosStockBajo->count() }}</span>
                    productos con stock bajo o agotado.
                    <button type="button" class="btn btn-sm btn-warning ms-2" data-bs-toggle="modal"
                        data-bs-target="#modalStockBajo">
                        Ver productos
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">Historial de Ajustes</h5>
                    <div class="d-flex flex-shrink-0 gap-2">
                        @can('inventario.ajustar')
                            <a href="{{ route('inventario.ajustes.create') }}" class="btn btn-primary">
                                <i class="ri-add-line me-1"></i> Nuevo Ajuste
                            </a>
                        @endcan
                    </div>
                </div>

                <div class="card-body">
                    <table id="tablaAjustes" class="table nowrap align-middle" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Motivo</th>
                                <th>Productos</th>
                                <th>Usuario</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ajustes as $ajuste)
                                <tr data-id="{{ $ajuste->id }}">
                                    <td>{{ $ajuste->id }}</td>
                                    <td>{{ $ajuste->fecha->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if ($ajuste->tipo === 'ENTRADA')
                                            <span class="badge bg-success"><i class="ri-add-line"></i> Entrada</span>
                                        @elseif($ajuste->tipo === 'SALIDA')
                                            <span class="badge bg-danger"><i class="ri-subtract-line"></i> Salida</span>
                                        @else
                                            <span class="badge bg-info"><i class="ri-survey-line"></i> Conteo</span>
                                        @endif
                                    </td>
                                    <td>{{ $motivos[$ajuste->motivo] ?? $ajuste->motivo }}</td>
                                    <td><span class="badge bg-primary">{{ $ajuste->detalles->count() }} items</span></td>
                                    <td>{{ $ajuste->user->name ?? 'N/A' }}</td>
                                    <td>
                                        @if ($ajuste->estado === 'APLICADO')
                                            <span class="badge bg-success">Aplicado</span>
                                        @elseif($ajuste->estado === 'BORRADOR')
                                            <span class="badge bg-warning">Borrador</span>
                                        @else
                                            <span class="badge bg-danger">Anulado</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <button type="button" class="btn btn-sm btn-info"
                                                onclick="verAjuste({{ $ajuste->id }})" title="Ver Detalle">
                                                <i class="ri-eye-line"></i>
                                            </button>
                                            @can('inventario.ajustar')
                                                @if ($ajuste->estado === 'BORRADOR')
                                                    <button type="button" class="btn btn-sm btn-success"
                                                        onclick="aplicarAjuste({{ $ajuste->id }})" title="Aplicar">
                                                        <i class="ri-check-line"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger"
                                                        onclick="eliminarAjuste({{ $ajuste->id }})" title="Eliminar">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                @elseif($ajuste->estado === 'APLICADO')
                                                    <button type="button" class="btn btn-sm btn-danger"
                                                        onclick="anularAjuste({{ $ajuste->id }})" title="Anular">
                                                        <i class="ri-close-circle-line"></i>
                                                    </button>
                                                @endif
                                            @endcan
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

    <!-- Modal Stock Bajo -->
    <div class="modal fade" id="modalStockBajo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title"><i class="ri-alert-line me-2"></i>Productos con Stock Bajo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Producto</th>
                                <th class="text-end">Stock Actual</th>
                                <th class="text-end">Stock Mínimo</th>
                                <th class="text-end">Faltante</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($productosStockBajo as $producto)
                                <tr>
                                    <td><code>{{ $producto->codigo }}</code></td>
                                    <td>{{ $producto->nombre }}</td>
                                    <td class="text-end">
                                        <span class="badge {{ $producto->stock <= 0 ? 'bg-danger' : 'bg-warning' }}">
                                            {{ number_format($producto->stock, 2) }}
                                            {{ $producto->unidad->codigo ?? 'UND' }}
                                        </span>
                                    </td>
                                    <td class="text-end">{{ number_format($producto->stock_minimo, 2) }}</td>
                                    <td class="text-end text-danger fw-bold">
                                        {{ number_format($producto->stock_minimo - $producto->stock, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    @can('inventario.ajustar')
                        <a href="{{ route('inventario.ajustes.create') }}" class="btn btn-primary">
                            <i class="ri-add-line me-1"></i> Crear Ajuste de Entrada
                        </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Ver Detalle -->
    <div class="modal fade" id="modalVerAjuste" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="ri-file-list-3-line me-2"></i>Detalle del Ajuste</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="contenidoDetalleAjuste">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
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

    <script>
        // Inicializar DataTable
        $('#tablaAjustes').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
            },
            order: [
                [0, 'desc']
            ]
        });

        // Ver detalle del ajuste
        function verAjuste(id) {
            $('#contenidoDetalleAjuste').html(
                '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>');
            new bootstrap.Modal(document.getElementById('modalVerAjuste')).show();

            fetch(`{{ url('inventario/ajustes') }}/${id}`)
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        const a = data.ajuste;
                        let html = `
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Tipo:</strong> ${a.tipo}</p>
                                    <p class="mb-1"><strong>Motivo:</strong> ${a.motivo}</p>
                                </div>
                                <div class="col-md-6 text-end">
                                    <p class="mb-1"><strong>Fecha:</strong> ${new Date(a.fecha).toLocaleString('es-PE')}</p>
                                    <p class="mb-1"><strong>Estado:</strong> <span class="badge ${a.estado === 'APLICADO' ? 'bg-success' : a.estado === 'BORRADOR' ? 'bg-warning' : 'bg-danger'}">${a.estado}</span></p>
                                </div>
                            </div>
                            ${a.descripcion ? `<p class="text-muted"><em>${a.descripcion}</em></p>` : ''}
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Producto</th>
                                        <th class="text-end">Stock Sistema</th>
                                        <th class="text-end">Stock Físico</th>
                                        <th class="text-end">Diferencia</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${a.detalles.map(d => `
                                            <tr>
                                                <td>${d.producto?.nombre || 'N/A'}</td>
                                                <td class="text-end">${parseFloat(d.stock_sistema).toFixed(3)}</td>
                                                <td class="text-end">${parseFloat(d.stock_fisico).toFixed(3)}</td>
                                                <td class="text-end ${parseFloat(d.diferencia) >= 0 ? 'text-success' : 'text-danger'} fw-bold">
                                                    ${parseFloat(d.diferencia) >= 0 ? '+' : ''}${parseFloat(d.diferencia).toFixed(3)}
                                                </td>
                                            </tr>
                                        `).join('')}
                                </tbody>
                            </table>
                        `;
                        $('#contenidoDetalleAjuste').html(html);
                    }
                });
        }

        // Aplicar ajuste
        function aplicarAjuste(id) {
            Swal.fire({
                title: '¿Aplicar ajuste?',
                text: 'Esta acción actualizará el stock de los productos.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0ab39c',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, aplicar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`{{ url('inventario/ajustes') }}/${id}/aplicar`, {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('¡Aplicado!', data.message, 'success').then(() => location.reload());
                            } else {
                                Swal.fire('Error', data.message, 'error');
                            }
                        });
                }
            });
        }

        // Anular ajuste
        function anularAjuste(id) {
            Swal.fire({
                title: '¿Anular este ajuste?',
                text: 'Esta acción revertirá los cambios de stock.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f06548',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, anular',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`{{ url('inventario/ajustes') }}/${id}/anular`, {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('¡Anulado!', data.message, 'success').then(() => location.reload());
                            } else {
                                Swal.fire('Error', data.message, 'error');
                            }
                        });
                }
            });
        }

        // Eliminar ajuste (solo borradores)
        function eliminarAjuste(id) {
            Swal.fire({
                title: '¿Eliminar este ajuste?',
                text: 'Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f06548',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`{{ url('inventario/ajustes') }}/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('¡Eliminado!', data.message, 'success').then(() => location.reload());
                            } else {
                                Swal.fire('Error', data.message, 'error');
                            }
                        });
                }
            });
        }
    </script>
@endsection

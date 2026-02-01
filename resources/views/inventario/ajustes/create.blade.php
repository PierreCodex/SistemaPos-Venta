@extends('layouts.master')

@section('title')
    Nuevo Ajuste de Inventario
@endsection

@section('css')
    <style>
        .producto-item {
            transition: all 0.3s ease;
        }

        .producto-item:hover {
            background-color: #f8f9fa;
        }

        .diferencia-positiva {
            color: #0ab39c;
            font-weight: bold;
        }

        .diferencia-negativa {
            color: #f06548;
            font-weight: bold;
        }

        .diferencia-cero {
            color: #878a99;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Nuevo Ajuste de Inventario</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('inventario.ajustes.index') }}">Ajustes</a></li>
                        <li class="breadcrumb-item active">Nuevo Ajuste</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <form id="formAjuste">
        @csrf
        <div class="row">
            <!-- Columna Izquierda: Información del Ajuste -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0"><i class="ri-settings-3-line me-2"></i>Información del Ajuste</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="tipo" class="form-label fw-semibold">Tipo de Ajuste <span
                                    class="text-danger">*</span></label>
                            <select id="tipo" name="tipo" class="form-select" required>
                                @foreach ($tipos as $valor => $etiqueta)
                                    <option value="{{ $valor }}">{{ $etiqueta }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="motivo" class="form-label fw-semibold">Motivo <span
                                    class="text-danger">*</span></label>
                            <select id="motivo" name="motivo" class="form-select" required>
                                @foreach ($motivos as $valor => $etiqueta)
                                    <option value="{{ $valor }}">{{ $etiqueta }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="fecha" class="form-label fw-semibold">Fecha</label>
                            <input type="datetime-local" id="fecha" name="fecha" class="form-control"
                                value="{{ date('Y-m-d\TH:i') }}">
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label fw-semibold">Descripción</label>
                            <textarea id="descripcion" name="descripcion" class="form-control" rows="3"
                                placeholder="Describe el motivo del ajuste..."></textarea>
                        </div>

                        <hr>

                        <!-- Resumen -->
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total productos:</span>
                            <span class="fw-semibold" id="totalProductos">0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted text-success">Sobrantes:</span>
                            <span class="fw-semibold text-success" id="totalSobrantes">0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted text-danger">Faltantes:</span>
                            <span class="fw-semibold text-danger" id="totalFaltantes">0</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna Central: Búsqueda y Lista de Productos -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-success text-white d-flex align-items-center">
                        <h5 class="card-title mb-0 flex-grow-1"><i class="ri-box-3-line me-2"></i>Productos a Ajustar</h5>
                    </div>
                    <div class="card-body">
                        <!-- Buscador de productos -->
                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="ri-search-line"></i></span>
                                <input type="text" id="buscarProducto" class="form-control"
                                    placeholder="Buscar producto por nombre o código...">
                            </div>
                            <div id="resultadosBusqueda" class="list-group mt-2 shadow-sm"
                                style="display: none; position: absolute; z-index: 1000; width: calc(100% - 2rem);"></div>
                        </div>

                        <!-- Tabla de productos agregados -->
                        <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                            <table class="table table-hover align-middle" id="tablaProductosAjuste">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th style="width: 35%">Producto</th>
                                        <th class="text-center" style="width: 15%">Stock Sistema</th>
                                        <th class="text-center" style="width: 15%">Stock Físico</th>
                                        <th class="text-center" style="width: 15%">Diferencia</th>
                                        <th class="text-center" style="width: 15%">Observación</th>
                                        <th style="width: 5%"></th>
                                    </tr>
                                </thead>
                                <tbody id="listaProductos">
                                    <tr id="filaVacia">
                                        <td colspan="6" class="text-center text-muted py-5">
                                            <i class="ri-inbox-line fs-1"></i>
                                            <p class="mt-2">Busque y agregue productos para ajustar</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <hr>

                        <!-- Botones de acción -->
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('inventario.ajustes.index') }}" class="btn btn-light">
                                <i class="ri-arrow-left-line me-1"></i>Cancelar
                            </a>
                            <button type="button" class="btn btn-warning" onclick="guardarBorrador()">
                                <i class="ri-save-line me-1"></i>Guardar Borrador
                            </button>
                            <button type="submit" class="btn btn-success" id="btnAplicar">
                                <i class="ri-check-double-line me-1"></i>Aplicar Ajuste
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // Estado del ajuste
        let productosAjuste = [];

        // Buscador de productos
        const inputBuscar = document.getElementById('buscarProducto');
        const resultados = document.getElementById('resultadosBusqueda');
        let timeoutBusqueda;

        inputBuscar.addEventListener('input', function() {
            clearTimeout(timeoutBusqueda);
            const termino = this.value.trim();

            if (termino.length < 2) {
                resultados.style.display = 'none';
                return;
            }

            timeoutBusqueda = setTimeout(() => {
                fetch(`{{ route('inventario.ajustes.buscar-productos') }}?q=${encodeURIComponent(termino)}`)
                    .then(r => r.json())
                    .then(productos => {
                        if (productos.length === 0) {
                            resultados.innerHTML =
                                '<div class="list-group-item text-muted">No se encontraron productos</div>';
                        } else {
                            resultados.innerHTML = productos.map(p => `
                                <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" 
                                    onclick='agregarProducto(${JSON.stringify(p).replace(/'/g, "\\'")})'>
                                    <div>
                                        <strong>${p.nombre}</strong><br>
                                        <small class="text-muted">${p.codigo} | ${p.unidad?.codigo || 'UND'}</small>
                                    </div>
                                    <span class="badge ${p.stock <= p.stock_minimo ? 'bg-danger' : 'bg-success'}">
                                        Stock: ${parseFloat(p.stock).toFixed(2)}
                                    </span>
                                </button>
                            `).join('');
                        }
                        resultados.style.display = 'block';
                    });
            }, 300);
        });

        // Cerrar resultados al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (!inputBuscar.contains(e.target) && !resultados.contains(e.target)) {
                resultados.style.display = 'none';
            }
        });

        // Agregar producto a la lista
        function agregarProducto(producto) {
            // Verificar si ya existe
            if (productosAjuste.find(p => p.producto_id === producto.id)) {
                Swal.fire('Aviso', 'Este producto ya está en la lista', 'info');
                return;
            }

            productosAjuste.push({
                producto_id: producto.id,
                nombre: producto.nombre,
                codigo: producto.codigo,
                unidad: producto.unidad?.codigo || 'UND',
                stock_sistema: parseFloat(producto.stock),
                stock_fisico: parseFloat(producto.stock),
                diferencia: 0,
                observacion: ''
            });

            inputBuscar.value = '';
            resultados.style.display = 'none';
            renderizarProductos();
            actualizarResumen();
        }

        // Renderizar lista de productos
        function renderizarProductos() {
            const lista = document.getElementById('listaProductos');
            const filaVacia = document.getElementById('filaVacia');

            if (productosAjuste.length === 0) {
                lista.innerHTML = `
                    <tr id="filaVacia">
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="ri-inbox-line fs-1"></i>
                            <p class="mt-2">Busque y agregue productos para ajustar</p>
                        </td>
                    </tr>
                `;
                return;
            }

            lista.innerHTML = productosAjuste.map((p, index) => `
                <tr class="producto-item">
                    <td>
                        <strong>${p.nombre}</strong><br>
                        <small class="text-muted">${p.codigo} | ${p.unidad}</small>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-secondary">${p.stock_sistema.toFixed(3)}</span>
                    </td>
                    <td class="text-center">
                        <input type="number" class="form-control form-control-sm text-center" 
                            value="${p.stock_fisico}" min="0" step="0.001"
                            onchange="actualizarStockFisico(${index}, this.value)">
                    </td>
                    <td class="text-center">
                        <span class="${p.diferencia > 0 ? 'diferencia-positiva' : p.diferencia < 0 ? 'diferencia-negativa' : 'diferencia-cero'}">
                            ${p.diferencia > 0 ? '+' : ''}${p.diferencia.toFixed(3)}
                        </span>
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm" 
                            placeholder="Nota..." value="${p.observacion || ''}"
                            onchange="actualizarObservacion(${index}, this.value)">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="quitarProducto(${index})">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        // Funciones de actualización
        function actualizarStockFisico(index, valor) {
            productosAjuste[index].stock_fisico = parseFloat(valor) || 0;
            productosAjuste[index].diferencia = productosAjuste[index].stock_fisico - productosAjuste[index].stock_sistema;
            renderizarProductos();
            actualizarResumen();
        }

        function actualizarObservacion(index, valor) {
            productosAjuste[index].observacion = valor;
        }

        function quitarProducto(index) {
            productosAjuste.splice(index, 1);
            renderizarProductos();
            actualizarResumen();
        }

        // Actualizar resumen
        function actualizarResumen() {
            document.getElementById('totalProductos').textContent = productosAjuste.length;
            document.getElementById('totalSobrantes').textContent = productosAjuste.filter(p => p.diferencia > 0).length;
            document.getElementById('totalFaltantes').textContent = productosAjuste.filter(p => p.diferencia < 0).length;
        }

        // Guardar borrador
        function guardarBorrador() {
            enviarFormulario(false);
        }

        // Enviar formulario
        document.getElementById('formAjuste').addEventListener('submit', function(e) {
            e.preventDefault();
            enviarFormulario(true);
        });

        function enviarFormulario(aplicarAhora) {
            if (productosAjuste.length === 0) {
                Swal.fire('Error', 'Debe agregar al menos un producto', 'error');
                return;
            }

            const btn = aplicarAhora ? document.getElementById('btnAplicar') : null;
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...';
            }

            const datos = {
                tipo: document.getElementById('tipo').value,
                motivo: document.getElementById('motivo').value,
                fecha: document.getElementById('fecha').value,
                descripcion: document.getElementById('descripcion').value,
                aplicar_ahora: aplicarAhora,
                productos: productosAjuste
            };

            fetch('{{ route('inventario.ajustes.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(datos)
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: aplicarAhora ? '¡Ajuste aplicado!' : '¡Borrador guardado!',
                            text: data.message,
                            confirmButtonColor: '#0ab39c'
                        }).then(() => {
                            window.location.href = '{{ route('inventario.ajustes.index') }}';
                        });
                    } else {
                        Swal.fire('Error', data.message, 'error');
                        if (btn) {
                            btn.disabled = false;
                            btn.innerHTML = '<i class="ri-check-double-line me-1"></i>Aplicar Ajuste';
                        }
                    }
                })
                .catch(error => {
                    Swal.fire('Error', 'Ocurrió un error al guardar', 'error');
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="ri-check-double-line me-1"></i>Aplicar Ajuste';
                    }
                });
        }
    </script>
@endsection

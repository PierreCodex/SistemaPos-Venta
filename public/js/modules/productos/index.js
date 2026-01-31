/**
 * Módulo JavaScript para Productos
 * Maneja el CRUD con AJAX, DataTables y subida de imágenes (10MB)
 */

let productos = [];
let categorias = [], marcas = [], unidades = [], proveedores = [];
let ROUTES = {};
let CSRF_TOKEN = '';
let dataTable = null;
let editandoId = null;

document.addEventListener('DOMContentLoaded', function () {
    if (window.PRODUCTOS_CONFIG) {
        productos = window.PRODUCTOS_CONFIG.productos || [];
        categorias = window.PRODUCTOS_CONFIG.categorias || [];
        marcas = window.PRODUCTOS_CONFIG.marcas || [];
        unidades = window.PRODUCTOS_CONFIG.unidades || [];
        proveedores = window.PRODUCTOS_CONFIG.proveedores || [];
        ROUTES = window.PRODUCTOS_CONFIG.routes || {};
        CSRF_TOKEN = window.PRODUCTOS_CONFIG.csrfToken || '';
    }

    initDataTable();
    initSelect2();
    initFormEvents();
    initCalculoMargen();
    initBarcodeScanner();
});

// =============================================
// DATATABLES
// =============================================
function initDataTable() {
    dataTable = $('#tablaProductos').DataTable({
        autoWidth: false,
        responsive: true,
        language: { url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json' },
        dom: 'Bfrtip',
        buttons: [
            { extend: 'excel', className: 'buttons-excel d-none', title: 'Reporte de Productos', exportOptions: { columns: ':not(.no-exportar)' } },
            { extend: 'pdf', className: 'buttons-pdf d-none', title: 'Reporte de Productos', exportOptions: { columns: ':not(.no-exportar)' } }
        ]
    });

    $('#btnExportarExcel').on('click', () => dataTable.button('.buttons-excel').trigger());
    $('#btnExportarPDF').on('click', () => dataTable.button('.buttons-pdf').trigger());
}

// =============================================
// SELECT2 (Para los 4 combos del modal)
// =============================================
function initSelect2() {
    const selects = ['#categoria_id', '#marca_id', '#unidad_id'];
    selects.forEach(id => {
        if ($(id).length) {
            $(id).select2({
                dropdownParent: $('#modalProducto'),
                placeholder: '-- Seleccionar --',
                allowClear: true,
                width: '100%'
            });
        }
    });
}

// =============================================
// CÁLCULO DE MARGEN DE GANANCIA
// =============================================
function initCalculoMargen() {
    const precioCompra = document.getElementById('precio_compra');
    const precioVenta = document.getElementById('precio_venta');
    const margenInfo = document.getElementById('margenGanancia');

    if (!precioCompra || !precioVenta || !margenInfo) return;

    function calcularMargen() {
        const compra = parseFloat(precioCompra.value) || 0;
        const venta = parseFloat(precioVenta.value) || 0;

        if (compra > 0 && venta > 0) {
            const ganancia = venta - compra;
            const porcentaje = ((ganancia / compra) * 100).toFixed(1);
            if (ganancia >= 0) {
                margenInfo.innerHTML = `<i class="ri-arrow-up-circle-line me-1"></i>Ganancia: S/${ganancia.toFixed(2)} (${porcentaje}%)`;
                margenInfo.className = 'text-success';
            } else {
                margenInfo.innerHTML = `<i class="ri-arrow-down-circle-line me-1"></i>Pérdida: S/${Math.abs(ganancia).toFixed(2)} (${Math.abs(porcentaje)}%)`;
                margenInfo.className = 'text-danger';
            }
        } else {
            margenInfo.innerHTML = '';
        }
    }

    precioCompra.addEventListener('input', calcularMargen);
    precioVenta.addEventListener('input', calcularMargen);
}

// =============================================
// LECTOR DE CÓDIGO DE BARRAS
// =============================================
function initBarcodeScanner() {
    const modalProducto = document.getElementById('modalProducto');
    const codigoBarrasInput = document.getElementById('codigo_barras');

    if (!modalProducto || !codigoBarrasInput) return;

    // Auto-enfocar en el campo de código de barras cuando se abre el modal (solo para nuevo producto)
    modalProducto.addEventListener('shown.bs.modal', function () {
        // Solo enfocar si estamos creando un nuevo producto (no editando)
        if (editandoId === null) {
            // Pequeño delay para asegurar que el modal esté completamente visible
            setTimeout(() => {
                codigoBarrasInput.focus();
                codigoBarrasInput.select();
            }, 100);
        }
    });

    // Detectar cuando el escáner envía Enter (fin del escaneo)
    codigoBarrasInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault(); // Evitar que el formulario se envíe

            // Si hay valor en el campo, mostrar feedback visual y sonido
            if (this.value.trim()) {
                const codigoEscaneado = this.value.trim();

                // 🔊 Reproducir sonido de beep de confirmación
                reproducirSonidoEscaneo();

                // 📢 Mostrar toast de confirmación
                mostrarToast(`✅ Código escaneado: ${codigoEscaneado}`, 'success');

                // Mostrar un breve feedback visual de escaneo exitoso
                this.classList.add('border-success');
                this.style.backgroundColor = '#d1e7dd';

                // Después de 500ms, mover el foco al siguiente campo (nombre)
                setTimeout(() => {
                    this.classList.remove('border-success');
                    this.style.backgroundColor = '';

                    // Mover el foco al campo nombre
                    const nombreInput = document.getElementById('nombre');
                    if (nombreInput && !nombreInput.value) {
                        nombreInput.focus();
                    }
                }, 500);
            }
        }
    });

    // Opcional: Detectar escaneo rápido (caracteres en menos de 100ms)
    let lastKeyTime = 0;
    let barcodeScanBuffer = '';

    codigoBarrasInput.addEventListener('keypress', function (e) {
        const currentTime = new Date().getTime();
        const timeDiff = currentTime - lastKeyTime;

        // Si el tiempo entre teclas es muy rápido (< 50ms), es probablemente un escáner
        if (timeDiff < 50) {
            barcodeScanBuffer += e.key;
        } else {
            barcodeScanBuffer = e.key;
        }

        lastKeyTime = currentTime;
    });
}

// =============================================
// SONIDO DE ESCANEO (Web Audio API)
// =============================================
function reproducirSonidoEscaneo() {
    try {
        // Crear contexto de audio
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();

        // Crear oscilador para el beep
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();

        // Conectar oscilador -> ganancia -> salida
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);

        // Configurar el sonido (frecuencia 1000Hz = beep agradable)
        oscillator.type = 'sine';
        oscillator.frequency.setValueAtTime(1000, audioContext.currentTime);

        // Configurar volumen (0.3 = 30% del volumen máximo)
        gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);

        // Fade out suave para evitar "click" al final
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.15);

        // Iniciar y detener el sonido
        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + 0.15);

        // Limpiar el contexto después de usar
        oscillator.onended = () => audioContext.close();
    } catch (error) {
        // Si el navegador no soporta Web Audio API, simplemente ignoramos
        console.log('Audio no soportado:', error);
    }
}

// =============================================
// EVENTOS Y SUBMIT (Manejo de Imágenes de 10MB)
// =============================================
function initFormEvents() {
    // Formulario Crear/Editar
    const formProducto = document.getElementById('formProducto');
    if (formProducto) {
        formProducto.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const btn = document.getElementById('btnGuardar');
            const textoNormal = document.getElementById('btnGuardarTexto');
            const spinnerTexto = document.getElementById('btnGuardarSpinner');
            const isEditing = editandoId !== null;

            btn.disabled = true;
            if (textoNormal) textoNormal.classList.add('d-none');
            if (spinnerTexto) spinnerTexto.classList.remove('d-none');

            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
                .then(response => {
                    if (response.ok) return response.json();
                    if (response.status === 422) return response.json().then(data => { throw { validationErrors: data.errors }; });
                    throw new Error('Error del servidor');
                })
                .then(data => {
                    btn.disabled = false;
                    if (textoNormal) textoNormal.classList.remove('d-none');
                    if (spinnerTexto) spinnerTexto.classList.add('d-none');

                    if (data.success) {
                        bootstrap.Modal.getInstance(document.getElementById('modalProducto')).hide();
                        if (isEditing) {
                            actualizarFilaEnTabla(data.data);
                        } else {
                            agregarFilaATabla(data.data);
                        }
                        mostrarToast(data.message, 'success');
                        limpiarFormulario();
                    }
                })
                .catch(error => {
                    btn.disabled = false;
                    if (textoNormal) textoNormal.classList.remove('d-none');
                    if (spinnerTexto) spinnerTexto.classList.add('d-none');

                    if (error.validationErrors) {
                        Object.keys(error.validationErrors).forEach(field => {
                            const input = document.getElementById(field);
                            if (input) input.classList.add('is-invalid');
                            error.validationErrors[field].forEach(message => mostrarToast(message, 'error'));
                        });
                    } else {
                        mostrarToast(error.message || "Error al procesar", 'error');
                    }
                });
        });
    }

    // Formulario Eliminar
    const formEliminar = document.getElementById('formEliminar');
    if (formEliminar) {
        formEliminar.addEventListener('submit', function (e) {
            e.preventDefault();
            fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        bootstrap.Modal.getInstance(document.getElementById('modalEliminar')).hide();
                        eliminarFilaDeTabla(data.id || editandoId);
                        productos = productos.filter(p => p.id !== (data.id || editandoId));
                        mostrarToast(data.message, 'success');
                    }
                })
                .catch(() => mostrarToast("Error al eliminar", 'error'));
        });
    }
}

// =============================================
// ACCIONES (Ver / Editar / Eliminar / Estado)
// =============================================
function verProducto(id) {
    const prod = productos.find(p => p.id === id);
    if (!prod) return;

    document.getElementById('verID').textContent = prod.id;
    document.getElementById('verCodigo').textContent = prod.codigo;
    document.getElementById('verNombre').textContent = prod.nombre;
    document.getElementById('verCategoria').textContent = prod.categoria ? prod.categoria.nombre : '-';
    document.getElementById('verMarca').textContent = prod.marca ? prod.marca.nombre : '-';
    document.getElementById('verPrecioVenta').textContent = `S/ ${parseFloat(prod.precio_venta).toFixed(2)}`;
    document.getElementById('verStock').innerHTML = `<span class="badge ${parseFloat(prod.stock) <= parseFloat(prod.stock_minimo) ? 'bg-danger' : 'bg-success'}">${prod.stock}</span>`;
    document.getElementById('verEstado').innerHTML = prod.estado ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>';

    new bootstrap.Modal(document.getElementById('modalVer')).show();
}

function editarProducto(id) {
    const prod = productos.find(p => p.id === id);
    if (!prod) return;

    limpiarFormulario();
    editandoId = id;
    document.getElementById('modalTitle').innerHTML = '<i class="ri-pencil-line me-2"></i>Editar Producto';
    document.getElementById('formProducto').action = `${ROUTES.store}/${id}`;

    // Crear input oculto para método PUT
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = '_method';
    input.id = 'formMethod';
    input.value = 'PUT';
    document.getElementById('formProducto').appendChild(input);

    const form = document.getElementById('formProducto');
    form.nombre.value = prod.nombre || '';
    form.codigo.value = prod.codigo || '';
    form.codigo_barras.value = prod.codigo_barras || '';
    form.precio_compra.value = prod.precio_compra || '';
    form.precio_venta.value = prod.precio_venta || '';
    form.stock_minimo.value = prod.stock_minimo || 5;
    form.material.value = prod.material || '';
    form.ubicacion.value = prod.ubicacion || '';
    form.descripcion.value = prod.descripcion || '';
    form.stock_inicial.value = prod.stock || 0;
    form.stock_inicial.disabled = true;

    $('#categoria_id').val(prod.categoria_id).trigger('change');
    $('#marca_id').val(prod.marca_id).trigger('change');
    $('#unidad_id').val(prod.unidad_id).trigger('change');

    // Al editar, marcar los pasos 1 y 2 como completados ya que el producto tiene datos
    marcarPasosComoCompletados();

    new bootstrap.Modal(document.getElementById('modalProducto')).show();
}

function eliminarProducto(id, nombre) {
    editandoId = id;
    document.getElementById('nombreEliminar').textContent = nombre;
    document.getElementById('formEliminar').action = `${ROUTES.store}/${id}`;
    new bootstrap.Modal(document.getElementById('modalEliminar')).show();
}

function toggleEstado(id) {
    fetch(`${ROUTES.store}/${id}/toggle-estado`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const p = productos.find(x => x.id === id);
                if (p) p.estado = data.estado;

                const badge = document.getElementById(`estado-badge-${id}`);
                if (badge) {
                    badge.innerHTML = data.estado ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>';
                }
                mostrarToast(data.message, 'success');
            }
        })
        .catch(() => mostrarToast("Error al cambiar estado", "error"));
}

// =============================================
// UI UPDATES
// =============================================
function eliminarFilaDeTabla(id) {
    const row = $(`tr[data-id="${id}"]`);
    if (row.length) {
        dataTable.row(row).remove().draw(false);
    }
}

function agregarFilaATabla(prod) {
    productos.push(prod);
    const rowHtml = generarFilaHTML(prod);
    dataTable.row.add($(rowHtml)).draw(false);
}

function actualizarFilaEnTabla(prod) {
    const index = productos.findIndex(p => p.id === prod.id);
    if (index !== -1) productos[index] = prod;

    const row = $(`tr[data-id="${prod.id}"]`);
    if (row.length) {
        const rowIndex = dataTable.row(row).index();
        dataTable.row(rowIndex).remove();
        const rowHtml = generarFilaHTML(prod);
        dataTable.row.add($(rowHtml)).draw(false);
    }
}
function limpiarFormulario() {
    document.getElementById('modalTitle').innerHTML = '<i class="ri-shopping-basket-2-line me-2"></i>Nuevo Producto';
    document.getElementById('formProducto').reset();
    document.getElementById('formProducto').action = ROUTES.store;
    editandoId = null;

    if (document.getElementById('formMethod')) {
        document.getElementById('formMethod').remove();
    }

    const stockInput = document.querySelector('input[name="stock_inicial"]');
    if (stockInput) {
        stockInput.disabled = false;
        stockInput.value = '';
    }

    $('#categoria_id, #marca_id, #unidad_id').val('').trigger('change');

    // Reiniciar los pasos del wizard
    irAPaso(1, true);
    resetearIndicadoresPasos();

    // Ocultar alerta de campos
    const alerta = document.getElementById('alertaCampos');
    if (alerta) alerta.classList.add('d-none');
}

// =============================================
// NAVEGACIÓN POR PASOS (WIZARD)
// =============================================
let pasoActual = 1;

function irAPaso(paso, forzar = false) {
    // Validar paso actual antes de avanzar (solo si avanzamos y no forzamos)
    if (!forzar && paso > pasoActual && !validarPasoActual()) {
        return false;
    }

    // Ocultar todos los tabs
    document.querySelectorAll('.tab-pane').forEach(tab => {
        tab.classList.remove('show', 'active');
    });

    // Mostrar el tab correspondiente
    const tabIds = ['tab-general', 'tab-precios', 'tab-adicional'];
    const tabDestino = document.getElementById(tabIds[paso - 1]);
    if (tabDestino) {
        tabDestino.classList.add('show', 'active');
    }

    // Actualizar indicadores de paso
    actualizarIndicadoresPaso(paso);
    pasoActual = paso;
    return true;
}

function validarPasoActual() {
    const camposIncompletos = [];
    let camposAValidar;

    if (pasoActual === 1) {
        camposAValidar = document.querySelectorAll('.campo-paso1');
    } else if (pasoActual === 2) {
        camposAValidar = document.querySelectorAll('.campo-paso2');
    } else {
        return true; // Paso 3 es opcional
    }

    camposAValidar.forEach(campo => {
        if (!campo.value || campo.value.trim() === '') {
            campo.classList.add('campo-incompleto');
            camposIncompletos.push(campo);
        } else {
            campo.classList.remove('campo-incompleto');
        }
    });

    if (camposIncompletos.length > 0) {
        const alerta = document.getElementById('alertaCampos');
        const alertaTexto = document.getElementById('alertaCamposTexto');
        if (alerta) {
            alertaTexto.textContent = `Faltan ${camposIncompletos.length} campo(s) obligatorios por completar`;
            alerta.classList.remove('d-none');
        }
        // Focus en el primer campo incompleto
        camposIncompletos[0].focus();

        // Remover clase de animación después de que termine
        setTimeout(() => {
            camposIncompletos.forEach(c => c.classList.remove('campo-incompleto'));
        }, 600);

        return false;
    }

    // Ocultar alerta si todo está ok
    const alerta = document.getElementById('alertaCampos');
    if (alerta) alerta.classList.add('d-none');

    return true;
}

function actualizarIndicadoresPaso(pasoDestino) {
    for (let i = 1; i <= 3; i++) {
        const circulo = document.getElementById(`stepCircle${i}`);
        const linea = document.getElementById(`stepLine${i}`);

        if (circulo) {
            circulo.classList.remove('active', 'completed');

            if (i < pasoDestino) {
                circulo.classList.add('completed');
            } else if (i === pasoDestino) {
                circulo.classList.add('active');
            }
        }

        if (linea) {
            linea.classList.remove('completed');
            if (i < pasoDestino) {
                linea.classList.add('completed');
            }
        }
    }
}

function resetearIndicadoresPasos() {
    pasoActual = 1;
    for (let i = 1; i <= 3; i++) {
        const circulo = document.getElementById(`stepCircle${i}`);
        const linea = document.getElementById(`stepLine${i}`);
        if (circulo) circulo.classList.remove('active', 'completed');
        if (linea) linea.classList.remove('completed');
    }
    const primerCirculo = document.getElementById('stepCircle1');
    if (primerCirculo) primerCirculo.classList.add('active');
}

function marcarPasosComoCompletados() {
    // Marcar pasos 1 y 2 como completados
    for (let i = 1; i <= 2; i++) {
        const circulo = document.getElementById(`stepCircle${i}`);
        const linea = document.getElementById(`stepLine${i}`);
        if (circulo) {
            circulo.classList.remove('active');
            circulo.classList.add('completed');
        }
        if (linea) {
            linea.classList.add('completed');
        }
    }
    // Activar paso 1 para empezar ahí
    const primerCirculo = document.getElementById('stepCircle1');
    if (primerCirculo) {
        primerCirculo.classList.remove('completed');
        primerCirculo.classList.add('active');
    }
}

// =============================================
// GENERADOR DE FILA HTML PARA DATATABLES
// =============================================
function generarFilaHTML(prod) {
    // Usar la base URL correcta para las imágenes
    const baseUrl = window.location.pathname.replace(/\/productos.*$/, '');
    const imgPath = prod.imagen
        ? `${baseUrl}/storage/productos/${prod.imagen}`
        : null;

    // Escapar comillas simples en el nombre para el onclick
    const nombreEscapado = prod.nombre ? prod.nombre.replace(/'/g, "\\'") : '';

    return `
        <tr data-id="${prod.id}">
            <td>
                <span class="badge bg-light text-primary">${prod.codigo}</span><br>
                <small class="text-muted">${prod.codigo_barras || 'Sin barras'}</small>
            </td>
            <td>
                <div class="d-flex align-items-center">
                    ${imgPath
            ? `<img src="${imgPath}" class="avatar-xs rounded me-2">`
            : `<div class="avatar-xs me-2"><span class="avatar-title rounded bg-soft-warning text-warning">P</span></div>`
        }
                    <strong>${prod.nombre}</strong>
                </div>
            </td>
            <td>${prod.categoria ? prod.categoria.nombre : '-'}</td>
            <td>${prod.marca ? prod.marca.nombre : '-'}</td>
            <td>
                <span class="badge ${parseFloat(prod.stock) <= parseFloat(prod.stock_minimo) ? 'bg-danger' : 'bg-success'}">
                    ${parseFloat(prod.stock).toFixed(2)} ${prod.unidad ? prod.unidad.codigo : ''}
                </span>
            </td>
            <td><strong>S/ ${parseFloat(prod.precio_venta).toFixed(2)}</strong></td>
            <td id="estado-badge-${prod.id}">
                ${prod.estado ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>'}
            </td>
            <td>
                <div class="d-flex gap-1">
                    <button type="button" class="btn btn-sm btn-warning" onclick="editarProducto(${prod.id})" title="Editar">
                        <i class="ri-pencil-line"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="eliminarProducto(${prod.id}, '${nombreEscapado}')" title="Eliminar">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </div>
            </td>
        </tr>
    `;
}

function mostrarToast(mensaje, tipo = 'success') {
    const colors = {
        success: "linear-gradient(to right, #0ab39c, #0ab39c)",
        error: "linear-gradient(to right, #f06548, #f06548)",
        warning: "linear-gradient(to right, #f7b84b, #f7b84b)"
    };

    if (typeof Toastify !== 'undefined') {
        Toastify({
            text: mensaje,
            duration: 3000,
            gravity: "top",
            position: "center",
            close: true,
            style: { background: colors[tipo] || colors.success }
        }).showToast();
    } else {
        // Fallback a Swal si Toastify no está (aunque el usuario prefiere Toastify)
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: tipo,
                title: mensaje,
                showConfirmButton: false,
                timer: 3000
            });
        } else {
            alert(mensaje);
        }
    }
}
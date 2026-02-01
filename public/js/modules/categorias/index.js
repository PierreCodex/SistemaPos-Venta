/**
 * Módulo JavaScript para Categorías (Subcategorías)
 * 
 * Maneja el CRUD completo con AJAX y DataTables
 * 
 * Requiere que la vista declare:
 * - window.CATEGORIAS_CONFIG = { categorias, categoriasGlobales, routes, csrfToken }
 */

// =============================================
// VARIABLES GLOBALES
// =============================================
let categorias = [];
let categoriasGlobales = [];
let ROUTES = {};
let CSRF_TOKEN = '';
let dataTable = null;
let editandoId = null;

// =============================================
// INICIALIZACIÓN
// =============================================
document.addEventListener('DOMContentLoaded', function () {
    // Obtener configuración pasada desde Blade
    if (window.CATEGORIAS_CONFIG) {
        categorias = window.CATEGORIAS_CONFIG.categorias || [];
        categoriasGlobales = window.CATEGORIAS_CONFIG.categoriasGlobales || [];
        ROUTES = window.CATEGORIAS_CONFIG.routes || {};
        CSRF_TOKEN = window.CATEGORIAS_CONFIG.csrfToken || '';
    }

    // Inicializar DataTables
    initDataTable();

    // Inicializar Select2
    initSelect2();

    // Inicializar eventos
    initFormEvents();
});

// =============================================
// DATATABLES
// =============================================
function initDataTable() {
    dataTable = $('#tablaCategorias').DataTable({
        autoWidth: false,
        responsive: true,

        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
        },
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excel',
                // AGREGAMOS 'd-none' para que este botón sea invisible en la interfaz
                className: 'buttons-excel d-none',
                title: 'Reporte de Categorías',
                exportOptions: {
                    // ESTO ES CLAVE: Excluye la última columna (Acciones) y formatea
                    columns: ':not(.no-exportar)',

                    // 2. FORMATEO DE DATOS
                    format: {
                        body: function (data, row, column, node) {
                            // Verificamos si la data es válida
                            if (!data) return '';
                            // Reemplaza cualquier etiqueta HTML (<span...>, </div>, etc) por vacío
                            // y luego quita los espacios en blanco sobrantes (.trim())
                            return typeof data === 'string'
                                ? data.replace(/<[^>]+>/g, '').trim()
                                : data;
                        }
                    }
                }
            },
            {
                extend: 'pdf',
                className: 'buttons-pdf d-none', // También lo ocultamos
                title: 'Reporte de Categorías',
                exportOptions: {
                    // ESTO ES CLAVE: Excluye la última columna (Acciones) y formatea
                    columns: ':not(.no-exportar)',

                    // 2. FORMATEO DE DATOS
                    format: {
                        body: function (data, row, column, node) {
                            // Verificamos si la data es válida
                            if (!data) return '';
                            // Reemplaza cualquier etiqueta HTML (<span...>, </div>, etc) por vacío
                            // y luego quita los espacios en blanco sobrantes (.trim())
                            return typeof data === 'string'
                                ? data.replace(/<[^>]+>/g, '').trim()
                                : data;
                        }
                    }
                }
            },
            {
                extend: 'print',
                className: 'buttons-print d-none', // También lo ocultamos
                text: 'Imprimir',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            }
        ],
        columnDefs: [{
            orderable: false,
            targets: -1 // -1 selecciona siempre la última columna (Acciones)
        }]
    });

    // 1. Conectar tu botón de EXCEL
    $('#btnExportarExcel').on('click', function () {
        dataTable.button('.buttons-excel').trigger();
    });

    // 2. Conectar tu botón de PDF
    $('#btnExportarPDF').on('click', function () {
        dataTable.button('.buttons-pdf').trigger();
    });
}

// =============================================
// SELECT2
// =============================================
function initSelect2() {
    // Inicializar Select2 en el select de categorías globales
    // Usa el estilo por defecto de Select2 (como en la plantilla forms-select2)
    $('#categoria_global_id').select2({
        dropdownParent: $('#modalCategoria'), // Importante: para que funcione dentro del modal
        placeholder: '-- Seleccionar --',
        allowClear: true
    });
}

// =============================================
// EVENTOS DE FORMULARIOS
// =============================================
function initFormEvents() {
    // Formulario Crear/Editar
    const formCategoria = document.getElementById('formCategoria');
    if (formCategoria) {
        formCategoria.addEventListener('submit', handleFormSubmit);
    }

    // Formulario Eliminar
    const formEliminar = document.getElementById('formEliminar');
    if (formEliminar) {
        formEliminar.addEventListener('submit', handleDeleteSubmit);
    }
}

function handleFormSubmit(e) {
    e.preventDefault();

    const form = this;
    const formData = new FormData(form);
    const btn = document.getElementById('btnGuardar');
    const textoNormal = document.getElementById('btnGuardarTexto');
    const spinnerTexto = document.getElementById('btnGuardarSpinner');
    const isEditing = editandoId !== null;

    // Mostrar spinner
    btn.disabled = true;
    textoNormal.classList.add('d-none');
    spinnerTexto.classList.remove('d-none');

    // Quitar errores anteriores
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
        .then(response => {
            if (response.ok) {
                return response.json();
            } else if (response.status === 422) {
                return response.json().then(data => {
                    throw { validationErrors: data.errors };
                });
            } else {
                throw new Error('Error del servidor');
            }
        })
        .then(data => {
            if (data.success) {
                // Cerrar modal
                bootstrap.Modal.getInstance(document.getElementById('modalCategoria')).hide();

                if (isEditing) {
                    actualizarFilaEnTabla(data.categoria);
                    actualizarArrayLocal(data.categoria);
                } else {
                    agregarFilaATabla(data.categoria);
                    categorias.push(data.categoria);
                }

                mostrarToast(data.message, 'success');
                limpiarFormulario();
            }
        })
        .catch(error => {
            btn.disabled = false;
            textoNormal.classList.remove('d-none');
            spinnerTexto.classList.add('d-none');

            if (error.validationErrors) {
                Object.keys(error.validationErrors).forEach(field => {
                    const input = document.getElementById(field);
                    if (input) input.classList.add('is-invalid');

                    error.validationErrors[field].forEach(message => {
                        mostrarToast(message, 'error');
                    });
                });
            } else {
                mostrarToast("Error al procesar la solicitud", 'error');
            }
        });
}

function handleDeleteSubmit(e) {
    e.preventDefault();

    const form = this;
    const formData = new FormData(form);

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
        .then(response => {
            if (response.ok) return response.json();
            return response.json().then(data => { throw data; });
        })
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalEliminar')).hide();
                eliminarFilaDeTabla(data.id);
                categorias = categorias.filter(c => c.id !== data.id);
                mostrarToast(data.message, 'success');
            } else {
                mostrarToast(data.message || "Error al eliminar", 'error');
            }
        })
        .catch(error => {
            console.error(error);
            mostrarToast(error.message || "Error al eliminar", 'error');
        });
}

// =============================================
// FUNCIONES CRUD
// =============================================
function limpiarFormulario() {
    document.getElementById('modalTitle').textContent = 'Nueva Categoría';
    document.getElementById('formCategoria').reset();
    document.getElementById('formCategoria').action = ROUTES.store;
    document.getElementById('formMethod').value = 'POST';
    editandoId = null;

    // Resetear Select2
    $('#categoria_global_id').val(null).trigger('change');

    // Resetear botón
    document.getElementById('btnGuardar').disabled = false;
    document.getElementById('btnGuardarTexto').classList.remove('d-none');
    document.getElementById('btnGuardarSpinner').classList.add('d-none');

    // Quitar errores
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
}

function editarCategoria(id) {
    const cat = categorias.find(c => c.id === id);
    if (!cat) return;

    editandoId = id;
    document.getElementById('modalTitle').textContent = 'Editar Categoría';
    document.getElementById('formCategoria').action = `${ROUTES.store}/${id}`;
    document.getElementById('formMethod').value = 'PUT';

    // Actualizar Select2 con el valor
    $('#categoria_global_id').val(cat.categoria_global_id).trigger('change');

    document.getElementById('nombre').value = cat.nombre;
    document.getElementById('descripcion').value = cat.descripcion || '';

    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    new bootstrap.Modal(document.getElementById('modalCategoria')).show();
}

function verCategoria(id) {
    const cat = categorias.find(c => c.id === id);
    if (!cat) return;

    const global = categoriasGlobales.find(g => g.id === cat.categoria_global_id);

    document.getElementById('verID').textContent = cat.id;
    document.getElementById('verGlobal').innerHTML = global ?
        `<span class="badge bg-primary">${global.nombre}</span>` : '-';
    document.getElementById('verNombre').textContent = cat.nombre;
    document.getElementById('verDescripcion').textContent = cat.descripcion || 'Sin descripción';
    document.getElementById('verEstado').innerHTML = cat.estado ?
        '<span class="badge bg-success">Activo</span>' :
        '<span class="badge bg-danger">Inactivo</span>';

    new bootstrap.Modal(document.getElementById('modalVer')).show();
}

function eliminarCategoria(id, nombre) {
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
                const categoria = categorias.find(c => c.id === id);
                if (categoria) categoria.estado = data.estado ? 1 : 0;

                const badgeCell = document.getElementById(`estado-badge-${id}`);
                badgeCell.innerHTML = data.estado ?
                    '<span class="badge bg-success">Activo</span>' :
                    '<span class="badge bg-danger">Inactivo</span>';

                mostrarToast(data.message, data.estado ? 'success' : 'warning');
            }
        })
        .catch(error => {
            const checkbox = document.getElementById(`toggle-estado-${id}`);
            checkbox.checked = !checkbox.checked;
            mostrarToast("Error al cambiar el estado", 'error');
        });
}

// =============================================
// FUNCIONES DATATABLES
// =============================================
function agregarFilaATabla(cat) {
    const rowHtml = generarFilaHTML(cat);
    dataTable.row.add($(rowHtml)).draw(false);
}

function actualizarFilaEnTabla(cat) {
    const row = $(`tr[data-id="${cat.id}"]`);
    if (row.length) {
        const rowIndex = dataTable.row(row).index();
        dataTable.row(rowIndex).remove();
        const rowHtml = generarFilaHTML(cat);
        dataTable.row.add($(rowHtml)).draw(false);
    }
}

function eliminarFilaDeTabla(id) {
    const row = $(`tr[data-id="${id}"]`);
    if (row.length) {
        dataTable.row(row).remove().draw(false);
    }
}

function actualizarArrayLocal(cat) {
    const index = categorias.findIndex(c => c.id === cat.id);
    if (index !== -1) {
        categorias[index] = { ...categorias[index], ...cat };
    }
}

function generarFilaHTML(cat) {
    return `
        <tr data-id="${cat.id}">
            <td><span class="badge bg-primary">${cat.categoria_global_nombre}</span></td>
            <td><strong>${cat.nombre}</strong></td>
            <td>${cat.descripcion ? cat.descripcion.substring(0, 40) : '-'}</td>
            <td id="estado-badge-${cat.id}">
                ${cat.estado ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>'}
            </td>
            <td class="no-exportar">
                <div class="d-flex gap-1 text-center justify-content-center">
                    <button type="button" class="btn btn-sm btn-info" onclick="verCategoria(${cat.id})" title="Ver">
                        <i class="ri-eye-line"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-warning" onclick="editarCategoria(${cat.id})" title="Editar">
                        <i class="ri-pencil-line"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="eliminarCategoria(${cat.id}, '${cat.nombre}')" title="Eliminar">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </div>
            </td>
            <td class="no-exportar">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="toggle-estado-${cat.id}"
                        ${cat.estado ? 'checked' : ''} onchange="toggleEstado(${cat.id})">
                </div>
            </td>

        </tr>
    `;
}

// =============================================
// UTILIDADES
// =============================================
function mostrarToast(message, type) {
    const colors = {
        success: "linear-gradient(to right, #0ab39c, #0ab39c)",
        error: "linear-gradient(to right, #f06548, #f06548)",
        warning: "linear-gradient(to right, #f7b84b, #f7b84b)"
    };

    Toastify({
        text: message,
        duration: 3000,
        gravity: "top",
        position: "center",
        close: true,
        style: { background: colors[type] || colors.success }
    }).showToast();
}

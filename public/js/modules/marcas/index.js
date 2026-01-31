/**
 * Módulo JavaScript para Categorías (Subcategorías)
 * 
 * Maneja el CRUD completo con AJAX y DataTables
 * 
 * Requiere que la vista declare:
 * - window.MARCAS_CONFIG = { marcas, routes, csrfToken }
 */

// =============================================
// VARIABLES GLOBALES
// =============================================
let marcas = [];
let ROUTES = {};
let CSRF_TOKEN = '';
let dataTable = null;
let editandoId = null;

// =============================================
// INICIALIZACIÓN
// =============================================
document.addEventListener('DOMContentLoaded', function () {
    // Obtener configuración pasada desde Blade
    if (window.MARCAS_CONFIG) {
        marcas = window.MARCAS_CONFIG.marcas || [];
        ROUTES = window.MARCAS_CONFIG.routes || {};
        CSRF_TOKEN = window.MARCAS_CONFIG.csrfToken || '';
    }

    // Inicializar DataTables
    initDataTable();

    // Inicializar eventos
    initFormEvents();
});

// =============================================
// DATATABLES
// =============================================
function initDataTable() {
    dataTable = $('#tablaMarcas').DataTable({
        autoWidth: false,
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
        },
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excel',
                className: 'buttons-excel d-none',
                title: 'Reporte de Marcas',
                exportOptions: {
                    columns: ':not(.no-exportar)',
                    format: {
                        body: function (data, row, column, node) {
                            if (!data) return '';
                            return typeof data === 'string'
                                ? data.replace(/<[^>]+>/g, '').trim()
                                : data;
                        }
                    }
                }
            },
            {
                extend: 'pdf',
                className: 'buttons-pdf d-none',
                title: 'Reporte de Marcas',
                exportOptions: {
                    columns: ':not(.no-exportar)',
                    format: {
                        body: function (data, row, column, node) {
                            if (!data) return '';
                            return typeof data === 'string'
                                ? data.replace(/<[^>]+>/g, '').trim()
                                : data;
                        }
                    }
                }
            },
            {
                extend: 'print',
                className: 'buttons-print d-none',
                text: 'Imprimir',
                exportOptions: {
                    columns: ':not(.no-exportar)'
                }
            }
        ],
        columnDefs: [{
            orderable: false,
            targets: [-1, -2]
        }]
    });

    // Conectar botones personalizados
    $('#btnExportarExcel').on('click', function () {
        dataTable.button('.buttons-excel').trigger();
    });

    $('#btnExportarPDF').on('click', function () {
        dataTable.button('.buttons-pdf').trigger();
    });
}

// =============================================
// EVENTOS DE FORMULARIOS
// =============================================
function initFormEvents() {
    // Formulario Crear/Editar
    const formMarca = document.getElementById('formMarca');
    if (formMarca) {
        formMarca.addEventListener('submit', handleFormSubmit);
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
                bootstrap.Modal.getInstance(document.getElementById('modalMarca')).hide();

                if (isEditing) {
                    actualizarFilaEnTabla(data.marca);
                    actualizarArrayLocal(data.marca);
                } else {
                    agregarFilaATabla(data.marca);
                    marcas.push(data.marca);
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
                marcas = marcas.filter(c => c.id !== data.id);
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
    document.getElementById('modalTitle').textContent = 'Nueva Marca';
    document.getElementById('formMarca').reset();
    document.getElementById('formMarca').action = ROUTES.store;
    document.getElementById('formMethod').value = 'POST';
    editandoId = null;

    // Resetear botón
    document.getElementById('btnGuardar').disabled = false;
    document.getElementById('btnGuardarTexto').classList.remove('d-none');
    document.getElementById('btnGuardarSpinner').classList.add('d-none');

    // Quitar errores
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
}

function editarMarca(id) {
    const marca = marcas.find(c => c.id === id);
    if (!marca) return;

    editandoId = id;
    document.getElementById('modalTitle').textContent = 'Editar Marca';
    document.getElementById('formMarca').action = `${ROUTES.store}/${id}`;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('codigo').value = marca.codigo;
    document.getElementById('nombre').value = marca.nombre;
    document.getElementById('descripcion').value = marca.descripcion || '';

    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    new bootstrap.Modal(document.getElementById('modalMarca')).show();
}

function verMarca(id) {
    const marca = marcas.find(c => c.id === id);
    if (!marca) return;

    document.getElementById('verID').textContent = marca.id;
    document.getElementById('verCodigo').textContent = marca.codigo;
    document.getElementById('verNombre').textContent = marca.nombre;
    document.getElementById('verDescripcion').textContent = marca.descripcion || 'Sin descripción';
    document.getElementById('verEstado').innerHTML = marca.estado ?
        '<span class="badge bg-success">Activo</span>' :
        '<span class="badge bg-danger">Inactivo</span>';

    new bootstrap.Modal(document.getElementById('modalVer')).show();
}

function eliminarMarca(id, nombre) {
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
                const marca = marcas.find(c => c.id === id);
                if (marca) marca.estado = data.estado ? 1 : 0;

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
function agregarFilaATabla(marca) {
    const rowHtml = generarFilaHTML(marca);
    dataTable.row.add($(rowHtml)).draw(false);
}

function actualizarFilaEnTabla(marca) {
    const row = $(`tr[data-id="${marca.id}"]`);
    if (row.length) {
        const rowIndex = dataTable.row(row).index();
        dataTable.row(rowIndex).remove();
        const rowHtml = generarFilaHTML(marca);
        dataTable.row.add($(rowHtml)).draw(false);
    }
}

function eliminarFilaDeTabla(id) {
    const row = $(`tr[data-id="${id}"]`);
    if (row.length) {
        dataTable.row(row).remove().draw(false);
    }
}

function actualizarArrayLocal(marca) {
    const index = marcas.findIndex(c => c.id === marca.id);
    if (index !== -1) {
        marcas[index] = { ...marcas[index], ...marca };
    }
}

function generarFilaHTML(marca) {
    return `
        <tr data-id="${marca.id}">
            <td><strong>${marca.codigo}</strong></td>
            <td><h5><span class="badge bg-primary">${marca.nombre ?? '-'}</span></h5></td>
            <td>${marca.descripcion ? '<i class="bx bx-comment-dots me-1"></i>' + marca.descripcion.substring(0, 40) : '-'}</td>
            <td id="estado-badge-${marca.id}">
                ${marca.estado ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>'}
            </td>
            <td>
                <div class="d-flex gap-1">
                    <button type="button" class="btn btn-sm btn-info" onclick="verMarca(${marca.id})" title="Ver">
                        <i class="ri-eye-line"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-warning" onclick="editarMarca(${marca.id})" title="Editar">
                        <i class="ri-pencil-line"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="eliminarMarca(${marca.id}, '${marca.nombre}')" title="Eliminar">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </div>
            </td>
            <td>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="toggle-estado-${marca.id}"
                        ${marca.estado ? 'checked' : ''} onchange="toggleEstado(${marca.id})">
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

/**
 * Genera un código aleatorio para marcas
 * Formato: MRC-XXXXXX (6 caracteres alfanuméricos)
 */
function generarCodigo() {
    const prefijo = 'MRC';
    const caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let codigo = '';

    for (let i = 0; i < 6; i++) {
        codigo += caracteres.charAt(Math.floor(Math.random() * caracteres.length));
    }

    document.getElementById('codigo').value = `${prefijo}-${codigo}`;
}

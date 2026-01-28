// ALERTAS DE AUTENTICACION
function showAlert() {
    Swal.fire({
        title: "Error!",
        text: "Usuario y/o contraseña incorrecta, intente de nuevo.",
        icon: "warning",
        confirmButtonText: "Ok",
        confirmButtonColor: "#3085d6",
        backdrop: false,
    });
}
// FIN DE ALERTAS DE AUTENTICACION

// ALERTAS DE LAS ORDENES

function confirmarCompletar(id) {
    Swal.fire({
        title: "¿Completar orden?",
        text: "Esta acción no se puede revertir.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, completar",
    }).then((result) => {
        if (result.isConfirmed) {
            enviarAccion(`/ordenes/${id}/completar`, "PUT");
        }
    });
}

function confirmarCancelar(id) {
    Swal.fire({
        title: "¿Cancelar orden?",
        text: "La orden será cancelada.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            enviarAccion(`/ordenes/${id}/cancelar`, "PUT");
        }
    });
}

function confirmarEliminar(id) {
    Swal.fire({
        title: "¿Eliminar orden?",
        text: "Esta acción es permanente.",
        icon: "error",
        showCancelButton: true,
        confirmButtonText: "Eliminar",
    }).then((result) => {
        if (result.isConfirmed) {
            enviarAccion(`/ordenes/${id}`, "DELETE");
        }
    });
}

function enviarAccion(url, method) {
    const token = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    const form = document.createElement("form");
    form.method = "POST";
    form.action = url;

    form.innerHTML = `
        <input type="hidden" name="_token" value="${token}">
        <input type="hidden" name="_method" value="${method}">
    `;

    document.body.appendChild(form);
    form.submit();
}

function alertaPacienteRequerido() {
    Swal.fire({
        icon: "warning",
        title: "Paciente requerido",
        text: "Debe seleccionar un paciente antes de guardar la orden.",
        confirmButtonText: "Ok",
    });
}

function alertaOrdenRequerido() {
    Swal.fire({
        icon: "warning",
        title: "Orden requerida",
        text: "Debe seleccionar una orden antes de guardar el consumo.",
        confirmButtonText: "Ok",
    });
}

function alertaHabitacionRequerida() {
    Swal.fire({
        icon: "warning",
        title: "Habitación requerida",
        text: "Debe seleccionar una habitación antes de registrar el consumo.",
        confirmButtonText: "Ok",
    });
}

// FIN DE ALERTAS DE LAS ORDENES

// Alertas de success, errores y advertencias 

function alertSuccess(message) {
    Swal.fire({
        icon: 'success',
        title: 'Éxito',
        text: message,
        confirmButtonColor: '#16a34a'
    });
}

function alertError(message) {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: message,
        confirmButtonColor: '#dc2626'
    });
}

function alertWarning(message) {
    Swal.fire({
        icon: 'warning',
        title: 'Advertencia',
        text: message,
        confirmButtonColor: '#f59e0b'
    });
}

// Fin alertas de success, errores y advertencias 

//Cerrar Consumo

function cerrarConsumo(id) {
     Swal.fire({
        title: '¿Cerrar consumos?',
        text: 'Esto cerrará los consumos mostrados y solo podrá verlos en el módulo de REPORTES.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, cerrar y generar PDF',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formCerrarConsumo').submit();
        }
    });
}

//Fin Cerrar Consumo
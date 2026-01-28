/* ===============================
   VALIDACIONES GENERALES
   =============================== */

// Solo letras
function soloLetras(event, errorId) {
    const key = event.key;
    const regex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]$/;
    const error = document.getElementById(errorId);

    if (!regex.test(key)) {
        event.preventDefault();

        if (error) {
            error.classList.remove("d-none");
            setTimeout(() => {
                error.classList.add("d-none");
            }, 3000);
        }
    }
}

// Fin Solo letras

function soloNumeros(event, errorId) {
    const key = event.key;
    const error = document.getElementById(errorId);

    if (!/[0-9]/.test(key)) {
        event.preventDefault();

        if (error) {
            error.classList.remove("d-none");

            setTimeout(() => {
                error.classList.add("d-none");
            }, 3000);
        }
    }
}

/**
 * NO permite números (solo letras, símbolos y espacios)
 */
function noNumeros(event) {
    if (/[0-9]/.test(event.key)) {
        event.preventDefault();
    }
}

/**
 * NO permite letras (solo números y símbolos)
 */
function noLetras(event) {
    if (/[a-zA-ZáéíóúÁÉÍÓÚñÑ]/.test(event.key)) {
        event.preventDefault();
    }
}

/**
 * Permite números con un solo punto decimal
 */
function numeroDecimal(event, errorId) {
    const key = event.key;
    const input = event.target;
    const regex = /^[0-9.]$/;
    const error = document.getElementById(errorId);

    // Bloquear caracteres inválidos
    if (!regex.test(key)) {
        event.preventDefault();
        error.classList.remove("d-none");
        setTimeout(() => {
            error.classList.add("d-none");
        }, 3000);
    }

    // Evitar más de un punto
    if (key === "." && input.value.includes(".")) {
        event.preventDefault();
        error.classList.remove("d-none");
        setTimeout(() => {
            error.classList.add("d-none");
        }, 3000);
    }
}

/**
 * Bloquea pegar texto inválido
 */
function bloquearPegado(event) {
    event.preventDefault();
}

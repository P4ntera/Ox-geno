
document.addEventListener('DOMContentLoaded', function () {
    const errorMessages = document.querySelectorAll('.invalid-feedback');

    errorMessages.forEach(error => {
        setTimeout(() => {
            error.style.display = 'none'; 
            const input = error.previousElementSibling; // Buscar el input asociado
            if (input && input.classList.contains('is-invalid')) {
                input.classList.remove('is-invalid'); // Remover la clase de error
            }
        }, 7000); // Se oculta después de 5 segundos
    });
});


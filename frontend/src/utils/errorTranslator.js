/**
 * Traductor de errores de Laravel a Español
 */
export const translateError = (msg) => {
    if (!msg) return msg;

    const dictionary = {
        'The email field is required.': 'El campo de correo electrónico es obligatorio.',
        'The password field is required.': 'El campo de contraseña es obligatorio.',
        'The name field is required.': 'El nombre es un campo obligatorio.',
        'The email must be a valid email address.': 'Debes introducir un correo electrónico válido.',
        'The password confirmation does not match.': 'La confirmación de la contraseña no coincide.',
        'The selected email is invalid.': 'El correo seleccionado no es válido.',
        'The email has already been taken.': 'Este correo electrónico ya está registrado.',
        'The password must be at least 8 characters.': 'La contraseña debe tener al menos 8 caracteres.',
        'These credentials do not match our records.': 'Estas credenciales no coinciden con nuestros registros.',
        'The address_id field is required.': 'Debes seleccionar una dirección de envío.',
        'The cart field is required.': 'Tu carrito no puede estar vacío.',
        'Unauthorized': 'No autorizado - Por favor, inicia sesión.',
        'Forbidden': 'Acceso prohibido.',
        'Not Found': 'Recurso no encontrado.',
        'Page Expired': 'La sesión ha expirado, por favor recarga la página.',
        'Server Error': 'Error interno del servidor. Inténtalo de nuevo más tarde.',
        'pending': 'Pendiente',
        'completed': 'Completado',
        'cancelled': 'Cancelado'
    };

    // Intentar traducción exacta
    if (dictionary[msg]) return dictionary[msg];

    // Traducciones parciales dinámicas
    let translated = msg;
    translated = translated.replace('The ', 'El campo ');
    translated = translated.replace(' field is required.', ' es obligatorio.');
    translated = translated.replace('has already been taken.', 'ya está en uso.');
    
    return translated;
};

export const translateLaravelErrors = (errors) => {
    if (!errors) return '';
    if (typeof errors === 'string') return translateError(errors);
    
    return Object.values(errors)
        .flat()
        .map(msg => translateError(msg))
        .join(' ');
};

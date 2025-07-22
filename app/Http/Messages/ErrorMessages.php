<?php

namespace App\Http\Messages;

class ErrorMessages
{
    public const NOT_FOUND = 'No se encontro el recurso';
    public const UNAUTHORIZED = 'No tiene permisos para realizar esta accion';
    public const BAD_REQUEST = 'Solicitud invalida';

    public const UNPROCESSABLE_ENTITY = 'Error de validacion';
    public const INVALID_CREDENTIALS = "Credenciales Invalidas";
    public const USER_ALREADY_EXISTS = 'El usuario ya existe';
    public const EMAIL_ALREADY_EXISTS = 'El correo ya fue asociado a otro user';

    public const NOT_FOUND_ROL = 'EL usuario no fue registrado como ';

    public const INVALID_PASSWORD = 'El password actual es incorrecto';
    public const USER_NOT_HAVE_PASSWORD = 'El usuario no se registro con contraseña';
    public const EMAIL_NOT_VERIFIED = 'Correo no verificado';
    public const ACCOUNT_INACTIVE = 'Cuenta inactiva';

    const USER_NOT_FOUND = 'Usuario no encontrado';

    //BILLBOARD FACE
    const BILLBOARD_FACE_NOT_FOUND = 'Cara de valla no encontrado';
    const BILLBOARD_FACE_NOT_AVAILABLE = 'Vara de valla no habilitada';
    
    //BILLBOARD
    const BILLBOARD_NOT_FOUND = 'Valla no encontrado';
    const BILLBOARD_NOT_AVAILABLE = 'Valla no habilitada';

    //BILLBOARD TYPE
    const BILLBOARD_TYPE_NOT_FOUND = 'Tipo de valla no encontrada';

    //CITY
    const CITY_NOT_FOUND = 'Ciudad no encontrada';

    //ACTIVITY
    const ACTIVITY_NOT_FOUND = 'Actividad no encontrada';

    //SALE
    const SALE_NOT_FOUND = 'Venta no encontrada';

    //QUOTES
    const QUOTE_NOT_FOUND = 'Cotización no encontrada';

    //GENERIC RESPONSE
    const OBJECT_NOT_FOUND = 'objeto encontrada';

}

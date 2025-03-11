<?php

namespace App\Http\Messages;

class ExeptionMessages
{
    public const CUSTOMER_ALREADY_EXIST = "Este Cliente Ya se encuentra Registrado";
    public const CONVERSATION_ALREADY_EXIST = "Conversacion ya existente";
    public const CUSTOMER_NOT_FOUND = "Cliente no encontrado";
    public const OPPORTUNITY_NOT_FOUND = "Oportunidad NO Encontrada";
    public const CONVERSATION_NOT_FOUND = "Conversacion no encontrada";
    public const CUSTOMER_HAS_OPPORTUNITY_ACTIVE = "Este Cliente Tiene una oportunidad activa";
    public const OPPORTUNITY_IS_PROSPECT = "Esta Oportunidad ya es un Prospecto";
    public const PROCESS_NOT_CHECK = "No todos los procesos fueron completados";

}

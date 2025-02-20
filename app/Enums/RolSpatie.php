<?php

namespace App\Enums;

enum RolSpatie :string
{
    case ADMINISTRADOR = "ADMINISTRADOR";
    case OPERADOR = "OPERADOR";
    case ANUNCIANTE = "ANUNCIANTE";
    case AGENCIA = "AGENCIA";
    case CLIENTE = "CLIENTE";
}

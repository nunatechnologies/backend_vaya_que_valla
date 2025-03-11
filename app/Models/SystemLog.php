<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class SystemLog extends Model
{
    use HasFactory;
    protected $table = 'system_logs';

    protected $fillable = [
        'user_id',           // Quién realizó la acción
        'username',           // Nombre de usuario para referencia rápida
        'ip_address',         // Dirección IP del usuario
        'action_type',        // Tipo de acción (create, update, delete, login, etc.)
        'model_type',         // Tipo de modelo afectado
        'model_id',           // ID del modelo específico
        'description',        // Descripción legible de la acción
        'route',              // Ruta de la solicitud
        'severity',           // Nivel de severidad (info, warning, error)
    ];

     // Relación con el usuario
     public function user()
     {
         return $this->belongsTo(User::class);
     }

     // Método estático para crear logs fácilmente
     public static function createLog($params)
     {
         return self::create(array_merge([
             'user_id' => Auth::id(),
             'username' => Auth::user() ? Auth::user()->name : 'Sistema',
             'ip_address' => request()->ip(),
             'route' => request()->fullUrl(),
         ], $params));
     }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'last_login',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login' => 'datetime',
    ];

    /**
     * ✅ Normaliza los valores del rol para mantener coherencia
     *    sin importar cómo estén guardados en la base de datos.
     */
    public function getRoleAttribute($value)
    {
        $map = [
            'Administrador' => 'Admin',
            'Admin' => 'Admin',
            'Cliente' => 'Cliente',
            'User' => 'Cliente',
            'Editor' => 'Editor',
        ];

        return $map[$value] ?? $value;
    }
}

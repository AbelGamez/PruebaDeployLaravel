<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable {
    
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'apellidos',
        'email',
        'password',
        'nickname',
        'telefono',
        'banned',
        'admin',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relación HasMany con el modelo UserActionLog.
     *
     * Esta relación indica que un registro en la tabla "users" puede tener múltiples registros asociados
     * en la tabla "user_actions_log". Es decir, cada usuario puede estar relacionado con varias acciones
     * registradas en el sistema. La relación se establece utilizando la columna "user_id" en la tabla
     * "user_actions_log".
     *
     * Ejemplo de uso:
     * $user = User::find(1);
     * $actions = $user->userActionsLogs; // Obtiene todas las acciones de usuario relacionadas con este usuario
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userActionsLogs() {
        return $this->hasMany(UserActionLog::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserActionLog extends Model {

    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_actions_log';

    protected $fillable = [
        'action_type',
        'user_id',
        'stock_id'
    ];

    /**
     * Relación BelongsTo con el modelo User.
     * 
     * Esta relación indica que cada registro en la tabla "user_actions_log" pertenece a un usuario específico.
     * La columna "user_id" en la tabla "user_actions_log" almacena el ID del usuario al que pertenece la acción.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación BelongsTo con el modelo Stock.
     *
     * Esta relación indica que cada registro en la tabla "user_actions_log" pertenece a un stock específico.
     * Es decir, cada acción registrada está asociada a un stock sobre el cual se realizó la acción. La columna
     * "stock_id" en la tabla "user_actions_log" almacena el ID del stock al que pertenece la acción.
     *
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function stock() {
        return $this->belongsTo(Stock::class);
    }
}
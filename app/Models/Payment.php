<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'holder_name',
        'creditcard_number',
        'expiry_month',
        'expiry_year',
        'total_price',
        'security_code',
        'user_id',
    ];

    /**
     * Get the user that owns the payment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function paymentStocks()
    {
        return $this->hasMany(Stock::class, 'payment_id');
    }
}

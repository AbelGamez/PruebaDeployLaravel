<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Stock;

class Product extends Model {
    
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'patten',
        'rarity_name',
        'rarity_color',
        'description',
        'image'
    ];

    /**
     * Relación One To Many.
     * 
     * Un producto puede tener varias entradas en la tabla Stock.
     * 
     */
    public function stocks(){
        return $this->hasMany(Stock::class);
    }
}
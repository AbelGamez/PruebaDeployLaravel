<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Product;

class Stock extends Model {

    use HasFactory;
    
    protected $fillable = [
        'stattrak',
        'float',
        'units',
        'unit_price',
        'user_id',
    ];
    
    /**
     * Relación One To Many (Inverse) / BelongsTo.
     * 
     * Esta relación indica que cada registro en la tabla "stocks" pertenece a un registro en la tabla "products".
     * Es decir, cada entrada de stock está asociada a un producto específico. La columna "product_id" en la tabla
     * "stocks" almacena el ID del producto al que pertenece el stock.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()  {
        return $this->belongsTo(Product::class);
        
    }
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Relación HasMany con el modelo UserActionLog.
     *
     * Esta relación indica que un registro en la tabla "stocks" puede tener múltiples registros asociados
     * en la tabla "user_actions_log". Es decir, cada stock puede estar relacionado con varias acciones de
     * usuario registradas. La relación se establece utilizando la columna "stock_id" en la tabla "user_actions_log".
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userActionsLogs() {
        return $this->hasMany(UserActionLog::class);
    }

    /**
     * Devuelve la colección de todos los productos en stock de una categoría específica,
     * incluyendo la relación con los productos.
     * 
     */
    public static function getProductsInStockByCategoryFiltered($category, $min_price, $max_price, $min_float, $max_float, $name, $stattrak = null, $page = 1, $forPage = 18) {
        // Inner Join entre la tabla "stocks" y "productos" mediante las columnas "product_id" y "id".
        $query = Stock::join('products', 'stocks.product_id', '=', 'products.id')
        // Filtrar los productos por la categoría especificada.
        ->where('products.category', $category)
        // Selecciona solo los productos disponibles en stock.
        ->where('stocks.available', 1)
        // Selecciona solo los productos que no se han vendido.
        ->where('stocks.payment_id', null)
        // Filtrar los registros de la tabla "stocks" por el rango de precios proporcionado.
        ->whereBetween('stocks.unit_price', [$min_price, $max_price])
        // Filtrar los registros de la tabla "stocks" por el rango de floats proporcionado.
        ->whereBetween('stocks.float', [$min_float, $max_float])
        // Selecciona todas las columnas de la tabla "stocks".
        ->select('stocks.*')
        // Carga la relación "product" para cada instancia de Stock (para acceder a los detalles del producto relacionado).
        ->with('product');

        // Si se especifica el filtro "stattrak", añadir condición
        if ($stattrak !== null) {
            $query->where('stattrak', $stattrak);
        }

        // Agrupar condiciones de name y pattern
        $query->where(function ($query) use ($name) {
            $query->where('products.name', 'like', "%$name%")
            ->orWhere('products.pattern', 'like', "%$name%");
        });

        return $query->paginate($forPage, ['*'], 'page', $page);
    }

    /**
     * Devuelve la colección de todos los productos en stock paginados (SE PUEDE ELIMINAR).
     *
     */
    public static function getAllProductsInStockPaginated($page = 1, $forPage = 10) {
        // Inner Join entre la tabla "stocks" y "productos" mediante las columnas "product_id" y "id".
        return Stock::join('products', 'stocks.product_id', '=', 'products.id')
            // Selecciona todas las columnas de la tabla "stocks".
            ->select('stocks.*')
            // Carga la relación "product" para cada instancia de Stock (para acceder a los detalles del producto relacionado).
            ->with('product')
            // Pagina la colección de todos los productos en stock en base al número de productos por página especificado.
            ->paginate($forPage, ['*'], 'page', $page);
    }

        /**
     * Devuelve la colección de productos en stock según los filtros proporcionados
     * (precio, float, nombre o pattern y opcionalmente stattrak).
     * 
     */
    public static function getProductsInStockFiltered($min_price, $max_price, $min_float, $max_float, $name, $stattrak = null, $page = 1, $forPage = 18) {
        // Inner Join entre la tabla "stocks" y "productos" mediante las columnas "product_id" y "id".
        $query = Stock::join('products', 'stocks.product_id', '=', 'products.id')
            // Agregar condición para seleccionar solo los productos disponibles en stock.
            ->where('stocks.available', 1)
            // Selecciona solo los productos que no se han vendido.
            ->where('stocks.payment_id', null)
            // Filtrar los registros de la tabla "stocks" por el rango de precios proporcionado.
            ->whereBetween('stocks.unit_price', [$min_price, $max_price])
            // Filtrar los registros de la tabla "stocks" por el rango de floats proporcionado.
            ->whereBetween('stocks.float', [$min_float, $max_float])
            // Selecciona todas las columnas de la tabla "stocks".
            ->select('stocks.*')
            // Carga la relación "product" para cada instancia de Stock (para acceder a los detalles del producto relacionado).
            ->with('product');
    
        // Si se especifica el filtro "stattrak", añadir condición
        if ($stattrak !== null) {
            $query->where('stattrak', $stattrak);
        }
    
        // Agrupar condiciones de name y pattern
        $query->where(function ($query) use ($name) {
            $query->where('products.name', 'like', "%$name%")
                ->orWhere('products.pattern', 'like', "%$name%");
        });
    
        return $query->paginate($forPage, ['*'], 'page', $page);
    }    

    public static function getProductsInStockFilteredWithStattrak($min_price, $max_price, $min_float, $max_float, $name, $page = 1, $forPage = 5) {
        // Inner Join entre la tabla "stocks" y "productos" mediante las columnas "product_id" y "id".
        $query = Stock::join('products', 'stocks.product_id', '=', 'products.id')
            // Filtrar los registros de la tabla "stocks" por el rango de precios proporcionado.
            ->whereBetween('stocks.unit_price', [$min_price, $max_price])
            // Filtrar los registros de la tabla "stocks" por el rango de precios proporcionado.
            ->whereBetween('stocks.float', [$min_float, $max_float])
            // Filtrar por Stattrak
            ->where('stattrak', 1)
            // Agrupar condiciones de name y pattern
            ->where(function ($query) use ($name) {
                $query->where('products.name', 'like', "%$name%")
                    ->orWhere('products.pattern', 'like', "%$name%");
            })
            // Selecciona todas las columnas de la tabla "stocks".
            ->select('stocks.*')
            // Carga la relación "product" para cada instancia de Stock (para acceder a los detalles del producto relacionado).
            ->with('product');
         
            return $query->paginate($forPage, ['*'], 'page', $page);
    }

    public static function getProductsInStockFilteredWithoutStattrak($min_price, $max_price, $min_float, $max_float, $name, $page = 1, $forPage = 5) {
        // Inner Join entre la tabla "stocks" y "productos" mediante las columnas "product_id" y "id".
        $query = Stock::join('products', 'stocks.product_id', '=', 'products.id')
            // Filtrar los registros de la tabla "stocks" por el rango de precios proporcionado.
            ->whereBetween('stocks.unit_price', [$min_price, $max_price])
            // Filtrar los registros de la tabla "stocks" por el rango de precios proporcionado.
            ->whereBetween('stocks.float', [$min_float, $max_float])
            // Agrupar condiciones de name y pattern
            ->where(function ($query) use ($name) {
                $query->where('products.name', 'like', "%$name%")
                ->orWhere('products.pattern', 'like', "%$name%");
            })
            // Selecciona todas las columnas de la tabla "stocks".
            ->select('stocks.*')
            // Carga la relación "product" para cada instancia de Stock (para acceder a los detalles del producto relacionado).
            ->with('product');
    
            return $query->paginate($forPage, ['*'], 'page', $page);
    }
}
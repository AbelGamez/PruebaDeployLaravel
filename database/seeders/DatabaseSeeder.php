<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Product;
use App\Models\Stock;

class DatabaseSeeder extends Seeder {

    /**
     * Seed the application's database.
     */
    public function run(): void {
        // User::create([
        //     'name' => 'Rodrigo',
        //     'apellidos' => 'Kumenius',
        //     'email' => 'rodrigo_kumeniusromero@iescarlesvallbona.cat',
        //     'password' => Hash::make('Rodrigo00'),
        //     'nickname' => 'RodrigoKR',
        //     'telefono' => 123456789,
        //     'admin' => 1,
        //     'banned' => 0,
        // ]);

        // User::create([
        //     'name' => 'Abel',
        //     'apellidos' => 'Gamez 2',
        //     'email' => 'abel_gamezgimenez@iescarlesvallbona.cat',
        //     'password' => Hash::make('Abel0000'),
        //     'nickname' => 'AbelGG',
        //     'telefono' => 987654321,
        //     'admin' => 1,
        //     'banned' => 0,
        // ]);

        // User::create([
        //     'name' => 'Ferran',
        //     'apellidos' => 'Calzadilla',
        //     'email' => 'ferran_calzadillafimia@iescarlesvallbona.cat',
        //     'password' => Hash::make('Ferran00'),
        //     'nickname' => 'FerranCF',
        //     'telefono' => 555555555,
        //     'admin' => 1,
        //     'banned' => 0,
        // ]);
    
        $categories = ['Pistols', 'Rifles', 'Heavy', 'SMGs', 'Gloves', 'Knives'];

        foreach ($categories as $category) {
            $products = Product::where('category', $category)->take(400)->get();

            foreach ($products as $product) {
                Stock::create([
                    'stattrak' => rand(0, 1), // Valor booleano aleatorio
                    'float' => round(mt_rand() / mt_getrandmax(), 3), // Valor flotante aleatorio entre 0 y 1
                    'unit_price' => mt_rand(0, 10000), // Valor entero aleatorio entre 0 y 10000
                    'units' => 1,
                    'product_id' => $product->id,
                    'available' => 1,
                    'created_at' => now()->subMonths(rand(0, 12)) // Fecha aleatoria en los últimos 12 meses
                ]);
            }
        }
    }
}

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ApiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/* ---------------------------------------------------------------- */
/*                           PRODUCT ROUTES                         */
/* ---------------------------------------------------------------- */

// LISTAR TODOS LOS PRODUCTOS EN STOCK.
Route::get('/products', [ApiController::class, 'listAllProductsInStock']);

// LISTAR TODOS LOS PRODUCTOS EN STOCK PAGINADOS (SE PUEDE ELIMINAR).
Route::get('/paginatedProducts', [ApiController::class, 'listAllProductsInStockPaginated']);

// LISTA LOS DATOS DE UN PRODUCTO EN STOCK MEDIANTE LA 'ID' PROPORCIONADA.
Route::get('/productDetail/{id}', [ApiController::class, 'listProductInStock']);

// AÑADIR UN PRODUCTO EN STOCK. 
Route::post('/addStock/{stockId}/{userId}', [ApiController::class, 'addStock']);
// Route::post('/addStock', [ApiController::class, 'addStock']);

//LISTAR TODOS LOS PORDUCTOS DE LA TABLA PRODUCT
Route::get('/uniqueProductCategories', [ApiController::class, 'listUniqueProductCategories']);

//GET ID DE PRODUCT
Route::get('/getid/{name}/{pattern}', [ApiController::class, 'getIdProduct']);

// LISTAR LOS PRODUCTOS POR NOMBRE
Route::get('/getSkins/{pattern}', [ApiController::class, 'listSkinsByProductName']);

// LISTAR LOS PRODUCTOS POR CATEGORIA
Route::get('/getProducts/{category}', [ApiController::class, 'listProductsByCategory']);

//LLISTAR LA IMAGEN DE LAS SKINS
Route::get('/list-skins/{name}/{pattern}', [ApiController::class, 'listSkinsIMGByProductName']);

//GET DESCRIPTION POR ID
Route::get('/getdescription/{name}/{pattern}', [ApiController::class, 'getDescriptionByID']);

// RUTA PARA EDITAR UN PRODUCTO EN STOCK PASADO MEDIANTE SU ID.
Route::put('/editProduct/{productId}/{userId}', [ApiController::class, 'editProductInStock']);
// Route::put('/editProduct/{id}', [ApiController::class, 'editProductInStock']);

// RUTA PARA ELIMINAR UN PRODUCTO EN STOCK PASADO MEDIANTE SU ID.
Route::put('/deleteProduct/{stockId}/{userId}', [ApiController::class, 'deleteProductInStock']);
// Route::put('/deleteProduct/{id}', [ApiController::class, 'deleteProductInStock']);

// LISTAR SOLO LOS PRODUCTOS EN STOCK POR LA CATEGORIA SELECCIONADA.
Route::get('/productsCategory/{category}', [ApiController::class, 'listProductsInStockByCategory']);

// LISTAR SOLO LOS PRODUCTOS EN STOCK FILTRADOS.
Route::get('/products/filtered', [ApiController::class, 'listProductsInStockFiltered']);

// LISTA 6 PRODUCTOS EN STOCK DE FORMA ALEATORIA.
Route::get('/randomProducts', [ApiController::class, 'listRandomProductsInStock']);

// LISTA TODOS LOS REGISTROS DE ACCIONES HECHAS POR EL USUARIO ADMIN.
Route::get('/user-actions', [ApiController::class, 'listUserActions']);

Route::get('/randomProductsHome', [ApiController::class, 'listRandomProductsInStockHome']);


/* ---------------------------------------------------------------- */
/*                             USER ROUTES                          */
/* ---------------------------------------------------------------- */

// LISTAR TODOS LOS USUARIOS.
Route::get('listAllUsers', [ApiController::class, 'listAllUsers']);

// LISTAR UN USER EN CONCRETO.
Route::get('user/{id}', [ApiController::class, 'listUser']);

// CREAR USER.
Route::post('/register', [ApiController::class, 'createUser']);

// MODIFICAR USER.
Route::post('/editUser/{id}', [ApiController::class, 'updateUser']);

// ELIMINAR USER.
Route::delete('/deleteUser/{id}', [ApiController::class, 'deleteUser']);

// 
Route::post('/login', [ApiController::class, 'login']);

// 
Route::post('/forgot-password', [ApiController::class, 'forgotPassword']);

// 
Route::post('/reset-password', [ApiController::class, 'resetPassword']);

// BANEAR USUARIO
Route::post('/ban/{id}', [ApiController::class, 'banUser']);

//
Route::post('/admin/{id}', [ApiController::class, 'getAdmin']);

//
Route::post('/check-email', [ApiController::class, 'checkEmailExists']);

//Graficos

Route::get('/get-buyer-profile-data', [ApiController::class, 'getBuyerProfileData']);

Route::get('/get-contUsers', [ApiController::class, 'countUsers']);

Route::get('/get-sumUnitPrice', [ApiController::class, 'sumUnitPrice']);

Route::post('/payment/{userId}', [ApiController::class, 'Payment']);

Route::get('/transactions/{userId}', [ApiController::class, 'getUserTransactions']);

Route::get('/productsByPayment/{paymentId}', [ApiController::class, 'listProductsInStockByPaymentId']);

Route::get('/MonthlyTransactions', [ApiController::class, 'getMonthlyTransactions']);

Route::get('/total-sales', [ApiController::class, 'getTotalSales']);


//Datos factura 

Route::get('/obtener-datos-tabla', [ApiController::class, 'RecentOrders'])->name('datos.tabla');

Route::get('/obtener-list-datos-tabla', [ApiController::class, 'listFacturas'])->name('datos.tabla');


Route::get('/usuarios/{id}/datos-tabla', [ApiController::class, 'obtenerDatosTablaPorUsuario']);

Route::get('/datos-tabla-usuario-pago', [TuControlador::class, 'obtenerDatosTablaPorUsuarioYPago']);



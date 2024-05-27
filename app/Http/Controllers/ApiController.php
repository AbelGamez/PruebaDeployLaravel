<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use App\Models\Stock;
use App\Models\User;
use App\Models\UserActionLog;
use App\Models\Payment;
use Illuminate\Support\Facades\DB; 

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

    class ApiController extends Controller {

     /* ---------------------------------------------------------------- */
    /*                             PRODUCTS                             */
    /* ---------------------------------------------------------------- */

    /**
     * Muestra un listado de todos los productos en stock (SE PUEDE ELIMINAR).
     * 
     */
    public function listAllProductsInStock() {
        // Almacena todos los productos en stock junto con la información del producto asociado.
        $productsInStock = Stock::with('product')->get();

        // Devuelve la colección de productos en stock.
        return response()->json($productsInStock);
    }

    /**
     * Devuelve la colección de todos los productos en stock paginados (SE PUEDE ELIMINAR).
     * 
     */
    public function listAllProductsInStockPaginated(Request $request) {
        // Obtener el número de página de la solicitud
        $page = $request->query('page', 1);

        // Almacena todos los productos en stock paginados.
        $productsInStock = Stock::getAllProductsInStockPaginated($page);
    
        // Devuelve la colección paginada de productos en stock.
        return response()->json($productsInStock);
    }

    /**
     * Devuelve los datos de un producto en stock mediante el ID proporcionado.
     * 
     */
    public function listProductInStock($id) {
        // Busca y almacena el producto en stock con el ID proporcionado junto con su información.
        $productInStock = Stock::with('product')->find($id);

        // Devuelve los datos del producto.
        return response()->json($productInStock);
    }

    /**
     * Devuelve una colección de 4 productos en stock de forma aleatoria.
     * 
     */
    public function listRandomProductsInStock() {
        // Almacena todos los productos en stock junto con la información del producto asociado.
        $productsInStock = Stock::with('product')->get();
    
        // Mezcla la colección para obtener productos aleatorios.
        $shuffledProducts = $productsInStock->shuffle();
    
        // Toma los primeros 4 productos de la colección mezclada.
        $randomProducts = $shuffledProducts->take(6);
    
        // Devuelve la colección de 4 productos en stock aleatorios.
        return response()->json($randomProducts);
    }

    /**
     * FALTAN AÑADIR COMENTARIOS Y LAS VALIDACIÓNES EN EL BACKEND Y FRONTEND
     *
     */
    public function addStock(Request $request, $userId) {
        // Validar los campos del request
        $request->validate([
            'unit_price' => 'required|numeric|min:0',
            'float' => 'required|numeric|between:0,1',
            'stattrak' => 'boolean',
        ]);
    
        $stock = new Stock;
    
        $stock->units = 1;
        $stock->unit_price = $request->unit_price;
    
        // Obtener el id del producto correctamente utilizando el método getIdProduct
        $productId = $this->getIdProduct($request->name, $request->pattern);
    
        $stock->product_id = $productId;
        $stock->float = $request->float;
        $stock->stattrak = $request->stattrak;
    
        // Establecer el valor de available a 1 (no se ha eliminado).
        $stock->available = 1;
    
        try {
            $stock->save();
        } catch (\Exception $e) {
            // Manejo de error: devolver una respuesta adecuada al usuario o registrar el error para su posterior análisis
            // Por ejemplo: Log::error($e->getMessage());
            // Devolver una respuesta de error al usuario
        }
    
        // Registrar la acción de usuario
        $this->logUserAction('Create', $userId, $stock->id);
    
        return $stock;
    }

    /**
     * FALTA AÑADIR COMENTARIOS.
     * 
     */
    public function listUniqueProductCategories() {
        $categories = Product::select('category')->distinct()->get();

        return response()->json($categories);
    }

    /**
     * FALTA AÑADIR COMENTARIOS.
     * 
     */
    public function getIdProduct($name, $pattern) {
        $product = Product::where('name', 'LIKE', "%$name%")
            ->where('pattern', 'LIKE', "%$pattern%")
            ->first();

        if ($product) {
            return $product->id;
        }

        return null; 
    }

    /**
     * FALTA AÑADIR COMENTARIOS.
     * 
     */
    public function listSkinsByProductName($name) {
        // Obtener los patrones de las skins basados en el nombre del producto
        $patterns = Product::where('name', 'LIKE', "%$name%")->distinct()->pluck('pattern');

        // Retornar los patrones de las skins como respuesta
        return response()->json($patterns);
    }

    /**
     * FALTA AÑADIR COMENTARIOS.
     * 
     */
    public function listProductsByCategory($category) {
        // Obtener los nombres de los productos únicos de la categoría
        $productNames = Product::where('category', $category)->distinct()->pluck('name');

        // Retornar los nombres de los productos únicos como respuesta
        return response()->json($productNames);
    }

    /**
     * FALTA AÑADIR COMENTARIOS.
     * 
     */
    public function listSkinsIMGByProductName($name, $pattern) {
        // Obtener las imágenes de las skins basadas en el nombre del producto y el patrón
        $imgs = Product::where('name', 'LIKE', "%$name%")
            ->where('pattern', 'LIKE', "%$pattern%")
            ->pluck('image');

        // Retornar las imágenes de las skins como respuesta
        return response()->json($imgs);
    }

    /**
     * FALTA AÑADIR COMENTARIOS.
     * 
     */
    public function getDescriptionByID($name, $pattern) {
        $id = $this->getIdProduct($name, $pattern);
        $product = Product::find($id);

        if ($product) {
            return response()->json(['description' => $product->description]);
        } else {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }
    }

    /**
     * FALTAN AÑADIR COMENTARIOS Y LAS VALIDACIÓNES EN EL BACKEND Y FRONTEND (RODRIGO).
     *
     */
    public function editProductInStock(Request $request, $productId, $userId ) {

        $stock = Stock::find($productId);

        $request->validate([
            'unit_price' => 'required | numeric | min:0',
            'float' => 'required | numeric | lte:1'
        ]);

        if (isset($request->unit_price)) {
            $stock->unit_price = $request->unit_price;
        }

        if (isset($request->float)) {
            $stock->float = $request->float;
        }

        $stock->save();

        // Registrar la acción de usuario
        $this->logUserAction('Edit', $userId, $stock->id);

        return response()->json($stock);
    }

    /**
     * FALTA AÑADIR COMENTARIOS (RODRIGO).
     */
    public function deleteProductInStock($stockId, $userId) {
        // Buscar el registro en la tabla "stocks" por su ID.
        $stock = Stock::find($stockId);

        // Buscar el registro en la tabla "stocks" por su ID.
        if ($stock) {
            // Establecer el campo "available" a 0 para indicar que ha sido eliminado.
            $stock->available = 0;

            $stock->save();

            // Registrar la acción de usuario antes de eliminar los registros
            $this->logUserAction('Delete', $userId, $stockId);

            return response()->json(['message' => 'Product in stock marked as unavailable.'], 200);
        }

        return response()->json(['message' => 'Stock not found.'], 404);
    }


    /**
     * FALTA AÑADIR COMENTARIOS (RODRIGO).
     * 
     */
    public function listProductsInStockByCategory(Request $request, $category) {
        // Obtener el número de página de la solicitud
        $page = $request->query('page', 1);

        // Obtener los filtros de la solicitud
        $filters = $request->only(['min_price', 'max_price', 'min_float', 'max_float', 'stattrak', 'name']);

        // Verificación de si el filtro 'stattrak' esta presente en la solicitud y lo convierte a booleano.
        if ($filters['stattrak'] == 'true') {
            $stattrak = $filters['stattrak'] == 'true' ? 1 : 0;
        } else {
            $stattrak = null;
        }

        // Obtener los productos en stock filtrados por categoría y aplicar los filtros adicionales
        $productsInStock = Stock::getProductsInStockByCategoryFiltered(
            $category,
            $filters['min_price'],
            $filters['max_price'],
            $filters['min_float'],
            $filters['max_float'],
            $filters['name'],
            $stattrak,
            $page
        );

        // Adjuntar los parámetros de filtro a la URL de paginación
        $productsInStock->appends($filters);

        return response()->json($productsInStock);
    }

    /**
     * Devuelve la colección de todos los productos en stock filtrados.
     * 
     */
    public function listProductsInStockFiltered(Request $request) {
        // Obtiene el número de página de la solicitud o establece el valor predeterminado en 1.
        $page = $request->query('page', 1);

        // Obtiene solo los parámetros de los filtros de la solicitud
        $filters = $request->only(['min_price', 'max_price', 'min_float', 'max_float', 'stattrak', 'name']);
        
        // Verificación de si el filtro 'stattrak' esta presente en la solicitud y lo convierte a booleano.
        if ($filters['stattrak'] == 'true') {
            $stattrak = $filters['stattrak'] == 'true' ? 1 : 0;
        } else {
            $stattrak = null;
        }

        // Obtiene los productos en stock filtrados.
        $productsInStock = Stock::getProductsInStockFiltered(
            $filters['min_price'], 
            $filters['max_price'], 
            $filters['min_float'], 
            $filters['max_float'], 
            $filters['name'], 
            $stattrak, 
            $page
        );
        // Adjunta los parámetros de filtro a la URL de paginación.
        $productsInStock->appends($filters);
        
        return response()->json($productsInStock);
    }   

    /**
     * Registra una acción en el registro de acciones de usuario.
     *
     */
    public function logUserAction($actionType, $userId, $stockId) {
        UserActionLog::create([
            'action_type' => $actionType,
            'user_id' => $userId,
            'stock_id' => $stockId
        ]);
    }

    /**
     * FALTA COMENTARIOS
     * 
     */
    public function listUserActions() {
        $userActions = UserActionLog::with(['stock.product', 'user'])->get();

        return response()->json($userActions);
    }

    /* ---------------------------------------------------------------- */
    /*                               USERS                              */
    /* ---------------------------------------------------------------- */

    public function listAllUsers() {
        return User::all();
    }

    public function listUser($id) {
        $user = User::find($id);

        return $user;
    }

    public function createUser(Request $request) {
        // Validación de los datos recibidos en la solicitud
        $validatedData = $request->validate([
            'name' => 'required|string|max:20',
            'apellidos' => 'required|string|max:20',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'nickname' => 'required|string|max:20|unique:users,nickname',
            'telefono' => 'required|integer|digits:9|unique:users,telefono',
        ], [
            'email.unique' => 'The email is already registered.',
            'nickname.unique' => 'The nickname is already in use.',
            'telefono.unique' => 'The phone number is already in use.',
        ]);
    
        // Creación del usuario con los datos proporcionados
        $user = new User();
        $user->name = $request->name;
        $user->apellidos = $request->apellidos;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->nickname = $request->nickname;
        $user->telefono = $request->telefono;
        $user->banned = 0;
        $user->admin = 0;
        $user->save();
    
        return response()->json(['message' => 'User registered successfully'], 201);
    }

    public function updateUser(Request $request, $id) {
        // Validación de los datos recibidos en la solicitud
        $request->validate([
            'name' => 'string',
            'apellidos' => 'string',
            'email' => 'email|unique:users,email,' . $id,
            'password' => 'string|min:6',
            'nickname' => 'string|unique:users,nickname,' . $id,
            'telefono' => 'integer|digits:9|unique:users,telefono,' . $id,
        ]);

        // Búsqueda del usuario por su ID
        $user = User::find($id);

        // Verificación de la existencia del usuario
        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        // Actualización de los datos del usuario si se proporcionan
        if ($request->filled('name')) {
            $user->name = $request->name;
        }

        if ($request->filled('apellidos')) {
            $user->apellidos = $request->apellidos;
        }

        if ($request->filled('email')) {
            $user->email = $request->email;
        }

        if ($request->filled('password')) {
            $user->password = $request->password;
        }

        if ($request->filled('nickname')) {
            $user->nickname = $request->nickname;
        }

        if ($request->filled('telefono')) {
            $user->telefono = $request->telefono;
        }

        $user->save();

        return $user;
    }

    public function deleteUser($id) {
        // Búsqueda del usuario por su ID
        $user = User::find($id);

        // Verificación de la existencia del usuario
        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        // Eliminación del usuario
        $user->delete();

        return response()->json(['message' => 'Usuario eliminado correctamente'], 200);
    }

    use AuthorizesRequests, ValidatesRequests;

    public function login(Request $request) {
        // Validación de los datos recibidos en la solicitud
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Buscar al usuario por su correo electrónico
        $user = User::where('email', $request->email)->first();

        // Verificar si el usuario está baneado
        if ($user && $user->banned == 1) {
            // Si el usuario está baneado, devuelve un mensaje de error
            return response()->json(['error' => 'This user is banned'], 401);
        }

        // Intento de inicio de sesión utilizando el email y la contraseña proporcionados
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            // Autenticación exitosa, devuelve el usuario autenticado
            return response()->json(['user' => Auth::user()], 200);
        } else {
            // Autenticación fallida, devuelve un mensaje de error
            return response()->json(['error' => 'Incorrect credentials'], 401);
        }
    }

    public function forgotPassword(Request $request) {
        $request->validate(['email' => 'required|email']);

        // Buscar el usuario por su dirección de correo electrónico
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'No user with that email address was found'], 404);
        }

        // Generar el token de restablecimiento de contraseña
        $token = Password::getRepository()->create($user);

        // Obtener la URL del frontend desde la configuración
        // $frontendUrl = Config::get('frontend_url');

        // Construir el enlace de restablecimiento de contraseña
        // $resetLink = $frontendUrl . "/reset/:{$token}";
        $resetLink = "https://proyecto-m12-4zils74j7-abels-projects-bac69a47.vercel.app/reset/:{$token}";

        // Enviar un correo con el enlace de restablecimiento de contraseña
        Mail::raw("To reset your password, click on the link below: $resetLink", function ($message) use ($user) {
            $message->to($user->email)->subject('Password Reset');
        });

        // Devolver una respuesta exitosa
        return response()->json(['message' => 'An email has been sent with instructions on how to reset your password'], 200);
    }

    public function resetPassword(Request $request) {
        $request->validate([
            'token' => 'required',
            'password' => 'required|confirmed|min:8',
        ]);

        $response = Password::reset(
            $request->only('password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => bcrypt($password)
                ])->save();
            }
        );

        if ($response == Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Password reset successfully'], 200);
        } else {
            return response()->json(['error' => 'Failed to reset password'], 400);
        }
    }
    
    public function banUser($id) {
        // Buscar al usuario por su ID
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Alternar el valor de la columna banned del usuario
        $user->banned = $user->banned == 0 ? 1 : 0;
        $user->save();

        // Enviar un correo al usuario informandole sobre su estado de baneo o desbaneo
        if ($user->banned == 1) {
            $statusMessage = '¡Banned Account!';
            $messageText = "Hello {$user->name}! We regret to inform you that your account has been banned.";
        } else {
            $statusMessage = '¡Unbanned Account!';
            $messageText = "Hello {$user->name}! We are happy to inform you that your account has been unbanned.";
        }
        
        Mail::raw($messageText, function ($message) use ($user, $statusMessage) {
            $message->to($user->email)->subject($statusMessage);
        });

        // Devolver una respuesta exitosa
        return response()->json(['message' => 'Usuario ' . ($user->banned == 1 ? 'baneado' : 'desbaneado') . ' correctamente'], 200);
    }

    public function getAdmin($id) {
        // Buscar al usuario por su ID
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Alternar el valor de la columna banned del usuario
        $user->admin = $user->admin == 0 ? 1 : 0;
        $user->save();

        // Enviar un correo al usuario informandole sobre su estado de baneo o desbaneo
        

        // Devolver una respuesta exitosa
        return response()->json(['message' => 'Usuario ' . ($user->admin == 1 ? 'admin' : 'admin') . ' correctamente'], 200);
    }

    public function checkEmailExists(Request $request) {
        $email = $request->input('email');

        // Buscar el correo electrónico en la base de datos
        $user = User::where('email', $email)->first();

        if ($user) {
            // Si se encuentra un usuario con ese correo electrónico, devolver un mensaje de error
            return response()->json(['emailExists' => true]);
        }

        // Si no se encuentra ningún usuario con ese correo electrónico, devolver un mensaje de éxito
        return response()->json(['emailExists' => false]);
    }

    //Graficos

    public function getBuyerProfileData()
    {
        // Consultar la base de datos para obtener el valor total por categoría
        $data = Stock::join('products', 'stocks.product_id', '=', 'products.id')
                     ->select('products.category', DB::raw('SUM(stocks.units * stocks.unit_price) as total_value'))
                     ->groupBy('products.category')
                     ->get();
    
        // Formatear los datos para que coincidan con el formato requerido por Recharts
        $formattedData = $data->map(function ($item) {
            return [
                'name' => $item->category,
                'value' => round($item->total_value, 2) // Redondear total_value a 2 decimales
            ];
        });
    
        return response()->json($formattedData);
    }
    

public function countUsers()
{
    // Obtener el recuento de usuarios en la tabla 'users'
    $userCount = User::count();

    return response()->json(['userCount' => $userCount]);
}

public function sumUnitPrice()
{
    $totalUnitPrice = Stock::sum('unit_price');
    
    return response()->json([
        'total_unit_price' => $totalUnitPrice
    ]);
}

    public function Payment(Request $request, $userId)
{
    $request->validate([
        'holder_name' => 'required|string|max:255',
        'creditcard_number' => 'required|integer',
        'expiry_month' => 'required|integer|between:1,12',
        'expiry_year' => 'required|integer',
        'total_price' => 'required|decimal:0,4',
        'security_code' => 'required|integer',
        'user_id' => 'required|exists:users,id',
    ]);

    // Crear el pago
    $payment = Payment::create([
        'holder_name' => $request->input('holder_name'),
        'creditcard_number' => $request->input('creditcard_number'),
        'expiry_month' => $request->input('expiry_month'),
        'expiry_year' => $request->input('expiry_year'),
        'total_price' => $request->input('total_price'),
        'security_code' => $request->input('security_code'),
        'user_id' => $userId
    ]);
    $paymentId = $payment->id;

    // Actualizar los registros de productos en el carrito con el payment_id correspondiente
    foreach ($request->input('cartItems') as $cartItem) {
        $productId = $cartItem['id'];
        // Actualizar el producto con el payment_id
        Stock::where('id', $productId)->update(['payment_id' => $paymentId]);
    }
    
    return response()->json($payment, 201);
}

public function listRandomProductsInStockHome() {
    // Almacena todos los productos en stock junto con la información del producto asociado.
    $productsInStock = Stock::with('product')->get();

    // Mezcla la colección para obtener productos aleatorios.
    $shuffledProducts = $productsInStock->shuffle();

    // Toma los primeros 4 productos de la colección mezclada.
    $randomProducts = $shuffledProducts->take(4);

    // Devuelve la colección de 4 productos en stock aleatorios.
    return response()->json($randomProducts);
}public function getUserTransactions($userId)
{
    $transactions = Payment::where('user_id', $userId)->get();

    return response()->json($transactions);
}
public function listProductsInStockByPaymentId($paymentId) {
    // Busca los stocks asociados al paymentId
    $stocks = Stock::where('payment_id', $paymentId)->get();

    // Array para almacenar los productos
    $products = [];

    // Para cada stock asociado al paymentId, obtener el producto correspondiente
    foreach ($stocks as $stock) {
        // Buscar el producto con la ID del stock
        $product = Product::find($stock->product_id);
        if ($product && !in_array($product, $products)) {
            $products[] = $product;
        }

    }

    // Devolver los productos asociados al pago
    return response()->json($products);
}


public function RecentOrders()
{
    // Obtener los últimos 5 pagos con la información requerida y sus relaciones
    $pagos = Payment::with(['user', 'paymentStocks.product'])
                    ->latest()
                    ->take(5) // Limitar a los últimos 5 pagos
                    ->get(['id', 'total_price', 'created_at', 'user_id']);

    // Mapear los resultados para incluir el nombre del usuario, los ID de stock y los nombres de los productos concatenados con sus patrones, además de stattrak y float
    $datosTabla = $pagos->map(function ($pago) {
        $productos = $pago->paymentStocks->map(function ($paymentStock) {
            return [
                'nombre' => $paymentStock->product->name,
                'patron' => $paymentStock->product->pattern,
                'stattrak' => $paymentStock->stattrak ? 'Sí' : 'No',
                'float' => $paymentStock->float,
            ];
        });

        return [
            'id' => $pago->id,
            'total_price' => $pago->total_price,
            'created_at' => $pago->created_at,
            'usuario' => $pago->user->name, // Suponiendo que el modelo User tiene un campo 'name'
            'stock_ids' => $pago->paymentStocks->pluck('id')->implode(', '), // Concatenar los IDs de stock separados por coma
            'productos' => $productos, // Detalles de todos los productos asociados al pago
        ];
    });

    return $datosTabla;
}

//Este te devuelve todas las facturas

public function listFacturas()
{
    // Obtener los últimos 5 pagos con la información requerida y sus relaciones
    $pagos = Payment::with(['user', 'paymentStocks.product'])
                    ->latest()
                    ->get(['id', 'total_price', 'created_at', 'user_id']);

    // Mapear los resultados para incluir el nombre del usuario, los ID de stock y los nombres de los productos concatenados con sus patrones, además de stattrak y float
    $datosTabla = $pagos->map(function ($pago) {
        $productos = $pago->paymentStocks->map(function ($paymentStock) {
            return [
                'nombre' => $paymentStock->product->name,
                'patron' => $paymentStock->product->pattern,
                'stattrak' => $paymentStock->stattrak ? 'Sí' : 'No',
                'float' => $paymentStock->float,
            ];
        });

        return [
            'id' => $pago->id,
            'total_price' => $pago->total_price,
            'created_at' => $pago->created_at,
            'usuario' => $pago->user->name, // Suponiendo que el modelo User tiene un campo 'name'
            'stock_ids' => $pago->paymentStocks->pluck('id')->implode(', '), // Concatenar los IDs de stock separados por coma
            'productos' => $productos, // Detalles de todos los productos asociados al pago
        ];
    });

    return $datosTabla;
}
//Este te devuelve todas las facturas del usuario
public function obtenerDatosTablaPorUsuario($usuarioId)
{
    // Obtener los últimos 5 pagos del usuario especificado con la información requerida y sus relaciones
    $pagos = Payment::with(['user', 'paymentStocks.product'])
                    ->where('user_id', $usuarioId)
                    ->latest()
                    ->get(['id', 'total_price', 'created_at', 'user_id']);

    // Mapear los resultados para incluir el nombre del usuario, los ID de stock y los nombres de los productos concatenados con sus patrones, además de stattrak y float
    $datosTabla = $pagos->map(function ($pago) {
        $productos = $pago->paymentStocks->map(function ($paymentStock) {
            return [
                'nombre' => $paymentStock->product->name,
                'patron' => $paymentStock->product->pattern,
                'stattrak' => $paymentStock->stattrak ? 'Sí' : 'No',
                'float' => $paymentStock->float,
            ];
        });

        return [
            'id' => $pago->id,
            'total_price' => $pago->total_price,
            'created_at' => $pago->created_at,
            'usuario' => $pago->user->name, // Suponiendo que el modelo User tiene un campo 'name'
            'stock_ids' => $pago->paymentStocks->pluck('id')->implode(', '), // Concatenar los IDs de stock separados por coma
            'productos' => $productos, // Detalles de todos los productos asociados al pago
        ];
    });

    return $datosTabla;
}

//Este te devuelve la orden para imprimirla en pdf 

public function obtenerDatosTablaPorUsuarioYPago($usuarioId, $paymentId)
{
    // Obtener los detalles del pago específico del usuario especificado con la información requerida y sus relaciones
    $pago = Payment::with(['user', 'paymentStocks.product'])
                    ->where('user_id', $usuarioId)
                    ->where('id', $paymentId)
                    ->first(['id', 'total_price', 'created_at', 'user_id']);

    // Verificar si se encontró el pago
    if (!$pago) {
        return null; // Retornar null si el pago no existe
    }

    // Mapear los resultados para incluir el nombre del usuario, los ID de stock y los nombres de los productos concatenados con sus patrones, además de stattrak y float
    $datosTabla = [
        'id' => $pago->id,
        'total_price' => $pago->total_price,
        'created_at' => $pago->created_at,
        'usuario' => $pago->user->name, // Suponiendo que el modelo User tiene un campo 'name'
        'stock_ids' => $pago->paymentStocks->pluck('id')->implode(', '), // Concatenar los IDs de stock separados por coma
        'productos' => $pago->paymentStocks->map(function ($paymentStock) {
            return [
                'nombre' => $paymentStock->product->name,
                'patron' => $paymentStock->product->pattern,
                'stattrak' => $paymentStock->stattrak ? 'Sí' : 'No',
                'float' => $paymentStock->float,
            ];
        }),
    ];

    return $datosTabla;
}


public function getMonthlyTransactions()
{
    // Obtener ingresos (Income) agrupados por mes
    $payments = Payment::selectRaw('
        MONTH(created_at) as month, 
        SUM(total_price) as total
    ')
    ->groupBy('month')
    ->get()
    ->pluck('total', 'month');

    // Obtener gastos (Expense) agrupados por mes
    $expenses = Stock::selectRaw('
        MONTH(created_at) as month, 
        SUM(units * unit_price) as total
    ')
    ->where('available', '>', 0)
    ->groupBy('month')
    ->get()
    ->pluck('total', 'month');

    $transactions = [];

    for ($i = 1; $i <= 12; $i++) {
        $transactions[] = [
            'name' => Carbon::create()->month($i)->format('M'),
            'Income' => $payments[$i] ?? 0,
            'Expense' => $expenses[$i] ?? 0,
        ];
    }

    return response()->json($transactions);
}

public function getTotalSales()
{
    // Sumar todos los total_price de la tabla payments
    $totalSales = Payment::sum('total_price');

    // Formatear el total a 2 decimales
    $formattedTotalSales = number_format($totalSales, 2, '.', '');

    // Devolver el resultado como JSON
    return response()->json(['total_sales' => $formattedTotalSales]);
}

}
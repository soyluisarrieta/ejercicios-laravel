<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Proyecto: CRUD Blog usando JWT

Este proyecto es un ejercicio donde me enfoco en practicar la autenticación JWT desde otra fuente y practicar con la administración básuca de recursos CRUD en API Laravel.

## Indice

- [Proyecto: CRUD Blog usando JWT](#proyecto-crud-blog-usando-jwt)
  - [Indice](#indice)
  - [Instalación](#instalación)
  - [Configuración JWT](#configuración-jwt)
    - [Router](#router)
      - [Endpoints](#endpoints)
      - [Rutas de autenticación](#rutas-de-autenticación)
    - [Controllers](#controllers)
      - [Crear nuevo controlador](#crear-nuevo-controlador)
      - [Autenticación](#autenticación)
        - [Método constructor](#método-constructor)
        - [Método Login](#método-login)
        - [Método Logout](#método-logout)
        - [Método Register](#método-register)
        - [Método Profile](#método-profile)
        - [Método Refresh Token](#método-refresh-token)
        - [Método Respond with Token](#método-respond-with-token)
      - [Blog](#blog)
    - [Requests](#requests)
      - [Crear request](#crear-request)
      - [Solicitud Login](#solicitud-login)
      - [Solicitud Register](#solicitud-register)
    - [Models](#models)
      - [Generar modelo](#generar-modelo)
      - [User](#user)
    - [Migrations](#migrations)
      - [Generar migración](#generar-migración)
  - [Constribución](#constribución)


## Instalación

1. Clone the repository:
   ```bash
   git clone https://github.com/ph-hitachi/laravel-api-jwt-starter.git
    ```

2. Navigate to the project directory:
    ```bash
    cd laravel-api-jwt-starter
    ```
3. Install the required dependencies using Composer::

   ```bash
   composer install
    ```
4. Set up your environment variables by copying the `.env.example` file:
   ```bash
   cp .env.example .env
    ```

5. Generate a new application key:
    ```bash
    php artisan key:generate
    ```
6. Configure your database connection in the `.env` file.
7. Run the migrations:
    ```bash
    php artisan migrate
    ```
8. Generate a JWT secret key:

   ```bash
   php artisan jwt:secret
   ```

## Configuración JWT

1. Añadir provider en './config/app.php'
    ```php
    'providers' => ServiceProvider::defaultProviders()->merge([
      //...

      Tymon\JWTAuth\Providers\LaravelServiceProvider::class,
    ])->toArray(),
    ```
2. Crear alias en './config/app.php'
    ```php
    'aliases' => Facade::defaultAliases()->merge([
        // ...
        'JWTAuth' => Tymon\JWTAuth\Facades\JWTAuth::class,
        'JWTFactory' => Tymon\JWTAuth\Facades\JWTFactory::class,
      ])->toArray(),
    ```
3. Publicar provider
    ```bash
    php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
    ```
4. Generar clave secreta JWT
    ```bash
    php artisan jwt:secret
    ```
5. Actualizar [modelo usuario](#user) con los métodos de la implementación [JWTSubject](https://jwt-auth.readthedocs.io/en/develop/quick-start/#update-your-user-model)
6. Configurar Auth guard en './config/auth.php'
    ```php
    // ...

    'defaults' => [
      'guard' => 'api',
      'passwords' => 'users',
    ],

    // ...

    'guards' => [
      'web' => [
        'driver' => 'session',
        'provider' => 'users',
      ],

      'api' => [
        'driver' => 'jwt',
        'provider' => 'users',
      ],
    ],
    ```
7. Generar [controlador de authenticación](#autenticación) y añadir las excepciones en el constructor
8. Crear las requests [Login](#solicitud-login) y [Register](#solicitud-register)
9. Crear los métodos [login](#método-login), [logout](#método-logout), [register](#método-register), [profile](#método-profile) y [refresh](#método-refresh-token) en el [controlador de authenticación](#autenticación).
10. Crear [rutas de cada método](#rutas-de-autenticación)
11. Crear render para mensajes de excepciones en el archivo './app/Exceptions/Handler.php'
    ```php
    use Exception;
    use Tymon\JWTAuth\Facades\JWTAuth;
    // ...

    class Handler extends ExceptionHandler
    {
      // ...

    public function render($request, Throwable $exception)
      {
        try {
          JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
          if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
            return response()->json(['success' => false, 'message' => 'Token is Invalid'], 401);
          } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
            return response()->json(['success' => false, 'message' => 'Token is Expired'], 401);
          } {
            return response()->json(['success' => false, 'message' => 'Authorization Token not found'], 401);
          }
        }

        return parent::render($request, $exception);
      }
    }
    ```

### Router

#### Endpoints

| Method | Endpoint           | Description                                  |
| ------ | ------------------ | -------------------------------------------- |
| POST   | /api/auth/register | Registrar nuevo usuario                      |
| POST   | /api/auth/login    | Inicio de sesión y generación de token       |
| GET    | /api/auth/profile  | Obtener información del usuario actual       |
| POST   | /api/auth/refresh  | Refrescar acceso token                       |
| POST   | /api/auth/logout   | Invalidar el token actual y cerrar la sesión |

#### Rutas de autenticación
```php
Route::group([
  'middleware' => 'api',
  'prefix' => 'auth'
], function ($router) {
  Route::post('login', [AuthController::class, 'login']);
  Route::post('logout', [AuthController::class, 'logout']);
  Route::post('register', [AuthController::class, 'register']);
  Route::post('refresh', [AuthController::class, 'refresh']);
  Route::get('profile', [AuthController::class, 'profile']);
});
```

### Controllers

#### Crear nuevo controlador

```bash
php artisan make:controller Api/AuthController
```

#### Autenticación

##### Método constructor

```php
/**
 * Create a new AuthController instance.
 *
 * @return void
 */
public function __construct()
{
  $this->middleware('auth:api', ['except' => ['login']]);
}
```

##### Método Login

```php
/**
 * Get a JWT via given credentials.
 *
 * @return \Illuminate\Http\JsonResponse
 */
public function login(LoginRequest $request)
{
  $credentials = $request->validated();
  try {
    if (!$token = JWTAuth::attempt($credentials)) {
      return response([
        'success' => false,
        'message' => 'Invalid email or password, try again',
      ], 401);
    }

    $user = JWTAuth::user();
  } catch (JWTException $e) {
    return response([
      'success' => false,
      'message' => 'Technical error!'
    ], 500);
  }
  return $this->respondWithToken($token, $user, 'User login successfully!');
}
```

##### Método Logout

```php
/**
 * Log the user out (Invalidate the token).
 *
 * @return \Illuminate\Http\JsonResponse
 */
public function logout()
{
  JWTAuth::parseToken()->invalidate();
  return response()->json([
    'success' => true,
    'message' => 'User logout successfully!'
  ]);
}
```

##### Método Register

```php
/**
 * Create new user
 * 
 * @return void
 */
public function register(RegisterRequest $request)
{
  $data = $request->validated();
  $user = User::create([
    'name' => $data['name'],
    'email' => $data['email'],
    'password' => bcrypt($data['password'])
  ]);

  $token = JWTAuth::fromUser($user);
  return $this->respondWithToken($token, $user, 'User created successfully!');
}
```

##### Método Profile

```php
/**
 * Get the authenticated User.
 *
 * @return \Illuminate\Http\JsonResponse
 */
public function profile()
{
  $user = JWTAuth::parseToken()->authenticate();
  return response()->json([
    'success' => true,
    'message' => 'User data found successfully!',
    'data' => ['user' => $user]
  ]);
}
```

##### Método Refresh Token

```php
/**
 * Refresh a token.
 *
 * @return \Illuminate\Http\JsonResponse
 */
public function refresh()
{
  $user = JWTAuth::parseToken()->authenticate();
  $newToken = JWTAuth::refresh();
  return $this->respondWithToken($newToken, $user, 'Token refresh successfully!');
}
```

##### Método Respond with Token

```php
/**
 * Get the token array structure.
 *
 * @param  string $token
 *
 * @return \Illuminate\Http\JsonResponse
 */
public function respondWithToken($token, $user, $message)
{
  return response()->json([
    'success' => true,
    'message' => $message,
    'data' => [
      'user' => $user,
      'authorization' => [
        'access_token' => $token,
        'token_type' => 'bearer',
        'expires_in' => JWTAuth::factory()->getTTL() * 60
      ]
    ],
  ], 200);
}
```

#### Blog

### Requests

#### Crear request

```bash
php artisan make:request LoginRequest
```

#### Solicitud Login

```php
public function authorize(): bool
{
  return true; // <-- True
}

// ...

public function rules(): array
{
  return [
    'email' => 'required|string|email|max:100|exists:users,email',
    'password' => 'required|string|min:6'
  ];
}
```

#### Solicitud Register

```php
use Illuminate\Validation\Rules\Password;
// ...

public function authorize(): bool
{
  public function authorize(): bool
  {
    return Auth::user() && $this->isAdmin();
  }
}

// ...

public function isAdmin(): bool
{
  // if (!$this->role === 'admin') {
  if (!true) {
    throw new HttpResponseException(response()->json([
      'success' => false,
      'message' => 'Unauthorized'
    ], 403));
  }

  return true;
}

// ...

public function rules(): array
{
  return [
    'name' => 'required|string|max:100',
    'email' => 'required|string|email|max:100|unique:users,email',
    'password' => [
      'required', 'string', 'confirmed', Password::min(6)->letters()->numbers()
    ]
  ];
}
```

### Models

#### Generar modelo

```bash
php artisan make:model Post
```

#### User

```php
use Tymon\JWTAuth\Contracts\JWTSubject;
// ...

class User extends Authenticatable implements JWTSubject
{
  // ...

  /**
   * Get the identifier that will be stored in the subject claim of the JWT.
   *
   * @return mixed
   */
  public function getJWTIdentifier()
  {
    return $this->getKey();
  }

  /**
   * Return a key value array, containing any custom claims to be added to the JWT.
   *
   * @return array
   */
  public function getJWTCustomClaims()
  {
    return [];
  }
}
```

### Migrations

#### Generar migración

La convención de nomenclatura `create_<nombre en plural>_table` es una práctica recomendada:

```bash
php artisan make:migration create_posts_table
```

## Constribución

> En este caso hice cada método de autenticación comparando los siguientes post a modo de referencia

- **Doc:** [JSON Web Token Authentication for Laravel & Lumen](https://jwt-auth.readthedocs.io/en/develop/quick-start/) - Sean Tymon  
- **Blog:** [Tutorial Laravel 8 API REST con autentificación JWT (CRUD)](https://andresledo.es/php/laravel/api-rest-autentificacion-jwt/) - andresledo  
- **Blog:** [Laravel 10 jwt auth using tymon/jwt-auth](https://dev.to/debo2696/laravel-10-jwt-auth-using-tymonjwt-auth-297g) - Debajyoti Das  
- **Blog:** [Laravel 10 JWT - Complete API Authentication Tutorial](https://www.laravelia.com/post/laravel-10-jwt-complete-api-authentication-tutorial) - Mahedi Hasan  
- **Blog:** [Laravel 10 JWT Rest API Authentication Tutorial Example](https://www.tutsmake.com/laravel-10-jwt-rest-api-authentication-example-tutorial/) - Devendra Dode  
- **Blog:** [Autenticación con JWT en Laravel](https://www.nigmacode.com/laravel/jwt-en-laravel/) - nigmacode  
- **Github:** [laravel-jwt-api-starter](https://github.com/ph-hitachi/laravel-jwt-api-starter) - Justin Lee  


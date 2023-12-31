# Proyecto: Autenticación con JSON Web Token

<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>

En el ejercicio anterior, el video supuestamente decia que era JWT pero en realidad era usando cookies con sanctum por lo tanto en este ejercicio realizaré la autenticación simple con JSON Web Token

## Contenido

- [Proyecto: Autenticación con JSON Web Token](#proyecto-autenticación-con-json-web-token)
  - [Contenido](#contenido)
  - [Configuracion](#configuracion)
      - [1. Crear proyecto](#1-crear-proyecto)
      - [2. Para autocompletado preciso del editor IDE Helper for Laravel](#2-para-autocompletado-preciso-del-editor-ide-helper-for-laravel)
      - [3. JWT por Tymon](#3-jwt-por-tymon)
      - [4. Añadir provider JWT en './config/app.php'](#4-añadir-provider-jwt-en-configappphp)
      - [5. Publicar provider JWT](#5-publicar-provider-jwt)
      - [6. Generar una clave secreta JWT en .env](#6-generar-una-clave-secreta-jwt-en-env)
      - [7. Configurar auth para jwt](#7-configurar-auth-para-jwt)
      - [8. Implementar JWTSubject en el modelo user](#8-implementar-jwtsubject-en-el-modelo-user)
  - [Uso](#uso)
      - [1. Después de clonar el repositorio, instalar dependencias](#1-después-de-clonar-el-repositorio-instalar-dependencias)
      - [2. Iniciar servidor](#2-iniciar-servidor)
      - [3. Duplicar archivo `.env.example` y ponerle el nombre `.env` (probablemente deba generar la APP\_KEY con el botón verde y reiniciar la página)](#3-duplicar-archivo-envexample-y-ponerle-el-nombre-env-probablemente-deba-generar-la-app_key-con-el-botón-verde-y-reiniciar-la-página)
      - [4. Crear base de datos MySQL en XAMPP con el nombre 'laravel-api-jwt' o cambiar nombre en el archivo .env `DB_DATABASE`](#4-crear-base-de-datos-mysql-en-xampp-con-el-nombre-laravel-api-jwt-o-cambiar-nombre-en-el-archivo-env-db_database)
      - [5. Ejecutar migraciones](#5-ejecutar-migraciones)
  - [Notas](#notas)
    - [Rutas](#rutas)
    - [Controllers](#controllers)
      - [Crear controlador](#crear-controlador)
      - [Método Constructor](#método-constructor)
      - [Método Login](#método-login)
      - [Método Me](#método-me)
      - [Método Logout](#método-logout)
      - [Método Refresh](#método-refresh)
      - [Método Respond with Token](#método-respond-with-token)
      - [Método Register](#método-register)
  - [Constribución](#constribución)

## Configuracion

#### 1. Crear proyecto

```bash
composer create-project laravel/laravel 5.authentication-jwt
```

#### 2. Para autocompletado preciso del editor [IDE Helper for Laravel](https://github.com/barryvdh/laravel-ide-helper)

```bash
composer require --dev barryvdh/laravel-ide-helper
```

#### 3. JWT por [Tymon](https://jwt-auth.readthedocs.io/en/develop/)

```bash
composer require tymon/jwt-auth
```

#### 4. Añadir provider JWT en './config/app.php'

```php
// ...

'providers' => ServiceProvider::defaultProviders()->merge([
  // ...
  
  Tymon\JWTAuth\Providers\LaravelServiceProvider::class,
])->toArray(),

// ...
```

#### 5. Publicar provider JWT

```bash
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
```

#### 6. Generar una clave secreta JWT en .env

```bash
php artisan jwt:secret
```

#### 7. Configurar auth para jwt

En el archivo './config/auth.php' configuramos lo siguiente:

```php
// ...

'defaults' => [
  'guard' => 'api',
  'passwords' => 'users',
]

// ...

'guards' => [
  'web' => [
    'driver' => 'session',
    'provider' => 'users',
  ],

  'api' => [
    'driver' => 'jwt',
    'provider' => 'users',
    'hash' => false
  ],
],

// ...
```

#### 8. Implementar JWTSubject en el modelo user

```php
use Tymon\JWTAuth\Contracts\JWTSubject;
// ...

class User extends Authenticatable implements JWTSubject
{
  // ...  

  public function getJWTIdentifier()
  {
    return $this->getKey();
  }

  public function getJWTCustomClaims()
  {
    return [];
  }
}
```

## Uso

#### 1. Después de clonar el repositorio, instalar dependencias

```bash
composer install
```

#### 2. Iniciar servidor

```bash
php artisan serve
```

#### 3. Duplicar archivo `.env.example` y ponerle el nombre `.env` (probablemente deba generar la APP_KEY con el botón verde y reiniciar la página)


#### 4. Crear base de datos MySQL en XAMPP con el nombre 'laravel-api-jwt' o cambiar nombre en el archivo .env `DB_DATABASE`

#### 5. Ejecutar migraciones

```bash
php artisan migrate
```

## Notas

### Rutas

```php
use App\Http\Controllers\AuthController;
// ...

Route::group([
  'middleware' => 'api',
  'prefix' => 'auth'
], function ($route) {
  Route::post('login', [AuthController::class, 'login']);
  Route::post('logout', [AuthController::class, 'logout']);
  Route::post('refresh', [AuthController::class, 'refresh']);
  Route::post('me', [AuthController::class, 'me']);
});
```

### Controllers

#### Crear controlador

```bash
php artisan make:controller AuthController
```

#### Método Constructor

```php
// ...

/**
   * Create a new AuthController instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->middleware('auth:api', ['except' => ['login', 'register']]);
  }

// ...
```

#### Método Login

```php
// ...

/**
 * Get a JWT via given credentials.
 *
 * @return \Illuminate\Http\JsonResponse
 */
public function login()
{
  $credentials = request(['email', 'password']);

  if (!$token = auth()->attempt($credentials)) {
    return response()->json(['error' => 'Unauthorized'], 401);
  }

  return $this->respondWithToken($token);
}

// ...
```

#### Método Me

```php
// ...

/**
 * Get the authenticated User.
 *
 * @return \Illuminate\Http\JsonResponse
 */
public function me()
{
  return response()->json(auth()->user());
}

// ...
```

#### Método Logout

```php
// ...

/**
 * Log the user out (Invalidate the token).
 *
 * @return \Illuminate\Http\JsonResponse
 */
public function logout()
{
  auth()->logout();

  return response()->json(['message' => 'Successfully logged out']);
}

// ...
```

#### Método Refresh

```php
// ...

/**
 * Refresh a token.
 *
 * @return \Illuminate\Http\JsonResponse
 */
public function refresh()
{
  return $this->respondWithToken(auth()->refresh());
}

// ...
```

#### Método Respond with Token

```php
// ...

/**
 * Get the token array structure.
 *
 * @param  string $token
 *
 * @return \Illuminate\Http\JsonResponse
 */
protected function respondWithToken($token)
{
  return response()->json([
    'access_token' => $token,
    'token_type' => 'bearer',
    'expires_in' => auth()->factory()->getTTL() * 60
  ]);
}

// ...
```

#### Método Register

```php
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
// ...

/**
 * Register new user
 *
 * @return \Illuminate\Http\JsonResponse
 */
public function register(Request $request)
{
  $validator = Validator::make($request->all(), [
    'name' => 'required',
    'email' => 'required|string|email|max:100|unique:users',
    'password' => 'required|string|min:6|confirmed',
  ]);

  if ($validator->fails()) {
    return response()->json($validator->errors()->toJson(), 400);
  }

  $user = User::create(array_merge(
    $validator->validate(),
    ['password' => bcrypt($request->password)]
  ));

  return response()->json([
    'message' => 'Success!',
    'user' => $user,
  ], 201);
}

// ...
```

## Constribución

Video de Youtube: [API REST JWT AUTH - LARAVEL](https://youtu.be/kP2N_eEv-iA)  
Por: [Informática DP](https://www.youtube.com/@InformaticaDP)

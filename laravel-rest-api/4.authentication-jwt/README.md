# Proyecto: Autenticación con JSON Web Token

<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>

En este ejercicio realicé una autenticación usando JWT por primera vez, el autor del video de YouTube realizó una lista de reproducción acerca de esto y además realizó la autenticación en React

## Contenido

- [Proyecto: Autenticación con JSON Web Token](#proyecto-autenticación-con-json-web-token)
  - [Contenido](#contenido)
  - [Instalaciones](#instalaciones)
      - [1. Crear proyecto](#1-crear-proyecto)
      - [2. Para autocompletado preciso del editor IDE Helper for Laravel](#2-para-autocompletado-preciso-del-editor-ide-helper-for-laravel)
      - [3. Laravel Sanctum](#3-laravel-sanctum)
  - [Uso](#uso)
      - [1. Después de clonar el repositorio, instalar dependencias](#1-después-de-clonar-el-repositorio-instalar-dependencias)
      - [2. Iniciar servidor](#2-iniciar-servidor)
      - [3. Duplicar archivo `.env.example` y ponerle el nombre `.env` (probablemente deba generar la APP\_KEY con el botón verde y reiniciar la página)](#3-duplicar-archivo-envexample-y-ponerle-el-nombre-env-probablemente-deba-generar-la-app_key-con-el-botón-verde-y-reiniciar-la-página)
      - [4. Crear base de datos MySQL en XAMPP con el nombre 'laravel-api-jwt' o cambiar nombre en el archivo .env `DB_DATABASE`](#4-crear-base-de-datos-mysql-en-xampp-con-el-nombre-laravel-api-jwt-o-cambiar-nombre-en-el-archivo-env-db_database)
      - [5. Ejecutar migraciones](#5-ejecutar-migraciones)
  - [Notas](#notas)
    - [Router](#router)
      - [Crear rutas](#crear-rutas)
    - [Controllers](#controllers)
      - [Crear controlador](#crear-controlador)
      - [Controlador user](#controlador-user)
      - [Controlador register](#controlador-register)
      - [Controlador login](#controlador-login)
    - [Migrations](#migrations)
      - [Migración de tabla user](#migración-de-tabla-user)
    - [Models](#models)
      - [Modelo usuario](#modelo-usuario)
      - [Verificar HasApiTokens](#verificar-hasapitokens)
  - [Constribución](#constribución)

## Instalaciones

#### 1. Crear proyecto

```bash
composer create-project laravel/laravel 4.authentication-jwt
```

#### 2. Para autocompletado preciso del editor [IDE Helper for Laravel](https://github.com/barryvdh/laravel-ide-helper)

```bash
composer require --dev barryvdh/laravel-ide-helper
```

#### 3. Laravel Sanctum

```bash
composer require laravel/sanctum
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

### Router

#### Crear rutas

En lugar de usar la ruta para autenticación Sanctum, usarémos esta ruta para realizar el proceso en el controlador

```php
Route::get('user', [AuthController::class, 'user']);
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
```

### Controllers

#### Crear controlador

```bash
php artisan make:controller AuthController
```

#### Controlador user


```php
// ...

function user()
{
  return 'Authenticated user';
}

// ...
```

#### Controlador register

```php
// ...

function register(Request $request)
{
  return User::create([
    'name' => $request->input('name'),
    'email' => $request->input('email'),
    'password' => Hash::make($request->input('password')),
  ]);
}

// ...
```

#### Controlador login

```php
// ...

function login(Request $request)
{
  if (!Auth::attempt($request->only('email', 'password'))) {
    return response([
      'message' => 'Invalid credentials!'
    ], Response::HTTP_UNAUTHORIZED);
  }

  /** @var User $user */
  $user = Auth::user();
  $token = $user->createToken('token')->plainTextToken;

  $cookie = cookie('jwt', $token, 60 * 24); // 1 day

  return response([
    'message' => 'Success'
  ])->withCookie($cookie);
}

// ...
```

### Migrations

#### Migración de tabla user

1. Eliminamos todas las migraciones excepto ...000000_create_user_table.php
2. En esta migración simplificaremos la tabla con estas columnas

```php
// ...

public function up(): void
{
  Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->string('password');
    $table->timestamps();
  });
}

// ...
```


### Models

#### Modelo usuario

Borramos los arreglos excepto estos 2 con estos indices

```php
// ...

protected $fillable = [
  'name',
  'email',
  'password',
];

/**
 * The attributes that should be hidden for serialization.
 *
 * @var array<int, string>
 */
protected $hidden = [
  'password'
];

// ...
```

#### Verificar HasApiTokens

```php
use Laravel\Sanctum\HasApiTokens;
// ...

use HasApiTokens, HasFactory, Notifiable, HasApiTokens;

// ...
```

## Constribución

Video de Youtube: [React & Laravel JWT Authentication](https://youtu.be/jIzPuM76-nI?list=PLlameCF3cMEuSQb-UCPDcUV_re5uXaOU9)  
Por: [Scalable Scripts](https://www.youtube.com/@ScalableScripts)

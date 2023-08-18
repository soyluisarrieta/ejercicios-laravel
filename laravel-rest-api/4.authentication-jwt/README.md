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
  - [Uso](#uso)
      - [1. Después de clonar el repositorio, instalar dependencias](#1-después-de-clonar-el-repositorio-instalar-dependencias)
      - [2. Iniciar servidor](#2-iniciar-servidor)
      - [3. Duplicar archivo `.env.example` y ponerle el nombre `.env` (probablemente deba generar la APP\_KEY con el botón verde y reiniciar la página)](#3-duplicar-archivo-envexample-y-ponerle-el-nombre-env-probablemente-deba-generar-la-app_key-con-el-botón-verde-y-reiniciar-la-página)
      - [4. Crear base de datos MySQL en XAMPP con el nombre 'laravel-api-jwt' o cambiar nombre en el archivo .env `DB_DATABASE`](#4-crear-base-de-datos-mysql-en-xampp-con-el-nombre-laravel-api-jwt-o-cambiar-nombre-en-el-archivo-env-db_database)
      - [5. Ejecutar migraciones](#5-ejecutar-migraciones)
  - [Notas](#notas)
    - [Router](#router)
      - [Crear ruta](#crear-ruta)
    - [Controllers](#controllers)
      - [Crear controlador](#crear-controlador)
    - [Migrations](#migrations)
      - [Migración de tabla user](#migración-de-tabla-user)
    - [Models](#models)
      - [Modelo usuario](#modelo-usuario)
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

#### Crear ruta

En lugar de usar la ruta para autenticación Sanctum, usarémos esta ruta para realizar el proceso en el controlador

```php
Route::get('user', [AuthController::class, 'user']);
```

### Controllers

#### Crear controlador

```bash
php artisan make:controller AuthController
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


## Constribución

Video de Youtube: [React & Laravel JWT Authentication](https://youtu.be/jIzPuM76-nI?list=PLlameCF3cMEuSQb-UCPDcUV_re5uXaOU9)  
Por: [Scalable Scripts](https://www.youtube.com/@ScalableScripts)

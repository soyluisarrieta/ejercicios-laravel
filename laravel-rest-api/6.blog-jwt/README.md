# Proyecto: CRUD Blog usando JWT

<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>

En este ejercicio quise practicar tanto la autenticación JWT para Laravel 10 siguiendo un post de un blog que me llamó la atención porque usa la clase 'JWTAuth' y un CRUD para un blog simple

## Contenido

- [Proyecto: CRUD Blog usando JWT](#proyecto-crud-blog-usando-jwt)
  - [Contenido](#contenido)
  - [Instalaciones](#instalaciones)
      - [1. Crear proyecto](#1-crear-proyecto)
      - [2. Para autocompletado preciso del editor IDE Helper for Laravel](#2-para-autocompletado-preciso-del-editor-ide-helper-for-laravel)
      - [3. JWT por Tymon](#3-jwt-por-tymon)
  - [Uso](#uso)
      - [1. Después de clonar el repositorio, instalar dependencias](#1-después-de-clonar-el-repositorio-instalar-dependencias)
      - [2. Iniciar servidor](#2-iniciar-servidor)
      - [3. Duplicar archivo `.env.example` y ponerle el nombre `.env` (probablemente deba generar la APP\_KEY con el botón verde y reiniciar la página)](#3-duplicar-archivo-envexample-y-ponerle-el-nombre-env-probablemente-deba-generar-la-app_key-con-el-botón-verde-y-reiniciar-la-página)
      - [4. Crear base de datos MySQL en XAMPP con el nombre 'laravel-api-blog-jwt' o cambiar nombre en el archivo .env `DB_DATABASE`](#4-crear-base-de-datos-mysql-en-xampp-con-el-nombre-laravel-api-blog-jwt-o-cambiar-nombre-en-el-archivo-env-db_database)
      - [5. Ejecutar migraciones](#5-ejecutar-migraciones)
  - [Notas](#notas)
    - [Rutas](#rutas)
    - [Controllers](#controllers)
      - [Crear controlador](#crear-controlador)
  - [Constribución](#constribución)

## Instalaciones

#### 1. Crear proyecto

```bash
composer create-project --prefer-dist laravel/laravel 6.blog-jwt
```

#### 2. Para autocompletado preciso del editor [IDE Helper for Laravel](https://github.com/barryvdh/laravel-ide-helper)

```bash
composer require --dev barryvdh/laravel-ide-helper
```

#### 3. JWT por [Tymon](https://jwt-auth.readthedocs.io/en/develop/)

```bash
composer require tymon/jwt-auth
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


#### 4. Crear base de datos MySQL en XAMPP con el nombre 'laravel-api-blog-jwt' o cambiar nombre en el archivo .env `DB_DATABASE`

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


## Constribución

Blog post: [Laravel 10 JWT Rest API Authentication Tutorial Example](https://www.tutsmake.com/laravel-10-jwt-rest-api-authentication-example-tutorial/)  
Por: [Devendra Dode](https://www.youtube.com/@InformaticaDP)

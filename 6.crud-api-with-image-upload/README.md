# Proyecto: CRUD con subida de imágenes

<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>

En este ejercicio me enfoqué en interiorizar más un CRUD pero esta vez con posibilidad de subida de imágenes.

## Contenido

- [Proyecto: CRUD con subida de imágenes](#proyecto-crud-con-subida-de-imágenes)
  - [Contenido](#contenido)
  - [Instalaciones](#instalaciones)
      - [1. Crear proyecto](#1-crear-proyecto)
      - [2. Para autocompletado preciso del editor IDE Helper for Laravel](#2-para-autocompletado-preciso-del-editor-ide-helper-for-laravel)
  - [Uso](#uso)
      - [1. Después de clonar el repositorio, instalar dependencias](#1-después-de-clonar-el-repositorio-instalar-dependencias)
      - [2. Iniciar servidor](#2-iniciar-servidor)
      - [3. Duplicar archivo `.env.example` y ponerle el nombre `.env` (probablemente deba generar la APP\_KEY con el botón verde y reiniciar la página)](#3-duplicar-archivo-envexample-y-ponerle-el-nombre-env-probablemente-deba-generar-la-app_key-con-el-botón-verde-y-reiniciar-la-página)
      - [4. Crear base de datos MySQL en XAMPP con el nombre 'laravel-crud-with-image-upload' o cambiar nombre en el archivo .env `DB_DATABASE`](#4-crear-base-de-datos-mysql-en-xampp-con-el-nombre-laravel-crud-with-image-upload-o-cambiar-nombre-en-el-archivo-env-db_database)
      - [5. Ejecutar migraciones](#5-ejecutar-migraciones)
  - [Proceso para el CRUD](#proceso-para-el-crud)
    - [Router](#router)
      - [Crear una ruta](#crear-una-ruta)
    - [Models \& Migrations (Database)](#models--migrations-database)
      - [1. Crear modelo y una migración](#1-crear-modelo-y-una-migración)
      - [2. Añadir columnas a la migración](#2-añadir-columnas-a-la-migración)
      - [3. Definir columnas rellenables en el modelo](#3-definir-columnas-rellenables-en-el-modelo)
      - [4. Migrar base de datos para añadir las nuevas tablas](#4-migrar-base-de-datos-para-añadir-las-nuevas-tablas)
    - [Controllers](#controllers)
      - [Crear controlador con los métodos](#crear-controlador-con-los-métodos)
      - [1. Método Index](#1-método-index)
      - [2. Método create](#2-método-create)
      - [3. Método store](#3-método-store)
      - [4. Método show](#4-método-show)
      - [5. Método edit](#5-método-edit)
      - [6. Método update](#6-método-update)
      - [7. Método destroy](#7-método-destroy)
    - [Requests](#requests)
      - [Crear una solicitud](#crear-una-solicitud)
      - [Autorizar solicitud](#autorizar-solicitud)
      - [Añadir las reglas de validaciones](#añadir-las-reglas-de-validaciones)
      - [Crear método para mensajes de error](#crear-método-para-mensajes-de-error)
  - [Constribución](#constribución)

## Instalaciones

#### 1. Crear proyecto

```bash
composer create-project laravel/laravel 3.crud-api-with-image-upload
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


#### 4. Crear base de datos MySQL en XAMPP con el nombre 'laravel-crud-with-image-upload' o cambiar nombre en el archivo .env `DB_DATABASE`

#### 5. Ejecutar migraciones

```bash
php artisan migrate
```

## Proceso para el CRUD

### Router

#### Crear una ruta

```php
Route::get('products', [ProductController::class, 'index']);
```

### Models & Migrations (Database)

#### 1. Crear modelo y una migración

```bash
php artisan make:model Product -m
```

#### 2. Añadir columnas a la migración

A la migración que se debe añadir las columnas con su [tipo de dato](https://laravel.com/docs/10.x/migrations#available-column-types)

```php
// ...

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('products', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->string('image');
      $table->text('description');
      $table->timestamps();
    });
  }

  // ...
}
```

#### 3. Definir columnas rellenables en el modelo

```php
// ...

class Product extends Model
{
  // ...

  protected $fillable = [
    'name',
    'image',
    'description'
  ];
}
```

#### 4. Migrar base de datos para añadir las nuevas tablas

```bash
php artisan migrate
```

### Controllers

#### Crear controlador con los métodos

Se compone de los siguientes método:

1. index: Listar todos los productos
2. create: 
3. store: Crear un nuevo producto
4. show: Mostrar un producto por id
5. edit:
6. update: 
7. destroy: Eliminar un producto

```bash
php artisan make:controller ProductController -r
```

#### 1. Método Index

Retornar todos los productos

```php
// ...

public function index()
  {
    $products = Product::all();

    return response()->json([
      'products' => $products
    ], 200);
  }

// ...
```

#### 2. Método create

#### 3. Método store

Crear nuevo producto

```php
// ...

public function store(ProductStoreRequest $request)
  {
    try {
      $imageName = Str::random(32) . "." . $request->image->getClientOriginalExtension();

      // Create Product
      Product::create([
        'name' => $request->name,
        'image' => $imageName,
        'description' => $request->description,
      ]);

      // Save Image in Store folder
      Storage::disk('public')->put($imageName, file_get_contents($request->image));

      // Return Json Response
      return response()->json([
        'message' => 'Product successfully created.'
      ], 200);
    } catch (\Throwable $th) {
      return response()->json([
        'message' => 'Something went really wrong!'
      ], 500);
    }
  }


// ...
```

#### 4. Método show

Retornar los detalles de un producto por id

```php
// ...

  public function show(string $id)
  {
    // Product detail
    $product = Product::find($id);
    if (!$product) {
      return response()->json([
        'message' => 'Product not found.'
      ], 404);
    }

    // Return Json Response
    return response()->json([
      'product' => $product
    ], 200);
  }

// ...
```


#### 5. Método edit

#### 6. Método update

Actualizar un producto por id

#### 7. Método destroy

Eliminar un producto por id

### Requests

#### Crear una solicitud

```bash
php artisan make:request ProductStoreRequest
```

#### Autorizar solicitud

```php
// ...

  public function authorize(): bool
  {
    return true;
  }

//...
```

#### Añadir las [reglas de validaciones](https://laravel.com/docs/10.x/validation#available-validation-rules)

```php
// ...

public function rules(): array
  {
    if (request()->isMethod('post')) {
      return [
        'name' => 'required|string|max:255',
        'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        'description' => 'required|string',
      ];
    } else {
      return [
        'name' => 'required|string|max:255',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        'description' => 'required|string',
      ];
    }
  }

// ...
```

#### Crear método para mensajes de error

```php
// ...

public function messages()
  {
    if (request()->isMethod('post')) {
      return [
        'name.required' => '¡El nombre es requerido!',
        'image.required' => '¡La imagen es requerida!',
        'description.required' => '¡La descripción es requerida!',
      ];
    } else {
      return [
        'name.required' => '¡El nombre es requerido!',
        'image.required' => '¡La imagen es requerida!',
        'description.required' => '¡La descripción es requerida!',
      ];
    }
  }

// ...
```

## Constribución

Video de Youtube: [React Laravel 10 REST API Crud (Create, Read, Update and Delete) with Upload image](https://youtu.be/g00WAcdYRpY)  
Por: [Cairocoders](https://www.youtube.com/@cairocoders)

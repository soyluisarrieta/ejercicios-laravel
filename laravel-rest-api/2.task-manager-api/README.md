# Proyecto: Administrador de Tareas con Laravel API

<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>

El ejercicio se centra en la implementación de un administrador de tareas mediante el uso de la API de Laravel. El objetivo principal es adquirir habilidades en la utilización de este framework en un entorno donde solo se admite PHP, como es el caso de un hosting compartido. Para lograr esto, he seguido un tutorial en YouTube el cual me proporcionará una comprensión básica de cómo utilizar Laravel, centrándome en su capacidad como API sólida y respaldada por un gran soporte.

## Contenido

- [Proyecto: Administrador de Tareas con Laravel API](#proyecto-administrador-de-tareas-con-laravel-api)
  - [Contenido](#contenido)
  - [Instalaciones](#instalaciones)
      - [1. Crear proyecto](#1-crear-proyecto)
      - [2. Para autocompletado preciso del editor IDE Helper for Laravel](#2-para-autocompletado-preciso-del-editor-ide-helper-for-laravel)
      - [3. Crear solicitudes Eloquent para API usando Laravel-query-builder](#3-crear-solicitudes-eloquent-para-api-usando-laravel-query-builder)
  - [Uso](#uso)
      - [1. Después de clonar el repositorio, instalar dependencias](#1-después-de-clonar-el-repositorio-instalar-dependencias)
      - [2. Iniciar servidor](#2-iniciar-servidor)
      - [3. Duplicar archivo `.env.example` y ponerle el nombre `.env` (probablemente deba generar la APP\_KEY con el botón verde y reiniciar la página)](#3-duplicar-archivo-envexample-y-ponerle-el-nombre-env-probablemente-deba-generar-la-app_key-con-el-botón-verde-y-reiniciar-la-página)
      - [4. Crear base de datos MySQL en XAMPP con el nombre 'task-manager-api' o cambiar nombre en el archivo .env `DB_DATABASE`](#4-crear-base-de-datos-mysql-en-xampp-con-el-nombre-task-manager-api-o-cambiar-nombre-en-el-archivo-env-db_database)
      - [5. Ejecutar migraciones](#5-ejecutar-migraciones)
  - [Notas](#notas)
    - [Migrations](#migrations)
      - [Refrescar migraciones](#refrescar-migraciones)
      - [Columnas a una tabla](#columnas-a-una-tabla)
    - [Router](#router)
      - [Crear rutas](#crear-rutas)
      - [Ruta para administrar recurso](#ruta-para-administrar-recurso)
      - [Ruta de recursos con acciones especificas](#ruta-de-recursos-con-acciones-especificas)
      - [Listar todas las rutas configuradas](#listar-todas-las-rutas-configuradas)
    - [Models](#models)
      - [Crear Modelo con un Controlador y una Migración](#crear-modelo-con-un-controlador-y-una-migración)
      - [Crear una relación hacia una tabla (Foreign Key)](#crear-una-relación-hacia-una-tabla-foreign-key)
      - [Restringir campos rellenables](#restringir-campos-rellenables)
      - [Representar atributos con el casts para la respuesta json](#representar-atributos-con-el-casts-para-la-respuesta-json)
      - [Ocultar atributos para la respuesta json](#ocultar-atributos-para-la-respuesta-json)
    - [Controllers](#controllers)
      - [Obtener todos las tareas](#obtener-todos-las-tareas)
      - [Obtener datos de una tarea](#obtener-datos-de-una-tarea)
      - [Retornar datos con paginación](#retornar-datos-con-paginación)
    - [Resources](#resources)
      - [Crear un recurso](#crear-un-recurso)
    - [Requests](#requests)
      - [Crear una solicitud](#crear-una-solicitud)
      - [Validar datos de la solicitud](#validar-datos-de-la-solicitud)
    - [Factories](#factories)
      - [Crear fábrica para un modelo](#crear-fábrica-para-un-modelo)
      - [Definir como se fabricarán los datos](#definir-como-se-fabricarán-los-datos)
    - [Tinker Shell](#tinker-shell)
      - [Crear manualmente una tarea](#crear-manualmente-una-tarea)
  - [Constribución](#constribución)

## Instalaciones

#### 1. Crear proyecto

```bash
composer create-project laravel/laravel 2.task-manage-api
```

#### 2. Para autocompletado preciso del editor [IDE Helper for Laravel](https://github.com/barryvdh/laravel-ide-helper)

```bash
composer require --dev barryvdh/laravel-ide-helper
```

#### 3. Crear solicitudes Eloquent para API usando [Laravel-query-builder](https://github.com/spatie/laravel-query-builder)

```bash
composer require spatie/laravel-query-builder
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


#### 4. Crear base de datos MySQL en XAMPP con el nombre 'task-manager-api' o cambiar nombre en el archivo .env `DB_DATABASE`

#### 5. Ejecutar migraciones

```bash
php artisan migrate
```

## Notas

### Migrations

#### Refrescar migraciones

⚠ Esto elimina las tablas y datos guardados y solo vuelve a crear las tablas

```bash
php artisan migrate:fresh
```

#### Columnas a una tabla

```php
//...

public function up(): void
{
  Schema::create('tasks', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->boolean('is_done')->default(false);
    $table->foreignId('creator_id')->constrained('users');
    $table->timestamps();
  });
}

//...
```

### Router

#### Crear rutas

En el archivo `./routes/api.php` agregamos la ruta indicando el controlador

```php
use App\Http\Controllers\TaskController;

// ...

Route::get('tasks', [TaskController::class, 'index']);
```

#### Ruta para administrar recurso

El administrador de recursos es el CRUD que con tiene por defecto los métodos que debería tener el controlador:

1. index: GET | Listar todo
2. show: GET | Buscar por id
3. store: POST | Crea nuevo
4. update: PUT | Actualizar por id
5. destroy: DELETE | Eliminar por id

La ruta se configura de la siguiente manera:

```php
// ...
Route::apiResource('tasks', TaskController::class);
```

#### Ruta de recursos con acciones especificas

Es posible definir qué método vamos a permitir en un recurso

```php
// ...
Route::apiResource('tasks', TaskController::class)->only([
  'index', 'show' 
]);
```

#### Listar todas las rutas configuradas

```bash
php artisan route:list
```

### Models


#### Crear Modelo con un Controlador y una Migración

```bash
php artisan make:model Task -cm
```

#### Crear una relación hacia una tabla (Foreign Key)

```php
 use Illuminate\Database\Eloquent\Relations\BelongsTo;
// ...

class Task extends Model
{
 // ...

 public function creator(): BelongsTo
  {
    return $this->belongsTo(User::class, 'creator_id');
  }
}
```

También en la tabla User

```php
// ...

class User extends Authenticatable
{
  // ...

  function tasks(): HasMany
  {
    return $this->hasMany(Task::class, 'creator_id');
  }
}
```

#### Restringir campos rellenables

```php
// ...

class Task extends Model
{
  // ...

  protected $fillable = [
    'title',
  ];

  // ...
}
```

#### Representar atributos con el [casts](https://laravel.com/docs/10.x/eloquent-mutators#attribute-casting) para la respuesta json

```php
// ...

class Task extends Model
{
  // ...

  protected $casts = [
    'is_done' => 'boolean'
  ];

  // ...
}
```

#### Ocultar atributos para la respuesta json

```php
// ...

class Task extends Model
{
  // ...

  protected $hidden = [
    'updated_at'
  ];

  // ...
}
```

### Controllers

#### Obtener todos las tareas

Es el método inicial de las rutas resource

```php
use App\Models\Task;

//...

public function index(Request $request)
{
  return response()->json(Task::all());
}

//...
```

#### Obtener datos de una tarea

En este caso cabe resaltar que se están usando los recursos

```php
use App\Http\Resources\TaskCollection;
use App\Http\Resources\TaskResource;

//...

public function show(Request $request, Task $task)
{
  return new TaskResource($task);
}

//...
```


#### Retornar datos con paginación

```php
//...

public function index(Request $request)
  {
    return new TaskCollection(Task::paginate());
  }

//...
```

### Resources

#### Crear un recurso

```bash
php artisan make:resource TaskResource
```

### Requests

#### Crear una solicitud

```bash
php artisan make:request StoreTaskResource
```

#### Validar datos de la solicitud

1. Se debe autorizar la solicitud

```php
//...

class StoreTaskRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  //...
}
```

2. Validación de campos con las [reglas](https://laravel.com/docs/10.x/validation#available-validation-rules)

```php
//...

class StoreTaskRequest extends FormRequest
{
  //...

  public function rules(): array
  {
    return [
      'title' => 'required|max:255'
    ];
  }
}
```

### Factories

Sirve fabricar datos ficticios

#### Crear fábrica para un modelo

```bash
php artisan make:factory TaskFactory --model=Task
```

#### Definir como se fabricarán los datos

```php
// ...

class TaskFactory extends Factory
{
  // ...
  
  public function definition(): array
  {
    return [
      'title' => $this->faker->sentence(),
      'is_done' => false,
    ];
  }
}
```

### Tinker Shell

Tinker es una consola de comandos con la que podremos interactuar con todas las clases y métodos de nuestra aplicación.

```bash
php artisan tinker
```

Si ocurre algun error, probar ejecutar el dump-autoload

```bash
composer dump-autoload
```

Si desea salir de la terminar puede usar `Ctrl`+`C`

#### Crear manualmente una tarea

1. Primero creamos una instancia del objeto
 
```bash
$task = new Task() 
```

2. Rellenamos las propiedades

```bash
$task->title = 'Do my homework'
```

3. Guardamos para que se inserte en la base de datos

```bash
$task->save()
```


## Constribución

Video de Youtube: [Laravel 10 for REST API](https://youtu.be/9FJeoq5z1_Y)  
Por: [Best Tutorial](https://www.youtube.com/@BestTutorial07)

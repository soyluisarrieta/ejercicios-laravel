# Mis apuntes

Autor del curso: [Victor Arana de Coders Free](https://codersfree.com/cursos/aprende-laravel-desde-cero)

```markdown
Usar `composer install` para instalar dependencias de un repositorio clonado
También se debe crear el archivo .env con las variables de .env.example
Al iniciar el proyecto, se debe hacer click en el botón Generate API
```

## Instalaciones previas

1. XAMPP con PHP v8.2
2. Composer
3. NodeJS
4. Git
5. Visual Studio Code
6. Extensiones:
   1. "Laravel Snipperts" por WinnieLin
   2. "Laravel Blade formatter" por Shuhei Hayashibara
   3. "Laravel Blade Snippets" por Winnie Lin
   4. "Laravel goto view" por codingyu
   5. PHP intelephense

## Laravel

### Crear nuevo proyecto

Via Composer

```bash
composer create-project laravel/laravel example-app
```

Via Laravel primero se debe configurar el comando `laravel` para el proyecto

```bash
composer global require laravel/installer

laravel new example-app
```

El comando `laravel new example-app` es una opción más rápida y sencilla, ya que el instalador de Laravel utilizará Composer internamente para descargar e instalar Laravel y sus dependencias.

### Rutas

#### Retornar cadenas

```php
Route::get('/', function () {
  return "Hola mundo";
});
```

#### Rutas estáticas

```php
Route::get('/cursos/create', function () {
  return "Esta es la vista crear un curso";
});
```

#### Rutas dinámicas

```php
Route::get('/cursos/{curso}', function ($curso) {
  return "Bienvenido al curso $curso";
});
```

#### Rutas dinámicas anidadas y opcionales

```php
Route::get('/cursos/{curso}/{categoria?}', function ($curso, $categoria = null) {
  if($categoria){
    return "Bienvenido al curso $curso de la categoría $categoria";
  } else {
    return "Bienvenido al curso $curso";
  }
});
```

El orden en el que se definen las rutas en Laravel es importante, especialmente cuando se usan rutas dinámicas con parámetros. Es necesario definir primero las rutas estáticas antes de las dinámicas para evitar que Laravel confunda una ruta estática con un parámetro de ruta.

#### Retornar vistas

```php
Route::get('/', function () {
  return view('home');
});
```

#### Retornar vistas dentro de carpetas

```php
Route::get('/cursos', function () {
  return view('cursos.index');
});
```

#### Retornar vistas dentro de carpetas

```php
Route::get('/cursos', function () {
  return view('cursos.index');
});
```

#### Retornar vistas con parámetros

```php
Route::get('/cursos/{curso}', function ($curso) {
  return view('cursos.show', ['curso' => $curso]);
});
```

Otra forma de hacer esto sería:

```php
Route::get('/cursos/{curso}', function ($curso) {
  return view('cursos.show', compact('curso'));
});

```

#### Rutas con controlador

Previamente se debe crear el controlador. La siguiente ruta llamará el método `__invoke`

```php
use App\Http\Controllers\HomeController;

Route::get('/', HomeController::class);
```

#### Rutas especificando un método

```php
use App\Http\Controllers\CursosController;

Route::get('/cursos/create', [CursosController::class, 'create']);
```

#### Grupo de rutas

Son rutas que comparten un mismo controlador pero con diferentes métodos

```php
Route::controller(CursoController::class)->group(function () {
  Route::get('/cursos', 'index');
  Route::get('/cursos/create', 'create');
  Route::get('/cursos/{curso}', 'show');
});
```

### Controladores

#### Crear un controlador desde la terminal

```bash
php artisan make:controller HomeController
```

El controlador se crea vacío pero si sólo se va administrar una única ruta, se puede crear el método `__invoke` que es ejecutado por una ruta :

```php
class HomeController extends Controller
{
  public function __invoke()
  {
    return "Esta es la página principal";
  }
}
```

#### Renderizar vistas

```php
public function index()
{
  // Vista directa
  return view('home');
}

public function index()
{
  // Vista dentro de carpeta
  return view('cursos.index');
}

public function index($curso)
{
  // Vista con parámetro
  return view('cursos.index', ['curso' => $curso]);
  // ó
  return view('cursos.index', compact('curso'));
}
```

### Vistas

#### Imprimir parámetros recibidos

```php
<h1>Nombre del curso: <?php echo $curso ?></h1>
```

### Blade

Es importante que el nombre de la plantilla o vista, termine en `.blade.php` por ejemplo `home.blade.php`

#### Crear parámetros para plantillas

```php
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>@yield('title')</title>
</head>
<body>
  @yield('content')
</body>
</html>
```

#### Extender plantilla e inicializar parámetros yield

```php
@extends('layouts.plantilla')

@section('title','Página de inicio')

@section('content')
  <h1>Bienvenido a la página principal</h1>
@endsection
```

#### Imprimir variables con php

```php
@extends('layouts.plantilla')

@section('title','Curso ' . $curso)

@section('content')
  <h1>Bienvenido al curso <?php echo $curso?></h1>
@endsection
```

#### Imprimir variables con blade

```php
@extends('layouts.plantilla')

@section('title','Curso ' . $curso)

@section('content')
  <h1>Bienvenido al curso {{$curso}}</h1>
@endsection
```

### Migraciones

#### Propiedades básicas de cada método

```php
// Integer | Unsigned | Autoincrement
$table->id();

// Varchar | 255
$table->string('name');

// Varchar | 100
$table->string('name', 100);

// Text
$table->text('name');
```

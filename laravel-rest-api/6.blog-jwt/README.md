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
## Endpoints

| Method | Endpoint          | Description                                  |
| ------ | ----------------- | -------------------------------------------- |
| POST   | /api/auth/login   | Inicio de sesión y generación de token       |
| GET    | /api/auth/profile | Obtener información del usuario actual       |
| POST   | /api/auth/refresh | Refrescar acceso token                       |
| POST   | /api/auth/logout  | Invalidar el token actual y cerrar la sesión |

### Models

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


## Constribución

Blog post: [Laravel 10 JWT Rest API Authentication Tutorial Example](https://www.tutsmake.com/laravel-10-jwt-rest-api-authentication-example-tutorial/)  
Por: [Devendra Dode](https://www.youtube.com/@InformaticaDP)

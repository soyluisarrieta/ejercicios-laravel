# Generar un proyecto de Laravel Breeze con React

En este proyecto quise documentar como es generar un proyecto monolito de Laravel breeze con React.

## Instrucciones de instalación

1. Generar proyecto Laravel y comprobar si descargó correctamente.
    ```bash
    composer create-project laravel/laravel laravel-breeze-and-react
    cd laravel-breeze-and-react
    php artisan serve
    ```
2. Instalar laravel/breeze como dependencia de desarrollo
    ```bash
    composer require laravel/breeze --dev
    ```
3. Instalar el starter kit Breeze con React en el proyecto Laravel
    ```bash
    php artisan breeze:install react
    ```
4. Generar proyecto Laravel
    ```bash
    composer require laravel/breeze --dev
    ```
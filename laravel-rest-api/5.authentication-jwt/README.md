# Proyecto: Autenticación con JSON Web Token

<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>

En el ejercicio anterior, el video supuestamente decia que era JWT pero en realidad era usando cookies con sanctum por lo tanto en este ejercicio realizaré la autenticación simple con JSON Web Token

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
    - [Rutas](#rutas)
    - [Controllers](#controllers)
      - [Crear controlador](#crear-controlador)
  - [Constribución](#constribución)

## Instalaciones

#### 1. Crear proyecto

```bash
composer create-project laravel/laravel 5.authentication-jwt
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

### Rutas

```php

```

### Controllers

#### Crear controlador

```bash
php artisan make:controller AuthController
```

## Constribución

Video de Youtube: [API REST JWT AUTH - LARAVEL](https://youtu.be/kP2N_eEv-iA)  
Por: [Informática DP](https://www.youtube.com/@InformaticaDP)

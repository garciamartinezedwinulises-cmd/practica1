# Aplicación Tienda Online - Proyecto Integrador Full-Stack

Este proyecto es una aplicación web SPA completa integrada por una API de servicios construida en Laravel y un cliente de interfaz reactivo diseñado con Vue.js, Pinia y Vue Router.

## Estructura de Carpetas
* backend/ : Código fuente del servidor e infraestructura de Laravel API.
* frontend/ : Código fuente de las vistas y componentes de Vue.js.

## Requisitos Previos
* PHP >= 8.1
* Composer
* Node.js & npm
* Servidor MySQL (XAMPP / Laragon)

## Instalación del Backend (Laravel)
1. Ingresa a la carpeta del servidor:
   cd backend
2. Instala las dependencias del ecosistema de PHP:
   composer install
3. Configura las credenciales de tu base de datos dentro del archivo .env
4. Genera la clave de seguridad del entorno:
   php artisan key:generate
5. Crea el enlace simbólico para la gestión multimedia pública:
   php artisan storage:link
6. Ejecuta las migraciones de las tablas junto con el campo de imagen:
   php artisan migrate
7. Pon en marcha el servidor de desarrollo local:
   php artisan serve

## Instalación del Frontend (Vue.js)
1. Ingresa a la carpeta de la interfaz:
   cd frontend
2. Descarga los paquetes y módulos de desarrollo:
   npm install
3. Levanta el compilador local en tu navegador:
   npm run dev
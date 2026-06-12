<?php

namespace App\Http\Controllers;

// 🛡️ Importamos el espacio de nombres de atributos nativos de OpenAPI
use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "Mi Tienda Online API",
    description: "API REST para gestión de tienda Vue + Laravel"
)]

#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "sanctum-token"
)]
class OpenApi
{
    // Al usar los símbolos "#[" le decimos a PHP 8+ que procese esto de forma nativa.
    // Swagger lo leerá directamente desde la memoria del servidor sin usar expresiones regulares en texto.
}
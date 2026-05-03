<?php

namespace App\Http\Controllers\Api;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Support Ticket API',
    description: 'API REST para gerenciamento de tickets de suporte',
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT',
)]
#[OA\Server(
    url: 'http://localhost:8000',
    description: 'Local Development Server',
)]

class SwaggerController {}

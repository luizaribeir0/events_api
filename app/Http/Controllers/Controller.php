<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'API de Eventos',
    description: 'API RESTful para gerenciamento de eventos'
)]
#[OA\Server(
    url: '/api',
    description: 'Servidor da API'
)]
#[OA\Tag(
    name: 'Eventos',
    description: 'Operações relacionadas a eventos'
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT',
    description: 'Token de autenticação Bearer. Use o formato: Bearer <token>. Token de exemplo para testes: 12345'
)]
#[OA\Schema(
    schema: 'Evento',
    type: 'object',
    required: ['id', 'descricao', 'data_inicio', 'data_final', 'cancelado'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'descricao', type: 'string', example: 'Workshop de Laravel'),
        new OA\Property(property: 'local', type: 'string', example: 'Auditório Principal', nullable: true),
        new OA\Property(property: 'vagas', type: 'integer', example: 50),
        new OA\Property(property: 'data_inicio', type: 'string', format: 'date-time', example: '2024-12-01 10:00:00'),
        new OA\Property(property: 'data_final', type: 'string', format: 'date-time', example: '2024-12-01 18:00:00'),
        new OA\Property(property: 'cancelado', type: 'boolean', example: false),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', nullable: true)
    ]
)]
abstract class Controller
{
    //
}

<?php

namespace App\Http\Swagger;

use OpenApi\Attributes as OA;

class TicketDocs
{
    #[OA\Get(
        path: '/api/tickets',
        summary: 'Listar Tickets',
        tags: ['Ticket'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'status', in: 'query', required: false,
                schema: new OA\Schema(type: 'string', enum: ['OPEN', 'IN_PROGRESS', 'WAITING_CUSTOMER', 'RESOLVED', 'CLOSED', 'CANCELLED'])
            ),
            new OA\Parameter(name: 'priority', in: 'query', required: false,
                schema: new OA\Schema(type: 'string', enum: ['LOW', 'MEDIUM', 'HIGH', 'URGENT'])
            ),
            new OA\Parameter(name: 'category', in: 'query', required: false,
                schema: new OA\Schema(type: 'string', enum: ['INCIDENT', 'ACCESS', 'BUG', 'FEATURE_REQUEST', 'INFRASTRUCTURE', 'OTHER'])
            ),
            new OA\Parameter(name: 'search', in: 'query', required: false,
                schema: new OA\Schema(type: 'string', example: 'erro no login')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista paginada de tickets'),
            new OA\Response(response: 401, description: 'Não autenticado'),
        ]
    )]
    public function index() {}

    #[OA\Get(
        path: '/api/tickets/{ticket}',
        summary: 'Buscar Ticket por ID',
        tags: ['Ticket'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'ticket', in: 'path', required: true,
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Dados do ticket'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 403, description: 'Sem permissão'),
            new OA\Response(response: 404, description: 'Ticket não encontrado'),
        ]
    )]
    public function show() {}

    #[OA\Post(
        path: '/api/tickets',
        summary: 'Criar Ticket',
        tags: ['Ticket'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['title', 'description', 'priority', 'category'],
                properties: [
                    new OA\Property(property: 'title', type: 'string', example: 'Erro ao acessar sistema'),
                    new OA\Property(property: 'description', type: 'string', example: 'Ao tentar logar o sistema retorna erro 500'),
                    new OA\Property(property: 'priority', type: 'string', enum: ['LOW', 'MEDIUM', 'HIGH', 'URGENT'], example: 'HIGH'),
                    new OA\Property(property: 'category', type: 'string', enum: ['INCIDENT', 'ACCESS', 'BUG', 'FEATURE_REQUEST', 'INFRASTRUCTURE', 'OTHER'], example: 'BUG'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Ticket criado com sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 403, description: 'Sem permissão'),
            new OA\Response(response: 422, description: 'Dados inválidos'),
        ]
    )]
    public function store() {}

    #[OA\Put(
        path: '/api/tickets/{ticket}',
        summary: 'Atualizar Ticket',
        tags: ['Ticket'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'ticket', in: 'path', required: true,
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'title', type: 'string', example: 'Erro ao acessar sistema'),
                    new OA\Property(property: 'description', type: 'string', example: 'Descrição atualizada do problema'),
                    new OA\Property(property: 'status', type: 'string', enum: ['OPEN', 'IN_PROGRESS', 'WAITING_CUSTOMER', 'RESOLVED', 'CLOSED', 'CANCELLED'], example: 'IN_PROGRESS'),
                    new OA\Property(property: 'priority', type: 'string', enum: ['LOW', 'MEDIUM', 'HIGH', 'URGENT'], example: 'MEDIUM'),
                    new OA\Property(property: 'category', type: 'string', enum: ['INCIDENT', 'ACCESS', 'BUG', 'FEATURE_REQUEST', 'INFRASTRUCTURE', 'OTHER'], example: 'BUG'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Ticket atualizado com sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 403, description: 'Sem permissão'),
            new OA\Response(response: 404, description: 'Ticket não encontrado'),
            new OA\Response(response: 422, description: 'Dados inválidos'),
        ]
    )]
    public function update() {}

    #[OA\Delete(
        path: '/api/tickets/{ticket}',
        summary: 'Deletar Ticket',
        tags: ['Ticket'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'ticket', in: 'path', required: true,
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Ticket deletado com sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 403, description: 'Sem permissão — apenas Admin'),
            new OA\Response(response: 404, description: 'Ticket não encontrado'),
        ]
    )]
    public function destroy() {}
}

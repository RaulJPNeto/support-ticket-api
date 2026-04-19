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
        responses: [
            new OA\Response(response: 200, description: 'Sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 422, description: 'Dados inválidos'),
        ]
    )]
    public function index() {}

    #[OA\Get(
        path: '/api/tickets/{ticket}',
        summary: 'Buscar Ticket por ID',
        tags: ['Ticket'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 422, description: 'Dados inválidos'),
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
                properties: [
                    // TODO: adicionar propriedades
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 422, description: 'Dados inválidos'),
        ]
    )]
    public function store() {}

    #[OA\Put(
        path: '/api/tickets/{ticket}',
        summary: 'Atualizar Ticket',
        tags: ['Ticket'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    // TODO: adicionar propriedades
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 422, description: 'Dados inválidos'),
        ]
    )]
    public function update() {}

    #[OA\Delete(
        path: '/api/tickets/{ticket}',
        summary: 'Deletar Ticket',
        tags: ['Ticket'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 422, description: 'Dados inválidos'),
        ]
    )]
    public function destroy() {}
}
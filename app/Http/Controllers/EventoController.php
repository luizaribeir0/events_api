<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

class EventoController extends Controller
{
    /**
     * Lista todos os eventos
     */
    #[OA\Get(
        path: '/api/eventos',
        summary: 'Lista todos os eventos',
        tags: ['Eventos'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de eventos retornada com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Eventos listados com sucesso!'),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/Evento')
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Token de autenticação inválido ou não fornecido',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Token de autenticação inválido ou não fornecido.'),
                        new OA\Property(property: 'data', type: 'null', example: null)
                    ]
                )
            )
        ]
    )]
    public function index(): JsonResponse
    {
        try {
            $eventos = Evento::all();

            return response()->json([
                'success' => true,
                'message' => 'Eventos listados com sucesso!',
                'data' => $eventos
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Não foi possível listar os eventos.',
                'data' => null
            ], 500);
        }
    }

    /**
     * Cria um novo evento
     */
    #[OA\Post(
        path: '/api/eventos',
        summary: 'Cria um novo evento',
        tags: ['Eventos'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['descricao', 'data_inicio', 'data_final'],
                properties: [
                    new OA\Property(property: 'descricao', type: 'string', example: 'Workshop de Laravel'),
                    new OA\Property(property: 'local', type: 'string', example: 'Auditório Principal'),
                    new OA\Property(property: 'vagas', type: 'integer', example: 50),
                    new OA\Property(property: 'data_inicio', type: 'string', format: 'date-time', example: '2024-12-01 10:00:00'),
                    new OA\Property(property: 'data_final', type: 'string', format: 'date-time', example: '2024-12-01 18:00:00'),
                    new OA\Property(property: 'cancelado', type: 'boolean', example: false)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Evento criado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Evento criado com sucesso!'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Evento')
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Erro de validação',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Não foi possível criar o evento. Verifique os dados informados.'),
                        new OA\Property(property: 'data', type: 'null', example: null),
                        new OA\Property(property: 'errors', type: 'object')
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Token de autenticação inválido ou não fornecido',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Token de autenticação inválido ou não fornecido.'),
                        new OA\Property(property: 'data', type: 'null', example: null)
                    ]
                )
            )
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'descricao' => 'required|string|max:255',
                'local' => 'nullable|string|max:255',
                'vagas' => 'nullable|integer|min:0',
                'data_inicio' => 'required|date',
                'data_final' => 'required|date|after:data_inicio',
                'cancelado' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não foi possível criar o evento. Verifique os dados informados.',
                    'data' => null,
                    'errors' => $validator->errors()
                ], 422);
            }

            $evento = Evento::create([
                'descricao' => $request->descricao,
                'local' => $request->local,
                'vagas' => $request->vagas ?? 0,
                'data_inicio' => $request->data_inicio,
                'data_final' => $request->data_final,
                'cancelado' => $request->cancelado ?? false,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Evento criado com sucesso!',
                'data' => $evento
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Não foi possível criar o evento.',
                'data' => null
            ], 500);
        }
    }

    /**
     * Consulta um evento específico
     */
    #[OA\Get(
        path: '/api/eventos/{id}',
        summary: 'Consulta um evento por ID',
        tags: ['Eventos'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID do evento',
                schema: new OA\Schema(type: 'integer', example: 1)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Evento encontrado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Evento encontrado com sucesso!'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Evento')
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Evento não encontrado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Evento não encontrado.'),
                        new OA\Property(property: 'data', type: 'null', example: null)
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Token de autenticação inválido ou não fornecido',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Token de autenticação inválido ou não fornecido.'),
                        new OA\Property(property: 'data', type: 'null', example: null)
                    ]
                )
            )
        ]
    )]
    public function show(int $id): JsonResponse
    {
        try {
            $evento = Evento::find($id);

            if (!$evento) {
                return response()->json([
                    'success' => false,
                    'message' => 'Evento não encontrado.',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Evento encontrado com sucesso!',
                'data' => $evento
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Não foi possível consultar o evento.',
                'data' => null
            ], 500);
        }
    }

    /**
     * Atualiza um evento existente
     */
    #[OA\Put(
        path: '/api/eventos/{id}',
        summary: 'Atualiza um evento',
        tags: ['Eventos'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID do evento',
                schema: new OA\Schema(type: 'integer', example: 1)
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'descricao', type: 'string', example: 'Workshop de Laravel Atualizado'),
                    new OA\Property(property: 'local', type: 'string', example: 'Auditório Principal'),
                    new OA\Property(property: 'vagas', type: 'integer', example: 50),
                    new OA\Property(property: 'data_inicio', type: 'string', format: 'date-time', example: '2024-12-01 10:00:00'),
                    new OA\Property(property: 'data_final', type: 'string', format: 'date-time', example: '2024-12-01 18:00:00'),
                    new OA\Property(property: 'cancelado', type: 'boolean', example: false)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Evento atualizado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Evento atualizado com sucesso!'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Evento')
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Evento não encontrado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Evento não encontrado.'),
                        new OA\Property(property: 'data', type: 'null', example: null)
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Erro de validação',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Não foi possível atualizar o evento. Verifique os dados informados.'),
                        new OA\Property(property: 'data', type: 'null', example: null),
                        new OA\Property(property: 'errors', type: 'object')
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Token de autenticação inválido ou não fornecido',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Token de autenticação inválido ou não fornecido.'),
                        new OA\Property(property: 'data', type: 'null', example: null)
                    ]
                )
            )
        ]
    )]
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $evento = Evento::find($id);

            if (!$evento) {
                return response()->json([
                    'success' => false,
                    'message' => 'Evento não encontrado.',
                    'data' => null
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'descricao' => 'sometimes|required|string|max:255',
                'local' => 'nullable|string|max:255',
                'vagas' => 'nullable|integer|min:0',
                'data_inicio' => 'sometimes|required|date',
                'data_final' => 'sometimes|required|date|after:data_inicio',
                'cancelado' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não foi possível atualizar o evento. Verifique os dados informados.',
                    'data' => null,
                    'errors' => $validator->errors()
                ], 422);
            }

            $evento->update($request->only(['descricao', 'local', 'vagas', 'data_inicio', 'data_final', 'cancelado']));

            return response()->json([
                'success' => true,
                'message' => 'Evento atualizado com sucesso!',
                'data' => $evento->fresh()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Não foi possível atualizar o evento.',
                'data' => null
            ], 500);
        }
    }

    /**
     * Remove um evento
     */
    #[OA\Delete(
        path: '/api/eventos/{id}',
        summary: 'Remove um evento',
        tags: ['Eventos'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID do evento',
                schema: new OA\Schema(type: 'integer', example: 1)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Evento removido com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Evento removido com sucesso!'),
                        new OA\Property(property: 'data', type: 'null', example: null)
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Evento não encontrado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Evento não encontrado.'),
                        new OA\Property(property: 'data', type: 'null', example: null)
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Token de autenticação inválido ou não fornecido',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Token de autenticação inválido ou não fornecido.'),
                        new OA\Property(property: 'data', type: 'null', example: null)
                    ]
                )
            )
        ]
    )]
    public function destroy(int $id): JsonResponse
    {
        try {
            $evento = Evento::find($id);

            if (!$evento) {
                return response()->json([
                    'success' => false,
                    'message' => 'Evento não encontrado.',
                    'data' => null
                ], 404);
            }

            $evento->delete();

            return response()->json([
                'success' => true,
                'message' => 'Evento removido com sucesso!',
                'data' => null
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Não foi possível remover o evento.',
                'data' => null
            ], 500);
        }
    }
}


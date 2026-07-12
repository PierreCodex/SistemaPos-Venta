<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ApiDocsController extends Controller
{
    /**
     * Muestra la documentación interactiva (Swagger UI) de las APIs del sistema.
     * Solo accesible para usuarios con el permiso apis.ver.
     */
    public function index()
    {
        return view('apis.index');
    }

    /**
     * Devuelve la especificación OpenAPI (Swagger) de las APIs protegidas por Sanctum.
     * La URL base se genera dinámicamente según la configuración de la app.
     */
    public function swaggerJson(): JsonResponse
    {
        $baseUrl = url('/api');

        return response()->json([
            'openapi' => '3.0.3',
            'info' => [
                'title' => 'APIs de SistemaPos-Venta',
                'description' => 'Documentación interactiva de las APIs del POS. '
                    . 'Cada endpoint requiere un token Sanctum y un permiso de API específico. '
                    . 'El rol administrador tiene acceso a todas.',
                'version' => '1.0.0',
                'contact' => [
                    'name' => 'Soporte SistemaPos-Venta',
                ],
            ],
            'servers' => [
                [
                    'url' => $baseUrl,
                    'description' => 'Servidor actual',
                ],
            ],
            'security' => [
                ['bearerAuth' => []],
            ],
            'components' => [
                'securitySchemes' => [
                    'bearerAuth' => [
                        'type' => 'http',
                        'scheme' => 'bearer',
                        'bearerFormat' => 'JWT',
                        'description' => 'Token Sanctum. Ejemplo: Bearer 1|xxxxxxxx',
                    ],
                ],
                'schemas' => [
                    'Producto' => [
                        'type' => 'object',
                        'properties' => [
                            'id' => ['type' => 'integer', 'example' => 1],
                            'codigo' => ['type' => 'string', 'example' => 'DEMO-COCA-500'],
                            'codigo_barras' => ['type' => 'string', 'example' => '7750182000045'],
                            'nombre' => ['type' => 'string', 'example' => 'Coca Cola 500ml'],
                            'stock' => ['type' => 'number', 'example' => 100],
                            'precio_venta' => ['type' => 'number', 'example' => 2.5],
                        ],
                    ],
                    'VigilanteVentasResponse' => [
                        'type' => 'object',
                        'properties' => [
                            'sku' => ['type' => 'string', 'example' => '7750182000045'],
                            'unidades_vendidas' => ['type' => 'number', 'example' => 2],
                            'producto_encontrado' => ['type' => 'boolean', 'example' => true],
                        ],
                    ],
                    'VigilanteStockResponse' => [
                        'type' => 'object',
                        'properties' => [
                            'sku' => ['type' => 'string', 'example' => '7750182000045'],
                            'nombre' => ['type' => 'string', 'example' => 'Coca Cola 500ml'],
                            'stock' => ['type' => 'number', 'example' => 100],
                            'producto_encontrado' => ['type' => 'boolean', 'example' => true],
                        ],
                    ],
                    'Unauthorized' => [
                        'type' => 'object',
                        'properties' => [
                            'message' => ['type' => 'string', 'example' => 'Unauthenticated.'],
                        ],
                    ],
                    'Forbidden' => [
                        'type' => 'object',
                        'properties' => [
                            'message' => ['type' => 'string', 'example' => 'This action is unauthorized.'],
                        ],
                    ],
                ],
            ],
            'paths' => [
                '/productos' => [
                    'get' => [
                        'tags' => ['Productos'],
                        'summary' => 'Listar productos',
                        'description' => 'Devuelve productos paginados. Permiso requerido: `api.productos.ver`.',
                        'operationId' => 'listarProductos',
                        'security' => [['bearerAuth' => []]],
                        'parameters' => [
                            [
                                'name' => 'buscar',
                                'in' => 'query',
                                'description' => 'Buscar por nombre, código o código de barras',
                                'required' => false,
                                'schema' => ['type' => 'string'],
                            ],
                            [
                                'name' => 'page',
                                'in' => 'query',
                                'description' => 'Número de página',
                                'required' => false,
                                'schema' => ['type' => 'integer', 'default' => 1],
                            ],
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Lista paginada de productos',
                            ],
                            '401' => [
                                'description' => 'No autenticado',
                                'content' => [
                                    'application/json' => [
                                        'schema' => ['$ref' => '#/components/schemas/Unauthorized'],
                                    ],
                                ],
                            ],
                            '403' => [
                                'description' => 'Sin permiso',
                                'content' => [
                                    'application/json' => [
                                        'schema' => ['$ref' => '#/components/schemas/Forbidden'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                '/productos/{id}' => [
                    'get' => [
                        'tags' => ['Productos'],
                        'summary' => 'Mostrar un producto',
                        'description' => 'Devuelve los detalles de un producto. Permiso requerido: `api.productos.ver`.',
                        'operationId' => 'mostrarProducto',
                        'security' => [['bearerAuth' => []]],
                        'parameters' => [
                            [
                                'name' => 'id',
                                'in' => 'path',
                                'required' => true,
                                'schema' => ['type' => 'integer'],
                            ],
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Producto encontrado',
                                'content' => [
                                    'application/json' => [
                                        'schema' => ['$ref' => '#/components/schemas/Producto'],
                                    ],
                                ],
                            ],
                            '404' => [
                                'description' => 'Producto no encontrado',
                            ],
                            '401' => [
                                'description' => 'No autenticado',
                                'content' => [
                                    'application/json' => [
                                        'schema' => ['$ref' => '#/components/schemas/Unauthorized'],
                                    ],
                                ],
                            ],
                            '403' => [
                                'description' => 'Sin permiso',
                                'content' => [
                                    'application/json' => [
                                        'schema' => ['$ref' => '#/components/schemas/Forbidden'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                '/vigilante/ventas' => [
                    'get' => [
                        'tags' => ['VigilanteIA'],
                        'summary' => 'Unidades vendidas de un SKU en un rango',
                        'description' => 'Consulta cuántas unidades de un SKU se vendieron en un rango de tiempo. '
                            . 'Permiso requerido: `api.vigilante.ventas`.',
                        'operationId' => 'vigilanteVentas',
                        'security' => [['bearerAuth' => []]],
                        'parameters' => [
                            [
                                'name' => 'sku',
                                'in' => 'query',
                                'description' => 'Código de barras o código interno del producto',
                                'required' => true,
                                'schema' => ['type' => 'string', 'example' => '7750182000045'],
                            ],
                            [
                                'name' => 'desde',
                                'in' => 'query',
                                'description' => 'Inicio del rango en ISO 8601',
                                'required' => true,
                                'schema' => ['type' => 'string', 'format' => 'date-time', 'example' => '2026-07-12T00:00:00-05:00'],
                            ],
                            [
                                'name' => 'hasta',
                                'in' => 'query',
                                'description' => 'Fin del rango en ISO 8601',
                                'required' => true,
                                'schema' => ['type' => 'string', 'format' => 'date-time', 'example' => '2026-07-12T23:59:59-05:00'],
                            ],
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Unidades vendidas',
                                'content' => [
                                    'application/json' => [
                                        'schema' => ['$ref' => '#/components/schemas/VigilanteVentasResponse'],
                                    ],
                                ],
                            ],
                            '401' => [
                                'description' => 'No autenticado',
                                'content' => [
                                    'application/json' => [
                                        'schema' => ['$ref' => '#/components/schemas/Unauthorized'],
                                    ],
                                ],
                            ],
                            '403' => [
                                'description' => 'Sin permiso',
                                'content' => [
                                    'application/json' => [
                                        'schema' => ['$ref' => '#/components/schemas/Forbidden'],
                                    ],
                                ],
                            ],
                            '422' => [
                                'description' => 'Datos de entrada inválidos',
                            ],
                        ],
                    ],
                ],
                '/vigilante/stock' => [
                    'get' => [
                        'tags' => ['VigilanteIA'],
                        'summary' => 'Stock teórico de un SKU',
                        'description' => 'Devuelve el stock actual de un producto. Permiso requerido: `api.vigilante.stock`.',
                        'operationId' => 'vigilanteStock',
                        'security' => [['bearerAuth' => []]],
                        'parameters' => [
                            [
                                'name' => 'sku',
                                'in' => 'query',
                                'description' => 'Código de barras o código interno del producto',
                                'required' => true,
                                'schema' => ['type' => 'string', 'example' => '7750182000045'],
                            ],
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Stock del producto',
                                'content' => [
                                    'application/json' => [
                                        'schema' => ['$ref' => '#/components/schemas/VigilanteStockResponse'],
                                    ],
                                ],
                            ],
                            '401' => [
                                'description' => 'No autenticado',
                                'content' => [
                                    'application/json' => [
                                        'schema' => ['$ref' => '#/components/schemas/Unauthorized'],
                                    ],
                                ],
                            ],
                            '403' => [
                                'description' => 'Sin permiso',
                                'content' => [
                                    'application/json' => [
                                        'schema' => ['$ref' => '#/components/schemas/Forbidden'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'tags' => [
                ['name' => 'Productos', 'description' => 'Catálogo de productos'],
                ['name' => 'VigilanteIA', 'description' => 'Integración con VigilanteIA'],
            ],
        ]);
    }
}

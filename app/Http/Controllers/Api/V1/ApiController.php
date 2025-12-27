<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Agiza Sokoni API",
 *     description="API documentation for Agiza Sokoni marketplace platform",
 *     @OA\Contact(
 *         email="admin@agizasokoni.com"
 *     )
 * )
 *
 * @OA\Server(
 *     url="/api/v1",
 *     description="API Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 *
 * @OA\Tag(name="Auth", description="Authentication endpoints")
 * @OA\Tag(name="Roles", description="Role management endpoints")
 * @OA\Tag(name="Permissions", description="Permission endpoints")
 * @OA\Tag(name="Product Categories", description="Product category management")
 * @OA\Tag(name="Units", description="Unit management")
 * @OA\Tag(name="Products", description="Product management")
 * @OA\Tag(name="Market Categories", description="Market category management")
 * @OA\Tag(name="Markets", description="Market management")
 * @OA\Tag(name="Market Products", description="Market product pricing")
 * @OA\Tag(name="Browse", description="Public browsing API")
 * @OA\Tag(name="Audit Logs", description="Audit log viewing")
 */
abstract class ApiController extends Controller
{
    use AuthorizesRequests;
    protected function successResponse($data, string $message = 'Success', int $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function errorResponse(string $message, int $code = 400, $errors = null)
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    protected function paginatedResponse($paginator, $resource, string $message = 'Success')
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $resource::collection($paginator->items()),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
        ]);
    }
}

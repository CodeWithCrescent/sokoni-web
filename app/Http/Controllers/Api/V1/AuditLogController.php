<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\AuditLogResource;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/audit-logs",
     *     tags={"Audit Logs"},
     *     summary="List audit logs",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="auditable_type", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="auditable_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="event", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="user_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="from_date", in="query", @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="to_date", in="query", @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", default=15)),
     *     @OA\Response(response=200, description="List of audit logs")
     * )
     */
    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('audit-logs.view')) {
            return $this->errorResponse('Forbidden', 403);
        }

        $query = AuditLog::query()->with('user')->latest();

        if ($request->filled('auditable_type')) {
            $query->where('auditable_type', 'like', "%{$request->auditable_type}%");
        }

        if ($request->filled('auditable_id')) {
            $query->where('auditable_id', $request->auditable_id);
        }

        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $logs = $query->paginate($request->integer('per_page', 15));

        return $this->paginatedResponse($logs, AuditLogResource);
    }

    /**
     * @OA\Get(
     *     path="/audit-logs/{id}",
     *     tags={"Audit Logs"},
     *     summary="Get audit log details",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Audit log details")
     * )
     */
    public function show(Request $request, AuditLog $auditLog)
    {
        if (!$request->user()->hasPermission('audit-logs.view')) {
            return $this->errorResponse('Forbidden', 403);
        }

        return $this->successResponse(
            new AuditLogResource($auditLog->load('user'))
        );
    }
}

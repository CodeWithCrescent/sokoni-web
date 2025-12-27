<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\NotificationResource;
use Illuminate\Http\Request;

class NotificationController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/notifications",
     *     tags={"Notifications"},
     *     summary="Get user's notifications",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="unread_only", in="query", @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="List of notifications")
     * )
     */
    public function index(Request $request)
    {
        $query = $request->user()->notifications();

        if ($request->boolean('unread_only')) {
            $query->whereNull('read_at');
        }

        $notifications = $query->latest()->paginate($request->integer('per_page', 20));

        return $this->paginatedResponse($notifications, NotificationResource::class);
    }

    /**
     * @OA\Get(
     *     path="/notifications/unread-count",
     *     tags={"Notifications"},
     *     summary="Get unread notifications count",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Unread count")
     * )
     */
    public function unreadCount(Request $request)
    {
        $count = $request->user()->unreadNotifications()->count();

        return $this->successResponse(['count' => $count]);
    }

    /**
     * @OA\Post(
     *     path="/notifications/{id}/read",
     *     tags={"Notifications"},
     *     summary="Mark notification as read",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Notification marked as read")
     * )
     */
    public function markAsRead(Request $request, string $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return $this->successResponse(
            new NotificationResource($notification),
            'Notification marked as read'
        );
    }

    /**
     * @OA\Post(
     *     path="/notifications/read-all",
     *     tags={"Notifications"},
     *     summary="Mark all notifications as read",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="All notifications marked as read")
     * )
     */
    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return $this->successResponse(null, 'All notifications marked as read');
    }

    /**
     * @OA\Delete(
     *     path="/notifications/{id}",
     *     tags={"Notifications"},
     *     summary="Delete notification",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Notification deleted")
     * )
     */
    public function destroy(Request $request, string $id)
    {
        $request->user()->notifications()->findOrFail($id)->delete();

        return $this->successResponse(null, 'Notification deleted');
    }
}

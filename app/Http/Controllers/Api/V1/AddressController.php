<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\AddressResource;
use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/addresses",
     *     tags={"Addresses"},
     *     summary="Get user's addresses",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="List of addresses")
     * )
     */
    public function index(Request $request)
    {
        $addresses = Address::where('user_id', $request->user()->id)
            ->orderByDesc('is_default')
            ->orderByDesc('updated_at')
            ->get();

        return $this->successResponse(AddressResource::collection($addresses));
    }

    /**
     * @OA\Post(
     *     path="/addresses",
     *     tags={"Addresses"},
     *     summary="Create new address",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"address"},
     *         @OA\Property(property="label", type="string"),
     *         @OA\Property(property="address", type="string"),
     *         @OA\Property(property="area", type="string"),
     *         @OA\Property(property="city", type="string"),
     *         @OA\Property(property="latitude", type="number"),
     *         @OA\Property(property="longitude", type="number"),
     *         @OA\Property(property="phone", type="string"),
     *         @OA\Property(property="instructions", type="string"),
     *         @OA\Property(property="is_default", type="boolean")
     *     )),
     *     @OA\Response(response=201, description="Address created")
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'label' => 'nullable|string|max:50',
            'address' => 'required|string|max:500',
            'area' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'phone' => 'nullable|string|max:20',
            'instructions' => 'nullable|string|max:500',
            'is_default' => 'nullable|boolean',
        ]);

        $user = $request->user();
        $data['user_id'] = $user->id;

        if ($data['is_default'] ?? false) {
            $user->addresses()->update(['is_default' => false]);
        }

        $address = Address::create($data);

        return $this->successResponse(
            new AddressResource($address),
            'Address created successfully',
            201
        );
    }

    /**
     * @OA\Put(
     *     path="/addresses/{id}",
     *     tags={"Addresses"},
     *     summary="Update address",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Address updated")
     * )
     */
    public function update(Request $request, int $id)
    {
        $address = Address::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $data = $request->validate([
            'label' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'area' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'phone' => 'nullable|string|max:20',
            'instructions' => 'nullable|string|max:500',
            'is_default' => 'nullable|boolean',
        ]);

        if ($data['is_default'] ?? false) {
            $request->user()->addresses()
                ->where('id', '!=', $id)
                ->update(['is_default' => false]);
        }

        $address->update($data);

        return $this->successResponse(
            new AddressResource($address),
            'Address updated successfully'
        );
    }

    /**
     * @OA\Delete(
     *     path="/addresses/{id}",
     *     tags={"Addresses"},
     *     summary="Delete address",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Address deleted")
     * )
     */
    public function destroy(Request $request, int $id)
    {
        $address = Address::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $address->delete();

        return $this->successResponse(null, 'Address deleted successfully');
    }

    /**
     * @OA\Post(
     *     path="/addresses/{id}/default",
     *     tags={"Addresses"},
     *     summary="Set address as default",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Address set as default")
     * )
     */
    public function setDefault(Request $request, int $id)
    {
        $address = Address::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $address->setAsDefault();

        return $this->successResponse(
            new AddressResource($address),
            'Address set as default'
        );
    }
}

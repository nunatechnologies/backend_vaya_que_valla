<?php

namespace App\Http\Controllers;

use App\Http\Messages\SuccessMessages;
use App\Http\Resources\BillboardFace\BillboardFaceResource;
use App\Http\Resources\PaginacionResource;
use App\Http\Responses\ApiResponse;
use App\Models\BillboardFace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProviderBillboardController extends Controller
{
    /**
     * List billboard faces for the authenticated provider.
     */
    public function index(Request $request)
    {
        try {
            $query = BillboardFace::forProvider(Auth::id())
                ->with(['city', 'zone', 'billboardStructure', 'media']);

            if ($request->filled('search')) {
                $search = $request->query('search');
                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', "%{$search}%")
                      ->orWhere('name', 'like', "%{$search}%")
                      ->orWhere('location', 'like', "%{$search}%");
                });
            }

            if ($request->query('approval_status')) {
                $query->where('approval_status', $request->query('approval_status'));
            }

            $sortBy = $request->query('sortBy', 'created_at');
            $orderBy = $request->query('orderBy', 'desc');
            $query->orderBy($sortBy, $orderBy);

            $itemsPerPage = $request->query('itemsPerPage', 10);
            $page = $request->query('page', 1);

            $result = $query->paginate($itemsPerPage, ['*'], 'page', $page);
            $result->data = BillboardFaceResource::collection($result->getCollection());

            return ApiResponse::success(SuccessMessages::SUCCESSFUL, new PaginacionResource($result), [], 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), null, [], 500);
        }
    }

    /**
     * Get a single billboard face (scoped to provider).
     */
    public function show($id)
    {
        try {
            $face = BillboardFace::forProvider(Auth::id())->findOrFail($id);
            return ApiResponse::success(SuccessMessages::SUCCESSFUL, new BillboardFaceResource($face), [], 200);
        } catch (\Exception $e) {
            return ApiResponse::error('Valla no encontrada', null, [], 404);
        }
    }

    /**
     * Store a new billboard face (pending approval).
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'code' => 'required|string|max:10',
                'face' => 'nullable|string|max:10',
                'name' => 'nullable|string|max:255',
                'location' => 'nullable|string|max:255',
                'location_detail' => 'nullable|string|max:255',
                'size' => 'nullable|string|max:100',
                'price_per_month' => 'nullable|numeric|min:0',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'city_id' => 'nullable|integer|exists:cities,id',
                'zone_id' => 'nullable|integer|exists:zones,id',
                'billboard_structure_id' => 'nullable|integer|exists:billboard_structures,id',
                'status' => 'nullable|in:VERDE,AMARILLO,ROJO',
                'traffic_data' => 'nullable|string',
            ]);

            $validated['advertiser_id'] = Auth::id();
            $validated['approval_status'] = 'pending';
            $validated['entity_status'] = 'active';
            $validated['status'] = $validated['status'] ?? 'VERDE';
            // Columnas NOT NULL sin default — defaults seguros si no vienen del form
            $validated['face'] = $validated['face'] ?? '';
            $validated['name'] = $validated['name'] ?? '';
            $validated['location'] = $validated['location'] ?? '';
            $validated['location_detail'] = $validated['location_detail'] ?? '';
            $validated['size'] = $validated['size'] ?? '';
            $validated['price_per_month'] = $validated['price_per_month'] ?? 0;
            $validated['latitude'] = $validated['latitude'] ?? 0;
            $validated['longitude'] = $validated['longitude'] ?? 0;

            $face = BillboardFace::create($validated);

            if ($request->hasFile('image')) {
                $face->addMediaFromRequest('image')->toMediaCollection();
            }

            return ApiResponse::success('Valla creada, pendiente de aprobacion', new BillboardFaceResource($face), [], 201);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), null, [], 500);
        }
    }

    /**
     * Update a billboard face (scoped to provider, blocked if active quote).
     */
    public function update(Request $request, $id)
    {
        try {
            $face = BillboardFace::forProvider(Auth::id())->findOrFail($id);

            if ($face->hasActiveQuote()) {
                return ApiResponse::error('No se puede editar una valla con cotizacion activa', null, [], 422);
            }

            $validated = $request->validate([
                'code' => 'sometimes|string|max:50',
                'face' => 'nullable|string|max:10',
                'name' => 'nullable|string|max:255',
                'location' => 'nullable|string|max:255',
                'location_detail' => 'nullable|string|max:255',
                'size' => 'nullable|string|max:100',
                'price_per_month' => 'nullable|numeric|min:0',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'city_id' => 'nullable|integer|exists:cities,id',
                'zone_id' => 'nullable|integer|exists:zones,id',
                'billboard_structure_id' => 'nullable|integer|exists:billboard_structures,id',
                'status' => 'nullable|in:VERDE,AMARILLO,ROJO',
                'traffic_data' => 'nullable|string',
            ]);

            // Columnas NOT NULL — convertir null a string vacio si vienen explicitamente null
            foreach (['name', 'location', 'location_detail'] as $f) {
                if (array_key_exists($f, $validated) && $validated[$f] === null) {
                    $validated[$f] = '';
                }
            }

            $face->update($validated);

            if ($request->hasFile('image')) {
                $face->addMediaFromRequest('image')->toMediaCollection();
            }

            return ApiResponse::success(SuccessMessages::UPDATE_SUCCESS, new BillboardFaceResource($face->fresh()), [], 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), null, [], 500);
        }
    }

    /**
     * Delete a billboard face (scoped to provider, blocked if active quote).
     */
    public function destroy($id)
    {
        try {
            $face = BillboardFace::forProvider(Auth::id())->findOrFail($id);

            if ($face->hasActiveQuote()) {
                return ApiResponse::error('No se puede eliminar una valla con cotizacion activa', null, [], 422);
            }

            $face->delete();

            return ApiResponse::success('Valla eliminada correctamente', null, [], 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), null, [], 500);
        }
    }

    /**
     * Admin: List billboard faces pending approval.
     */
    public function pendingApproval(Request $request)
    {
        try {
            $query = BillboardFace::pendingApproval()
                ->with(['city', 'zone', 'billboardStructure', 'advertiser', 'media']);

            if ($request->filled('search')) {
                $search = $request->query('search');
                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', "%{$search}%")
                      ->orWhere('name', 'like', "%{$search}%");
                });
            }

            $itemsPerPage = $request->query('itemsPerPage', 20);
            $page = $request->query('page', 1);

            $result = $query->orderBy('created_at', 'desc')
                ->paginate($itemsPerPage, ['*'], 'page', $page);

            $result->data = BillboardFaceResource::collection($result->getCollection());

            return ApiResponse::success(SuccessMessages::SUCCESSFUL, new PaginacionResource($result), [], 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), null, [], 500);
        }
    }

    /**
     * Admin: Bulk approve billboard faces.
     */
    public function bulkApprove(Request $request)
    {
        try {
            $request->validate([
                'ids' => 'required|array|min:1',
                'ids.*' => 'integer|exists:billboard_faces,id',
            ]);

            $updated = BillboardFace::whereIn('id', $request->ids)
                ->where('approval_status', 'pending')
                ->update(['approval_status' => 'approved']);

            return ApiResponse::success("Se aprobaron {$updated} vallas correctamente", ['approved_count' => $updated], [], 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), null, [], 500);
        }
    }

    /**
     * Admin: Bulk reject billboard faces.
     */
    public function bulkReject(Request $request)
    {
        try {
            $request->validate([
                'ids' => 'required|array|min:1',
                'ids.*' => 'integer|exists:billboard_faces,id',
            ]);

            $updated = BillboardFace::whereIn('id', $request->ids)
                ->where('approval_status', 'pending')
                ->update(['approval_status' => 'rejected']);

            return ApiResponse::success("Se rechazaron {$updated} vallas", ['rejected_count' => $updated], [], 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), null, [], 500);
        }
    }
}

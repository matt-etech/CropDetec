<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\UpsertCropRequest;
use App\Models\Crop;
use App\Support\AdminAuthorizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminCropController extends Controller
{
    public function index(Request $request, AdminAuthorizer $admin): JsonResponse
    {
        $admin->user($request);

        return response()->json([
            'crops' => Crop::query()
                ->with('diseases.treatments')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(UpsertCropRequest $request, AdminAuthorizer $admin): JsonResponse
    {
        $admin->user($request);

        $crop = Crop::query()->create($request->validated());

        return response()->json([
            'message' => 'Crop created successfully.',
            'crop' => $crop->fresh('diseases'),
        ], 201);
    }

    public function update(UpsertCropRequest $request, Crop $crop, AdminAuthorizer $admin): JsonResponse
    {
        $admin->user($request);
        $crop->update($request->validated());

        return response()->json([
            'message' => 'Crop updated successfully.',
            'crop' => $crop->fresh('diseases'),
        ]);
    }
}

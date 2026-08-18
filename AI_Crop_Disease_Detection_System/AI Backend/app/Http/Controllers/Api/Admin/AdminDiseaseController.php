<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\UpsertDiseaseRequest;
use App\Models\Disease;
use App\Support\AdminAuthorizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminDiseaseController extends Controller
{
    public function index(Request $request, AdminAuthorizer $admin): JsonResponse
    {
        $admin->user($request);

        return response()->json([
            'diseases' => Disease::query()
                ->with(['crop', 'treatments'])
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(UpsertDiseaseRequest $request, AdminAuthorizer $admin): JsonResponse
    {
        $admin->user($request);

        $disease = Disease::query()->create($request->validated());

        return response()->json([
            'message' => 'Disease created successfully.',
            'disease' => $disease->fresh(['crop', 'treatments']),
        ], 201);
    }

    public function update(UpsertDiseaseRequest $request, Disease $disease, AdminAuthorizer $admin): JsonResponse
    {
        $admin->user($request);
        $disease->update($request->validated());

        return response()->json([
            'message' => 'Disease updated successfully.',
            'disease' => $disease->fresh(['crop', 'treatments']),
        ]);
    }
}

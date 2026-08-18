<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\UpsertTreatmentRequest;
use App\Models\Treatment;
use App\Support\AdminAuthorizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminTreatmentController extends Controller
{
    public function index(Request $request, AdminAuthorizer $admin): JsonResponse
    {
        $admin->user($request);

        return response()->json([
            'treatments' => Treatment::query()
                ->with('disease.crop')
                ->orderBy('title')
                ->get(),
        ]);
    }

    public function store(UpsertTreatmentRequest $request, AdminAuthorizer $admin): JsonResponse
    {
        $admin->user($request);

        $treatment = Treatment::query()->create($request->validated());

        return response()->json([
            'message' => 'Treatment created successfully.',
            'treatment' => $treatment->fresh('disease.crop'),
        ], 201);
    }

    public function update(UpsertTreatmentRequest $request, Treatment $treatment, AdminAuthorizer $admin): JsonResponse
    {
        $admin->user($request);
        $treatment->update($request->validated());

        return response()->json([
            'message' => 'Treatment updated successfully.',
            'treatment' => $treatment->fresh('disease.crop'),
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Diagnosis;
use App\Models\User;
use App\Support\AdminAuthorizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminOverviewController extends Controller
{
    public function users(Request $request, AdminAuthorizer $admin): JsonResponse
    {
        $admin->user($request);

        return response()->json([
            'users' => User::query()
                ->select(['id', 'name', 'email', 'phone', 'role', 'language_preference', 'created_at'])
                ->latest()
                ->get(),
        ]);
    }

    public function diagnoses(Request $request, AdminAuthorizer $admin): JsonResponse
    {
        $admin->user($request);

        return response()->json([
            'diagnoses' => Diagnosis::query()
                ->with(['user:id,name,email', 'crop', 'disease.treatments'])
                ->latest()
                ->get(),
        ]);
    }
}

<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Service\DoctorService;
use App\Shared\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    use ApiResponseTrait;

    protected DoctorService $doctorService;

    public function __construct(DoctorService $doctorService)
    {
        $this->doctorService = $doctorService;
    }

    public function index(): JsonResponse
    {
        $doctors = $this->doctorService->all();
        return $this->successResponse($doctors);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id|unique:doctors,user_id',
            'specialization_id' => 'required|exists:specializations,id',
            'license_number' => 'required|string|unique:doctors,license_number',
            'bio' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $doctor = $this->doctorService->create($validated);
        return $this->successResponse($doctor, 'Doctor profile created successfully', 201);
    }

    public function show($id): JsonResponse
    {
        $doctor = $this->doctorService->findOrFail($id);
        return $this->successResponse($doctor);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'specialization_id' => 'nullable|exists:specializations,id',
            'license_number' => 'nullable|string|unique:doctors,license_number,' . $id,
            'bio' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $doctor = $this->doctorService->update($id, $validated);
        return $this->successResponse($doctor, 'Doctor profile updated successfully');
    }

    public function destroy($id): JsonResponse
    {
        $this->doctorService->delete($id);
        return $this->successResponse(null, 'Doctor profile deleted successfully');
    }
}

<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Service\PatientService;
use App\Shared\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    use ApiResponseTrait;

    protected PatientService $patientService;

    public function __construct(PatientService $patientService)
    {
        $this->patientService = $patientService;
    }

    public function index(): JsonResponse
    {
        $patients = $this->patientService->all();
        return $this->successResponse($patients);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id|unique:patients,user_id',
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'required|date',
            'gender' => 'required|string|in:male,female,other',
            'address' => 'nullable|string',
            'medical_history' => 'nullable|string',
        ]);

        $patient = $this->patientService->create($validated);
        return $this->successResponse($patient, 'Patient profile created successfully', 201);
    }

    public function show($id): JsonResponse
    {
        $patient = $this->patientService->findOrFail($id);
        return $this->successResponse($patient);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string|in:male,female,other',
            'address' => 'nullable|string',
            'medical_history' => 'nullable|string',
        ]);

        $patient = $this->patientService->update($id, $validated);
        return $this->successResponse($patient, 'Patient profile updated successfully');
    }

    public function destroy($id): JsonResponse
    {
        $this->patientService->delete($id);
        return $this->successResponse(null, 'Patient profile deleted successfully');
    }
}

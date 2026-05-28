<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Http\Requests\Patient\StorePatientRequest;
use App\Http\Requests\Patient\UpdatePatientRequest;
use App\Service\PatientService;
use App\Shared\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;

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
        // Ambil semua data pasien via service
        $patients = $this->patientService->all();
        return $this->successResponse($patients);
    }

    public function store(StorePatientRequest $request): JsonResponse
    {
        // Validasi sudah ditangani StorePatientRequest
        $patient = $this->patientService->create($request->validated());
        return $this->successResponse($patient, 'Patient profile created successfully', 201);
    }

    public function show(string $id): JsonResponse
    {
        // Lempar 404 otomatis jika tidak ditemukan via findOrFail di BaseRepository
        $patient = $this->patientService->findOrFail($id);
        return $this->successResponse($patient);
    }

    public function update(UpdatePatientRequest $request, string $id): JsonResponse
    {
        // Validasi sudah ditangani UpdatePatientRequest
        $patient = $this->patientService->update($id, $request->validated());
        return $this->successResponse($patient, 'Patient profile updated successfully');
    }

    public function destroy(string $id): JsonResponse
    {
        // Soft delete karena model pakai SoftDeletes
        $this->patientService->delete($id);
        return $this->successResponse(null, 'Patient profile deleted successfully');
    }
}
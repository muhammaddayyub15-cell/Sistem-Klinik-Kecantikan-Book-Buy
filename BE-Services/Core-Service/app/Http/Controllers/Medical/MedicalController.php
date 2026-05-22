<?php

namespace App\Http\Controllers\Medical;

use App\Http\Controllers\Controller;
use App\Service\MedicalService;
use App\Shared\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MedicalController extends Controller
{
    use ApiResponseTrait;

    protected MedicalService $medicalService;

    public function __construct(MedicalService $medicalService)
    {
        $this->medicalService = $medicalService;
    }

    public function index(): JsonResponse
    {
        $records = $this->medicalService->all();
        return $this->successResponse($records);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id|unique:medical_records,booking_id',
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'diagnosis' => 'required|string',
            'treatment_plan' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $record = $this->medicalService->create($validated);
        return $this->successResponse($record, 'Medical record created successfully', 201);
    }

    public function show($id): JsonResponse
    {
        $record = $this->medicalService->findOrFail($id);
        return $this->successResponse($record);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'booking_id' => 'nullable|exists:bookings,id|unique:medical_records,booking_id,' . $id,
            'patient_id' => 'nullable|exists:patients,id',
            'doctor_id' => 'nullable|exists:doctors,id',
            'diagnosis' => 'nullable|string',
            'treatment_plan' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $record = $this->medicalService->update($id, $validated);
        return $this->successResponse($record, 'Medical record updated successfully');
    }

    public function destroy($id): JsonResponse
    {
        $this->medicalService->delete($id);
        return $this->successResponse(null, 'Medical record deleted successfully');
    }
}

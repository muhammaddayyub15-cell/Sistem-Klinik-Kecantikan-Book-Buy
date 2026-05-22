<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use App\Http\Requests\Service\StoreServiceRequest;
use App\Http\Requests\Service\UpdateServiceRequest;
use App\Service\ServiceService;
use App\Shared\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;

class ServiceController extends Controller
{
    use ApiResponseTrait;

    protected ServiceService $serviceService;

    public function __construct(ServiceService $serviceService)
    {
        $this->serviceService = $serviceService;
    }

    public function index(): JsonResponse
    {
        // Ambil semua layanan via service layer
        $services = $this->serviceService->all();
        return $this->successResponse($services);
    }

    public function store(StoreServiceRequest $request): JsonResponse
    {
        // Validasi sudah ditangani StoreServiceRequest
        $service = $this->serviceService->create($request->validated());
        return $this->successResponse($service, 'Service created successfully', 201);
    }

    public function show(string $id): JsonResponse
    {
        // findOrFail otomatis lempar 404 jika tidak ditemukan
        $service = $this->serviceService->findOrFail($id);
        return $this->successResponse($service);
    }

    public function update(UpdateServiceRequest $request, string $id): JsonResponse
    {
        // Validasi sudah ditangani UpdateServiceRequest
        $service = $this->serviceService->update($id, $request->validated());
        return $this->successResponse($service, 'Service updated successfully');
    }

    public function destroy(string $id): JsonResponse
    {
        // Soft delete karena model pakai SoftDeletes
        $this->serviceService->delete($id);
        return $this->successResponse(null, 'Service deleted successfully');
    }
}
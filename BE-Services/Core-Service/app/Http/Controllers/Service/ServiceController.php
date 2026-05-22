<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use App\Service\ServiceService;
use App\Shared\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        $services = $this->serviceService->all();
        return $this->successResponse($services);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:service_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'is_active' => 'nullable|boolean',
        ]);

        $service = $this->serviceService->create($validated);
        return $this->successResponse($service, 'Service created successfully', 201);
    }

    public function show($id): JsonResponse
    {
        $service = $this->serviceService->findOrFail($id);
        return $this->successResponse($service);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => 'nullable|exists:service_categories,id',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'duration' => 'nullable|integer|min:1',
            'is_active' => 'nullable|boolean',
        ]);

        $service = $this->serviceService->update($id, $validated);
        return $this->successResponse($service, 'Service updated successfully');
    }

    public function destroy($id): JsonResponse
    {
        $this->serviceService->delete($id);
        return $this->successResponse(null, 'Service deleted successfully');
    }
}

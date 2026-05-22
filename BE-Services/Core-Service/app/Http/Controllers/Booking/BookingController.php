<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use App\Service\BookingService;
use App\Shared\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    use ApiResponseTrait;

    protected BookingService $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function index(): JsonResponse
    {
        $bookings = $this->bookingService->all();
        return $this->successResponse($bookings);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'service_id' => 'required|exists:services,id',
            'booked_at' => 'required|date_format:Y-m-d H:i:s',
            'status' => 'nullable|string|in:pending,confirmed,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $booking = $this->bookingService->create($validated);
        return $this->successResponse($booking, 'Booking created successfully', 201);
    }

    public function show($id): JsonResponse
    {
        $booking = $this->bookingService->findOrFail($id);
        return $this->successResponse($booking);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'patient_id' => 'nullable|exists:patients,id',
            'doctor_id' => 'nullable|exists:doctors,id',
            'service_id' => 'nullable|exists:services,id',
            'booked_at' => 'nullable|date_format:Y-m-d H:i:s',
            'status' => 'nullable|string|in:pending,confirmed,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $booking = $this->bookingService->update($id, $validated);
        return $this->successResponse($booking, 'Booking updated successfully');
    }

    public function destroy($id): JsonResponse
    {
        $this->bookingService->delete($id);
        return $this->successResponse(null, 'Booking deleted successfully');
    }
}

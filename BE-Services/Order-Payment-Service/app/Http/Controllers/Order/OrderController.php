<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\UpdateOrderStatusRequest;
use App\Service\OrderService;
use App\Service\Shared\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// OrderController: Thin controller — hanya menerima request, memanggil service,
// dan mengembalikan response. Tidak ada business logic di sini.
class OrderController extends Controller
{
    use ApiResponseTrait;

    public function __construct(private OrderService $orderService)
    {
    }

    // index: GET /orders
    // Ambil semua order dengan pagination. Hanya admin.
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->query('per_page', 15);
        $orders  = $this->orderService->getAllOrders((int) $perPage);
        return $this->successResponse($orders, 'Daftar order berhasil diambil');
    }

    // show: GET /orders/{id}
    // Detail satu order beserta items dan payment.
    public function show(int $id): JsonResponse
    {
        $order = $this->orderService->getOrderById($id);
        return $this->successResponse($order, 'Detail order berhasil diambil');
    }

    // store: POST /orders
    // Buat order baru. Admin dan patient bisa membuat order.
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = $this->orderService->createOrder($request->validated());
        return $this->createdResponse($order, 'Order berhasil dibuat');
    }

    // getByPatient: GET /orders/patient/{patientId}
    // Ambil semua order milik pasien tertentu.
    public function getByPatient(int $patientId): JsonResponse
    {
        $orders = $this->orderService->getOrdersByPatient($patientId);
        return $this->successResponse($orders, 'Daftar order pasien berhasil diambil');
    }

    // getByStatus: GET /orders/status/{status}
    // Filter order berdasarkan status. Hanya admin.
    public function getByStatus(string $status): JsonResponse
    {
        $orders = $this->orderService->getOrdersByStatus($status);
        return $this->successResponse($orders, 'Daftar order berhasil difilter');
    }

    // updateStatus: PATCH /orders/{id}/status
    // Update status order secara manual. Hanya admin.
    public function updateStatus(UpdateOrderStatusRequest $request, int $id): JsonResponse
    {
        // Gunakan method yang sesuai berdasarkan status yang diminta
        $status = $request->validated()['status'];

        $order = match ($status) {
            'cancelled' => $this->orderService->cancelOrder($id),
            'completed' => $this->orderService->completeOrder($id),
            default     => throw new \Exception('Perubahan status ini tidak didukung via endpoint ini.', 422),
        };

        return $this->successResponse($order, 'Status order berhasil diperbarui');
    }
}
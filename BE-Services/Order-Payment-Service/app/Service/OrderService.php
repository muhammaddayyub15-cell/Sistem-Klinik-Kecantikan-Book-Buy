<?php

namespace App\Service;

use App\Service\Repositories\OrderItemRepository;
use App\Service\Repositories\OrderRepository;
use App\Service\Shared\Base\BaseService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderService extends BaseService
{
    private OrderRepository $orderRepository;
    private OrderItemRepository $orderItemRepository;

    public function __construct(
        OrderRepository $orderRepository,
        OrderItemRepository $orderItemRepository
    ) {
        parent::__construct($orderRepository);
        $this->orderRepository     = $orderRepository;
        $this->orderItemRepository = $orderItemRepository;
    }

    public function getAllOrders(int $perPage = 15): LengthAwarePaginator
    {
        return $this->orderRepository->findAllPaginated($perPage);
    }

    public function getOrderById(int $id): Model
    {
        return $this->orderRepository->findById($id, ['orderItems', 'payment']);
    }

    public function getOrdersByPatient(int $patientId): Collection
    {
        return $this->orderRepository->findByPatientSnapshot($patientId);
    }

    public function getOrdersByStatus(string $status): Collection
    {
        return $this->orderRepository->findByStatus($status);
    }

    public function createOrder(array $data): Model
    {
        // ── 1. Fetch patient snapshot dari Core Service ───────────────
        $patient = $this->fetchPatient($data['patient_id']);

        // ── 2. Fetch product snapshot dari Product Service ────────────
        $productSnapshots = $this->fetchProducts(
            collect($data['items'])->pluck('product_id')->toArray()
        );

        // ── 3. Susun items dengan snapshot ────────────────────────────
        $itemsWithSnapshot = collect($data['items'])->map(function ($item) use ($productSnapshots) {
            $product = $productSnapshots[$item['product_id']] ?? null;

            if (!$product) {
                throw new \Exception("Produk dengan ID {$item['product_id']} tidak ditemukan.", 422);
            }

            return [
                'product_id_snapshot'   => $product['product_id'],
                'product_name_snapshot' => $product['product_name'],
                'unit_price_snapshot'   => $product['price'],
                'qty'                   => $item['qty'],
            ];
        });

        // ── 4. Hitung total ───────────────────────────────────────────
        $totalAmount = $itemsWithSnapshot->sum(fn($item) => $item['unit_price_snapshot'] * $item['qty']);

        // ── 5. Buat order ─────────────────────────────────────────────
        $order = $this->orderRepository->create([
            'order_number'          => $this->generateOrderNumber(),
            'patient_id_snapshot'   => $patient['patient_id'],
            'patient_name_snapshot' => $patient['full_name'] ?? $patient['name'] ?? 'Unknown',
            'booking_id_snapshot'   => $data['booking_id'] ?? null,
            'total_amount'          => $totalAmount,
            'status'                => 'pending',
        ]);

        // ── 6. Bulk insert items ──────────────────────────────────────
        $items = $itemsWithSnapshot->map(fn($item) => [
            ...$item,
            'order_id'   => $order->id,
            'created_at' => now(),
            'updated_at' => now(),
        ])->toArray();

        $this->orderItemRepository->createBulk($items);

        return $this->orderRepository->findById($order->id, ['orderItems', 'payment']);
    }

    public function cancelOrder(int $id): Model
    {
        $order = $this->orderRepository->findById($id);

        if ($order->status !== 'pending') {
            throw new \Exception('Hanya order dengan status pending yang dapat dibatalkan.', 422);
        }

        return $this->orderRepository->updateStatus($id, 'cancelled', [
            'cancelled_at' => now(),
        ]);
    }

    public function completeOrder(int $id): Model
    {
        return $this->orderRepository->updateStatus($id, 'completed', [
            'completed_at' => now(),
        ]);
    }

    // ── Private helpers ───────────────────────────────────────────────────

    private function fetchPatient(int $patientId): array
    {
        $gatewayUrl = config('services.gateway.base_url'); // direct to core service | pakai gateway 'services.gateway.base_url'
        $token      = request()->bearerToken();            // ← forward token user
        Log::info('fetchPatient token', ['token' => request()->bearerToken()]);

        $response = Http::withHeaders([
            'Authorization'      => 'Bearer ' . $token,
            'Accept'             => 'application/json',
            'X-Internal-Service' => 'order-service',      // ← identitas pemanggil
        ])->timeout(10)->get("{$gatewayUrl}/api/patients/{$patientId}");

        if (!$response->successful()) {
            throw new \Exception("Gagal mengambil data patient ID {$patientId}.", 422);
        }

        $patient = $response->json('data');

        if (!$patient) {
            throw new \Exception("Patient ID {$patientId} tidak ditemukan.", 422);
        }

        return $patient;
    }

    private function fetchProducts(array $productIds): array
    {
        $gatewayUrl = config('services.gateway.base_url'); //  pakai gateway
        $token      = request()->bearerToken();            // ← forward token user
        $snapshots  = [];

        foreach ($productIds as $productId) {
            $response = Http::withHeaders([
                'Authorization'      => 'Bearer ' . $token,
                'Accept'             => 'application/json',
                'X-Internal-Service' => 'order-service',      // ← identitas pemanggil
            ])->timeout(10)->get("{$gatewayUrl}/api/products/{$productId}");

            if (!$response->successful()) {
                throw new \Exception("Gagal mengambil data produk ID {$productId}.", 422);
            }

            $product = $response->json('data');

            if (!$product) {
                throw new \Exception("Produk ID {$productId} tidak ditemukan.", 422);
            }

            $snapshots[$productId] = $product;
        }

        return $snapshots;
    }

    private function generateOrderNumber(): string
    {
        return 'ORD-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));
    }
}
<?php

namespace App\Service;

use App\Service\Repositories\OrderItemRepository;
use App\Service\Repositories\OrderRepository;
use App\Service\Shared\Base\BaseService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

// OrderService: Business logic untuk manajemen order.
// Mengelola dua repository sekaligus: OrderRepository & OrderItemRepository.
//
// CATATAN ARSITEKTUR:
// Service ini tidak langsung query ke Core Service atau Product Service.
// Data pasien dan produk diterima dari request (sudah divalidasi di FormRequest)
// lalu disimpan sebagai snapshot. Proses pengambilan data dari service lain
// dilakukan di Controller sebelum memanggil service ini, atau via HTTP client
// (misal Guzzle) jika diperlukan validasi silang.
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

    // getAllOrders: Ambil semua order dengan pagination.
    public function getAllOrders(int $perPage = 15): LengthAwarePaginator
    {
        return $this->orderRepository->findAllPaginated($perPage);
    }

    // getOrderById: Ambil detail satu order beserta items dan payment.
    public function getOrderById(int $id): Model
    {
        return $this->orderRepository->findById($id, ['orderItems', 'payment']);
    }

    // getOrdersByPatient: Ambil semua order milik pasien tertentu.
    public function getOrdersByPatient(int $patientId): Collection
    {
        return $this->orderRepository->findByPatientSnapshot($patientId);
    }

    // getOrdersByStatus: Filter order berdasarkan status.
    public function getOrdersByStatus(string $status): Collection
    {
        return $this->orderRepository->findByStatus($status);
    }

    // createOrder: Buat order baru beserta semua item-nya secara atomik.
    // $data wajib mengandung:
    //   - patient_id_snapshot, patient_name_snapshot
    //   - booking_id_snapshot (opsional)
    //   - items: array of { product_id_snapshot, product_name_snapshot, unit_price_snapshot, qty }
    public function createOrder(array $data): Model
    {
        // Hitung total amount dari items
        $totalAmount = collect($data['items'])->sum(function ($item) {
            return $item['unit_price_snapshot'] * $item['qty'];
        });

        // Buat record order
        $order = $this->orderRepository->create([
            'order_number'          => $this->generateOrderNumber(),
            'patient_id_snapshot'   => $data['patient_id_snapshot'],
            'patient_name_snapshot' => $data['patient_name_snapshot'],
            'booking_id_snapshot'   => $data['booking_id_snapshot'] ?? null,
            'total_amount'          => $totalAmount,
            'status'                => 'pending',
        ]);

        // Siapkan items dengan order_id yang baru dibuat
        $items = collect($data['items'])->map(function ($item) use ($order) {
            return [
                'order_id'              => $order->id,
                'product_id_snapshot'   => $item['product_id_snapshot'],
                'product_name_snapshot' => $item['product_name_snapshot'],
                'unit_price_snapshot'   => $item['unit_price_snapshot'],
                'qty'                   => $item['qty'],
                'created_at'            => now(),
                'updated_at'            => now(),
            ];
        })->toArray();

        // Simpan semua items sekaligus (bulk insert)
        $this->orderItemRepository->createBulk($items);

        return $this->orderRepository->findById($order->id, ['orderItems', 'payment']);
    }

    // cancelOrder: Batalkan order.
    // Hanya order dengan status 'pending' yang bisa dibatalkan.
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

    // completeOrder: Tandai order sebagai selesai.
    // Dipanggil dari PaymentService setelah webhook settlement diterima dari Midtrans.
    public function completeOrder(int $id): Model
    {
        return $this->orderRepository->updateStatus($id, 'completed', [
            'completed_at' => now(),
        ]);
    }

    // generateOrderNumber: Generate nomor order unik dengan format ORD-YYYYMMDD-XXXX.
    // Contoh: ORD-20250522-A3F9
    private function generateOrderNumber(): string
    {
        return 'ORD-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));
    }
}
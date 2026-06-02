<?php

namespace App\Service\Repositories;

use App\Models\Order\Order;
use App\Service\Shared\Base\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

// OrderRepository: Query layer untuk tabel orders.
class OrderRepository extends BaseRepository
{
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }

    // findAllPaginated: Ambil semua order dengan pagination.
    public function findAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['orderItems', 'payment'])->paginate($perPage);
    }

    // findByPatientSnapshot: Ambil semua order milik pasien tertentu.
    // Pakai patient_id_snapshot karena tidak ada FK hidup ke Core Service.
    public function findByPatientSnapshot(int $patientId): Collection
    {
        return $this->model->with(['orderItems', 'payment'])
            ->where('patient_id_snapshot', $patientId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    // findByOrderNumber: Cari order berdasarkan order_number.
    // Dipakai saat webhook Midtrans masuk — Midtrans mengembalikan order_number
    // di field 'order_id' payload karena itulah yang kita kirim saat initiate.
    public function findByOrderNumber(string $orderNumber): ?Order
    {
        return $this->model->with(['orderItems', 'payment'])
            ->where('order_number', $orderNumber)
            ->first();
    }

    // findByStatus: Filter order berdasarkan status.
    public function findByStatus(string $status): Collection
    {
        return $this->model->with(['orderItems', 'payment'])
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    // updateStatus: Update status order dan timestamp terkait.
    // $timestamps: array opsional, misal ['paid_at' => now(), 'completed_at' => now()]
    public function updateStatus(int $id, string $status, array $timestamps = []): Order
    {
        $order = $this->findById($id);
        $order->update(array_merge(['status' => $status], $timestamps));
        return $order->fresh();
    }

    // updateOrderNumber: Simpan order_number yang di-generate PaymentService.
    // Dipanggil tepat sebelum Snap request ke Midtrans agar order_number
    // sudah tersimpan sebelum webhook mungkin masuk.
    public function updateOrderNumber(int $id, string $orderNumber): Order
    {
        $order = $this->findById($id);
        $order->update(['order_number' => $orderNumber]);
        return $order->fresh();
    }
}
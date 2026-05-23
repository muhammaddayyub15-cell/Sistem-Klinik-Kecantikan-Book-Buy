<?php

namespace App\Service\Shared\Base;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

// BaseRepository: Kelas induk untuk semua repository di Product Service.
// Menyediakan operasi CRUD dasar agar tidak ada duplikasi kode di setiap repository.
abstract class BaseRepository
{
    // $model: Instance Eloquent Model yang di-inject via constructor di child class.
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    // findAll: Ambil semua data. Mendukung relasi eager load opsional.
    public function findAll(array $with = []): Collection
    {
        return $this->model->with($with)->get();
    }

    // findById: Cari satu record berdasarkan primary key.
    // Melempar ModelNotFoundException secara otomatis jika tidak ditemukan (findOrFail).
    public function findById(int $id, array $with = []): Model
    {
        return $this->model->with($with)->findOrFail($id);
    }

    // create: Simpan record baru ke database.
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    // update: Update record berdasarkan ID, kemudian kembalikan instance terbaru.
    public function update(int $id, array $data): Model
    {
        $record = $this->findById($id);
        $record->update($data);
        return $record->fresh();
    }

    // delete: Soft delete record berdasarkan ID (membutuhkan SoftDeletes di model).
    public function delete(int $id): bool
    {
        $record = $this->findById($id);
        return $record->delete();
    }
}
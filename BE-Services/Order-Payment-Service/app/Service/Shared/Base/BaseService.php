<?php

namespace App\Service\Shared\Base;

// BaseService: Kelas induk untuk semua service class di Order-Payment Service.
// Tujuan: memisahkan business logic dari controller (thin controller, fat service).
// Setiap child service menerima repository via constructor injection.
abstract class BaseService
{
    // $repository: Repository yang di-inject, digunakan untuk akses data layer.
    protected BaseRepository $repository;

    public function __construct(BaseRepository $repository)
    {
        $this->repository = $repository;
    }
}
<?php

namespace App\Shared\Base;

abstract class BaseService
{
    protected BaseRepository $repository;

    public function __construct(BaseRepository $repository)
    {
        $this->repository = $repository;
    }

    public function all()
    {
        return $this->repository->all();
    }

    public function find($id)
    {
        return $this->repository->find($id);
    }

    public function findOrFail($id)
    {
        return $this->repository->findOrFail($id);
    }

    public function create(array $attributes)
    {
        return $this->repository->create($attributes);
    }

    public function update($id, array $attributes)
    {
        return $this->repository->update($id, $attributes);
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }
}

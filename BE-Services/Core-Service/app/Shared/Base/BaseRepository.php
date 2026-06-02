<?php

namespace App\Shared\Base;

use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function findOrFail($id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $attributes)
    {
        $model = $this->model->create($attributes);
        // FIX: gunakan refresh() agar selalu respect custom $primaryKey (misal patient_id, doctor_id, dsb)
        // fresh() berpotensi query dengan kolom 'id' sehingga gagal pada model dengan custom PK
        return $model->refresh();
    }

    public function update($id, array $attributes)
    {
        $record = $this->findOrFail($id);
        $record->update($attributes);
        // FIX: sama seperti create(), gunakan refresh() bukan fresh()
        return $record->refresh();
    }

    public function delete($id)
    {
        $record = $this->findOrFail($id);
        return $record->delete();
    }
}
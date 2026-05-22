<?php

namespace App\Models\Medical;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Prescription extends Model
{
    use HasFactory;

    protected $primaryKey = 'presc_id';

    protected $fillable = [
        'record_id',
        'product_id',
        // Snapshot data produk — disimpan agar tidak berubah jika produk diedit
        'product_name_snapshot',
        'qty',
        'dosage_instruction',
        'prescribed_at',
    ];

    protected function casts(): array
    {
        return [
            'qty'           => 'integer',
            'prescribed_at' => 'datetime',
        ];
    }

    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class, 'record_id', 'record_id');
    }
}
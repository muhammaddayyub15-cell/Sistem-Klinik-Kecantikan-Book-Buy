<?php

namespace App\Models\Doctor;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Specialization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function doctors(): HasMany
    {
        return $this->hasMany(Doctor::class, 'specialization_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    /** @use HasFactory<\Database\Factories\WorkerFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'role',
        'cost_per_hour',
        'availability',
    ];

    protected $casts = [
        'cost_per_hour' => 'decimal:2',
        'availability' => 'boolean',
    ];

    /**
     * Esdeveniments als que el treballador ha estat assignat.
     */
    public function events()
    {
        return $this->belongsToMany(Event::class)
                    ->withPivot('hours', 'notes')
                    ->withTimestamps();
    }
}

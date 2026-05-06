<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    /** @use HasFactory<\Database\Factories\WorkerFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'role',
        'cost_per_hour',
        'availability',
        'location',
        'specialization_tags',
        'rating',
    ];

    protected $casts = [
        'cost_per_hour' => 'decimal:2',
        'specialization_tags' => 'array',
        'availability' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function timeOffs()
    {
        return $this->hasMany(TimeOff::class);
    }

    public function events()
    {
        return $this->belongsToMany(Event::class)
                    ->withPivot('hours', 'notes')
                    ->withTimestamps();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    /** @use HasFactory<\Database\Factories\EventFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'date',
        'location',
        'type',
        'assistants',
        'status',
        'client_id',
    ];

    protected $casts = [
        'date' => 'datetime',
        'assistants' => 'integer',
    ];

    /**
     * Obtenir el client propietari de l'esdeveniment.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Treballadors assignats a l'esdeveniment.
     */
    public function workers()
    {
        return $this->belongsToMany(Worker::class)
                    ->withPivot('hours', 'notes')
                    ->withTimestamps();
    }
}

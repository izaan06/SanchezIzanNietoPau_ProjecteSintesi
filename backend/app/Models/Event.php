<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    /** @use HasFactory<\Database\Factories\EventFactory> */
    use HasFactory;

    protected $fillable = [
        'name',                   // Nom de l'esdeveniment
        'date',                   // Data i hora
        'location',               // Ubicació física
        'type',                   // Tipus (Boda, Corporatiu, etc.)
        'assistants',             // Nombre de convidats
        'status',                 // Estat actual (confirmed, planning, etc.)
        'client_id',              // ID del client propietari
        'estimated_cost',         // Cost calculat de l'esdeveniment
        'appointment_request_id', // Enllaç amb la petició original de la IA
        'tasks',                  // Checklist de tasques en format JSON
    ];

    protected $casts = [
        'date' => 'datetime',
        'end_date' => 'datetime',
        'assistants' => 'integer',
        'tasks' => 'array', // Converteix el JSON de la BD a un array de PHP automàticament
    ];

    /**
     * Relació: Obté la sol·licitud original processada per la IA.
     */
    public function appointmentRequest()
    {
        return $this->belongsTo(AppointmentRequest::class);
    }

    /**
     * Relació: Obté el client que ha contractat l'esdeveniment.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Relació: Treballadors que han estat assignats a aquest esdeveniment.
     * Inclou dades extra de la taula pivot com les hores i notes.
     */
    public function workers()
    {
        return $this->belongsToMany(Worker::class)
                    ->withPivot('hours', 'notes')
                    ->withTimestamps();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppointmentRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'event_type',
        'attendees',
        'desired_date',
        'message',
        'status',
        'user_id',
        'ai_estimated_budget',
        'ai_recommendations',
        'start_time',
        'end_time',
        'location_name',
        'wants_music',
        'music_type',
        'menu_type',
        'dietary_requirements',
        'ai_address',
        'client_budget',
        'ai_operational_cost',
        'ai_estimated_profit',
    ];

    protected $casts = [
        'desired_date' => 'date',
        'ai_recommendations' => 'array',
        'ai_estimated_budget' => 'decimal:2',
        'client_budget' => 'decimal:2',
        'ai_operational_cost' => 'decimal:2',
        'ai_estimated_profit' => 'decimal:2',
        'wants_music' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

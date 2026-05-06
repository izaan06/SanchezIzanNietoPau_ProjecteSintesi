<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeOff extends Model
{
    use HasFactory;

    protected $fillable = [
        'worker_id',
        'start_date',
        'end_date',
        'reason',
        'status',
    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }
}

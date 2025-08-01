<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class CallOutJob extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'client_id',
        'job_name',
        'work_order_number',
        'description',
        'priority',
        'status',
        'start_date',
        'end_date',
        'documents'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'documents' => 'array'
    ];

    /**
     * Get the activity log options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }

    /**
     * Get the client that owns the call-out job.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the service tickets for the call-out job.
     */
    public function serviceTickets()
    {
        return $this->hasMany(ServiceTicket::class);
    }
}

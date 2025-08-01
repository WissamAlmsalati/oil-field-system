<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class TicketIssue extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'ticket_id',
        'description',
        'status',
        'remarks',
        'date_reported'
    ];

    protected $casts = [
        'date_reported' => 'date'
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
     * Get the service ticket that owns the ticket issue.
     */
    public function ticket()
    {
        return $this->belongsTo(ServiceTicket::class, 'ticket_id');
    }
}

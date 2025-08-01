<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ServiceTicket extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'ticket_number',
        'client_id',
        'sub_agreement_id',
        'call_out_job_id',
        'date',
        'status',
        'amount',
        'related_log_ids',
        'documents'
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
        'related_log_ids' => 'array',
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
     * Get the client that owns the service ticket.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the sub-agreement that owns the service ticket.
     */
    public function subAgreement()
    {
        return $this->belongsTo(SubAgreement::class);
    }

    /**
     * Get the call-out job that owns the service ticket.
     */
    public function callOutJob()
    {
        return $this->belongsTo(CallOutJob::class);
    }

    /**
     * Get the ticket issues for the service ticket.
     */
    public function ticketIssues()
    {
        return $this->hasMany(TicketIssue::class, 'ticket_id');
    }

    /**
     * Generate unique ticket number
     */
    public static function generateTicketNumber()
    {
        $lastTicket = self::orderBy('id', 'desc')->first();
        $lastNumber = $lastTicket ? intval(substr($lastTicket->ticket_number, -6)) : 0;
        return 'ST-' . str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
    }
}

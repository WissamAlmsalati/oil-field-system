<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class SubAgreement extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'client_id',
        'name',
        'amount',
        'balance',
        'start_date',
        'end_date',
        'file_path',
        'file_name'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'amount' => 'decimal:2',
        'balance' => 'decimal:2'
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
     * Get the client that owns the sub-agreement.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the service tickets for the sub-agreement.
     */
    public function serviceTickets()
    {
        return $this->hasMany(ServiceTicket::class);
    }
}

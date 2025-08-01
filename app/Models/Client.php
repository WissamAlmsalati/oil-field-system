<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Client extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'logo_url',
        'logo_file_path'
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
     * Get the contact people for the client.
     */
    public function contacts()
    {
        return $this->hasMany(ContactPerson::class);
    }

    /**
     * Get the sub-agreements for the client.
     */
    public function subAgreements()
    {
        return $this->hasMany(SubAgreement::class);
    }

    /**
     * Get the call-out jobs for the client.
     */
    public function callOutJobs()
    {
        return $this->hasMany(CallOutJob::class);
    }

    /**
     * Get the daily service logs for the client.
     */
    public function dailyServiceLogs()
    {
        return $this->hasMany(DailyServiceLog::class);
    }

    /**
     * Get the service tickets for the client.
     */
    public function serviceTickets()
    {
        return $this->hasMany(ServiceTicket::class);
    }
}

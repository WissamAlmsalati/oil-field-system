<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ContactPerson extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'client_id',
        'name',
        'email',
        'phone',
        'position'
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
     * Get the client that owns the contact person.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}

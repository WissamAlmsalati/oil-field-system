<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class DailyServiceLog extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'log_number',
        'client_id',
        'field',
        'well',
        'contract',
        'job_no',
        'date',
        'linked_job_id',
        'personnel',
        'equipment_used',
        'almansoori_rep',
        'mog_approval_1',
        'mog_approval_2',
        'excel_file_path',
        'excel_file_name',
        'pdf_file_path',
        'pdf_file_name'
    ];

    protected $casts = [
        'date' => 'date',
        'personnel' => 'array',
        'equipment_used' => 'array',
        'almansoori_rep' => 'array',
        'mog_approval_1' => 'array',
        'mog_approval_2' => 'array'
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
     * Get the client that owns the daily service log.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Generate unique log number
     */
    public static function generateLogNumber()
    {
        $lastLog = self::orderBy('id', 'desc')->first();
        $lastNumber = $lastLog ? intval(substr($lastLog->log_number, -6)) : 0;
        return 'DSL-' . str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
    }
}

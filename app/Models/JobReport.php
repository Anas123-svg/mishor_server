<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobReport extends Model
{
    use HasFactory;

    protected $table = 'job_reports';

    protected $fillable = [
        'report_name',
        'job_id', //server_job_id
        'form_data',
        'layout'
    ];
    protected $casts = [
        'form_data' => 'array',
    ];

    public function job()
    {
        return $this->belongsTo(AppJob::class, 'job_id');
    }
}

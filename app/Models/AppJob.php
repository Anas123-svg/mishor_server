<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class AppJob extends Model
{
    use HasFactory;

    protected $table = 'app_jobs';

    protected $fillable = [
        'client_name',
        'job_title',
        'notes',
        'on_site_date',
        'on_site_time',
        'status',
        'due_on',
        'clientId',
        'site_address'
    ];


    public function client()
    {
        return $this->belongsTo(Client::class, 'clientId');
    }
    public function reports()
    {
        return $this->hasMany(JobReport::class, 'job_id');
    }

}

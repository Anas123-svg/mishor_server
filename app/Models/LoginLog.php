<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{
    protected $fillable = [
        'client_id',
        'client_user_id',
        'ip_address',
        'user_agent',
        'logged_in_at'
    ];

    public $timestamps = true;
}

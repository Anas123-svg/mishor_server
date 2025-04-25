<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Client extends Authenticatable
{
    use HasApiTokens, Notifiable;
    protected $fillable = [
        'name', 'surname', 'email', 'password', 'phone', 'country', 'city', 'notes', 'profileImage'
    ];

    protected $hidden = [
        'password',
    ];

    public function folders()
    {
        return $this->hasMany(Folder::class, 'clientId');
    }

    public function files()
    {
        return $this->hasMany(File::class, 'clientId');
    }
}

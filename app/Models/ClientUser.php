<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class ClientUser extends Model
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'client_users';

    protected $fillable = [
        'client_id',
        'name',
        'surname',
        'email',
        'password',
        'phone',
        'country',
        'city',
        'notes',
        'profileImage',
    ];

    protected $hidden = [
        'password',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}

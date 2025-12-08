<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAssignedFolder extends Model
{
    use HasFactory;

    protected $table = 'user_assigned_folders';

    protected $fillable = [
        'client_user_id',
        'folder_id',
    ];

    // Relationships
    public function clientUser()
    {
        return $this->belongsTo(ClientUser::class, 'client_user_id');
    }

    public function folder()
    {
        return $this->belongsTo(Folder::class, 'folder_id');
    }
}

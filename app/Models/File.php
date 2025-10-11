<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = [
        'name', 'path', 'folderId', 'clientId', 'status', 'built_in_portal', 'template'
    ];
        protected $casts = [
        'built_in_portal' => 'boolean', 
    ];

    public function folder()
    {
        return $this->belongsTo(Folder::class, 'folderId');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'clientId');
    }
}

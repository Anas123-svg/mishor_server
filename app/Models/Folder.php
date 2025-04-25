<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    protected $fillable = [
        'name', 'parentId', 'clientId'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'clientId');
    }

    public function parent()
    {
        return $this->belongsTo(Folder::class, 'parentId');
    }

    public function children()
    {
        return $this->hasMany(Folder::class, 'parentId');
    }

    public function files()
    {
        return $this->hasMany(File::class, 'folderId');
    }
}

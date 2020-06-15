<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = [
        'name', 'type', 'file_extension_id', 'description', 'user_id', 'path'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fileExtension()
    {
        return $this->belongsTo(FileExtension::class);
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FileExtension extends Model
{
    protected $fillable = [
        'file_type_id', 'extension', 'mime_type', 'icon'
    ];

    public function fileType()
    {
        return $this->belongsTo(FileType::class);
    }
}

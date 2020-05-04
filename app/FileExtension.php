<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FileExtension extends Model
{
    protected $fillable = [
        'file_type', 'extension', 'extension_icon'
    ];


}

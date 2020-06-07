<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FileType extends Model
{
    protected $fillable = [
        'name', 'description',
    ];

    public function extensions()
    {
        return $this->hasMany(FileExtension::class);
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = [
        'name',  'type', 'extension','description', 'user_id', 'path'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

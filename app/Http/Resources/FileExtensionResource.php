<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class FileExtensionResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }

//    public function getExtensionsAttribute(){
//        return json_decode($this->extensions);
//    }
}

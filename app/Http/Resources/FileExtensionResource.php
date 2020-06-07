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
        $default_data = parent::toArray($request);

        $additional_data = [
            'file_type' => $this->fileType,
        ];

        return array_merge($default_data, $additional_data);
    }

//    public function getExtensionsAttribute(){
//        return json_decode($this->extensions);
//    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class FileResource extends Resource
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
            'user' => $this->user,
            'file_extension' => $this->fileExtension
        ];

        return array_merge($default_data,$additional_data);
    }
}

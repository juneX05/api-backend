<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class UserResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $default_data =  parent::toArray($request);

        $additional_data = [
            'role' => $this->roles->first(),
            'permissions' => $this->permissions,
            'image' => $this->file
        ];

        return array_merge($default_data,$additional_data);
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class RoleResource extends Resource
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
            'permissions' => $this->permissions,
        ];

        return array_merge($default_data,$additional_data);
    }
}

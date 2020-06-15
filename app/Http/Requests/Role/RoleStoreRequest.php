<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;

class RoleStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|unique:roles,name',
            'description' => 'nullable|max:255',
            'permissions' => 'nullable|array'
        ];
    }

    /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'Role Name is required!',
            'name.unique' => 'Role Name :input is already taken!',
            'description.max' => 'Role Description must not exceed 255 characters!',
            'permissions.array' => 'Permissions must be an array of selected permissions!',
        ];
    }
}

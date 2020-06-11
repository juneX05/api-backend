<?php

namespace App\Http\Requests\FileExtension;

use Illuminate\Foundation\Http\FormRequest;

class FileExtensionStoreRequest extends FormRequest
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
            'extension' => 'required|unique:file_extensions',
            'mime_type' => 'required|unique:file_extensions',
            'icon' => ''
        ];
    }
}

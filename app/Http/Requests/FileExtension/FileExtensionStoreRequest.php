<?php

namespace App\Http\Requests\FileExtension;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
    public function rules(Request $request)
    {
        return [
            'mime_type' => ['required',
                Rule::unique('file_extensions')->where(function ($query) use ($request) {
                    return $query
                        ->where('extension', $request->extension)
                        ->where('mime_type', $request->mime_type);
                })
            ],
            'extension' => 'required|unique:file_extensions',
            'icon' => ''
        ];
    }

    public function messages()
    {
        return [
            'mime_type.unique' => 'A mime type associated with the extension exists.',
            'extension.unique' => 'An extension associated with the mime type exists.',
        ];
    }
}

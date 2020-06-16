<?php


namespace App\Http\Traits;

use App\FileExtension;

trait FileValidator
{
    public function validateFile($request, $name = 'name', $file_request = 'file', $checks = [], $overwrite = false)
    {

        $max_size = (int)ini_get('upload_max_filesize') * 1000;

        $rules = [
            'description' => 'nullable|string|max:255'
        ];

        $file_name = $request[$name];
        $description = $request['description'];

        $file = $request->file($file_request);
        $file_extension = null;

        if ($file) {
            $file_name = preg_replace("/\.[^.]+$/", "", $file->getClientOriginalName());
            $request[$name] = $file_name;
//            $request->merge([$name => $file_name]);

            $rules[$file_request] = ['required', 'file', 'max:' . $max_size];
            $file_extension = $this->validateMimeType($file, $file_request, $checks);
            if (gettype($file_extension) === 'array') {
                return $file_extension;
            }
        }

        // IF NOT EDITING A FILE JUST SKIP THIS STEP
        if (!$overwrite) {
            $rules[$name] = 'required|unique:files';
        }

        $validator = $this->validate($request, $rules);

        if ($validator) {
            return $this->setFileDetails($file_name, $description, $file, $file_extension, $overwrite);
        } else {
            return $validator;
        }
    }

    public function validateMimeType($file, $file_request, $checks)
    {
        $error_messages = [
            'errors' => [$file_request => ''],
            'status' => false,
            'message' => ''
        ];
        //DEPENDING ON $check VALUE, ALLOW ONLY SPECIFIED FILES TO GO THROUGH.
        $client_mimeType = $file->getClientMimeType();
        $client_file_type = explode("/", $client_mimeType)[0];
        if (count($checks) > 0 && !in_array($client_file_type, $checks)) {
            $message = 'Only ' . implode(" ", $checks) . ' required';
            $error_messages['errors'][$file_request] = $message;
            $error_messages['message'] = $message;
            return $error_messages;
        }

        $mimeType = $file->getMimeType();
        $file_extension = FileExtension::where('mime_type', $mimeType)->first();

        if ($file_extension) {
            //ONLY TRUST A FILE THAT ITS MIMETYPE MATCHES THE ALLOWED EXTENSIONS
            if ($file->getClientOriginalExtension() === $file_extension->extension) {
                return $file_extension;
            }

            $message = 'File Extension does not match the File Uploaded.';
            $error_messages['errors'][$file_request] = $message;
            $error_messages['message'] = $message;
            return $error_messages;
        }

        $message = 'File Type not supported.';
        $error_messages['errors'][$file_request] = $message;
        $error_messages['message'] = $message;
        return $error_messages;
    }

    public function setFileDetails($file_name, $description, $file, $file_extension, $overwrite)
    {
        if ($file) {
            $extension = $file_extension->extension;
            $extension_id = $file_extension->id;
            $type = explode("/", $file_extension->mime_type)[0];
        } else {
            $extension_id = '';
            $extension = '';
            $type = '';
        }

        return (object)[
            'extension_id' => $extension_id,
            'name' => $file_name,
            'original_file' => $file,
            'description' => $description,
            'type' => $type,
            'extension' => $extension,
            'path' => '/public/' . $type . '/',
            'store_path' => $type . '/' . $file_name . '.' . $extension,
            'overwrite' => $overwrite
        ];
    }
}

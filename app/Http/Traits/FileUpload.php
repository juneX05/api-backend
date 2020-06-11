<?php


namespace App\Http\Traits;


use App\File;
use App\FileExtension;
use App\FileType;
use App\Http\Resources\FileExtensionResource;
use App\Rules\MimeTypes;
use Auth;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpParser\Node\Stmt\DeclareDeclare;

trait FileUpload
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
            $file_extension = $this->validateMimeType($file, $checks);
            if (gettype($file_extension) === 'array') {
                return $file_extension;
            }
        }

        // TODO: CHECK IF FILE REPLACING WILL WORK WITH THIS CODE BELOW
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

    public function validateMimeType($file, $checks)
    {
        $mimeType = $file->getMimeType();
        $original_file_type = explode("/", $mimeType)[0];
        $file_extension = FileExtension::where('mime_type', $mimeType)->first();

        if ($file_extension) {
            if ($file->getClientOriginalExtension() === $file_extension->extension) {
                if (count($checks) > 0) {
                    if (in_array($original_file_type, $checks)) {
                        return $file_extension;
                    }

                    return ['status' => false, 'message' => 'Required file types are.' . implode(" ", $checks)];
                }

                return $file_extension;
            }

            return ['status' => false, 'message' => 'File Extension does not match the File Uploaded.'];
        }

        return ['status' => false, 'message' => 'File Type not supported.'];
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

        return (Object)[
            'extension_id' => $extension_id,
            'name' => $file_name,
            'original_file' => $file,
            'description' => $description,
            'extension' => $extension,
            'path' => '/public/' . $type . '/',
            'store_path' => $type . '/' . $file_name . '.' . $extension,
            'overwrite' => $overwrite
        ];
    }

    public function uploadFile($uploaded_file)
    {
        if (gettype($uploaded_file) === 'array') {
            return $uploaded_file['message'];
        }

        $model = new File();
        $full_file_name = $uploaded_file->name . '.' . $uploaded_file->extension;
        if ($uploaded_file->overwrite && Storage::disk('local')->exists($uploaded_file->path . '/' . $full_file_name)) {
            Storage::disk('local')->delete($uploaded_file->path . '/' . $full_file_name);
        }

        if (Storage::putFileAs($uploaded_file->path, $uploaded_file->original_file, $full_file_name)) {

            return $model::create([
                'name' => $uploaded_file->name,
                'file_extension_id' => $uploaded_file->extension_id,
                'user_id' => auth()->user()->id,
                'description' => $uploaded_file->description,
                'path' => Storage::url($uploaded_file->store_path)
            ]);
        }

        return false;
    }

    protected function changeFile($file,$uploaded_file)
    {
        $type = explode("/", $file->fileExtension->mime_type)[0];
        $old_filename = '/public/' . $type . '/' . $file->name . '.' . $file->fileExtension->extension;
        $new_filename = '/public/' . $type . '/' . $uploaded_file->name . '.' . $file->fileExtension->extension;

        if ($uploaded_file->original_file) {
            if (Storage::disk('local')->exists($old_filename)) {
                Storage::disk('local')->delete($old_filename);
            }
            return Storage::putFileAs($uploaded_file->path, $uploaded_file->original_file, $uploaded_file->name . $uploaded_file->extension);
        } else {
            if (Storage::disk('local')->exists($old_filename)) {
                return Storage::disk('local')->move($old_filename, $new_filename);
            }
            return false;
        }
    }

    protected function updateFileInfo($file,$uploaded_file){
        $type = explode("/", $file->fileExtension->mime_type)[0];
        $file->name = $uploaded_file->name;
        $file->description = $uploaded_file->description;

        if ($uploaded_file->original_file)
        {
            $file->path = Storage::url($uploaded_file->store_path);
        }
        else{
            $file->path = Storage::url($type . '/' . $uploaded_file->name . '.' . $file->fileExtension->extension);
        }

        return $file->save();
    }

    public function modifyFile($file,$uploaded_file)
    {
        //$file = File::where('id', $id)->where('user_id', Auth::id())->first();

        if ($this->changeFile($file, $uploaded_file))
            return $this->updateFileInfo($file, $uploaded_file);

        return "Error while updating file info";
    }

    public function delete_file($file){
        $file_path = str_replace('storage', 'public', $file->path);

        if (Storage::disk('local')->exists($file_path)) {
            if (Storage::disk('local')->delete($file_path)) {
                return $file->delete();
            }
        }

        return false;
    }

    protected function process_file($model,$uploaded_file) {
        if ($uploaded_file){
            if ($model->file_id == null || $model->file_id == ''){
                $image = $this->uploadFile($uploaded_file);
                $model->file_id = $image->id;
                return $model->save();
            }
            else {
                $file = File::where('id', $model->file_id)->first();
                return $this->modifyFile($file,$uploaded_file);
            }
        }

        return false;
    }
}

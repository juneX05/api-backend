<?php


namespace App\Http\Traits;


use App\File;
use App\FileExtension;
use Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait FileUpload
{
    protected function getExtensions($checks)
    {
        $file_extensions = FileExtension::all();
        $extensions = [];

        if (count($checks) > 0){
            foreach ($file_extensions as $file_extension){
                $file_type = Str::lower($file_extension->file_type);
                if (in_array($file_type, $checks))
                {
                    $extensions = array_merge($extensions,json_decode($file_extension->extensions));
                }
            }
        }
        else{
            foreach ($file_extensions as $file_extension){
                $extensions = array_merge($extensions,json_decode($file_extension->extensions));
            }
        }

        return $extensions;
    }

    public function validateFile($request,$name='name',$file_request='file',$checks = [],$overwrite = false){
        $extensions = $this->getExtensions($checks);
        $max_size = (int)ini_get('upload_max_filesize') * 1000;
        $all_extensions = implode(',', $extensions);
        $rules = [];

        $file = $request->file($file_request);

        if (!$overwrite) {
            if ($file){
                $request->merge([$name => $file->getClientOriginalName()]);
            }

            $rules[$name] = 'required|unique:files';
        }

        if ($file) {
            $rules[$file_request] = 'required|file|mimes:' . $all_extensions . '|max:' . $max_size;
        }

        $validator = $this->validate($request, $rules);

        if($validator){
            return $this->setFileDetails($request,$name,$file_request,$overwrite);
        }
        else{
            return $validator;
        }
    }

    public function setFileDetails($request,$name,$file_request,$overwrite){
        $file = $request->file($file_request);

        if ($file){
            $extension = $file->getClientOriginalExtension();
            $type = $this->getType($extension);
        }
        else{
            $extension = '';
            $type = '';
        }

        if (!$overwrite) {
            $file_name = $request[$name];
        }
        else{
            $file_name = $request[$name] . '.' .$file->getClientOriginalExtension();
        }

        return (Object) [
            'name' => $file_name,
            'original_file' => $file,
            'description' => $request['description'],
            'extension' => $extension,
            'type' => $type,
            'path' => '/public/' . $type . '/',
            'store_path' => $type. '/' . $file_name,
            'overwrite' => $overwrite
        ];
    }

    public function uploadFile($uploaded_file){
        $model = new File();
        if ($uploaded_file->overwrite && Storage::disk('local')->exists($uploaded_file->path. '/' .$uploaded_file->name)) {
            Storage::disk('local')->delete($uploaded_file->path. '/' .$uploaded_file->name);
        }

        if (Storage::putFileAs($uploaded_file->path,$uploaded_file->original_file,$uploaded_file->name)) {
            return $model::create([
                'name' => $uploaded_file->name,
                'type' => $uploaded_file->type,
                'extension' => $uploaded_file->extension,
                'user_id' => auth()->user()->id,
                'description' => $uploaded_file->description,
                'path' => Storage::url($uploaded_file->store_path)
            ]);
        }

        return false;
    }

    protected function changeFile($file,$uploaded_file){
        $old_filename = '/public/' . $file->type . '/' . $file->name;
        $new_filename = '/public/' . $file->type . '/' . $uploaded_file->name;

        if ($uploaded_file->original_file){
            if (Storage::disk('local')->exists($old_filename)){
                Storage::disk('local')->delete($old_filename);
            }
            return Storage::putFileAs($uploaded_file->path,$uploaded_file->original_file,$uploaded_file->name);
        }
        else{
            if (Storage::disk('local')->exists($old_filename)){
                return Storage::disk('local')->move($old_filename, $new_filename);
            }
            return false;
        }
    }

    protected function updateFileInfo($file,$uploaded_file){
        $file->name = $uploaded_file->name;
        $file->description = $uploaded_file->description;

        if ($uploaded_file->original_file)
        {
            $file->path = Storage::url($uploaded_file->store_path);
        }
        else{
            $file->path = Storage::url($file->type . '/' .$uploaded_file->name);
        }

        return $file->save();
    }

    public function modifyFile($file,$uploaded_file)
    {
        //$file = File::where('id', $id)->where('user_id', Auth::id())->first();

        $this->changeFile($file,$uploaded_file);
        return $this->updateFileInfo($file,$uploaded_file);

    }

    public function delete_file($file){
        $file_path = '/public/' . $file->type . '/' . $file->name . '.' . $file->extension;

        if (Storage::disk('local')->exists($file_path)) {
            if (Storage::disk('local')->delete($file_path)) {
                return $file->delete();
            }
        }

        return false;
    }

    /**
     * Get type by extension
     * @param  string $ext Specific extension
     * @return string      Type
     */
    protected function getType($ext)
    {
        $file_extensions = FileExtension::all();

        foreach ($file_extensions as $file_extension){
            $type = $file_extension->file_type;
            $extensions = json_decode($file_extension->extensions);
            if (in_array($ext, $extensions)) {
                return Str::lower($type);
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

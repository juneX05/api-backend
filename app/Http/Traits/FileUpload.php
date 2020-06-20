<?php


namespace App\Http\Traits;

use App\File;
use Auth;
use Illuminate\Support\Facades\Storage;

trait FileUpload
{
    use FileValidator;

    public function uploadFile($uploaded_file)
    {
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

    protected function renameFile($file, $uploaded_file)
    {
        $type = explode("/", $file->fileExtension->mime_type)[0];
        $old_filename = '/public/' . $type . '/' . $file->name . '.' . $file->fileExtension->extension;
        $new_filename = '/public/' . $type . '/' . $uploaded_file->name . '.' . $file->fileExtension->extension;

        if ($old_filename !== $new_filename && Storage::disk('local')->exists($old_filename)) {
            return Storage::disk('local')->move($old_filename, $new_filename);
        }

        return [
            'status' => false, 'message' => 'No file to rename'
        ];
    }

    protected function replaceFile($file, $uploaded_file)
    {
        $type = explode("/", $file->fileExtension->mime_type)[0];
        $old_filename = '/public/' . $type . '/' . $file->name . '.' . $file->fileExtension->extension;
        if ($uploaded_file->original_file) {
            $file_name = $uploaded_file->name . '.' . $uploaded_file->extension;
            if (Storage::disk('local')->exists($old_filename)) {
                Storage::disk('local')->delete($old_filename);
            }
            return Storage::putFileAs($uploaded_file->path, $uploaded_file->original_file, $file_name);
        }

        return [
            'status' => false, 'message' => 'No File to replace with'
        ];
    }

    protected function updateFileInfo($file, $file_updated)
    {
        $type = explode("/", $file->fileExtension->mime_type)[0];
        $file->name = $file_updated->name;
        $file->description = $file_updated->description;

        if ($file_updated->original_file) {
            $file->path = Storage::url($file_updated->store_path);
        } else {
            $file->path = Storage::url($type . '/' . $file_updated->name . '.' . $file->fileExtension->extension);
        }

        return $file->save();
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

    protected function process_file($model, $file_update)
    {
        if ($model->file_id == null || $model->file_id == '') {
            $file = $this->uploadFile($file_update);
            $model->file_id = $file->id;
            return $model->save();
        } else {
            $file = File::where('id', $model->file_id)->first();
            $this->renameFile($file, $file_update);
            $this->replaceFile($file, $file_update);
            return $this->updateFileInfo($file, $file_update);
        }
    }
}

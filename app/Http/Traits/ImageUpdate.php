<?php


namespace App\Http\Traits;


use App\File;
use Illuminate\Support\Str;

trait ImageUpdate
{
    use FileUpload;

    protected function imageRemoval($user_id, $model)
    {
        if ($model->file_id === null) {
            return [
                'status' => false,
                'message' => 'No image to remove'
            ];
        } else {
            $file = File::where(['id' => $model->file_id])->get()->first();
            $this->delete_file($file);

            $model->file_id = null;
            $model->save();
            return [
                'status' => true,
                'message' => 'Image removed successfully'
            ];
        }
    }

    protected function imageUpdate($model, $request, $field, $resourceName)
    {

        $upload_file = $this->validateFile($request, 'image_name', $field, $checks = ['image'], true);

        if (gettype($upload_file) === 'object') {
            $upload_file = $this->imageInfoUpdate($upload_file, $model, $field, $resourceName);
            $this->process_file($model, $upload_file);
        } elseif (gettype($upload_file) === 'array') {
            return $upload_file;
        }

        return ['status' => true, 'message' => "$resourceName Image Updated"];

    }

    private function imageInfoUpdate($upload_file, $model, $field, $resource)
    {
        $upload_file->name = Str::snake("$model->name-$field");
        $file_name = $upload_file->name . '.' . $upload_file->extension;
        $upload_file->description = "This is $resource $model->name $field";
        $upload_file->store_path = $upload_file->type . '/' . $file_name;

        return $upload_file;
    }
}

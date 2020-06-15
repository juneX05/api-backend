<?php

namespace App\Http\Controllers;

use App\File;
use App\Http\Traits\FileUpload;
use App\Http\Resources\FileResource;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{

    use FileUpload;

    /**
     * Fetch files by Type or Id
     * @param  string $type  File type
     * @param  integer $id   File Id
     * @return object        Files list, JSON
     */
    public function index()
    {
        return FileResource::collection(File::all());
    }

    public function userFiles($user_id){
        return FileResource::collection(File::where('user_id', $user_id)->get());
    }

    public function show($id){
        return FileResource::collection(File::where('id', $id)->get());
    }

    /**
     * Upload new file and store it
     * @param  Request $request Request with form data: filename and file info
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $uploaded_file = $this->validateFile($request);
        if (gettype($uploaded_file) === 'array') {
            return response()->json($uploaded_file['message'], 422);
        }
        $upload_status = $this->uploadFile($uploaded_file);
        return response()->json(['message' => 'File Uploaded Successfully']);
    }

    /**
     * Edit specific file details
     * @param integer $id File Id
     * @param Request $request Request with form data: filename
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $file = File::with('fileExtension')->where('id', $id)->first();
        $this->renameFile($file, $request);
        return response()->json($this->updateFileInfo($file, $request));
    }

    public function check(Request $request)
    {
        if ($file = $request->file('file')) {
            return $file->getMimeType();
        }
        return response()->json(['message' => 'No File uploaded']);
    }

    /**
     * Delete file from disk and database
     * @param integer $id File Id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $file = File::with('fileExtension')->findOrFail($id);

        return response()->json($this->delete_file($file));
    }

}

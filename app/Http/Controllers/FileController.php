<?php

namespace App\Http\Controllers;

use App\File;
use App\Http\Traits\FileUpload;
use App\Http\Resources\FileResource;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FileController extends Controller
{

    use FileUpload;

    /**
     * Fetch files by Type or Id
     * @return object        Files list, JSON
     */
    public function index()
    {
        abort_if(
            \Gate::denies('files_' . 'access'),
            Response::HTTP_FORBIDDEN,
            $this->messager('access', 'files')
        );
        return FileResource::collection(File::all());
    }

    public function userFiles($user_id)
    {
        abort_if(
            \Gate::denies('files_' . 'users'),
            Response::HTTP_FORBIDDEN,
            $this->messager("user files", 'files')
        );
        return FileResource::collection(File::where('user_id', $user_id)->get());
    }

    public function show(File $file)
    {
        abort_if(
            \Gate::denies('files_' . 'show'),
            Response::HTTP_FORBIDDEN,
            $this->messager("show", 'files')
        );
        return new FileResource($file);
    }

    /**
     * Upload new file and store it
     * @param  Request $request Request with form data: filename and file info
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        abort_if(
            \Gate::denies('files_' . 'store'),
            Response::HTTP_FORBIDDEN,
            $this->messager('store', 'files')
        );
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
    public function update(Request $request, File $file)
    {
        abort_if(
            \Gate::denies('files_' . 'update'),
            Response::HTTP_FORBIDDEN,
            $this->messager('update', 'files')
        );
//        $file = new FileResource($file);
        $file->fileExtension;
        $this->renameFile($file, $request);
        return response()->json($this->updateFileInfo($file, $request));
    }

    public function check(Request $request)
    {
        abort_if(
            \Gate::denies('files_' . 'check_mime'),
            Response::HTTP_FORBIDDEN,
            $this->messager('check_mime', 'files')
        );
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
    public function destroy(File $file)
    {
        abort_if(
            \Gate::denies('files_' . 'destroy'),
            Response::HTTP_FORBIDDEN,
            $this->messager('destroy', 'files')
        );
        $file = new FileResource($file);

        return response()->json($this->delete_file($file));
    }

}

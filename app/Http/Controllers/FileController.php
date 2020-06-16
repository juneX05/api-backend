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
            '403 Forbidden'
        );
        return FileResource::collection(File::all());
    }

    public function userFiles($user_id)
    {
        abort_if(
            \Gate::denies('files_' . 'users'),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );
        return FileResource::collection(File::where('user_id', $user_id)->get());
    }

    public function show($id)
    {
        abort_if(
            \Gate::denies('files_' . 'show'),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );
        return FileResource::collection(File::where('id', $id)->get());
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
            '403 Forbidden'
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
    public function update(Request $request, $id)
    {
        abort_if(
            \Gate::denies('files_' . 'update'),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );
        $file = File::with('fileExtension')->where('id', $id)->first();
        $this->renameFile($file, $request);
        return response()->json($this->updateFileInfo($file, $request));
    }

    public function check(Request $request)
    {
        abort_if(
            \Gate::denies('files_' . 'check_mime'),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
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
    public function destroy($id)
    {
        abort_if(
            \Gate::denies('files_' . 'destroy'),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );
        $file = File::with('fileExtension')->findOrFail($id);

        return response()->json($this->delete_file($file));
    }

}

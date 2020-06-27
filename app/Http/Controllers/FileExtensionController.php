<?php

namespace App\Http\Controllers;

use App\FileExtension;
use App\Http\Requests\FileExtension\FileExtensionStoreRequest;
use App\Http\Requests\FileExtension\FileExtensionUpdateRequest;
use App\Http\Resources\FileExtensionResource;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class FileExtensionController extends Controller
{
    private $file_extension;

    function __construct(FileExtension $file_extension)
    {
        $this->file_extension = $file_extension;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        abort_if(
            \Gate::denies('file_extensions' . '_' . 'access'),
            Response::HTTP_FORBIDDEN,
            $this->messager('access', 'file extensions')
        );
        return FileExtensionResource::collection(FileExtension::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FileExtensionStoreRequest $request)
    {
        abort_if(
            \Gate::denies('file_extensions' . '_' . 'store'),
            Response::HTTP_FORBIDDEN,
            $this->messager('store', 'file extensions')
        );
        $file_extension = $this->file_extension->create([
            'extension' => $request->extension,
            'mime_type' => $request->mime_type,
            'icon' => $request->icon
        ]);

        return response(['message' => 'FileExtension Created']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(FileExtension $fileExtension)
    {
        abort_if(
            \Gate::denies('file_extensions' . '_' . 'show'),
            Response::HTTP_FORBIDDEN,
            $this->messager('show', 'file extensions')
        );
        return new FileExtensionResource($fileExtension);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param FileExtension $file_extension
     * @return \Illuminate\Http\Response
     */
    public function update(FileExtensionUpdateRequest $request, FileExtension $fileExtension)
    {
        abort_if(
            \Gate::denies('file_extensions' . '_' . 'update'),
            Response::HTTP_FORBIDDEN,
            $this->messager('update', 'file extensions')
        );

        $fileExtension->update([
            'extension' => $request->extension,
            'mime_type' => $request->mime_type,
            'icon' => $request->icon
        ]);

        return response(['message' => 'FileExtensions Updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return int
     */
    public function destroy(FileExtension $fileExtension)
    {
        abort_if(
            \Gate::denies('file_extensions' . '_' . 'destroy'),
            Response::HTTP_FORBIDDEN,
            $this->messager('destroy', 'file extensions')
        );
        return $fileExtension->delete();
    }
}

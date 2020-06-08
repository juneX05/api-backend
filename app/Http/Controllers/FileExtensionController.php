<?php

namespace App\Http\Controllers;

use App\FileExtension;
use App\Http\Resources\FileExtensionResource;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
        return FileExtensionResource::collection(FileExtension::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'extension' => 'required|unique:file_extensions',
            'mime_type' => 'required|unique:file_extensions',
            'icon' => ''
        ];
        $params = [];

        $request->validate($rules, $params);

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
    public function show($id)
    {
        return FileExtensionResource::collection(FileExtension::where('id', $id)->get());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param FileExtension $file_extension
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $file_extension = FileExtension::findOrFail($id);
        $rules = [
            'extension' => ['required', Rule::unique('file_extensions')->ignore($file_extension->id)],
            'mime_type' => ['required', Rule::unique('file_extensions')->ignore($file_extension->id)],
        ];
        $params = [];

        $request->validate($rules, $params);

        $file_extension->update([
            'extension' => $request->extension,
            'mime_type' => $request->mime_type,
            'icon' => $request->icon
        ]);

        return $file_extension;

        return response(['message' => 'FileExtensions Updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return int
     */
    public function destroy($id)
    {
        return $this->file_extension->destroy($id);
    }
}

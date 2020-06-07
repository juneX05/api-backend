<?php

namespace App\Http\Controllers;

use App\FileType;
use App\Http\Resources\FileTypeResource;
use Illuminate\Http\Request;

class FileTypeController extends Controller
{
    private $file_type;

    function __construct(FileType $file_type)
    {
        $this->file_type = $file_type;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return FileTypeResource::collection(FileType::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
        ];
        $params = [];

        $request->validate($rules, $params);

        $file_type = $this->file_type->create($request->all());

        return response(['message' => 'FileType Created']);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function show($id)
    {
        return FileTypeResource::collection(FileType::where('id', $id)->get());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param FileType $file_type
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $file_type = FileType::findOrFail($id);
        $rules = [
            'name' => 'required',
        ];
        $params = [];

        $request->validate($rules, $params);

        $file_type->update($request->all());

        return response(['message' => 'FileType Updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return int
     */
    public function destroy($id)
    {
        return $this->file_type->destroy($id);
    }
}

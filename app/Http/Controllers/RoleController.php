<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoleResource;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    private $role;

    function __construct(Role $role)
    {
        $this->role = $role;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return RoleResource::collection(Role::all());
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
            'name' => 'required',
        ];
        $params = [];

        $request->validate($rules, $params);

        $role = $this->role->create([
            'name' => $request->name
        ]);

        if ($request->has('permissions')){
            $role->givePermissionTo(collect($request->permissions)->pluck('id')->toArray());
        }

        return response(['message' => 'Role Created']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
        $rules = [
            'name' => 'required',
        ];
        $params = [];

        $request->validate($rules, $params);

        $role->update([
            'name' => $request->name,
        ]);

        if ($request->has('permissions')){
            $role->syncPermissions(collect($request->permissions)->pluck('id')->toArray());
        }

        return response(['message' => 'Role Updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return int
     */
    public function destroy($id)
    {
        return $this->role->destroy($id);
    }
}

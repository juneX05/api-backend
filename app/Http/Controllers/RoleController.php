<?php

namespace App\Http\Controllers;

use App\Http\Requests\Role\RoleStoreRequest;
use App\Http\Requests\Role\RoleUpdateRequest;
use App\Http\Resources\RoleResource;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Response;

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
        abort_if(
            \Gate::denies('roles' . '_' . 'access'),
            Response::HTTP_FORBIDDEN,
            $this->messager('access', 'roles')
        );
        return RoleResource::collection(Role::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RoleStoreRequest $request)
    {
        abort_if(
            \Gate::denies('roles' . '_' . 'store'),
            Response::HTTP_FORBIDDEN,
            $this->messager('store', 'roles')
        );
        $role = $this->role->create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        if ($request->has('permissions')) {
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
    public function show(Role $role)
    {
        abort_if(
            \Gate::denies('roles' . '_' . 'show'),
            Response::HTTP_FORBIDDEN,
            $this->messager('show', 'roles')
        );
        return new RoleResource($role);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RoleUpdateRequest $request, Role $role)
    {
        abort_if(
            \Gate::denies('roles' . '_' . 'update'),
            Response::HTTP_FORBIDDEN,
            $this->messager('update', 'roles')
        );
        $role->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        if ($request->has('permissions')) {
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
    public function destroy(Role $role)
    {
        abort_if(
            \Gate::denies('roles' . '_' . 'destroy'),
            Response::HTTP_FORBIDDEN,
            $this->messager('destroy', 'roles')
        );
        return $role->delete();
    }
}

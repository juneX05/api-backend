<?php

namespace App\Http\Controllers;

use App\Http\Requests\Permission\PermissionUpdateRequest;
use App\Http\Requests\Permission\PermissionStoreRequest;
use App\Http\Resources\PermissionResource;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Symfony\Component\HttpFoundation\Response;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        abort_if(
            \Gate::denies('permissions' . '_' . 'access'),
            Response::HTTP_FORBIDDEN,
            $this->messager('access', 'permissions')
        );
        return PermissionResource::collection(Permission::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PermissionStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PermissionStoreRequest $request)
    {
        abort_if(
            \Gate::denies('permissions' . '_' . 'store'),
            Response::HTTP_FORBIDDEN,
            $this->messager('store', 'permissions')
        );
        $permission = $this->permission->create([
            'name' => $request->name,
            'description' => $request->description
        ]);

        return response()->json(['message' => 'Permission Created']);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function show(Permission $permission)
    {
        abort_if(
            \Gate::denies('permissions' . '_' . 'show'),
            Response::HTTP_FORBIDDEN,
            $this->messager('show', 'permissions')
        );
        return new PermissionResource($permission);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Permission $permission
     * @return \Illuminate\Http\Response
     */
    public function update(PermissionUpdateRequest $request, Permission $permission)
    {
        abort_if(
            \Gate::denies('permissions' . '_' . 'update'),
            Response::HTTP_FORBIDDEN,
            $this->messager('update', 'permissions')
        );
        $permission->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response(['message' => 'Permission Updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return int
     */
    public function destroy(Permission $permission)
    {
        abort_if(
            \Gate::denies('permissions' . '_' . 'destroy'),
            Response::HTTP_FORBIDDEN,
            $this->messager('destroy', 'permissions')
        );
        return $permission->delete();
    }
}

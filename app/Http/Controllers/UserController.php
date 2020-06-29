<?php

namespace App\Http\Controllers;

use App\Http\Traits\FileUpload;
use App\Http\Resources\UserResource;
use App\Http\Traits\ImageUpdate;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    use ImageUpdate;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        abort_if(
            \Gate::denies('users_' . 'access'),
            Response::HTTP_FORBIDDEN,
            $this->messager('access', 'users')
        );
        return UserResource::collection(User::where('id', '<>', 1)->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_if(
            \Gate::denies('users_' . 'store'),
            Response::HTTP_FORBIDDEN,
            $this->messager('store', 'users')
        );
        $upload_file = null;

        $request->merge([
            'role' => json_decode($request->role, true),
            'permissions' => json_decode($request->permissions, true),
        ]);

        $rules = [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed',
        ];
        $params = [];

        $request->validate($rules, $params);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        $this->manageRoles($user, $request->role, empty($request->role));
        $this->managePermissions($user, $request->permissions, empty($request->permissions));

        if ($request->file('profile_picture')) {
            $upload_status = $this->imageUpdate($user, $request, 'profile_picture', 'User');
            if (!$upload_status['status']) {
                return response()->json(['message' => $upload_status['message']], 422);
            }
        }

        return response(['message' => 'User Created']);
    }

    public function show(Request $request, User $user)
    {
        abort_if(
            \Gate::denies('users_' . 'show') || ($request->user()->id !== 1 && $user->id === 1),
            Response::HTTP_FORBIDDEN,
            $this->messager('show', 'users')
        );
        return new UserResource($user);
    }

    public function removeProfilePicture(Request $request)
    {
        abort_if(
            \Gate::denies('users_' . 'update') || ($request->user_id === 1 && $request->user()->id !== 1),
            Response::HTTP_FORBIDDEN,
            $this->messager('remove profile picture', 'users')
        );
        $user = User::findOrFail($request->user_id);
        $image_removal_status = $this->imageRemoval($request->user_id, $user);

        if (!$image_removal_status['status']) {
            return response()->json(['message' => $image_removal_status['message']], 422);
        }

        return response()->json(['message' => 'Profile Picture removed Successully', 'data' => $user]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        abort_if(
            \Gate::denies('users_' . 'update') || ($user->id === 1 && $request->user()->id !== 1),
            Response::HTTP_FORBIDDEN,
            $this->messager('update', 'users')
        );

        $rules = [
            'name' => 'required',
            'email' => 'required|email',
            Rule::unique('users')->where(function ($query) use ($request, $user) {
                return $query
                    ->whereEmail($request->email)
                    ->whereNotIn('id', [$user->id]);
            }),
        ];
        $params = [];

        $request->validate($rules, $params);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        $this->manageRoles($user, $request->role, empty($request->role));
        $this->managePermissions($user, $request->permissions, empty($request->permissions));

        if (!empty($request->password)) {
            $request->validate(['password' => 'confirmed|string'], $params);
            $user->update([
                'password' => bcrypt($request->password)
            ]);
        }

        if ($request->file('profile_picture')) {
            $upload_status = $this->imageUpdate($user, $request, 'profile_picture', 'User');
            if (!$upload_status['status']) {
                return response()->json(['message' => $upload_status['message']], 422);
            }
        }

        return response(['message' => 'User Updated']);
    }

    protected function manageRoles($user, $request_role, $status)
    {
        if ($status) return;
        $role = json_decode($request_role, true);
        $user->syncRoles($role['name']);
    }

    protected function managePermissions($user, $request_permissions, $status)
    {
        if ($status) return;
        $permissions = json_decode($request_permissions, true);
        $user->syncPermissions(collect($permissions)->pluck('id')->toArray());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return int
     */
    public function destroy(Request $request, User $user)
    {
        $is_superadmin_id = $user->id === 1;
        $is_self_delete = $request->user()->id === $user->id;
        $checker = $is_self_delete || $is_superadmin_id;
        abort_if(
            \Gate::denies('users_' . 'destroy') || $checker,
            Response::HTTP_FORBIDDEN,
            $this->messager('destroy', 'users')
        );
        return $user->delete();
    }
}

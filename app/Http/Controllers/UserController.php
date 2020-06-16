<?php

namespace App\Http\Controllers;

use App\File;
use App\Http\Traits\FileUpload;
use App\Http\Resources\UserResource;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    use FileUpload;

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
            '403 Forbidden'
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
            '403 Forbidden'
        );
        $upload_file = null;

        if ($request->file('profile_picture')) {
            $upload_file = $this->validateFile($request, 'image_name', 'profile_picture', $checks = ['image'], true);
        }

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

        if ($request->has('role') && $request->role !== null){
            $user->assignRole($request->role['name']);
        }

        if ($request->has('permissions') && $request['permissions'] !== null){
            $user->givePermissionTo(collect($request->permissions)->pluck('id')->toArray());
        }

        if (gettype($upload_file) === 'object') {
            $upload_file = $this->updateProfilePictureInfo($upload_file, $user);
            $file = $this->process_file($user, $upload_file);
        } else if (gettype($upload_file) === 'array') {
            return response()->json(['message' => $upload_file['message']], 422);
        }

        return response(['message' => 'User Created', 'user' => $user]);
    }

    protected function updateProfilePictureInfo($upload_file, $user)
    {
        $upload_file->name = Str::snake($user->name . '-profile_picture');
        $file_name = $upload_file->name . '.' . $upload_file->extension;
        $upload_file->description = 'This is user ' . $user->name . ' profile picture';
        $upload_file->store_path = $upload_file->type . '/' . $file_name;

        return $upload_file;
    }

    public function show(Request $request, $id)
    {
        abort_if(
            \Gate::denies('users_' . 'show') || ($request->user()->id !== 1 && $id === 1),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );
        return UserResource::collection(User::where('id', $id)->get());
    }

    public function removeProfilePicture(Request $request)
    {
        abort_if(
            \Gate::denies('users_' . 'update') || ($request->user_id === 1 && $request->user()->id !== 1),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );
        $user_model = User::findOrFail($request->user_id);

        if ($user_model->file_id === null) {
            return response()->json(false);
        } else {
            $file = File::where(['id' => $user_model->file_id])->get()->first();
            $this->delete_file($file);

            $user_model->file_id = null;
            $user_model->save();
            return UserResource::collection(User::where('id', $request->user_id)->get())->first();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        abort_if(
            \Gate::denies('users_' . 'update') || ($id === 1 && $request->user()->id !== 1),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );
        $user = User::findOrFail($id);
        $upload_file = null;

        if ($request->file('profile_picture')) {
            $upload_file = $this->validateFile($request, 'image_name', 'profile_picture', $checks = ['image'], true);
        }

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

        if (!empty($request->password)) {
            $request->validate(['password' => 'confirmed|string'], $params);
            $user->update([
                'password' => bcrypt($request->password)
            ]);
        }

        if ($request->has('role')) {
            $request->merge(['role' => json_decode($request->role, true)]);
            $user->syncRoles($request->role['name']);
        }

        if ($request->has('permissions')){
            $request->merge(['permissions' => json_decode($request->permissions,true)]);
            $user->syncPermissions(collect($request->permissions)->pluck('id')->toArray());
        }

        if (gettype($upload_file) === 'object') {
            $upload_file = $this->updateProfilePictureInfo($upload_file, $user);
            $file = $this->process_file($user, $upload_file);
        } elseif (gettype($upload_file) === 'array') {
            return response()->json($upload_file, 422);
        }

        return response(['message' => 'User Updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return int
     */
    public function destroy(Request $request, $id)
    {
        $is_superadmin_id = $id === 1;
        $is_self_delete = $request->user()->id === $id;
        $checker = $is_self_delete || $is_superadmin_id;
        abort_if(
            \Gate::denies('users_' . 'destroy') || $checker,
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );
        return User::destroy($id);
    }
}

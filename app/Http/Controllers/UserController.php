<?php

namespace App\Http\Controllers;

use App\Http\Traits\FileUpload;
use App\Http\Resources\UserResource;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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
        return UserResource::collection(User::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $upload_file = null;

        if ($request->file('profile_picture')){
            $upload_file = $this->validateFile($request,'image_name','profile_picture', $checks = ['image'],true);
        }

        $request->merge([
            'role' => json_decode($request->role,true),
            'permissions' => json_decode($request->permissions,true),
            'profile_picture' => json_decode($request->image)
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

        if ($upload_file !== null){
            $upload_file = $this->updateProfilePictureInfo($upload_file,$user);
            $file = $this->process_file($user,$upload_file);
        }

        return response(['message' => 'User Created', 'user' => $user]);
    }

    protected function updateProfilePictureInfo($upload_file,$user){
        $upload_file->name = Str::snake($user->name.'-profile_picture').'.'.$upload_file->extension;
        $upload_file->description ='This is user '.$user->name.' profile picture';
        $upload_file->store_path = $upload_file->type. '/' . $upload_file->name;

        return $upload_file;
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
        $upload_file = null;

        if ($request->file('profile_picture')){
            $upload_file = $this->validateFile($request,'image_name','profile_picture', $checks = ['image'],true);
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

        if ($request->password != null || $request->password !== ''){
            $request->validate(['password' => 'confirmed'], $params);
            $user->update([
                'password' => bcrypt($request->password)
            ]);
        }

        if ($request->has('role')){
            $request->merge(['role' => json_decode($request->role,true)]);
            $user->syncRoles( $request->role['name']);
        }

        if ($request->has('permissions')){
            $request->merge(['permissions' => json_decode($request->permissions,true)]);
            $user->syncPermissions(collect($request->permissions)->pluck('id')->toArray());
        }

        if ($upload_file !== null){
            $upload_file = $this->updateProfilePictureInfo($upload_file,$user);
            $file = $this->process_file($user,$upload_file);
        }

        return response(['message' => 'User Updated',$user]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return int
     */
    public function destroy($id)
    {
        return User::destroy($id);
    }
}

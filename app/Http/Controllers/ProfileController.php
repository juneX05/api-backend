<?php

namespace App\Http\Controllers;

use App\Http\Traits\ImageUpdate;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends Controller
{
    use ImageUpdate;

    public function removeProfilePicture(Request $request)
    {
        abort_if(
            \Gate::denies('profile_' . 'update'),
            Response::HTTP_FORBIDDEN,
            $this->messager('update', 'profile')
        );
        $user = User::findOrFail($request->user()->id);
        $image_removal_status = $this->imageRemoval($request->user_id, $user);

        if (!$image_removal_status['status']) {
            return response()->json(['message' => $image_removal_status['message']], 422);
        }

        return response()->json(['message' => 'Profile Picture removed Successully']);
    }

    public function updateProfilePicture(Request $request)
    {
        abort_if(
            \Gate::denies('profile_' . 'update'),
            Response::HTTP_FORBIDDEN,
            $this->messager('update', 'profile')
        );
        $user = User::findOrFail($request->user()->id);
        if ($request->file('profile_picture')) {
            $upload_status = $this->imageUpdate($user, $request, 'profile_picture', 'User');
            if (!$upload_status['status']) {
                return response()->json(['message' => $upload_status['message']], 422);
            }

            return response()->json(['message' => 'Profile Picture updated successfully']);
        }
        return response()->json(['message' => 'No profile Picture uploaded']);
    }

    public function updatePassword(Request $request)
    {
        abort_if(
            \Gate::denies('profile_' . 'update'),
            Response::HTTP_FORBIDDEN,
            $this->messager('update', 'profile')
        );

        $user = User::findOrFail($request->user()->id);

        if (!empty($request->password)) {
            $request->validate(['password' => 'confirmed|string']);
            $user->update([
                'password' => bcrypt($request->password)
            ]);

            return response()->json(['message' => 'Password Updated Successfully']);
        }

        return response()->json(['errors' => ['password' => 'Cannot update. Empty Password Provided']], 422);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function updateMyInfo(Request $request)
    {
        abort_if(
            \Gate::denies('profile_' . 'update'),
            Response::HTTP_FORBIDDEN,
            $this->messager('update', 'profile')
        );

        $user = User::findOrFail($request->user()->id);

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

        return response(['message' => 'Info Updated Successfully']);
    }
}

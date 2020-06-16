<?php

// @formatter:off

/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App {
    /**
     * App\User
     *
     * @property int $id
     * @property string $name
     * @property string $email
     * @property string $password
     * @property string|null $remember_token
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property int|null $file_id
     * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Client[] $clients
     * @property-read int|null $clients_count
     * @property-read \App\File|null $file
     * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
     * @property-read int|null $notifications_count
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Permission[] $permissions
     * @property-read int|null $permissions_count
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Role[] $roles
     * @property-read int|null $roles_count
     * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Token[] $tokens
     * @property-read int|null $tokens_count
     * @method static \Illuminate\Database\Eloquent\Builder|\App\User newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|\App\User newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|\App\User permission($permissions)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\User query()
     * @method static \Illuminate\Database\Eloquent\Builder|\App\User role($roles, $guard = null)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereEmail($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereFileId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePassword($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereRememberToken($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUpdatedAt($value)
     */
    class User extends \Eloquent
    {
    }
}

namespace App {
    /**
     * App\File
     *
     * @property int $id
     * @property string $name
     * @property string $file_extension_id
     * @property string|null $description
     * @property int $user_id
     * @property string $path
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read \App\FileExtension $fileExtension
     * @property-read \App\User $user
     * @method static \Illuminate\Database\Eloquent\Builder|\App\File newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|\App\File newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|\App\File query()
     * @method static \Illuminate\Database\Eloquent\Builder|\App\File whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\File whereDescription($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\File whereFileExtensionId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\File whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\File whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\File wherePath($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\File whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\File whereUserId($value)
     */
    class File extends \Eloquent
    {
    }
}


<?php

use Illuminate\Database\Seeder;

class ModelHasPermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $all_permissions = \App\Permission::all();
        $user = \App\User::findOrFail(2);

        $user_permissions = [];

        foreach ($all_permissions as $permission) {
            $user_permissions[] = $permission->id;
        }

        $user->syncPermissions($user_permissions);
    }
}

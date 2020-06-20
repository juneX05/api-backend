<?php

use Illuminate\Database\Seeder;
use App\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (Role::count() === 0) {
            $roles = [
                [
                    'id' => 1,
                    'name' => 'Admin',
                    'guard_name' => 'api',
                ],
            ];

            Role::insert($roles);

            $all_permissions = \App\Permission::all();
            $admin_role = \App\Role::findOrFail(1);

            $role_permissions = [];

            foreach ($all_permissions as $permission) {
                $user_permissions[] = $permission->id;
            }

            $admin_role->syncPermissions($role_permissions);
        } else {
            print "Roles table not empty. \n";
        }
    }
}

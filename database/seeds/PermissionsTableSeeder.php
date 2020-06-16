<?php

use App\Permission;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    private $counter = 1;

    public function run()
    {
        $permissions = [];

        if (Permission::count() === 0) {
            $modules = [
                'roles', 'permissions', 'users', 'file_extensions', 'files',
            ];
            $accessors = [
                'access', 'show', 'update', 'destroy', 'store',
            ];
            $other_permissions = [
                'files_users', 'files_check_mime',
            ];
            foreach ($modules as $module) {
                foreach ($accessors as $accessor) {
                    $name = $module . '_' . $accessor;
                    $permissions[] = $this->addPermission($name);
                }
            }

            foreach ($other_permissions as $str) {
                $name = $str;
                $permissions[] = $this->addPermission($name);
            }

            Permission::insert($permissions);
        } else {
            print "Permissions Table is not empty.\n";
        }
    }

    public function addPermission($name)
    {
        $permission = [];
        $permission['id'] = $this->counter;
        $permission['name'] = $name;
        $permission['guard_name'] = 'api';
        $this->counter++;
        return $permission;
    }
}

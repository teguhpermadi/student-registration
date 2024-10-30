<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use BezhanSalleh\FilamentShield\Support\Utils;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $rolesWithPermissions = '[{
                                    "name":"admin",
                                    "guard_name":"web",
                                    "permissions":
                                        ["view_academic::year",
                                        "view_any_academic::year",
                                        "create_academic::year",
                                        "update_academic::year",
                                        "restore_academic::year",
                                        "restore_any_academic::year",
                                        "replicate_academic::year",
                                        "reorder_academic::year",
                                        "delete_academic::year",
                                        "delete_any_academic::year",
                                        "force_delete_academic::year",
                                        "force_delete_any_academic::year",
                                        "view_role",
                                        "view_any_role",
                                        "create_role",
                                        "update_role",
                                        "delete_role",
                                        "delete_any_role",
                                        "view_student",
                                        "view_any_student",
                                        "create_student",
                                        "update_student",
                                        "restore_student",
                                        "restore_any_student",
                                        "replicate_student",
                                        "reorder_student",
                                        "delete_student",
                                        "delete_any_student",
                                        "force_delete_student",
                                        "force_delete_any_student",
                                        "view_user",
                                        "view_any_user",
                                        "create_user",
                                        "update_user",
                                        "restore_user",
                                        "restore_any_user",
                                        "replicate_user",
                                        "reorder_user",
                                        "delete_user",
                                        "delete_any_user",
                                        "force_delete_user",
                                        "force_delete_any_user"
                                ]},
                                {
                                    "name":"student",
                                    "guard_name":"web",
                                    "permissions":[
                                        "page_StudentRegistration"
                                    ]}]';
        $directPermissions = '[]';

        static::makeRolesWithPermissions($rolesWithPermissions);
        static::makeDirectPermissions($directPermissions);

        $this->command->info('Shield Seeding Completed.');
    }

    protected static function makeRolesWithPermissions(string $rolesWithPermissions): void
    {
        if (! blank($rolePlusPermissions = json_decode($rolesWithPermissions, true))) {
            /** @var Model $roleModel */
            $roleModel = Utils::getRoleModel();
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($rolePlusPermissions as $rolePlusPermission) {
                $role = $roleModel::firstOrCreate([
                    'name' => $rolePlusPermission['name'],
                    'guard_name' => $rolePlusPermission['guard_name'],
                ]);

                if (! blank($rolePlusPermission['permissions'])) {
                    $permissionModels = collect($rolePlusPermission['permissions'])
                        ->map(fn ($permission) => $permissionModel::firstOrCreate([
                            'name' => $permission,
                            'guard_name' => $rolePlusPermission['guard_name'],
                        ]))
                        ->all();

                    $role->syncPermissions($permissionModels);
                }
            }
        }
    }

    public static function makeDirectPermissions(string $directPermissions): void
    {
        if (! blank($permissions = json_decode($directPermissions, true))) {
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($permissions as $permission) {
                if ($permissionModel::whereName($permission)->doesntExist()) {
                    $permissionModel::create([
                        'name' => $permission['name'],
                        'guard_name' => $permission['guard_name'],
                    ]);
                }
            }
        }
    }
}

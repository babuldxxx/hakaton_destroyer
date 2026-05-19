<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Сброс кеша разрешений
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Разрешения
        $permissions = [
            'view dashboard',
            'manage artists',
            'manage tracks',
            'view finances',
            'create payouts',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Роли
        $label = Role::firstOrCreate(['name' => 'label']);
        $label->syncPermissions([
            'view dashboard',
            'manage artists',
            'manage tracks',
            'view finances',
            'create payouts',
        ]);

        $artist = Role::firstOrCreate(['name' => 'artist']);
        $artist->syncPermissions(['view dashboard']);
    }
}

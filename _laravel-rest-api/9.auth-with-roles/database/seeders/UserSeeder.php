<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear permisos para listar, ver, crear, actualizar y eliminar usuarios
        $user_list = Permission::create(['name'=>'users.list']);
        $user_view = Permission::create(['name'=>'users.view']);
        $user_create = Permission::create(['name'=>'users.create']);
        $user_update = Permission::create(['name'=>'users.update']);
        $user_delete = Permission::create(['name'=>'users.delete']);

        // Crear el rol de administrador y asignarle todos los permisos
        $admin_role = Role::create(['name'=> 'admin']);
        $admin_role->givePermissionTo([
          $user_create,
          $user_view,
          $user_list,
          $user_update,
          $user_delete,
        ]);

        // Crear un usuario administrador y asignarle el rol de administrador y los permisos correspondientes
        $admin = User::create([
          'name' => 'Admin',
          'email' => 'admin@admin.com',
          'password' => bcrypt('password'),
        ]);
        $admin->assignRole($admin_role);
        $admin->givePermissionTo([
          $user_create,
          $user_view,
          $user_list,
          $user_update,
          $user_delete,
        ]);

        // Crear rol de usuario regular y asignarle el permiso para listar usuarios
        $user_role = Role::create(['name'=> 'user']);
        $user_role->givePermissionTo([
          $user_list,
        ]);

        // Crear un usuario regular y asignarle el rol de usuario regular con el permiso correspondiente
        $user = User::create([
          'name' => 'user',
          'email' => 'user@user.com',
          'password' => bcrypt('password'),
        ]);
        $user->assignRole($user_role);
        $user->givePermissionTo([
          $user_list,
        ]);

        $user_role->givePermissionTo([
          $user_list,
        ]);
    }
}

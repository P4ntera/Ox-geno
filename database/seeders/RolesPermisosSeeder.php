<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesPermisosSeeder extends Seeder
{
    public function run(): void
    {
        $role1 = Role::create(['name' => 'Administrativo']);
        $role2 = Role::create(['name' => 'Médico']);
        $role3 = Role::create(['name' => 'Enfermera']);
        $role4 = Role::create(['name' => 'Soporte']);

        // PERMISOS PARA ADMINISTRATIVO

        $permission = Permission::create(['name' => 'pacientes.index'])->syncRoles($role1);
        $permission = Permission::create(['name' => 'ordenes.index'])->syncRoles($role1);
        $permission = Permission::create(['name' => 'consumo.index'])->syncRoles($role1);
        $permission = Permission::create(['name' => 'centrosalud.index'])->syncRoles($role1);
        $permission = Permission::create(['name' => 'usuarios.index'])->syncRoles($role1);
        $permission = Permission::create(['name' => 'reportes.index'])->syncRoles($role1);
        $permission = Permission::create(['name' => 'ars.index'])->syncRoles($role1);
        $permission = Permission::create(['name' => 'habitaciones.index'])->syncRoles($role1);

        // PERMISOS PARA MEDICO

        $permission = Permission::create(['name' => 'ordenes.index'])->syncRoles($role2);
        $permission = Permission::create(['name' => 'pacientes.index'])->syncRoles($role2);

        // PERMISOS PARA ENFERMERA

        $permission = Permission::create(['name' => 'consumo.index'])->syncRoles($role3);

        // PERMISOS PARA SOPORTE

        $permission = Permission::create(['name' => 'pacientes.index'])->syncRoles($role4);
        $permission = Permission::create(['name' => 'ordenes.index'])->syncRoles($role4);
        $permission = Permission::create(['name' => 'consumo.index'])->syncRoles($role4);
        $permission = Permission::create(['name' => 'centrosalud.index'])->syncRoles($role4);
        $permission = Permission::create(['name' => 'reportes.index'])->syncRoles($role4);
        $permission = Permission::create(['name' => 'ars.index'])->syncRoles($role4);
        $permission = Permission::create(['name' => 'habitaciones.index'])->syncRoles($role4);

    }
}

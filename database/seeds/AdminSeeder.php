<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    protected $roles = [
        'encoder' => [
            'users_view',
            'users_create',
            'users_update',
            'users_document_view',
            'users_document_update',
            'voter_view',
            'social_service_view',
            'social_service_create',
            'social_service_update',
            'social_service_delete'
        ],
    ];
    public function run()
    {
        foreach($this->roles as $role => $permissions){
            $_role = Role::findOrCreate($role,'web');
            foreach($permissions as $permission){
                $_role->givePermissionTo($permission);
            }
        }

        Role::where('name', 'admin')->get()->each(function($role){
            Permission::get()->each(function($permission) use ($role){
                $role->givePermissionTo($permission->name);
            });
        });

        
    }
}

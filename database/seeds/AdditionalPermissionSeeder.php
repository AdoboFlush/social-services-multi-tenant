<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdditionalPermissionSeeder extends Seeder
{

    protected $permissions=[
        'id_template_view',
        'id_template_add',
        'id_template_edit',
        'events_view',
        'events_add',
        'events_edit',
        'voter_restore',
        'voter_delete',
        'assistance_events_view',
        'assistance_events_create',
        'assistance_events_update',
        'assistance_events_scan',
        'poll_management_view',
        'poll_management_create',
        'poll_management_update',
        'assistance_queue_view',
        'assistance_queue_create',
        'assistance_queue_update',
        'assistance_queue_reset',
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach($this->permissions as $permission){
            Permission::findOrCreate($permission,'web');
        }
        $permissions = $this->permissions;
        Role::where('name', 'admin')->get()->each(function($role) use ($permissions) {
            collect($permissions)->each(function($permission) use ($role){
                $role->givePermissionTo($permission);
            });
        });

    }

}

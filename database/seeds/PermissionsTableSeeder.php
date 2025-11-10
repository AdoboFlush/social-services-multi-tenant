<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsTableSeeder extends Seeder
{
    protected $permissions=[
        'admin_view',
        'general_settings_view',
        'activity_logs_view',
        'message_template_view',
        'language_view',
        'language_create',
        'language_update',
        'language_delete',
        'database_backup_view',
        'roles_and_permissions_view',
        'rates_view',
        'users_view',
        'users_create',
        'users_update',
        'users_delete',
        'users_document_view',
        'users_document_update',
        'users_document_delete',
        'settings_view',
        'reports_view',
        'staffs_view',
        'tickets_view',
        'tickets_create',
        'tickets_update',
        'tag_view',
        'tag_create',
        'tag_update',
        'tag_delete',
        'voter_view',
        'voter_create',
        'voter_update',
        'social_service_view',
        'social_service_create',
        'social_service_update',
        'social_service_delete',
        'social_service_status_update',
        'welcome_message_view',
		'reports_view',
		'reports_export',
		'admin_dashboard_view',
		'id_system_view',
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
    }
}

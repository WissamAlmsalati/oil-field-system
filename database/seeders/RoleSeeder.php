<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $adminRole = Role::create(['name' => 'Admin', 'guard_name' => 'web']);
        $managerRole = Role::create(['name' => 'Manager', 'guard_name' => 'web']);
        $userRole = Role::create(['name' => 'User', 'guard_name' => 'web']);

        // Create permissions
        $permissions = [
            // User management permissions
            'view users',
            'create users',
            'edit users',
            'delete users',
            'manage user roles',
            'reset user passwords',
            'view user activity logs',
            
            // Client management permissions
            'view clients',
            'create clients',
            'edit clients',
            'delete clients',
            
            // Sub-agreement permissions
            'view sub-agreements',
            'create sub-agreements',
            'edit sub-agreements',
            'delete sub-agreements',
            
            // Call-out job permissions
            'view call-out-jobs',
            'create call-out-jobs',
            'edit call-out-jobs',
            'delete call-out-jobs',
            'update call-out-job status',
            
            // Daily service log permissions
            'view daily-logs',
            'create daily-logs',
            'edit daily-logs',
            'delete daily-logs',
            'generate daily-log reports',
            'download daily-log files',
            
            // Service ticket permissions
            'view service-tickets',
            'create service-tickets',
            'edit service-tickets',
            'delete service-tickets',
            'generate service-tickets',
            
            // Ticket issue permissions
            'view ticket-issues',
            'create ticket-issues',
            'edit ticket-issues',
            'delete ticket-issues',
            
            // Document permissions
            'view documents',
            'create documents',
            'edit documents',
            'delete documents',
            'upload documents',
            'download documents',
            'bulk upload documents',
            'bulk delete documents',
            
            // Dashboard permissions
            'view dashboard',
            'view statistics',
            'view recent activities',
        ];

        // Create all permissions
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        // Assign all permissions to Admin role
        $adminRole->givePermissionTo(Permission::all());

        // Assign limited permissions to Manager role
        $managerRole->givePermissionTo([
            'view users',
            'view clients',
            'create clients',
            'edit clients',
            'view sub-agreements',
            'create sub-agreements',
            'edit sub-agreements',
            'view call-out-jobs',
            'create call-out-jobs',
            'edit call-out-jobs',
            'update call-out-job status',
            'view daily-logs',
            'create daily-logs',
            'edit daily-logs',
            'generate daily-log reports',
            'download daily-log files',
            'view service-tickets',
            'create service-tickets',
            'edit service-tickets',
            'generate service-tickets',
            'view ticket-issues',
            'create ticket-issues',
            'edit ticket-issues',
            'view documents',
            'create documents',
            'edit documents',
            'upload documents',
            'download documents',
            'bulk upload documents',
            'view dashboard',
            'view statistics',
            'view recent activities',
        ]);

        // Assign basic permissions to User role
        $userRole->givePermissionTo([
            'view clients',
            'view sub-agreements',
            'view call-out-jobs',
            'view daily-logs',
            'view service-tickets',
            'view ticket-issues',
            'view documents',
            'download documents',
            'view dashboard',
        ]);

        $this->command->info('Roles and permissions created successfully!');
    }
} 
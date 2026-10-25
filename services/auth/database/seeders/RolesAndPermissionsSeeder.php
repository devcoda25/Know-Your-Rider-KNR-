<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

/**
 * Roles and permissions are data, not code: add or change rows here (or through
 * the super admin app later) instead of editing controllers.
 */
class RolesAndPermissionsSeeder extends Seeder
{
    /** permission key => description */
    private const PERMISSIONS = [
        'rider.view_public' => 'See the public rider view',
        'rider.view_full' => 'See the full verified rider profile',
        'rider.view_own_stage' => 'See riders of own stage',
        'rider.create' => 'Register a rider',
        'rider.update' => 'Edit a rider',
        'rider.suspend' => 'Suspend or reinstate a rider',
        'rider.transfer' => 'Transfer a rider between stages',
        'stage.create' => 'Create a stage',
        'stage.approve' => 'Approve or reject a stage',
        'stage.suspend' => 'Suspend a stage',
        'stage.manage_officials' => 'Add or remove stage officials',
        'incident.create' => 'Report an incident',
        'incident.view' => 'View incidents',
        'bike.flag_stolen' => 'Flag a bike as stolen',
        'insurance.update' => 'Update insurance records',
        'permit.update' => 'Update permit records',
        'user.invite' => 'Invite users into an organization',
        'user.approve' => 'Approve users',
        'user.suspend' => 'Suspend users',
        'report.view_area' => 'View area compliance reports',
        'report.view_aggregate' => 'View aggregate (non-personal) reports',
        'audit.view' => 'View the scan and search audit log',
        'subscription.manage' => 'Manage plans and subscriptions',
        'role.manage' => 'Manage roles and permissions',
    ];

    /** role key => [name, permission keys] */
    private const ROLES = [
        'super_admin' => ['Super admin', ['*']],
        'org_admin' => ['Organization admin', [
            'rider.view_full', 'user.invite', 'user.approve', 'user.suspend', 'report.view_area', 'audit.view',
        ]],
        'stage_head' => ['Stage head', [
            'rider.view_public', 'rider.view_own_stage', 'rider.view_full', 'rider.create', 'rider.update',
            'rider.suspend', 'rider.transfer', 'stage.manage_officials', 'incident.view',
        ]],
        'stage_official' => ['Stage official', [
            'rider.view_public', 'rider.view_own_stage', 'rider.create', 'incident.view',
        ]],
        'traffic_officer' => ['Traffic police officer', [
            'rider.view_public', 'rider.view_full', 'incident.create', 'incident.view', 'bike.flag_stolen',
        ]],
        'lc_official' => ['Local council official', [
            'rider.view_public', 'incident.create', 'incident.view',
        ]],
        'municipal_officer' => ['KCCA / municipal officer', [
            'rider.view_public', 'stage.approve', 'stage.suspend', 'report.view_area',
        ]],
        'insurer_agent' => ['Insurer agent', ['rider.view_public', 'insurance.update', 'incident.view']],
        'licensing_officer' => ['Licensing body officer', ['rider.view_public', 'permit.update']],
        'ura_officer' => ['URA officer', ['report.view_aggregate']],
        'rider' => ['Rider', ['rider.view_public']],
    ];

    public function run(): void
    {
        $permissions = [];
        foreach (self::PERMISSIONS as $key => $description) {
            $permissions[$key] = Permission::updateOrCreate(['key' => $key], ['description' => $description]);
        }

        foreach (self::ROLES as $key => [$name, $keys]) {
            $role = Role::updateOrCreate(['key' => $key], ['name' => $name]);

            $ids = $keys === ['*']
                ? collect($permissions)->pluck('id')
                : collect($keys)->map(fn ($k) => $permissions[$k]->id);

            $role->permissions()->sync($ids->all());
        }
    }
}

<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Route;
use App\Models\Report;

class ProductionSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(/* User $user, Role $role */)
    {
        ////////////////////////////////////
        // Load the routes
        Route::loadLaravelRoutes();
        // Look for and delete route named 'do-not-load' if it exist.
        // That route is used to test the Authorization middleware and should not be loaded automatically.
        $routeToDelete = Route::where('name', 'do-not-load')->get()->first();
        if ($routeToDelete) Route::destroy($routeToDelete->id);


        ////////////////////////////////////
        // Create basic set of permissions
        $permGuestOnly = Permission::create([
            'name'          => 'guest-only',
            'display_name'  => 'Guest only access',
            'description'   => 'Only guest users can access these.',
            'enabled'       => true,
        ]);
        $permOpenToAll = Permission::create([
            'name'          => 'open-to-all',
            'display_name'  => 'Open to all',
            'description'   => 'Everyone can access these, even unauthenticated (guest) users.',
            'enabled'       => true,
        ]);
        $permBasicAuthenticated = Permission::create([
            'name'          => 'basic-authenticated',
            'display_name'  => 'Basic authenticated',
            'description'   => 'Basic permission after being authenticated.',
            'enabled'       => true,
        ]);
        // Create a few permissions for the admin|security section
        $permManageUsers = Permission::create([
            'name'          => 'manage-users',
            'display_name'  => 'Manage users',
            'description'   => 'Allows a user to manage the site users.',
            'enabled'       => true,
        ]);
        $permManageRoles = Permission::create([
            'name'          => 'manage-roles',
            'display_name'  => 'Manage roles',
            'description'   => 'Allows a user to manage the site roles.',
            'enabled'       => true,
        ]);
        $permManagePermissions = Permission::create([
            'name'          => 'manage-permissions',
            'display_name'  => 'Manage permissions',
            'description'   => 'Allows a user to manage the site permissions.',
            'enabled'       => true,
        ]);
        $permManageRoutes = Permission::create([
            'name'          => 'manage-routes',
            'display_name'  => 'Manage routes',
            'description'   => 'Allows a user to Manage the site routes.',
            'enabled'       => true,
        ]);
        // Create a few permissions for the admin|audit section
        $permAuditLogView = Permission::create([
            'name'          => 'audit-log-view',
            'display_name'  => 'View audit log',
            'description'   => 'Allows a user to view the audit log.',
            'enabled'       => true,
        ]);
        $permAuditReplay = Permission::create([
            'name'          => 'audit-log-replay',
            'display_name'  => 'Replay audit log item',
            'description'   => 'Allows a user to replay items from the audit log.',
            'enabled'       => true,
        ]);
        $permAuditPurge = Permission::create([
            'name'          => 'audit-log-purge',
            'display_name'  => 'Purge audit log',
            'description'   => 'Allows a user to purge old items from the audit log.',
            'enabled'       => true,
        ]);
        // Create permission for managing CUCM Clusters
        $permManageClusters = Permission::create([
            'name'          => 'manage-clusters',
            'display_name'  => 'Manage clusters',
            'description'   => 'Allows a user to manage CUCM cluster settings.',
            'enabled'       => true,
        ]);
        // Create permission for erasing IP Phone certificates
        $permEraseCertificates = Permission::create([
            'name'          => 'erase-certificates',
            'display_name'  => 'Erase Certificates',
            'description'   => 'Allows a user to eraser IP Phone security certificates.',
            'enabled'       => true,
        ]);
        // Create permission for running SQL queries
        $permRunSqlQuery    = Permission::create([
            'name'          => 'sql-run',
            'display_name'  => 'Run SQL Queries',
            'description'   => 'Allows a user to run pre-existing queries against the active CUCM cluster.',
            'enabled'       => true,
        ]);
        // Create permission for running new SQL queries
        $permAddSqlQuery    = Permission::create([
            'name'          => 'sql-add',
            'display_name'  => 'Add SQL Queries',
            'description'   => 'Allows a user to run new SQL queries against the active CUCM cluster.',
            'enabled'       => true,
        ]);
        // Create permission for deleting SQL queries
        $permDeleteSqlQuery    = Permission::create([
            'name'          => 'sql-delete',
            'display_name'  => 'Delete SQL Queries',
            'description'   => 'Allows a user to delete existing SQL queries.',
            'enabled'       => true,
        ]);
        // Create permission for deleting SQL queries
        $permAutoDialer    = Permission::create([
            'name'          => 'autodialer',
            'display_name'  => 'AutoDialer',
            'description'   => 'Allows a user to place calls using AutoDialer.',
            'enabled'       => true,
        ]);
        // Create permission for generating IOS configs
        $permJfsUser    = Permission::create([
            'name'          => 'jfs-user',
            'display_name'  => 'JFS R/O',
            'description'   => 'Allows a user to view JFS features.',
            'enabled'       => true,
        ]);
        // Create permission for generating IOS configs
        $permJfsAdmin    = Permission::create([
            'name'          => 'jfs-admin',
            'display_name'  => 'Manage all things JFS',
            'description'   => 'Allows a user to manage the JFS features.',
            'enabled'       => true,
        ]);
        // Create permission for managing Duo Users
        $permManageDuoUsers    = Permission::create([
            'name'          => 'duo-users-admin',
            'display_name'  => 'Manage Duo Users',
            'description'   => 'Allows a user to manage the Duo user accounts.',
            'enabled'       => true,
        ]);

        ////////////////////////////////////
        // Associate open-to-all permission to some routes
        $routeBackslash = Route::where('name', 'backslash')->get()->first();
        $routeBackslash->permission()->associate($permOpenToAll);
        $routeBackslash->save();
        $routeHome = Route::where('name', 'home')->get()->first();
        $routeHome->permission()->associate($permOpenToAll);
        $routeHome->save();
        $routeFaust = Route::where('name', 'faust')->get()->first();
        $routeFaust->permission()->associate($permOpenToAll);
        $routeFaust->save();
        // Associate basic-authenticated permission to the dashboard route
        $routeDashboard = Route::where('name', 'dashboard')->get()->first();
        $routeDashboard->permission()->associate($permBasicAuthenticated);
        $routeDashboard->save();
        // Associate the audit-log permissions
        $routeAuditView = Route::where('name', 'admin.audit.index')->get()->first();
        $routeAuditView->permission()->associate($permAuditLogView);
        $routeAuditView->save();
        $routeAuditPurge = Route::where('name', 'admin.audit.purge')->get()->first();
        $routeAuditPurge->permission()->associate($permAuditPurge);
        $routeAuditPurge->save();
        $routeAuditReplay = Route::where('name', 'admin.audit.replay')->get()->first();
        $routeAuditReplay->permission()->associate($permAuditReplay);
        $routeAuditReplay->save();
        // Associate manage-permission permissions to routes starting with 'admin.permissions.'
        $managePermRoutes = Route::where('name', 'like', "admin.permissions.%")->get()->all();
        foreach ($managePermRoutes as $route)
        {
            $route->permission()->associate($permManagePermissions);
            $route->save();
        }
        // Associate manage-roles permissions to routes starting with 'admin.roles.'
        $manageRoleRoutes = Route::where('name', 'like', "admin.roles.%")->get()->all();
        foreach ($manageRoleRoutes as $route)
        {
            $route->permission()->associate($permManageRoles);
            $route->save();
        }
        // Associate manage-routes permissions to routes starting with 'admin.routes.'
        $manageRouteRoutes = Route::where('name', 'like', "admin.routes.%")->get()->all();
        foreach ($manageRouteRoutes as $route)
        {
            $route->permission()->associate($permManageRoutes);
            $route->save();
        }
        // Associate manage-users permissions to routes starting with 'admin.users.'
        $manageUserRoutes = Route::where('name', 'like', "admin.users.%")->get()->all();
        foreach ($manageUserRoutes as $route)
        {
            $route->permission()->associate($permManageUsers);
            $route->save();
        }


        ////////////////////////////////////
        // Create role: admins
        $roleAdmins = Role::create([
            "name"          => "admins",
            "display_name"  => "Administrators",
            "description"   => "Administrators have no restrictions",
            "enabled"       => true
            ]);
        // Create role: users
        // Assign permission basic-authenticated
        $roleUsers = Role::create([
            "name"          => "users",
            "display_name"  => "Users",
            "description"   => "All authenticated users",
            "enabled"       => true
            ]);
        $roleUsers->perms()->attach($permBasicAuthenticated->id);

        // Create role: user-manager
        // Assign permission manage-users
        $roleUserManagers = Role::create([
            "name"          => "user-managers",
            "display_name"  => "User managers",
            "description"   => "User managers are granted all permissions to the Admin|Users section.",
            "enabled"       => true
        ]);
        $roleUserManagers->perms()->attach($permManageUsers->id);

        // Create role: role-manager
        // Assign permission: manage-roles
        $roleRoleManagers = Role::create([
            "name"          => "role-managers",
            "display_name"  => "Role managers",
            "description"   => "Role managers are granted all permissions to the Admin|Roles section.",
            "enabled"       => true
        ]);
        $roleRoleManagers->perms()->attach($permManageRoles->id);

        // Create role: audit-viewers
        // Assign permission: audit-log-view
        $roleAuditViewers = Role::create([
            "name"          => "audit-viewers",
            "display_name"  => "Audit viewers",
            "description"   => "Users allowed to view the audit log.",
            "enabled"       => true
        ]);
        $roleAuditViewers->perms()->attach($permAuditLogView->id);

        // Create role: audit-replayers
        // Assign permission: audit-log-replay
        $roleAuditReplayers = Role::create([
            "name"          => "audit-replayers",
            "display_name"  => "Audit replayers",
            "description"   => "Users allowed to replay items from the audit log.",
            "enabled"       => true
        ]);
        $roleAuditReplayers->perms()->attach($permAuditReplay->id);

        // Create role: audit-purgers
        // Assign permission: audit-log-purge
        $roleAuditPurgers = Role::create([
            "name"          => "audit-purgers",
            "display_name"  => "Audit purgers",
            "description"   => "Users allowed to purge old items from the audit log.",
            "enabled"       => true
        ]);
        $roleAuditPurgers->perms()->attach($permAuditPurge->id);

        // Create role: cluster-managers
        // Assign permission: permManageClusters
        $roleClusterManagers = Role::create([
            "name"          => "cluster-managers",
            "display_name"  => "Cluster managers",
            "description"   => "Cluster managers are granted all permissions to the Admin|Clusters section.",
            "enabled"       => true
        ]);
        $roleClusterManagers->perms()->attach($permManageClusters->id);

        // Create role: cert-erasers
        // Assign permission: permManageClusters
        $roleCertErasers = Role::create([
            "name"          => "cert-erasers",
            "display_name"  => "Certificate erasers",
            "description"   => "Certificate erasers are granted permissions to erase IP Phone security certificates.",
            "enabled"       => true
        ]);
        $roleCertErasers->perms()->attach($permEraseCertificates->id);

        // Create role: sql-runner
        // Assign permission: permRunSqlQuery
        $roleSqlRunner = Role::create([
            "name"          => "sql-runner",
            "display_name"  => "SQL Runner",
            "description"   => "SQL Runners can run existing queries against the active CUCM cluster.",
            "enabled"       => true
        ]);
        $roleSqlRunner->perms()->attach($permRunSqlQuery->id);

        // Create role: sql-creator
        // Assign permission: permRunSqlQuery
        // Assign permission: permAddSqlQuery
        $roleSqlCreator = Role::create([
            "name"          => "sql-creator",
            "display_name"  => "SQL Creator",
            "description"   => "SQL Creators can run new and existing queries against the active CUCM cluster.",
            "enabled"       => true
        ]);
        $roleSqlCreator->perms()->attach($permRunSqlQuery->id);
        $roleSqlCreator->perms()->attach($permAddSqlQuery->id);

        // Create role: sql-admin
        // Assign permission: permRunSqlQuery
        // Assign permission: permAddSqlQuery
        // Assign permission: permDeleteSqlQuery
        $roleSqlAdmin = Role::create([
            "name"          => "sql-admin",
            "display_name"  => "SQL Admin",
            "description"   => "SQL Admins can run new and existing queries against the active CUCM cluster and delete queries.",
            "enabled"       => true
        ]);
        $roleSqlAdmin->perms()->attach($permRunSqlQuery->id);
        $roleSqlAdmin->perms()->attach($permAddSqlQuery->id);
        $roleSqlAdmin->perms()->attach($permDeleteSqlQuery->id);

        // Create role: autodialer
        // Assign permission: permAutoDialer
        $roleAutoDialer = Role::create([
            "name"          => "autodialer",
            "display_name"  => "AutoDialer",
            "description"   => "AutoDialer can use the AutoDialer system.",
            "enabled"       => true
        ]);
        $roleAutoDialer->perms()->attach($permAutoDialer->id);

        // Create role: ios-config-user
        // Assign permission: permAutoDialer
        $roleJfsUser = Role::create([
            "name"          => "jfs-user",
            "display_name"  => "JFS User",
            "description"   => "JFS Users can view JFS features.",
            "enabled"       => true
        ]);
        $roleJfsUser->perms()->attach($permJfsUser->id);

        // Create role: ios-config-admin
        // Assign permission: permAutoDialer
        $roleJfsAdmin = Role::create([
            "name"          => "jfs-admin",
            "display_name"  => "JFS Admin",
            "description"   => "JFS Admins manage the JFS features.",
            "enabled"       => true
        ]);
        $roleJfsAdmin->perms()->attach($permJfsAdmin->id);

        // Create role: duo-user-admin
        // Assign permission: permManageDuoUsers
        $roleDuoUserAdmin = Role::create([
            "name"          => "duo-user-admin",
            "display_name"  => "Duo User Admin",
            "description"   => "Duo User Admins manage the Duo User accounts.",
            "enabled"       => true
        ]);
        $roleDuoUserAdmin->perms()->attach($permManageDuoUsers->id);


        ////////////////////////////////////
        // Create user: root
        // Assign membership to role admins, membership to role users is
        // automatic.
        $userRoot = User::create([
            "first_name"    => "Root",
            "last_name"     => "SuperUser",
            "username"      => "root",
            "email"         => "root@email.com",
            "password"      => "Password1",
            "auth_type"     => "internal",
            "enabled"       => true
            ]);
        $userRoot->roles()->attach($roleAdmins->id);


    }
}

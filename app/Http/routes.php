<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

// Authentication routes...
Route::get( 'auth/login',               ['as' => 'login',                   'uses' => 'Auth\AuthController@getLogin']);
Route::post('auth/login',               ['as' => 'loginPost',               'uses' => 'Auth\AuthController@postLogin']);
Route::get( 'auth/logout',              ['as' => 'logout',                  'uses' => 'Auth\AuthController@getLogout']);
// Registration routes...
Route::get( 'auth/register',            ['as' => 'register',                'uses' => 'Auth\AuthController@getRegister']);
Route::post('auth/register',            ['as' => 'registerPost',            'uses' => 'Auth\AuthController@postRegister']);
// Password reset link request routes...
Route::get( 'password/email',           ['as' => 'recover_password',        'uses' => 'Auth\PasswordController@getEmail']);
Route::post('password/email',           ['as' => 'recover_passwordPost',    'uses' => 'Auth\PasswordController@postEmail']);
// Password reset routes...
Route::get( 'password/reset/{token}',   ['as' => 'reset_password',          'uses' => 'Auth\PasswordController@getReset']);
Route::post('password/reset',           ['as' => 'reset_passwordPost',      'uses' => 'Auth\PasswordController@postReset']);
// Registration terms
Route::get( 'faust',                    ['as' => 'faust',                   'uses' => function(){
                                                                                            return view('faust');
                                                                                      }]);

// Application routes...
Route::get( '/',    ['as' => 'backslash',   'uses' => 'HomeController@index']);
Route::get( 'home', ['as' => 'home',        'uses' => 'HomeController@index']);

// Routes in this group must be authorized.
Route::group(['middleware' => 'authorize'], function () {
    // Application routes...
    Route::get( 'dashboard', ['as' => 'dashboard', 'uses' => 'DashboardController@index']);

    // Site administration section
    Route::group(['prefix' => 'admin'], function () {
        // User routes
        Route::post(  'users/enableSelected',          ['as' => 'admin.users.enable-selected',  'uses' => 'UsersController@enableSelected']);
        Route::post(  'users/disableSelected',         ['as' => 'admin.users.disable-selected', 'uses' => 'UsersController@disableSelected']);
        Route::get(   'users/search',                  ['as' => 'admin.users.search',           'uses' => 'UsersController@searchByName']);
        Route::get(   'users/list',                    ['as' => 'admin.users.list',             'uses' => 'UsersController@listByPage']);
        Route::post(  'users/getInfo',                 ['as' => 'admin.users.get-info',         'uses' => 'UsersController@getInfo']);
        Route::post(  'users',                         ['as' => 'admin.users.store',            'uses' => 'UsersController@store']);
        Route::get(   'users',                         ['as' => 'admin.users.index',            'uses' => 'UsersController@index']);
        Route::get(   'users/create',                  ['as' => 'admin.users.create',           'uses' => 'UsersController@create']);
        Route::get(   'users/{userId}',                ['as' => 'admin.users.show',             'uses' => 'UsersController@show']);
        Route::patch( 'users/{userId}',                ['as' => 'admin.users.patch',            'uses' => 'UsersController@update']);
        Route::put(   'users/{userId}',                ['as' => 'admin.users.update',           'uses' => 'UsersController@update']);
        Route::delete('users/{userId}',                ['as' => 'admin.users.destroy',          'uses' => 'UsersController@destroy']);
        Route::get(   'users/{userId}/edit',           ['as' => 'admin.users.edit',             'uses' => 'UsersController@edit']);
        Route::get(   'users/{userId}/confirm-delete', ['as' => 'admin.users.confirm-delete',   'uses' => 'UsersController@getModalDelete']);
        Route::get(   'users/{userId}/delete',         ['as' => 'admin.users.delete',           'uses' => 'UsersController@destroy']);
        Route::get(   'users/{userId}/enable',         ['as' => 'admin.users.enable',           'uses' => 'UsersController@enable']);
        Route::get(   'users/{userId}/disable',        ['as' => 'admin.users.disable',          'uses' => 'UsersController@disable']);
        Route::get(   'users/{userId}/replayEdit',      ['as' => 'admin.users.replay-edit',      'uses' => 'UsersController@replayEdit']);
        // Role routes
        Route::post(  'roles/enableSelected',          ['as' => 'admin.roles.enable-selected',  'uses' => 'RolesController@enableSelected']);
        Route::post(  'roles/disableSelected',         ['as' => 'admin.roles.disable-selected', 'uses' => 'RolesController@disableSelected']);
        Route::get(   'roles/search',                  ['as' => 'admin.roles.search',           'uses' => 'RolesController@searchByName']);
        Route::post(  'roles/getInfo',                 ['as' => 'admin.roles.get-info',         'uses' => 'RolesController@getInfo']);
        Route::post(  'roles',                         ['as' => 'admin.roles.store',            'uses' => 'RolesController@store']);
        Route::get(   'roles',                         ['as' => 'admin.roles.index',            'uses' => 'RolesController@index']);
        Route::get(   'roles/create',                  ['as' => 'admin.roles.create',           'uses' => 'RolesController@create']);
        Route::get(   'roles/{roleId}',                ['as' => 'admin.roles.show',             'uses' => 'RolesController@show']);
        Route::patch( 'roles/{roleId}',                ['as' => 'admin.roles.patch',            'uses' => 'RolesController@update']);
        Route::put(   'roles/{roleId}',                ['as' => 'admin.roles.update',           'uses' => 'RolesController@update']);
        Route::delete('roles/{roleId}',                ['as' => 'admin.roles.destroy',          'uses' => 'RolesController@destroy']);
        Route::get(   'roles/{roleId}/edit',           ['as' => 'admin.roles.edit',             'uses' => 'RolesController@edit']);
        Route::get(   'roles/{roleId}/confirm-delete', ['as' => 'admin.roles.confirm-delete',   'uses' => 'RolesController@getModalDelete']);
        Route::get(   'roles/{roleId}/delete',         ['as' => 'admin.roles.delete',           'uses' => 'RolesController@destroy']);
        Route::get(   'roles/{roleId}/enable',         ['as' => 'admin.roles.enable',           'uses' => 'RolesController@enable']);
        Route::get(   'roles/{roleId}/disable',        ['as' => 'admin.roles.disable',          'uses' => 'RolesController@disable']);
        // Permission routes
        Route::get(   'permissions/generate',                      ['as' => 'admin.permissions.generate',         'uses' => 'PermissionsController@generate']);
        Route::post(  'permissions/enableSelected',                ['as' => 'admin.permissions.enable-selected',  'uses' => 'PermissionsController@enableSelected']);
        Route::post(  'permissions/disableSelected',               ['as' => 'admin.permissions.disable-selected', 'uses' => 'PermissionsController@disableSelected']);
        Route::post(  'permissions',                               ['as' => 'admin.permissions.store',            'uses' => 'PermissionsController@store']);
        Route::get(   'permissions',                               ['as' => 'admin.permissions.index',            'uses' => 'PermissionsController@index']);
        Route::get(   'permissions/create',                        ['as' => 'admin.permissions.create',           'uses' => 'PermissionsController@create']);
        Route::get(   'permissions/{permissionId}',                ['as' => 'admin.permissions.show',             'uses' => 'PermissionsController@show']);
        Route::patch( 'permissions/{permissionId}',                ['as' => 'admin.permissions.patch',            'uses' => 'PermissionsController@update']);
        Route::put(   'permissions/{permissionId}',                ['as' => 'admin.permissions.update',           'uses' => 'PermissionsController@update']);
        Route::delete('permissions/{permissionId}',                ['as' => 'admin.permissions.destroy',          'uses' => 'PermissionsController@destroy']);
        Route::get(   'permissions/{permissionId}/edit',           ['as' => 'admin.permissions.edit',             'uses' => 'PermissionsController@edit']);
        Route::get(   'permissions/{permissionId}/confirm-delete', ['as' => 'admin.permissions.confirm-delete',   'uses' => 'PermissionsController@getModalDelete']);
        Route::get(   'permissions/{permissionId}/delete',         ['as' => 'admin.permissions.delete',           'uses' => 'PermissionsController@destroy']);
        Route::get(   'permissions/{permissionId}/enable',         ['as' => 'admin.permissions.enable',           'uses' => 'PermissionsController@enable']);
        Route::get(   'permissions/{permissionId}/disable',        ['as' => 'admin.permissions.disable',          'uses' => 'PermissionsController@disable']);
        // Route routes
        Route::get(   'routes/load',                     ['as' => 'admin.routes.load',             'uses' => 'RoutesController@load']);
        Route::post(  'routes/enableSelected',           ['as' => 'admin.routes.enable-selected',  'uses' => 'RoutesController@enableSelected']);
        Route::post(  'routes/disableSelected',          ['as' => 'admin.routes.disable-selected', 'uses' => 'RoutesController@disableSelected']);
        Route::post(  'routes/savePerms',                ['as' => 'admin.routes.save-perms',       'uses' => 'RoutesController@savePerms']);
        Route::get(   'routes/search',                   ['as' => 'admin.routes.search',           'uses' => 'RoutesController@searchByName']);
        Route::post(  'routes/getInfo',                  ['as' => 'admin.routes.get-info',         'uses' => 'RoutesController@getInfo']);
        Route::post(  'routes',                          ['as' => 'admin.routes.store',            'uses' => 'RoutesController@store']);
        Route::get(   'routes',                          ['as' => 'admin.routes.index',            'uses' => 'RoutesController@index']);
        Route::get(   'routes/create',                   ['as' => 'admin.routes.create',           'uses' => 'RoutesController@create']);
        Route::get(   'routes/{routeId}',                ['as' => 'admin.routes.show',             'uses' => 'RoutesController@show']);
        Route::patch( 'routes/{routeId}',                ['as' => 'admin.routes.patch',            'uses' => 'RoutesController@update']);
        Route::put(   'routes/{routeId}',                ['as' => 'admin.routes.update',           'uses' => 'RoutesController@update']);
        Route::delete('routes/{routeId}',                ['as' => 'admin.routes.destroy',          'uses' => 'RoutesController@destroy']);
        Route::get(   'routes/{routeId}/edit',           ['as' => 'admin.routes.edit',             'uses' => 'RoutesController@edit']);
        Route::get(   'routes/{routeId}/confirm-delete', ['as' => 'admin.routes.confirm-delete',   'uses' => 'RoutesController@getModalDelete']);
        Route::get(   'routes/{routeId}/delete',         ['as' => 'admin.routes.delete',           'uses' => 'RoutesController@destroy']);
        Route::get(   'routes/{routeId}/enable',         ['as' => 'admin.routes.enable',           'uses' => 'RoutesController@enable']);
        Route::get(   'routes/{routeId}/disable',        ['as' => 'admin.routes.disable',          'uses' => 'RoutesController@disable']);
        // Audit routes
        Route::get( 'audit',                           ['as' => 'admin.audit.index',             'uses' => 'AuditsController@index']);
        Route::get( 'audit/purge',                     ['as' => 'admin.audit.purge',             'uses' => 'AuditsController@purge']);
        Route::get( 'audit/{userId}/replay',           ['as' => 'admin.audit.replay',            'uses' => 'AuditsController@replay']);
        Route::get( 'audit/{userId}/show',             ['as' => 'admin.audit.show',              'uses' => 'AuditsController@show']);

    }); // End of ADMIN group

    // Template tests and demo routes
    Route::get('flashsuccess',  ['as' => 'flash_test_success',  'uses' => 'TestController@flash_success']);
    Route::get('flashinfo',     ['as' => 'flash_test_info',     'uses' => 'TestController@flash_info']);
    Route::get('flashwarning',  ['as' => 'flash_test_warning',  'uses' => 'TestController@flash_warning']);
    Route::get('flasherror',    ['as' => 'flash_test_error',    'uses' => 'TestController@flash_error']);

    // Authorization tests
    Route::group(['prefix' => 'acl-test'], function () {
        Route::get('do-not-load',           ['as' => 'do-not-load',         'uses' => 'TestController@acl_test_do_not_load']);
        Route::get('no-perm',               ['as' => 'no-perm',             'uses' => 'TestController@acl_test_no_perm']);
        Route::get('basic-authenticated',   ['as' => 'basic-authenticated', 'uses' => 'TestController@acl_test_basic_authenticated']);
        Route::get('guest-only',            ['as' => 'guest-only',          'uses' => 'TestController@acl_test_guest_only']);
        Route::get('open-to-all',           ['as' => 'open-to-all',         'uses' => 'TestController@acl_test_open_to_all']);
        Route::get('admins',                ['as' => 'admins',              'uses' => 'TestController@acl_test_admins']);
        Route::get('power-users',           ['as' => 'power-users',         'uses' => 'TestController@acl_test_power_users']);
    }); // End of ACL-TEST group

    /*
     * End User Profile Routes
     */
    Route::get(   'users/{userId}',                ['as' => 'users.show',             'uses' => 'UsersController@euShow']);
    Route::get(   'users/{userId}/edit',           ['as' => 'users.edit',             'uses' => 'UsersController@euEdit']);
    Route::patch( 'users/{userId}',                ['as' => 'users.update',            'uses' => 'UsersController@euUpdate']);


    /*
     * UC Insight Routes
     */

    // Cluster Routes
    Route::get('cluster/{clusterId}/confirm-delete', ['as' => 'cluster.confirm-delete', 'uses' => 'ClusterController@getModalDelete']);
    Route::get('cluster/{clusterId}/delete', ['as' => 'cluster.delete', 'uses' => 'ClusterController@destroy']);
    Route::resource('cluster', 'ClusterController');

    //ITL Routes
    Route::get('itl', ['as'   => 'itl.index', 'uses' => 'EraserController@itlIndex']);
    Route::post('itl',['as'   => 'itl.store', 'uses' => 'EraserController@itlStore']);

    // CTL Routes
    Route::get('ctl', ['as'   => 'ctl.index', 'uses' => 'EraserController@ctlIndex']);
    Route::post('ctl',['as'   => 'ctl.store', 'uses' => 'EraserController@ctlStore']);

    // Eraser Bulk Routes
    Route::get('bulk',['as'   => 'eraser.bulk.index', 'uses' => 'EraserController@bulkIndex']);
    Route::get('bulk/create',['as'   =>  'eraser.bulk.create', 'uses' => 'EraserController@bulkCreate']);
    Route::get('bulk/{bulk}',['as'   =>  'eraser.bulk.show', 'uses' => 'EraserController@bulkShow']);
    Route::post('bulk',['as'   => 'eraser.bulk.store', 'uses' => 'EraserController@bulkStore']);

    // SQL Routes
    Route::get('sql/history', ['as' => 'sql.history', 'uses' => 'SqlController@history']);
    Route::get('sql/favorites', ['as' => 'sql.favorites', 'uses' => 'SqlController@favorites']);
    Route::resource('sql','SqlController', ['except' => ['destroy', 'edit']]);
    Route::resource('favorite', 'FavoriteController', ['only' => ['store', 'destroy']]);

    // AutoDialer Routes
    Route::get('autodialer/bulk', ['as'   => 'autodialer.bulk.index', 'uses' => 'AutoDialerController@bulkIndex']);
    Route::post('autodialer/bulk', ['as'   => 'autodialer.bulk.store', 'uses' => 'AutoDialerController@bulkStore']);
    Route::get('autodialer', ['as'   => 'autodialer.index', 'uses' => 'AutoDialerController@index']);
    Route::post('autodialer',['as'   => 'autodialer.store', 'uses' => 'AutoDialerController@placeCall']);

    // CDR Routes
    Route::get('cdrs', ['as' => 'cdrs.index', 'uses' => 'CdrController@index']);

    // Show Phone
    Route::get('phone/{phone}', ['as'   => 'phone.show', 'uses' => 'DeviceController@phoneIndex']);

    // Reporting Routes
    Route::group(['prefix' => 'reports/'], function() {

        // Services Route
        Route::get('services', ['as' => 'service.store', 'uses' => 'ReportingController@servicesIndex']);

        // Device Registration Route
        Route::get('registration', ['as' => 'registration.store', 'uses' => 'ReportingController@registrationIndex']);

        // Firmware Report Route
        Route::get('firmware', ['as' => 'firmware.index', 'uses' => 'ReportingController@firmwareIndex']);
        Route::post('firmware', ['as' => 'firmware.store', 'uses' => 'ReportingController@firmwareStore']);

    });

    // Vue.js API Routes
    Route::group(['prefix' => 'api/v1'], function(){

        // Eraser API Routes
        Route::group(['prefix' => 'eraser'], function() {

            Route::get('itls', function () {
                return App\Models\Device::with(['latestItlEraser'])->get()->toArray();

            });
            Route::get('ctls', function () {
                return App\Models\Device::with(['latestCtlEraser'])->get()->toArray();

            });
            Route::get('bulk', function () {
                return App\Models\Bulk::orderBy('updated_at','desc')->get()->toArray();
            });
            Route::get('bulk/{bulk}', function ($bulk) {
                return App\Models\Bulk::find($bulk->id)->toArray();
            });
        });

    });

    /*
     * JFS Routes
     */
    Route::group(['prefix' => 'jfs'], function () {

        /*
         * Dashboard Routes
         */
        Route::get('sites', [
            'as' => 'jfs.sites.index',
            'uses' => 'Jfs\SiteController@index'
        ]);
        Route::get('sites/task', [
            'as' => 'jfs.site.task.update',
            'uses' => 'Jfs\SiteController@update'
        ]);
        Route::get('sites/{site}', [
            'as' => 'jfs.sites.show',
            'uses' => 'Jfs\SiteController@show'
        ]);
        //End Dashboard Routes

        /*
         * Config Generator Routes
         */
        Route::get('configs',[
            'as' => 'jfs.configs',
            'uses' => 'Jfs\ConfigController@index'
        ]);
        Route::get('configs/file',[
            'as' => 'jfs.configs.create',
            'uses' => 'Jfs\ConfigController@create'
        ]);
        Route::post('configs/', [
            'as' => 'jfs.configs.store',
            'uses' => 'Jfs\ConfigController@store',
        ]);
        Route::post('configs/{filename}',[
            'as' => 'jfs.configs.loadfile',
            'uses' => 'Jfs\ConfigController@loadfile'
        ]);
        Route::get('configs/confirm-delete', [
            'as' => 'jfs.configs.confirm-delete',
            'uses' => 'Jfs\ConfigController@getModalDelete'
        ]);
        Route::get('configs/delete', [
            'as' => 'jfs.configs.delete',
            'uses' => 'Jfs\ConfigController@destroy'
        ]);
        Route::get('configs/download', [
            'as' => 'jfs.configs.download',
            'uses' => 'Jfs\ConfigController@download'
        ]);
        //End Config Generator Routes

    }); // End JFS Routes

    //Duo Routes
    Route::get('duo', ['as' => 'duo.index', 'uses' => 'DuoController@index']);
    Route::get('duo/user/{id}', ['as' => 'duo.show', 'uses' => 'DuoController@showUser']);
    Route::put('duo/user/{id}', ['as' => 'duo.store', 'uses' => 'DuoController@updateUser']);
    Route::put('duo/user/groups/{id}', ['as' => 'duo.user.groups.update', 'uses' => 'DuoController@updateUserGroups']);
    Route::get('duo/user/{id}/groups/report/', ['as' => 'duo.user.group.report', 'uses' => 'DuoController@onDemandGroupReport']);
    Route::get('duo/user/registration/report/', ['as' => 'duo.user.registration.report', 'uses' => 'DuoController@registeredUsersReport']);
    Route::get('duo/user/{id}/sync', ['as' => 'duo.user.sync', 'uses' => 'DuoController@onDemandUserSync']);
    Route::get('duo/user/{id}/migrate', ['as' => 'duo.user.migrate', 'uses' => 'DuoController@migrateUser']);
    Route::get('duo/auth/logs', ['as' => 'duo.auth.logs', 'uses' => 'DuoController@logs']);
    Route::get('duo/auth/logs/data', ['as' => 'duo.auth.logs.data', 'uses' => 'DuoController@logData']);
    Route::get('duo/auth/logs/export', ['as' => 'duo.auth.logs.export', 'uses' => 'DuoController@exportLogData']);

}); // end of AUTHORIZE group


//Dingo API
$api = app('Dingo\Api\Routing\Router');

$api->version('v1', ['middleware' => 'api.auth'], function ($api) {
    $api->post('phone-reset/', 'App\Api\Controllers\PhoneController@resetPhone');
});
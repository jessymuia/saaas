<?php

declare(strict_types=1);

use Stancl\Tenancy\Bootstrappers\CacheTenancyBootstrapper;
use Stancl\Tenancy\Bootstrappers\FilesystemTenancyBootstrapper;
use Stancl\Tenancy\Bootstrappers\QueueTenancyBootstrapper;

return [

    /*
    |--------------------------------------------------------------------------
    | Tenant Model
    |--------------------------------------------------------------------------
    */
    'tenant_model' => \App\Models\SaasClient::class,
     'domain_model' => \App\Models\Domain::class, 

    /*
    |--------------------------------------------------------------------------
    | Home URL
    |--------------------------------------------------------------------------
    */
    'home_url' => '/',

    /*
    |--------------------------------------------------------------------------
    | Central Domains
    |--------------------------------------------------------------------------
    | Requests on these domains will NOT be identified as tenant requests.
    */
    'central_domains' => array_filter(array_merge(
        ['127.0.0.1', 'localhost', 'test.localhost'],
        explode(',', env('CENTRAL_DOMAINS', ''))
    )),

    /*
    |--------------------------------------------------------------------------
    | Tenancy Bootstrappers
    |--------------------------------------------------------------------------
    | Single-database mode: no DatabaseTenancyBootstrapper.
    */
    'bootstrappers' => [
        CacheTenancyBootstrapper::class,
        FilesystemTenancyBootstrapper::class,
        QueueTenancyBootstrapper::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Database
    |--------------------------------------------------------------------------
    */
    'database' => [
        'central_connection' => env('DB_CONNECTION', 'pgsql'),
        'throw_if_not_found' => false,
        'template_tenant_connection' => null,
        'prefix' => '',
        'suffix' => '',
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    */
    'cache' => [
        'tag_base' => 'tenant',
    ],

    /*
    |--------------------------------------------------------------------------
    | Filesystem
    |--------------------------------------------------------------------------
    */
    'filesystem' => [
        'suffix_base' => 'tenant',

        /*
         | Prevent the asset() helper from being rewritten to /tenantXXX/...
         | URLs. Filament's JS/CSS live in public/ (not tenant storage), so
         | they must always be served from the normal /js/, /css/ paths.
         | Tenant-specific file uploads still use Storage::disk('public')
         | which is correctly scoped by the root_override below.
         */
        'asset_helper_tenancy' => false,

        'disks' => [
            'local',
            'public',
        ],
        'root_override' => [
            'local'  => '%storage_path%/app/',
            'public' => '%storage_path%/app/public/',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Features
    |--------------------------------------------------------------------------
    */
    'features' => [],

    /*
    |--------------------------------------------------------------------------
    | Routes
    |--------------------------------------------------------------------------
    */
    'routes' => true,

    'route_namespace' => 'App\\Http\\Controllers',

    /*
    |--------------------------------------------------------------------------
    | Migration Parameters
    |--------------------------------------------------------------------------
    */
    'migration_parameters' => [
        '--force'    => true,
        '--path'     => database_path('migrations'),
        '--realpath' => true,
    ],

    'seeder_parameters' => [
        '--force' => true,
    ],
'middleware' => [
    'exempt_uris' => [
        'css/filament',
        'js/filament', 
        'fonts/filament',
        'livewire',
    ],
],
];
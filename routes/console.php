<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// override various commands to ensure safety of data
Artisan::command('migrate:fresh', function () {
    $this->comment('This command is disabled. Please use "php artisan migrate:fresh --seed" instead.');
    // check if in production or staging or app_url is not localhost
    if (getenv('APP_ENV') === 'production' || getenv('APP_ENV') === 'staging'
        || App::environment('production') || App::environment('staging')
        || getenv('APP_URL') !== 'http://localhost') {
        $this->error('This command is disabled');
    }
})->purpose('Override the default migrate:fresh command');

// migrate refresh
Artisan::command('migrate:refresh', function () {
    $this->comment('This command is disabled. Please use "php artisan migrate:refresh --seed" instead.');
    // check if in production or staging or app_url is not localhost
    if (getenv('APP_ENV') === 'production' || getenv('APP_ENV') === 'staging'
        || App::environment('production') || App::environment('staging')
        || getenv('APP_URL') !== 'http://localhost') {
        $this->error('This command is disabled');
    }
})->purpose('Override the default migrate:refresh command');


// migrate rollback
Artisan::command('migrate:rollback', function () {
    $this->comment('This command is disabled. Please use "php artisan migrate:rollback --step=1" instead.');
    // check if in production or staging or app_url is not localhost
    if (getenv('APP_ENV') === 'production' || getenv('APP_ENV') === 'staging'
        || App::environment('production') || App::environment('staging')
        || getenv('APP_URL') !== 'http://localhost') {
        $this->error('This command is disabled');
    }
})->purpose('Override the default migrate:rollback command');

// migrate reset
Artisan::command('migrate:reset', function () {
    $this->comment('This command is disabled. Please use "php artisan migrate:reset --force" instead.');
    // check if in production or staging or app_url is not localhost
    if (getenv('APP_ENV') === 'production' || getenv('APP_ENV') === 'staging'
        || App::environment('production') || App::environment('staging')
        || getenv('APP_URL') !== 'http://localhost') {
        $this->error('This command is disabled');
    }
})->purpose('Override the default migrate:reset command');

// queue flush php artisan queue:flush

Artisan::command('queue:flush', function (){
    // check if in production or staging or app_url is not localhost
    if (getenv('APP_ENV') === 'production' || getenv('APP_ENV') === 'staging'
        || App::environment('production') || App::environment('staging')
        || getenv('APP_URL') !== 'http://localhost') {
        $this->error('This command is disabled');
    }
});

// queue reset
Artisan::command('queue:reset', function (){
    // check if in production or staging or app_url is not localhost
    if (getenv('APP_ENV') === 'production' || getenv('APP_ENV') === 'staging'
        || App::environment('production') || App::environment('staging')
        || getenv('APP_URL') !== 'http://localhost') {
        $this->error('This command is disabled');
    }
});

Artisan::command('migrate:reset --force', function (){
    // check if in production or staging or app_url is not localhost
    if (getenv('APP_ENV') === 'production' || getenv('APP_ENV') === 'staging'
        || App::environment('production') || App::environment('staging')
        || getenv('APP_URL') !== 'http://localhost') {
        $this->error('This command is disabled');
    }
});


//// make migration
//Artisan::command('make:migration', function (){
//    // check if in production or staging or app_url is not localhost
//    if (getenv('APP_ENV') === 'production' || getenv('APP_ENV') === 'staging'
//        || App::environment('production') || App::environment('staging')
//        || getenv('APP_URL') !== 'http://localhost') {
//        $this->error('This command is disabled');
//    }
//
//});

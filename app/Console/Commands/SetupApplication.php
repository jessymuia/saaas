<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetupApplication extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:setup-application';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to setup the application after installation.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->call('migrate:fresh');
        $this->call('app:setup-admin-user');
        $this->call('app:init-default-references');
        $this->call('db:seed');
//        $this->call('passport:install');
        $this->call('storage:link');
        $this->call('optimize');
        $this->call('config:cache');
        $this->call('route:cache');
        $this->call('view:cache');
    }
}

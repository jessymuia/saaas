<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ChangeUserPasswordCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:change-user-password-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change user password for user with defined email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // get the inserted user email
        $email = $this->ask('Enter user email');
        // get user with given email
        $user = User::query()->where('email', $email)->first();
        // if user with given email exists
        if ($user) {
            // get the new password
            $password = $this->secret('Enter new password');
            // update user password
            $user->password = bcrypt($password);
            $user->save();
            $this->info('Password changed successfully');
        } else {
            $this->error('User with given email does not exist');
        }
    }
}

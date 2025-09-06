<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ShowUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:show';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show all users in the database with their credentials';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::all(['id', 'name', 'email']);

        if ($users->isEmpty()) {
            $this->info('No users found in the database.');
            $this->info('Run: php artisan db:seed to create test users');
            return;
        }

        $this->info('Available Users:');
        $this->info('================');
        $this->info('Default Password: password');
        $this->info('');

        foreach ($users as $user) {
            $this->line("ID: {$user->id}");
            $this->line("Name: {$user->name}");
            $this->line("Email: {$user->email}");
            $this->line("Password: password");
            $this->line('---');
        }

        $this->info('');
        $this->info('You can use any of these credentials to login!');
    }
}

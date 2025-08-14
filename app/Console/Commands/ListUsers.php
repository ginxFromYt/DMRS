<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class ListUsers extends Command
{
    protected $signature = 'list:users';
    protected $description = 'List all users and their roles';

    public function handle()
    {
        $users = User::with('roles')->get();

        $this->info('Users and their roles:');

        foreach ($users as $user) {
            $roles = $user->roles->pluck('name')->join(', ');
            $this->line($user->email . ' - Roles: ' . ($roles ?: 'No roles'));
        }
    }
}

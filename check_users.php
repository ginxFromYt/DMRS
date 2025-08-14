<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "=== DMRS System Users and Roles ===\n\n";

$users = User::with('roles')->get();

foreach ($users as $user) {
    $roles = $user->roles->pluck('name')->implode(', ');
    echo "{$user->name} ({$user->email})\n";
    echo "  Roles: {$roles}\n\n";
}

echo "Total users: " . $users->count() . "\n";

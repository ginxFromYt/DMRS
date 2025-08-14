<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "=== Users Created ===\n\n";

$users = User::with('roles')->get();

foreach ($users as $user) {
    $roles = $user->roles->pluck('name')->implode(', ');
    echo "Name: {$user->name}\n";
    echo "Email: {$user->email}\n";
    echo "Roles: {$roles}\n";
    echo "---\n";
}

echo "\nTotal users: " . $users->count() . "\n";

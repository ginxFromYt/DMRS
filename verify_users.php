<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== DMRS User Roles and Users Verification ===\n\n";

// Check all roles
echo "📋 Available Roles:\n";
$roles = Role::all();
foreach ($roles as $role) {
    echo "   - {$role->name}\n";
}

echo "\n👥 Current Users by Role:\n";

// Check users for each role
foreach ($roles as $role) {
    echo "\n🔹 {$role->name}:\n";
    $users = User::whereHas('roles', function ($query) use ($role) {
        $query->where('name', $role->name);
    })->get();

    if ($users->count() > 0) {
        foreach ($users as $user) {
            echo "   - {$user->full_name} ({$user->email}) - {$user->designation}\n";
        }
    } else {
        echo "   - No users assigned to this role\n";
    }
}

// Check if Document Releaser exists
echo "\n🔍 Checking for Document Releaser user...\n";
$documentReleaserRole = Role::where('name', 'Document Releaser')->first();
if ($documentReleaserRole) {
    $documentReleaserUser = User::whereHas('roles', function ($query) {
        $query->where('name', 'Document Releaser');
    })->first();

    if (!$documentReleaserUser) {
        echo "❌ Document Releaser user not found. Creating Jasmin...\n";

        $jasmin = User::create([
            'first_name' => 'Jasmin',
            'middle_name' => 'Mae',
            'last_name' => 'Santos',
            'designation' => 'Document Releaser',
            'email' => 'jasmin.releaser@dmrs.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        $jasmin->roles()->attach($documentReleaserRole->id);
        echo "✅ Created Jasmin Mae Santos as Document Releaser\n";
    } else {
        echo "✅ Document Releaser user exists: {$documentReleaserUser->full_name}\n";
    }
} else {
    echo "❌ Document Releaser role not found!\n";
}

echo "\n=== Verification Complete ===\n";

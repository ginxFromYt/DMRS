<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all roles
        $superAdminRole = Role::where('name', 'SuperAdmin')->first();
        $recordsOfficerRole = Role::where('name', 'Records Officer')->first();
        $approvingAuthorityRole = Role::where('name', 'Approving Authority')->first();
        $documentReleaserRole = Role::where('name', 'Document Releaser')->first();
        $employeeRole = Role::where('name', 'Employee')->first();
        $administratorRole = Role::where('name', 'Administrator')->first();
        $eventManagerRole = Role::where('name', 'Event Manager')->first();

        // Create SuperAdmin user
        $superAdmin = User::create([
            'first_name' => 'Super',
            'middle_name' => null,
            'last_name' => 'Administrator',
            'designation' => 'System Administrator',
            'email' => 'admin@dmrs.com',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
        ]);
        $superAdmin->roles()->attach($superAdminRole->id);

        // Create Records Officer user
        $recordsOfficer = User::create([
            'first_name' => 'Maria',
            'middle_name' => 'Santos',
            'last_name' => 'Cruz',
            'designation' => 'Records Officer',
            'email' => 'records.officer@dmrs.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        $recordsOfficer->roles()->attach($recordsOfficerRole->id);

        // Create CEO/Approving Authority user (Sir Odz) - reviews and approves documents
        $ceoAuthority = User::create([
            'first_name' => 'Odz',
            'middle_name' => '',
            'last_name' => 'CEO',
            'designation' => 'Chief Executive Officer',
            'email' => 'ceo.odz@dmrs.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        $ceoAuthority->roles()->attach($approvingAuthorityRole->id);

        // Create Document Releaser user (Jasmin) - finalizes and sends documents
        $documentReleaser = User::create([
            'first_name' => 'Jasmin',
            'middle_name' => 'L',
            'last_name' => 'Sambrano',
            'designation' => 'Document Releaser',
            'email' => 'jasmin.releaser@dmrs.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        $documentReleaser->roles()->attach($documentReleaserRole->id);

        // Create Event Manager user
        $eventManager = User::create([
            'first_name' => 'Alice',
            'middle_name' => 'Jane',
            'last_name' => 'Johnson',
            'designation' => 'Event Manager',
            'email' => 'events.manager@dmrs.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        $eventManager->roles()->attach($eventManagerRole->id);

        // Create Employee users
        $employee1 = User::create([
            'first_name' => 'Robert',
            'middle_name' => 'James',
            'last_name' => 'Wilson',
            'designation' => 'Faculty Member',
            'email' => 'robert.wilson@dmrs.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        $employee1->roles()->attach($employeeRole->id);

        $employee2 = User::create([
            'first_name' => 'Emily',
            'middle_name' => null,
            'last_name' => 'Davis',
            'designation' => 'Staff Member',
            'email' => 'emily.davis@dmrs.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        $employee2->roles()->attach($employeeRole->id);

        $employee3 = User::create([
            'first_name' => 'Michael',
            'middle_name' => 'Anthony',
            'last_name' => 'Brown',
            'designation' => 'Department Head',
            'email' => 'michael.brown@dmrs.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        $employee3->roles()->attach($employeeRole->id);
    }
}

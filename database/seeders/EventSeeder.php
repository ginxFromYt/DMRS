<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\User;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get event manager
        $eventManager = User::whereHas('roles', function ($query) {
            $query->where('name', 'Event Manager');
        })->first();

        if (!$eventManager) {
            return;
        }

        $events = [
            // University Events
            [
                'title' => 'Academic Excellence Awards Ceremony',
                'description' => 'Annual ceremony to recognize outstanding academic achievements',
                'category' => 'university',
                'event_date' => now()->addDays(15)->toDateString(),
                'event_time' => '14:00:00',
                'location' => 'University Auditorium',
                'is_deadline' => false,
                'created_by' => $eventManager->id,
            ],
            [
                'title' => 'Research Symposium 2025',
                'description' => 'University-wide research presentation and collaboration event',
                'category' => 'university',
                'event_date' => now()->addDays(30)->toDateString(),
                'event_time' => '09:00:00',
                'location' => 'Main Conference Hall',
                'is_deadline' => false,
                'created_by' => $eventManager->id,
            ],
            [
                'title' => 'Student Enrollment Deadline',
                'description' => 'Final deadline for semester enrollment',
                'category' => 'university',
                'event_date' => now()->addDays(7)->toDateString(),
                'event_time' => '17:00:00',
                'location' => 'Registrar Office',
                'is_deadline' => true,
                'created_by' => $eventManager->id,
            ],

            // Internal Campus Events
            [
                'title' => 'Faculty Meeting - Department Heads',
                'description' => 'Monthly meeting for all department heads',
                'category' => 'internal_campus',
                'event_date' => now()->addDays(5)->toDateString(),
                'event_time' => '10:00:00',
                'location' => 'Board Room A',
                'is_deadline' => false,
                'created_by' => $eventManager->id,
            ],
            [
                'title' => 'IT Infrastructure Maintenance',
                'description' => 'Scheduled maintenance of campus-wide IT systems',
                'category' => 'internal_campus',
                'event_date' => now()->addDays(10)->toDateString(),
                'event_time' => '02:00:00',
                'location' => 'IT Center',
                'is_deadline' => false,
                'created_by' => $eventManager->id,
            ],
            [
                'title' => 'Budget Report Submission',
                'description' => 'Deadline for department budget reports',
                'category' => 'internal_campus',
                'event_date' => now()->addDays(3)->toDateString(),
                'event_time' => '16:00:00',
                'location' => 'Finance Office',
                'is_deadline' => true,
                'created_by' => $eventManager->id,
            ],

            // External Partners Events
            [
                'title' => 'Industry Partnership Forum',
                'description' => 'Networking event with industry partners',
                'category' => 'external_partners',
                'event_date' => now()->addDays(20)->toDateString(),
                'event_time' => '13:00:00',
                'location' => 'Partnership Center',
                'is_deadline' => false,
                'created_by' => $eventManager->id,
            ],
            [
                'title' => 'International Student Exchange Program',
                'description' => 'Orientation for incoming exchange students',
                'category' => 'external_partners',
                'event_date' => now()->addDays(25)->toDateString(),
                'event_time' => '11:00:00',
                'location' => 'International Office',
                'is_deadline' => false,
                'created_by' => $eventManager->id,
            ],
            [
                'title' => 'Partnership Agreement Renewal',
                'description' => 'Deadline for partnership agreement renewals',
                'category' => 'external_partners',
                'event_date' => now()->addDays(12)->toDateString(),
                'event_time' => '15:00:00',
                'location' => 'Legal Office',
                'is_deadline' => true,
                'created_by' => $eventManager->id,
            ],
        ];

        foreach ($events as $event) {
            Event::create($event);
        }
    }
}

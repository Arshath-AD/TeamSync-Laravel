<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@teamsync.test'],
            ['name' => 'Alex Chen', 'password' => Hash::make('password'), 'role' => 'admin']
        );

        $users = collect([
            ['name' => 'Jordan Lee', 'email' => 'jordan@teamsync.test', 'role' => 'user'],
            ['name' => 'Sam Rivera', 'email' => 'sam@teamsync.test', 'role' => 'user'],
            ['name' => 'Taylor Kim', 'email' => 'taylor@teamsync.test', 'role' => 'user'],
            ['name' => 'Morgan Blake', 'email' => 'morgan@teamsync.test', 'role' => 'user'],
        ])->map(fn ($data) => User::updateOrCreate(
            ['email' => $data['email']],
            ['name' => $data['name'], 'password' => Hash::make('password'), 'role' => $data['role']]
        ));

        $projects = [
            [
                'name' => 'Platform Redesign',
                'description' => 'Modernize the core product experience with a focus on workspace productivity and information density.',
                'lead' => $admin,
                'members' => [$users[0], $users[1], $users[2]],
            ],
            [
                'name' => 'Mobile App v2',
                'description' => 'Ship the next generation mobile experience with offline support and push notifications.',
                'lead' => $users[0],
                'members' => [$users[1], $users[3]],
            ],
            [
                'name' => 'API Infrastructure',
                'description' => 'Scale backend services, improve latency, and establish observability standards.',
                'lead' => $users[1],
                'members' => [$users[2], $users[3]],
            ],
            [
                'name' => 'Customer Portal',
                'description' => 'Self-service portal for enterprise customers with billing and support integration.',
                'lead' => $users[2],
                'members' => [$users[0], $users[3]],
            ],
        ];

        $taskTemplates = [
            ['Design system tokens', 'Critical', 'In Progress', 2],
            ['Implement auth flow', 'High', 'Pending', 5],
            ['Write API documentation', 'Medium', 'Pending', 10],
            ['Fix navigation bug', 'High', 'In Progress', -2],
            ['Deploy staging environment', 'Medium', 'Completed', -5],
            ['User research synthesis', 'Low', 'Pending', 14],
            ['Performance audit', 'Critical', 'Pending', -1],
            ['QA regression pass', 'High', 'In Progress', 3],
        ];

        foreach ($projects as $i => $data) {
            $project = Project::updateOrCreate(
                ['project_name' => $data['name']],
                [
                    'description' => $data['description'],
                    'project_lead_id' => $data['lead']->id,
                ]
            );

            $memberIds = collect($data['members'])->pluck('id')->push($data['lead']->id)->unique();
            $project->members()->syncWithoutDetaching($memberIds->mapWithKeys(fn ($id) => [$id => ['created_at' => now(), 'updated_at' => now()]])->all());

            foreach ($taskTemplates as $j => $tpl) {
                $assignee = $users[($i + $j) % $users->count()];
                Task::updateOrCreate(
                    [
                        'project_id' => $project->id,
                        'task_name' => $tpl[0] . ' — ' . substr($data['name'], 0, 8),
                    ],
                    [
                        'description' => 'Task for ' . $data['name'],
                        'assigned_to' => $assignee->id,
                        'priority' => $tpl[1],
                        'status' => $tpl[2],
                        'deadline' => now()->addDays($tpl[3])->toDateString(),
                    ]
                );
            }
        }
    }
}

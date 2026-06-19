<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class VerifyModels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:verify-models';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("==============================");
        $this->info("USER VERIFICATION");
        $this->info("==============================");
        $user = \App\Models\User::with(['ledProjects', 'tasks', 'memberOfProjects'])->first();
        if ($user) {
            $this->line(json_encode($user->toArray(), JSON_PRETTY_PRINT));
        } else {
            $this->error("No user found.");
        }

        $this->info("\n==============================");
        $this->info("PROJECT VERIFICATION");
        $this->info("==============================");
        $project = \App\Models\Project::with(['lead', 'tasks', 'members'])->first();
        if ($project) {
            $this->line(json_encode($project->toArray(), JSON_PRETTY_PRINT));
        } else {
            $this->error("No project found.");
        }

        $this->info("\n==============================");
        $this->info("TASK VERIFICATION");
        $this->info("==============================");
        $task = \App\Models\Task::with(['project', 'assignee'])->first();
        if ($task) {
            $this->line(json_encode($task->toArray(), JSON_PRETTY_PRINT));
        } else {
            $this->error("No task found.");
        }
    }
}

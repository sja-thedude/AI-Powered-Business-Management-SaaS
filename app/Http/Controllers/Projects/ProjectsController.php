<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Models\Projects\Project;
use App\Models\Projects\Task;
use App\Models\Projects\TimeEntry;
use Inertia\Inertia;

class ProjectsController extends Controller
{
    public function index()
    {
        return Inertia::render('Modules/Scaffold', [
            'module' => [
                'name' => 'Projects',
                'icon' => 'rectangle-stack',
                'description' => 'Tasks, teams, Kanban boards & time tracking.',
                'status' => 'scaffold',
            ],
            'stats' => [
                ['label' => 'Projects', 'value' => Project::count(), 'icon' => 'rectangle-stack', 'accent' => 'brand'],
                ['label' => 'Active Projects', 'value' => Project::where('status', 'active')->count(), 'icon' => 'rectangle-stack', 'accent' => 'emerald'],
                ['label' => 'Tasks', 'value' => Task::count(), 'icon' => 'rectangle-stack', 'accent' => 'violet'],
                ['label' => 'Time Entries', 'value' => TimeEntry::count(), 'icon' => 'clock', 'accent' => 'amber'],
            ],
            'tables' => ['projects', 'project_members', 'task_columns', 'tasks', 'time_entries'],
        ]);
    }
}

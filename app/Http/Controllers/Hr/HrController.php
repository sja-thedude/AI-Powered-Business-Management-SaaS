<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\Hr\Department;
use App\Models\Hr\Employee;
use App\Models\Hr\JobPosting;
use App\Models\Hr\LeaveRequest;
use Inertia\Inertia;

class HrController extends Controller
{
    public function index()
    {
        return Inertia::render('Modules/Scaffold', [
            'module' => [
                'name' => 'HR Management',
                'icon' => 'id-badge',
                'description' => 'Employee records, recruitment, leave & performance tracking.',
                'status' => 'scaffold',
            ],
            'stats' => [
                ['label' => 'Employees', 'value' => Employee::count(), 'icon' => 'id-badge', 'accent' => 'brand'],
                ['label' => 'Departments', 'value' => Department::count(), 'icon' => 'building', 'accent' => 'violet'],
                ['label' => 'Open Postings', 'value' => JobPosting::where('status', 'open')->count(), 'icon' => 'id-badge', 'accent' => 'emerald'],
                ['label' => 'Pending Leave', 'value' => LeaveRequest::where('status', 'pending')->count(), 'icon' => 'clock', 'accent' => 'amber'],
            ],
            'tables' => ['departments', 'employees', 'job_postings', 'job_applications', 'leave_requests', 'performance_reviews'],
        ]);
    }
}

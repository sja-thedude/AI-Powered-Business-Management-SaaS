<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance\Attendance;
use App\Models\Attendance\Shift;
use App\Models\Attendance\ShiftAssignment;
use Inertia\Inertia;

class AttendanceController extends Controller
{
    public function index()
    {
        return Inertia::render('Modules/Scaffold', [
            'module' => [
                'name' => 'Attendance',
                'icon' => 'clock',
                'description' => 'Check-in/out, biometric-ready structure & shift management.',
                'status' => 'scaffold',
            ],
            'stats' => [
                ['label' => 'Shifts', 'value' => Shift::count(), 'icon' => 'clock', 'accent' => 'brand'],
                ['label' => 'Assignments', 'value' => ShiftAssignment::count(), 'icon' => 'id-badge', 'accent' => 'violet'],
                ['label' => 'Records', 'value' => Attendance::count(), 'icon' => 'clock', 'accent' => 'emerald'],
            ],
            'tables' => ['shifts', 'shift_assignments', 'attendances'],
        ]);
    }
}

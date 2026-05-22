<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Payroll\PayrollRun;
use App\Models\Payroll\Payslip;
use App\Models\Payroll\SalaryStructure;
use App\Models\Payroll\TaxRule;
use Inertia\Inertia;

class PayrollController extends Controller
{
    public function index()
    {
        return Inertia::render('Modules/Scaffold', [
            'module' => [
                'name' => 'Payroll',
                'icon' => 'banknotes',
                'description' => 'Salary generation, payslips, tax calculations & attendance-linked pay.',
                'status' => 'scaffold',
            ],
            'stats' => [
                ['label' => 'Payroll Runs', 'value' => PayrollRun::count(), 'icon' => 'banknotes', 'accent' => 'brand'],
                ['label' => 'Payslips', 'value' => Payslip::count(), 'icon' => 'document-text', 'accent' => 'violet'],
                ['label' => 'Salary Structures', 'value' => SalaryStructure::count(), 'icon' => 'banknotes', 'accent' => 'emerald'],
                ['label' => 'Tax Rules', 'value' => TaxRule::count(), 'icon' => 'document-text', 'accent' => 'amber'],
            ],
            'tables' => ['salary_structures', 'payroll_runs', 'payslips', 'tax_rules'],
        ]);
    }
}

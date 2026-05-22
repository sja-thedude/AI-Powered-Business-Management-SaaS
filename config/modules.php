<?php

/*
|--------------------------------------------------------------------------
| Modular ERP Registry
|--------------------------------------------------------------------------
|
| NovaBiz AI is designed so new ERP modules can be added "forever". Every
| module is declared here once and the rest of the platform reacts to it:
|
|   - The sidebar navigation is generated from this list.
|   - Plan/subscription gating reads `min_plan` to decide availability.
|   - Permissions are seeded per-module (see PermissionSeeder).
|   - Feature flags per tenant can override availability at runtime.
|
| `status` is either "stable" (shipped) or "scaffold" (schema + models in
| place, UI/logic being built out) so the UI can badge work-in-progress.
|
*/

return [

    'crm' => [
        'name' => 'CRM',
        'icon' => 'users',
        'description' => 'Leads, clients, pipelines, sales tracking & communication history.',
        'route' => 'crm.dashboard',
        'min_plan' => 'starter',
        'status' => 'stable',
        'order' => 10,
    ],

    'hr' => [
        'name' => 'HR Management',
        'icon' => 'id-badge',
        'description' => 'Employee records, recruitment, leave & performance tracking.',
        'route' => 'hr.index',
        'min_plan' => 'growth',
        'status' => 'scaffold',
        'order' => 20,
    ],

    'payroll' => [
        'name' => 'Payroll',
        'icon' => 'banknotes',
        'description' => 'Salary generation, payslips, tax calculations & attendance-linked pay.',
        'route' => 'payroll.index',
        'min_plan' => 'growth',
        'status' => 'scaffold',
        'order' => 30,
    ],

    'attendance' => [
        'name' => 'Attendance',
        'icon' => 'clock',
        'description' => 'Check-in/out, biometric-ready structure & shift management.',
        'route' => 'attendance.index',
        'min_plan' => 'growth',
        'status' => 'scaffold',
        'order' => 40,
    ],

    'inventory' => [
        'name' => 'Inventory',
        'icon' => 'cube',
        'description' => 'Stock, warehouses, suppliers & inventory analytics.',
        'route' => 'inventory.index',
        'min_plan' => 'growth',
        'status' => 'scaffold',
        'order' => 50,
    ],

    'invoicing' => [
        'name' => 'Invoicing',
        'icon' => 'document-text',
        'description' => 'Invoice generation, online payments, tax handling & analytics.',
        'route' => 'invoicing.index',
        'min_plan' => 'starter',
        'status' => 'scaffold',
        'order' => 60,
    ],

    'projects' => [
        'name' => 'Projects',
        'icon' => 'rectangle-stack',
        'description' => 'Tasks, teams, Kanban boards & time tracking.',
        'route' => 'projects.index',
        'min_plan' => 'starter',
        'status' => 'scaffold',
        'order' => 70,
    ],

    'portal' => [
        'name' => 'Client Portal',
        'icon' => 'lifebuoy',
        'description' => 'Client dashboard, ticketing, file sharing & communication.',
        'route' => 'portal.index',
        'min_plan' => 'growth',
        'status' => 'scaffold',
        'order' => 80,
    ],

];

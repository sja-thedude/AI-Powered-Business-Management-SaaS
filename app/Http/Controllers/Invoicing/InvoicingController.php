<?php

namespace App\Http\Controllers\Invoicing;

use App\Http\Controllers\Controller;
use App\Models\Invoicing\Invoice;
use App\Models\Invoicing\Payment;
use Inertia\Inertia;

class InvoicingController extends Controller
{
    public function index()
    {
        return Inertia::render('Modules/Scaffold', [
            'module' => [
                'name' => 'Invoicing',
                'icon' => 'document-text',
                'description' => 'Invoice generation, online payments, tax handling & analytics.',
                'status' => 'scaffold',
            ],
            'stats' => [
                ['label' => 'Invoices', 'value' => Invoice::count(), 'icon' => 'document-text', 'accent' => 'brand'],
                ['label' => 'Paid', 'value' => Invoice::where('status', 'paid')->count(), 'icon' => 'document-text', 'accent' => 'emerald'],
                ['label' => 'Outstanding', 'value' => Invoice::whereIn('status', ['draft', 'sent', 'overdue'])->count(), 'icon' => 'document-text', 'accent' => 'amber'],
                ['label' => 'Payments', 'value' => Payment::count(), 'icon' => 'banknotes', 'accent' => 'violet'],
            ],
            'tables' => ['invoices', 'invoice_items', 'payments'],
        ]);
    }
}

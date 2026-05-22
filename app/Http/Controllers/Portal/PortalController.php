<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Portal\PortalMessage;
use App\Models\Portal\SharedFile;
use App\Models\Portal\Ticket;
use Inertia\Inertia;

class PortalController extends Controller
{
    public function index()
    {
        return Inertia::render('Modules/Scaffold', [
            'module' => [
                'name' => 'Client Portal',
                'icon' => 'lifebuoy',
                'description' => 'Client dashboard, ticketing, file sharing & communication.',
                'status' => 'scaffold',
            ],
            'stats' => [
                ['label' => 'Tickets', 'value' => Ticket::count(), 'icon' => 'lifebuoy', 'accent' => 'brand'],
                ['label' => 'Open Tickets', 'value' => Ticket::where('status', 'open')->count(), 'icon' => 'lifebuoy', 'accent' => 'amber'],
                ['label' => 'Shared Files', 'value' => SharedFile::count(), 'icon' => 'document-text', 'accent' => 'violet'],
                ['label' => 'Messages', 'value' => PortalMessage::count(), 'icon' => 'lifebuoy', 'accent' => 'emerald'],
            ],
            'tables' => ['tickets', 'ticket_replies', 'shared_files', 'portal_messages'],
        ]);
    }
}

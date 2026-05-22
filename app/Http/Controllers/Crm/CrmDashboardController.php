<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Crm\Deal;
use App\Models\Crm\Lead;
use Inertia\Inertia;

class CrmDashboardController extends Controller
{
    public function __invoke()
    {
        $this->authorize('viewAny', Lead::class);

        return Inertia::render('Crm/Dashboard', [
            'kpis' => [
                'leads_total'  => Lead::count(),
                'leads_hot'    => Lead::where('score', '>=', 70)->count(),
                'open_deals'   => Deal::whereNotIn('status', ['won', 'lost'])->count(),
                'pipeline'     => (float) Deal::whereNotIn('status', ['won', 'lost'])->sum('value'),
                'won_value'    => (float) Deal::where('status', 'won')->sum('value'),
                'win_rate'     => $this->winRate(),
            ],
            'topLeads' => Lead::orderByDesc('score')
                ->where('status', '!=', 'converted')
                ->limit(5)->get(['id', 'name', 'company', 'score', 'status']),
            'closingSoon' => Deal::whereNotIn('status', ['won', 'lost'])
                ->whereNotNull('expected_close_date')
                ->orderBy('expected_close_date')
                ->with('client:id,name')
                ->limit(5)->get(),
        ]);
    }

    protected function winRate(): float
    {
        $closed = Deal::whereIn('status', ['won', 'lost'])->count();

        return $closed === 0
            ? 0.0
            : round(Deal::where('status', 'won')->count() / $closed * 100, 1);
    }
}

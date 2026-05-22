<?php

namespace App\Http\Controllers;

use App\Models\Crm\Activity;
use App\Models\Crm\Client;
use App\Models\Crm\Deal;
use App\Models\Crm\Lead;
use App\Support\TenantContext;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

/**
 * The command-center dashboard. Aggregates cross-module KPIs and time-series
 * for the analytics widgets. Everything is tenant-scoped automatically by the
 * global TenantScope, so these queries only ever see the current workspace.
 */
class DashboardController extends Controller
{
    public function __invoke(TenantContext $context)
    {
        $tenant = $context->get();
        $startOfWeek = now()->startOfWeek();
        $startOfMonth = now()->startOfMonth();

        $kpis = [
            'leads_total'    => Lead::count(),
            'leads_new'      => Lead::where('created_at', '>=', $startOfWeek)->count(),
            'clients_total'  => Client::count(),
            'deals_open'     => Deal::whereNotIn('status', ['won', 'lost'])->count(),
            'pipeline_value' => (float) Deal::whereNotIn('status', ['won', 'lost'])->sum('value'),
            'won_this_month' => (float) Deal::where('status', 'won')
                ->where('closed_at', '>=', $startOfMonth)->sum('value'),
        ];

        return Inertia::render('Dashboard', [
            'kpis'             => $kpis,
            'revenueSeries'    => $this->revenueSeries(),
            'leadsBySource'    => $this->leadsBySource(),
            'pipelineByStage'  => $this->pipelineByStage(),
            'recentActivities' => Activity::with('user:id,name')
                ->latest()->limit(8)->get(),
            'currency'         => $tenant->currency,
        ]);
    }

    /** Won-deal revenue for the last 6 months (DB-agnostic month bucketing). */
    protected function revenueSeries(): array
    {
        $rows = Deal::query()
            ->where('status', 'won')
            ->where('closed_at', '>=', now()->subMonths(5)->startOfMonth())
            ->get(['value', 'closed_at'])
            ->groupBy(fn ($deal) => Carbon::parse($deal->closed_at)->format('Y-m'))
            ->map(fn ($group) => (float) $group->sum('value'));

        return collect(range(5, 0))
            ->map(function ($monthsAgo) use ($rows) {
                $month = now()->subMonths($monthsAgo);

                return [
                    'label' => $month->format('M'),
                    'value' => $rows->get($month->format('Y-m'), 0),
                ];
            })
            ->values()
            ->all();
    }

    protected function leadsBySource(): array
    {
        return Lead::query()
            ->select('source', DB::raw('count(*) as total'))
            ->groupBy('source')
            ->pluck('total', 'source')
            ->all();
    }

    protected function pipelineByStage(): array
    {
        return Deal::query()
            ->whereNotIn('status', ['won', 'lost'])
            ->join('pipeline_stages', 'deals.pipeline_stage_id', '=', 'pipeline_stages.id')
            ->select('pipeline_stages.name', DB::raw('sum(deals.value) as total'))
            ->groupBy('pipeline_stages.name')
            ->pluck('total', 'name')
            ->all();
    }
}

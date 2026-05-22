<?php

namespace App\Http\Controllers\Crm;

use App\Events\Crm\DealMoved;
use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\DealRequest;
use App\Models\Crm\Client;
use App\Models\Crm\Deal;
use App\Models\Crm\Pipeline;
use App\Services\Crm\PipelineFactory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DealController extends Controller
{
    /** Kanban board for a pipeline: stages as columns, deals as cards. */
    public function board(Request $request, PipelineFactory $factory)
    {
        $this->authorize('viewAny', Deal::class);

        $pipeline = $request->filled('pipeline')
            ? Pipeline::with('stages')->findOrFail($request->integer('pipeline'))
            : $factory->defaultPipeline();

        $pipeline->load(['stages.deals' => fn ($q) => $q
            ->whereNotIn('status', ['won', 'lost'])
            ->with('client:id,name', 'owner:id,name')
            ->orderBy('position')]);

        return Inertia::render('Crm/Deals/Board', [
            'pipeline'  => $pipeline,
            'pipelines' => Pipeline::select('id', 'name')->get(),
            'clients'   => Client::select('id', 'name')->orderBy('name')->get(),
            'summary'   => [
                'open_value' => (float) Deal::where('pipeline_id', $pipeline->id)
                    ->whereNotIn('status', ['won', 'lost'])->sum('value'),
                'open_count' => Deal::where('pipeline_id', $pipeline->id)
                    ->whereNotIn('status', ['won', 'lost'])->count(),
            ],
        ]);
    }

    public function store(DealRequest $request)
    {
        $this->authorize('create', Deal::class);

        Deal::create($request->validated() + [
            'owner_id' => $request->input('owner_id', $request->user()->id),
            'status'   => 'open',
        ]);

        return back()->with('success', 'Deal created.');
    }

    public function update(DealRequest $request, Deal $deal)
    {
        $this->authorize('update', $deal);

        $deal->update($request->validated());

        return back()->with('success', 'Deal updated.');
    }

    /**
     * Drag-and-drop: move a deal to a stage/position. Broadcasts so other
     * users watching the board see the card move in real time.
     */
    public function move(Request $request, Deal $deal)
    {
        $this->authorize('update', $deal);

        $data = $request->validate([
            'pipeline_stage_id' => ['required', 'integer', 'exists:pipeline_stages,id'],
            'position'          => ['required', 'integer', 'min:0'],
        ]);

        $deal->update($data);

        broadcast(new DealMoved($deal))->toOthers();

        return back();
    }

    public function won(Deal $deal)
    {
        $this->authorize('update', $deal);
        $deal->markWon();

        return back()->with('success', 'Deal marked as won 🎉');
    }

    public function lost(Request $request, Deal $deal)
    {
        $this->authorize('update', $deal);
        $deal->markLost($request->string('reason')->toString() ?: null);

        return back()->with('success', 'Deal marked as lost.');
    }

    public function destroy(Deal $deal)
    {
        $this->authorize('delete', $deal);
        $deal->delete();

        return back()->with('success', 'Deal deleted.');
    }
}

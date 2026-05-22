<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Crm\Activity;
use App\Models\Crm\Client;
use App\Models\Crm\Deal;
use App\Models\Crm\Lead;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Logs communication-history entries (notes, calls, emails, meetings, tasks)
 * against any CRM subject. The subject is resolved from a short key so the
 * frontend can post from a lead, client, or deal timeline uniformly.
 */
class ActivityController extends Controller
{
    protected const SUBJECTS = [
        'lead'   => Lead::class,
        'client' => Client::class,
        'deal'   => Deal::class,
    ];

    public function store(Request $request)
    {
        $this->authorize('create', Activity::class);

        $data = $request->validate([
            'subject_type' => ['required', Rule::in(array_keys(self::SUBJECTS))],
            'subject_id'   => ['required', 'integer'],
            'type'         => ['required', Rule::in(['note', 'call', 'email', 'meeting', 'task'])],
            'title'        => ['nullable', 'string', 'max:255'],
            'body'         => ['nullable', 'string'],
            'direction'    => ['nullable', Rule::in(['inbound', 'outbound'])],
            'scheduled_at' => ['nullable', 'date'],
        ]);

        // Resolve and authorize the subject is in the current tenant (scoped find).
        $subjectClass = self::SUBJECTS[$data['subject_type']];
        $subject = $subjectClass::findOrFail($data['subject_id']);

        $subject->activities()->create([
            'user_id'      => $request->user()->id,
            'type'         => $data['type'],
            'title'        => $data['title'] ?? null,
            'body'         => $data['body'] ?? null,
            'direction'    => $data['direction'] ?? null,
            'scheduled_at' => $data['scheduled_at'] ?? null,
            'completed_at' => $data['type'] === 'task' ? null : now(),
        ]);

        return back()->with('success', 'Activity logged.');
    }

    public function destroy(Activity $activity)
    {
        $this->authorize('delete', Activity::class);

        $activity->delete();

        return back()->with('success', 'Activity removed.');
    }
}

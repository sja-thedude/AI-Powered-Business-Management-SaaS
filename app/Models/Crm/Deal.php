<?php

namespace App\Models\Crm;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToTenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Deal extends Model
{
    use HasFactory, BelongsToTenant, Auditable, SoftDeletes;

    protected $fillable = [
        'tenant_id', 'pipeline_id', 'pipeline_stage_id', 'client_id', 'owner_id',
        'title', 'value', 'currency', 'status', 'probability', 'position',
        'expected_close_date', 'closed_at', 'lost_reason',
    ];

    protected function casts(): array
    {
        return [
            'value'               => 'decimal:2',
            'expected_close_date' => 'date',
            'closed_at'           => 'datetime',
        ];
    }

    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(Pipeline::class);
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(PipelineStage::class, 'pipeline_stage_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'subject')->latest();
    }

    public function markWon(): void
    {
        $this->update(['status' => 'won', 'closed_at' => now(), 'probability' => 100]);
    }

    public function markLost(?string $reason = null): void
    {
        $this->update(['status' => 'lost', 'closed_at' => now(), 'lost_reason' => $reason]);
    }
}
